<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\TemplateMedicine;

class PostSurgeryController extends Controller
{
    /* ══════════════════════════════════════════
       INDEX — Patient list
       ✅ শুধু সেই patients যাদের
          on_admission record আছে এবং
          status = 'moved_to_postsurgery'
          (post_surgery_done হলে আর দেখাবে না)
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->where('nursing_admissions.status', 'moved_to_postsurgery')
            ->select(
                'patients.id',
                'patients.patientname',
                'patients.patientcode',
                'patients.patientfather',
                'patients.mobile_no',
                'patients.age',
                'patients.gender',
                'patients.blood_group',
                'patients.address',
                'patients.upozila',
                DB::raw('MAX(nursing_admissions.id) as admission_id'),
                DB::raw('MAX(nursing_admissions.admission_date) as admission_date'),
                DB::raw('MAX(nursing_admissions.rx_date) as rx_date')
            )
            ->groupBy(
                'patients.id',
                'patients.patientname',
                'patients.patientcode',
                'patients.patientfather',
                'patients.mobile_no',
                'patients.age',
                'patients.gender',
                'patients.blood_group',
                'patients.address',
                'patients.upozila'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patients.patientname',  'LIKE', "%{$search}%")
                  ->orWhere('patients.patientcode', 'LIKE', "%{$search}%")
                  ->orWhere('patients.mobile_no',   'LIKE', "%{$search}%");
            });
        }

        $patients  = $query->orderBy('admission_id', 'desc')->paginate(20)->withQueryString();
        
        // Query for past post-surgery prescriptions (latest per patient)
        $postSurgeryQuery = DB::table('nursing_postsurgery_prescriptions')
            ->join('patients', 'nursing_postsurgery_prescriptions.patient_id', '=', 'patients.id')
            ->select(
                'nursing_postsurgery_prescriptions.*',
                'patients.patientname',
                'patients.patientcode',
                'patients.age',
                'patients.gender',
                'patients.mobile_no',
                'patients.address',
                'patients.upozila',
                'patients.blood_group'
            )
            ->whereRaw('nursing_postsurgery_prescriptions.id IN (
                SELECT MAX(id) 
                FROM nursing_postsurgery_prescriptions 
                GROUP BY patient_id
            )')
            ->orderBy('nursing_postsurgery_prescriptions.created_at', 'desc');
        
        $PostSurgeryPatients = $postSurgeryQuery->paginate(20)->withQueryString();
        
        $medicines = DB::table('template_medicine')->where('order_type', 'preorder')->orderBy('group')->get();
        $templates = DB::table('tbl_template')
                        ->where('status', 1)
                        ->orderBy('title')
                        ->get();

        return view('nursing.postsurgery', compact(
            'patients',
            'PostSurgeryPatients',
            'search',
            'medicines',
            'templates'
        ));
    }

    /* ══════════════════════════════════════════
       GET PATIENT ADMISSION DATA (AJAX)
    ══════════════════════════════════════════ */
    public function getPatientAdmissionData($patientId)
    {
        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderBy('id', 'desc')
            ->first();

        $medicines = [];
        if ($admission) {
            $rawMeds = DB::table('nursing_admission_medicines')
                ->where('nursing_admission_id', $admission->id)
                ->get();

            foreach ($rawMeds as $m) {
                $medicines[] = [
                    'medicine_name' => $m->medicine_name ?? $m->name ?? '',
                    'dose'          => $m->dose          ?? '',
                    'route'         => $m->route         ?? '',
                    'frequency'     => $m->frequency     ?? '',
                    'duration'      => $m->duration      ?? '',
                    'timing'        => $m->timing        ?? '',
                ];
            }
        }

        return response()->json([
            'success'                    => true,
            'admission'                  => $admission,
            'medicines'                  => $medicines,
            'previous_prescriptions'     => $this->getPreviousAdmissionPrescriptions($patientId),
            'fresh_prescriptions'        => $this->getFreshPrescriptions($patientId),
            'post_surgery_prescriptions' => $this->getPostSurgeryPrescriptions($patientId),
            'message'                    => $admission
                                            ? 'Admission record found.'
                                            : 'No admission record found.',
        ]);
    }

    /* ══════════════════════════════════════════
       STORE POST-SURGERY PRESCRIPTION (AJAX)
       ✅ Save হলে status = 'post_surgery_done'
          PostSurgery list থেকে সরে Fresh এ যাবে
    ══════════════════════════════════════════ */
    public function storePrescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'        => 'required|integer',
            'patient_name'      => 'nullable|string|max:255',
            'patient_age'       => 'nullable|string|max:100',
            'prescription_date' => 'nullable|date',
            'post_op_time'      => 'nullable',
            'notes'             => 'nullable|string',
            'medicines'         => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $this->ensureTables();

        DB::beginTransaction();
        try {
            $rxId = DB::table('nursing_postsurgery_prescriptions')->insertGetId([
                'patient_id'        => $request->patient_id,
                'patient_name'      => $request->patient_name,
                'patient_age'       => $request->patient_age,
                'prescription_date' => $request->prescription_date ?: now()->toDateString(),
                'post_op_time'      => $request->post_op_time      ?: null,
                'notes'             => $request->notes,
                'created_by'        => auth()->id(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $rows = [];
            foreach ((array) $request->input('medicines', []) as $m) {
                $name = trim((string) ($m['name'] ?? $m['medicine_name'] ?? ''));
                if ($name === '') continue;

                $rows[] = [
                    'postsurgery_prescription_id' => $rxId,
                    'medicine_name'               => $name,
                    'strength'                    => $m['strength']  ?? null,
                    'dose'                        => $m['dose']      ?? null,
                    'route'                       => $m['route']     ?? null,
                    'frequency'                   => $m['frequency'] ?? null,
                    'duration'                    => $m['duration']  ?? null,
                    'timing'                      => $m['timing']    ?? null,
                    'note'                        => $m['note']      ?? null,
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ];
            }

            if (!empty($rows)) {
                DB::table('nursing_postsurgery_medicines')->insert($rows);
            }

            // ✅ Fix: 'fresh' → 'post_surgery_done' (consistent status)
            DB::table('nursing_admissions')
                ->where('patient_id', $request->patient_id)
                ->where('admission_type', 'on_admission')
                ->update([
                    'status'     => 'post_surgery_done', // ✅ match FreshController expectation
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Post-surgery prescription saved successfully. Patient moved to Fresh list.',
                'prescription_id' => $rxId,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Save failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
       GET PRE-OPERATION MEDICINES FROM TEMPLATES (AJAX)
       ✅ Fetch all pre-order medicines from all templates
    ══════════════════════════════════════════ */
    public function getPreOperationMedicines()
    {
        try {
            // Get all Pre-Operation medicines directly using the model
            $preOpMeds = TemplateMedicine::where('order_type', 'preorder')
                ->where('active', 1)
                ->orderBy('group')
                ->orderBy('name')
                ->get();

            \Log::info("Direct query found: " . $preOpMeds->count() . " Pre-Operation medicines");

            // Format the response
            $allPreOpMeds = [];
            foreach ($preOpMeds as $med) {
                $allPreOpMeds[] = [
                    'id'           => $med->id,
                    'name'         => $med->name,
                    'dose'         => $med->dose,
                    'route'        => $med->route,
                    'frequency'    => $med->frequency,
                    'duration'     => $med->duration,
                    'timing'       => $med->timing,
                    'order_type'   => $med->order_type,
                    'templateid'   => $med->templeteid,
                    'template_name'=> 'Template ' . $med->templeteid
                ];
            }

            \Log::info("Final result: " . count($allPreOpMeds) . " Pre-Operation medicines");

            return response()->json([
                'success' => true,
                'count'   => count($allPreOpMeds),
                'rows'    => $allPreOpMeds,
                'message' => count($allPreOpMeds) . ' Pre-Operation medicines found'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPreOperationMedicines: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Pre-Operation medicines: ' . $e->getMessage()
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
       TEMPLATE DATA & APPLY TEMPLATE
       (UNCHANGED LOGIC)
    ══════════════════════════════════════════ */
    public function getTemplateData($id)
    {
        $template = DB::table('tbl_template')->where('ID', $id)->first()
                 ?? DB::table('tbl_template')->where('templateid', $id)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $tplCode = $template->templateid ?? null;

        return response()->json([
            'success'        => true,
            'template'       => $template,
            'medicines'      => $this->safeFetch('template_medicine',      $tplCode, 'group'),
            'investigations' => $this->safeFetch('template_investigations', $tplCode, 'id'),
            'diagnoses'      => $this->safeFetch('template_diagnosis',      $tplCode, 'id'),
        ]);
    }

    public function applyTemplate(Request $request)
    {
        $templateId = $request->get('template_id');
        if (!$templateId) {
            return response()->json(['success' => false, 'message' => 'Template ID is required'], 422);
        }

        $template = DB::table('tbl_template')->where('ID', $templateId)->first()
                 ?? DB::table('tbl_template')->where('templateid', $templateId)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $tplCode = $template->templateid ?? null;

        return response()->json([
            'success'        => true,
            'message'        => 'Template applied successfully',
            'template'       => $template,
            'medicines'      => $this->safeFetch('template_medicine',      $tplCode, 'group'),
            'investigations' => $this->safeFetch('template_investigations', $tplCode, 'id'),
            'diagnoses'      => $this->safeFetch('template_diagnosis',      $tplCode, 'id'),
        ]);
    }

    /* ══════════════════════════════════════════
       DETAIL — Show post-surgery prescription details
       GET /postsurgery/detail/{id}
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        // Debug: Log the ID being requested
        \Log::info('PostSurgery detail requested for ID: ' . $id);
        
        $prescription = DB::table('nursing_postsurgery_prescriptions')->where('id', $id)->first();
        
        // Debug: Check if prescription exists
        if (!$prescription) {
            \Log::warning('PostSurgery prescription not found for ID: ' . $id);
            
            // Check if there are any prescriptions at all
            $allPrescriptions = DB::table('nursing_postsurgery_prescriptions')->get();
            \Log::info('Total post-surgery prescriptions in DB: ' . $allPrescriptions->count());
            
            return response()->json(['success' => false, 'message' => 'Post-surgery prescription not found.'], 404);
        }
        
        \Log::info('PostSurgery prescription found: ' . json_encode($prescription));

        $medicines = DB::table('nursing_postsurgery_medicines')
            ->where('postsurgery_prescription_id', $id)
            ->get();

        // Get patient information
        $patient = DB::table('patients')->where('id', $prescription->patient_id)->first();

        // Get all patient post-surgery prescriptions for history
        $patientPrescriptions = DB::table('nursing_postsurgery_prescriptions')
            ->where('patient_id', $prescription->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $prescription->id,
                'patient_name' => $prescription->patient_name,
                'patient_age' => $prescription->patient_age,
                'prescription_date' => $prescription->prescription_date,
                'post_op_time' => $prescription->post_op_time,
                'notes' => $prescription->notes,
                'created_at' => $prescription->created_at,
                'medicines' => $medicines,
                'patient' => $patient,
                'patientPrescriptions' => $patientPrescriptions
            ]
        ]);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
       (UNCHANGED LOGIC)
    ══════════════════════════════════════════ */

    private function getPreviousAdmissionPrescriptions($patientId): array
    {
        $result = [];
        if (!Schema::hasTable('nursing_admissions')) return $result;

        $rows = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        foreach ($rows as $row) {
            $lines = [];
            if (Schema::hasTable('nursing_admission_medicines')) {
                $meds = DB::table('nursing_admission_medicines')
                    ->where('nursing_admission_id', $row->id)
                    ->get();

                foreach ($meds as $m) {
                    $parts = array_filter([
                        $m->medicine_name ?? $m->name ?? null,
                        $m->dose      ?? null,
                        $m->route     ?? null,
                        $m->frequency ?? null,
                        !empty($m->duration) ? ('× ' . $m->duration) : null,
                        !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_filter(preg_split('/\r\n|\r|\n/', trim((string) $row->notes)));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->rx_date ?? $row->admission_date ?? $row->created_at,
                'doctor' => null,
                'type'   => 'on_admission',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    private function getFreshPrescriptions($patientId): array
    {
        if (!Schema::hasTable('nursing_fresh_prescriptions')) return [];

        $rows = DB::table('nursing_fresh_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];
            if (Schema::hasTable('nursing_fresh_medicines')) {
                $meds = DB::table('nursing_fresh_medicines')
                    ->where('fresh_prescription_id', $row->id)
                    ->get();

                foreach ($meds as $m) {
                    $parts = array_filter([
                        $m->medicine_name ?? null,
                        $m->dose      ?? null,
                        $m->route     ?? null,
                        $m->frequency ?? null,
                        !empty($m->duration) ? ('× ' . $m->duration) : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->rx_text)) {
                $lines = array_filter(preg_split('/\r\n|\r|\n/', trim((string) $row->rx_text)));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->prescription_date ?? $row->created_at,
                'doctor' => $row->doctor_name ?? null,
                'type'   => 'fresh',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    private function getPostSurgeryPrescriptions($patientId): array
    {
        if (!Schema::hasTable('nursing_postsurgery_prescriptions')) return [];

        $rows = DB::table('nursing_postsurgery_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];
            if (Schema::hasTable('nursing_postsurgery_medicines')) {
                $meds = DB::table('nursing_postsurgery_medicines')
                    ->where('postsurgery_prescription_id', $row->id)
                    ->get();

                foreach ($meds as $m) {
                    $parts = array_filter([
                        $m->medicine_name ?? null,
                        $m->strength  ?? null,
                        $m->dose      ?? null,
                        $m->route     ?? null,
                        $m->frequency ?? null,
                        !empty($m->duration) ? ('× ' . $m->duration) : null,
                        !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_filter(preg_split('/\r\n|\r|\n/', trim((string) $row->notes)));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->prescription_date ?? $row->created_at,
                'doctor' => null,
                'type'   => 'post-surgery',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    private function ensureTables(): void
    {
        if (!Schema::hasTable('nursing_postsurgery_prescriptions')) {
            DB::statement("
                CREATE TABLE nursing_postsurgery_prescriptions (
                    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    patient_id        BIGINT UNSIGNED NOT NULL,
                    patient_name      VARCHAR(255)    NULL,
                    patient_age       VARCHAR(100)    NULL,
                    prescription_date DATE            NULL,
                    post_op_time      TIME            NULL,
                    notes             TEXT            NULL,
                    created_by        BIGINT UNSIGNED NULL,
                    created_at        TIMESTAMP       NULL,
                    updated_at        TIMESTAMP       NULL
                )
            ");
        }

        if (!Schema::hasTable('nursing_postsurgery_medicines')) {
            DB::statement("
                CREATE TABLE nursing_postsurgery_medicines (
                    id                           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    postsurgery_prescription_id  BIGINT UNSIGNED NOT NULL,
                    medicine_name                VARCHAR(255)    NULL,
                    strength                     VARCHAR(100)    NULL,
                    dose                         VARCHAR(100)    NULL,
                    route                        VARCHAR(100)    NULL,
                    frequency                    VARCHAR(100)    NULL,
                    duration                     VARCHAR(100)    NULL,
                    timing                       VARCHAR(100)    NULL,
                    note                         TEXT            NULL,
                    created_at                   TIMESTAMP       NULL,
                    updated_at                   TIMESTAMP       NULL,
                    INDEX idx_rx_id (postsurgery_prescription_id)
                )
            ");
        }
    }

    private function safeFetch(string $table, $tplCode, string $orderBy = 'id')
    {
        try {
            return DB::table($table)
                ->when($tplCode, fn($q) => $q->where('templateid', $tplCode))
                ->orderBy($orderBy)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
}