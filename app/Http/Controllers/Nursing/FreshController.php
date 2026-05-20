<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\TemplateMedicine;
use App\Services\FreshMedicineService;

class FreshController extends Controller
{
    protected $medicineService;

    public function __construct(FreshMedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    /* ══════════════════════════════════════════
       INDEX — Fresh patient list
       ✅ শুধু সেই patients যাদের
          status = 'post_surgery_done'
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->where('nursing_admissions.status', 'post_surgery_done')
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
                DB::raw('MAX(nursing_admissions.admission_date) as admission_date')
            )
            ->groupBy(
                'patients.id', 'patients.patientname', 'patients.patientcode',
                'patients.patientfather', 'patients.mobile_no', 'patients.age',
                'patients.gender', 'patients.blood_group', 'patients.address',
                'patients.upozila'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patients.patientname', 'LIKE', "%{$search}%")
                  ->orWhere('patients.patientcode', 'LIKE', "%{$search}%")
                  ->orWhere('patients.mobile_no',   'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('admission_id', 'desc')
                          ->paginate(20)
                          ->withQueryString();

        // Past Fresh prescriptions (latest per patient)
        $freshQuery = DB::table('nursing_fresh_prescriptions')
            ->join('patients', 'nursing_fresh_prescriptions.patient_id', '=', 'patients.id')
            ->select(
                'nursing_fresh_prescriptions.*',
                'patients.patientname', 'patients.patientcode',
                'patients.age', 'patients.gender', 'patients.mobile_no',
                'patients.address', 'patients.upozila', 'patients.blood_group'
            )
            ->whereRaw('nursing_fresh_prescriptions.id IN (
                SELECT MAX(id) FROM nursing_fresh_prescriptions GROUP BY patient_id
            )')
            ->orderBy('nursing_fresh_prescriptions.created_at', 'desc');

        $FreshPatients = $freshQuery->paginate(20)->withQueryString();

        // ✅ "আরো Medicine যোগ করুন" — common_medicine table
        $medicines = $this->medicineService->getAvailableMedicinesForFresh();

        // ✅ Auto-load Selected Medicines — template_medicine WHERE order_type='fresh prescription'
        $templateMedicines = $this->medicineService->getTemplateMedicinesForFreshPrescription();

        $investigations = DB::table('template_investigations')->orderBy('id')->get();
        $templates      = DB::table('tbl_template')->where('status', 1)->orderBy('title')->get();
        $doctors        = DB::table('doctors')
                            ->whereIn('active', [1, '1', true, 'yes', 'active'])
                            ->orderBy('name')
                            ->get();

        return view('nursing.fresh', compact(
            'patients', 'FreshPatients', 'search',
            'medicines',          // common_medicine → আরো Medicine যোগ করুন
            'templateMedicines',  // template_medicine order_type='fresh prescription' → auto-load
            'investigations', 'doctors', 'templates'
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

        $medicines            = $this->fetchAdmissionMedicines($admission);
        $postSurgeryMedicines = $this->fetchLatestPostSurgeryMedicines($patientId);

        return response()->json([
            'success'                    => true,
            'admission'                  => $admission,
            'medicines'                  => $medicines,
            'post_surgery_medicines'     => $postSurgeryMedicines,
            'previous_prescriptions'     => $this->getPreviousAdmissionPrescriptions($patientId),
            'fresh_prescriptions'        => $this->getFreshPrescriptions($patientId),
            'post_surgery_prescriptions' => $this->getPostSurgeryPrescriptions($patientId),
            'message'                    => $admission
                                            ? 'Admission record found.'
                                            : 'No admission record found.',
        ]);
    }

    /* ══════════════════════════════════════════
       STORE FRESH PRESCRIPTION (AJAX)
    ══════════════════════════════════════════ */
    public function storePrescription(Request $request)
    {
        $request->validate([
            'patient_id'        => 'required|integer',
            'patient_name'      => 'nullable|string|max:255',
            'patient_age'       => 'nullable|string|max:100',
            'patient_code'      => 'nullable|string|max:100',
            'doctor_name'       => 'nullable|string|max:255',
            'prescription_date' => 'nullable|date',
            'rx_text'           => 'nullable|string',
            'notes'             => 'nullable|string',
            'medicines'         => 'nullable|array',
        ]);

        if (!Schema::hasTable('nursing_fresh_prescriptions')) {
            return response()->json([
                'success' => false,
                'message' => 'Table nursing_fresh_prescriptions not found. Please run SQL first.',
            ], 500);
        }

        DB::beginTransaction();
        try {
            $freshId = DB::table('nursing_fresh_prescriptions')->insertGetId([
                'patient_id'        => $request->patient_id,
                'patient_name'      => $request->patient_name,
                'patient_age'       => $request->patient_age,
                'patient_code'      => $request->patient_code,
                'doctor_name'       => $request->doctor_name,
                'prescription_date' => $request->prescription_date ?: now()->toDateString(),
                'rx_text'           => $request->rx_text,
                'notes'             => $request->notes,
                'created_by'        => auth()->id(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            if (Schema::hasTable('nursing_fresh_medicines')) {
                $rows = [];
                foreach ((array) $request->input('medicines', []) as $m) {
                    $name = trim((string) ($m['name'] ?? $m['medicine_name'] ?? ''));
                    if ($name === '') continue;

                    $rows[] = [
                        'fresh_prescription_id' => $freshId,
                        'medicine_name'         => $name,
                        'strength'              => $m['strength']  ?? null,
                        'dose'                  => $m['dose']      ?? null,
                        'route'                 => $m['route']     ?? null,
                        'frequency'             => $m['frequency'] ?? null,
                        'duration'              => $m['duration']  ?? null,
                        'timing'                => $m['timing']    ?? null,
                        'note'                  => $m['note']      ?? null,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                }
                if (!empty($rows)) {
                    DB::table('nursing_fresh_medicines')->insert($rows);
                }
            }

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Fresh prescription saved successfully.',
                'prescription_id' => $freshId,
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
       TEMPLATE DATA (AJAX)
    ══════════════════════════════════════════ */
    public function getTemplateData($id = null)
    {
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'No template ID provided.'], 400);
        }

        $template = DB::table('tbl_template')->where('id', $id)->first()
                 ?? DB::table('tbl_template')->where('templateid', $id)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $templateData       = $this->medicineService->applyTemplateWithCommonMedicines($template->templateid ?? $template->id);
        $mappedMedicines    = $templateData['template_medicines'];
        $availableMedicines = $templateData['available_medicines'];

        return response()->json([
            'success'             => true,
            'template'            => $template,
            'medicines'           => $mappedMedicines,
            'available_medicines' => $availableMedicines,
            'debug_info'          => [
                'template_id'               => $id,
                'template_templateid'       => $template->templateid,
                'template_medicines_count'  => $templateData['template_medicines_count'],
                'available_medicines_count' => $templateData['available_medicines_count'],
            ],
        ]);
    }

    /* ══════════════════════════════════════════
       GET POST-OPERATION MEDICINES (AJAX)
    ══════════════════════════════════════════ */
    public function getPostOperationMedicines()
    {
        try {
            $postOpMeds = TemplateMedicine::where('order_type', 'postorder')
                ->where('active', 1)
                ->orderBy('group')
                ->orderBy('name')
                ->get();

            \Log::info("Direct query found: " . $postOpMeds->count() . " Post-Operation medicines");

            $allPostOpMeds = [];
            foreach ($postOpMeds as $med) {
                $allPostOpMeds[] = [
                    'id'            => $med->id,
                    'name'          => $med->name,
                    'dose'          => $med->dose,
                    'route'         => $med->route,
                    'frequency'     => $med->frequency,
                    'duration'      => $med->duration,
                    'timing'        => $med->timing,
                    'order_type'    => $med->order_type,
                    'templateid'    => $med->templeteid,
                    'template_name' => 'Template ' . $med->templeteid,
                ];
            }

            return response()->json([
                'success' => true,
                'count'   => count($allPostOpMeds),
                'rows'    => $allPostOpMeds,
                'message' => count($allPostOpMeds) . ' Post-Operation medicines found',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPostOperationMedicines: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Post-Operation medicines: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
       DETAIL — GET /fresh/detail/{id}
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        $prescription = DB::table('nursing_fresh_prescriptions')->where('id', $id)->first();
        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Fresh prescription not found.'], 404);
        }

        $medicines = DB::table('nursing_fresh_medicines')
            ->where('fresh_prescription_id', $id)
            ->get();

        $patient = DB::table('patients')
            ->where('id', $prescription->patient_id)
            ->first();

        $patientPrescriptions = DB::table('nursing_fresh_prescriptions')
            ->where('patient_id', $prescription->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $prescription->id,
                'patient_name'         => $prescription->patient_name,
                'patient_age'          => $prescription->patient_age,
                'patient_code'         => $prescription->patient_code,
                'doctor_name'          => $prescription->doctor_name,
                'prescription_date'    => $prescription->prescription_date,
                'rx_text'              => $prescription->rx_text,
                'notes'                => $prescription->notes,
                'created_at'           => $prescription->created_at,
                'medicines'            => $medicines,
                'patient'              => $patient,
                'patientPrescriptions' => $patientPrescriptions,
            ],
        ]);
    }

    /* ══════════════════════════════════════
       APPLY TEMPLATE (AJAX POST)
    ══════════════════════════════════════ */
    public function applyTemplate(Request $request)
    {
        $templateId = $request->get('template_id');
        if (!$templateId) {
            return response()->json(['success' => false, 'message' => 'Template ID is required'], 422);
        }

        $template = DB::table('tbl_template')->where('ID', $templateId)->first()
                 ?? DB::table('tbl_template')->where('id', $templateId)->first()
                 ?? DB::table('tbl_template')->where('templateid', $templateId)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $tplCode = $template->templateid;
        if (empty($tplCode)) {
            $tplCode = $templateId;
        }

        $templateData       = $this->medicineService->applyTemplateWithCommonMedicines($tplCode);
        $mappedMedicines    = $templateData['template_medicines'];
        $availableMedicines = $templateData['available_medicines'];

        return response()->json([
            'success'             => true,
            'message'             => 'Template applied successfully',
            'template'            => $template,
            'medicines'           => $mappedMedicines,
            'available_medicines' => $availableMedicines,
            'debug_info'          => [
                'template_id'               => $templateId,
                'template_templateid'       => $template->templateid,
                'tplCode_used'              => $tplCode,
                'template_medicines_count'  => $templateData['template_medicines_count'],
                'available_medicines_count' => $templateData['available_medicines_count'],
            ],
        ]);
    }

    /* ══════════════════════════════════════
       PRIVATE HELPERS (UNCHANGED LOGIC)
    ══════════════════════════════════════ */

    private function fetchAdmissionMedicines($admission): array
    {
        $medicines = [];
        if ($admission && Schema::hasTable('nursing_admission_medicines')) {
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
        return $medicines;
    }

    private function fetchLatestPostSurgeryMedicines($patientId): array
    {
        $postSurgeryMedicines = [];
        if (Schema::hasTable('nursing_postsurgery_prescriptions') &&
            Schema::hasTable('nursing_postsurgery_medicines')) {
            $latestPs = DB::table('nursing_postsurgery_prescriptions')
                ->where('patient_id', $patientId)
                ->orderByDesc('id')
                ->first();
            if ($latestPs) {
                $meds = DB::table('nursing_postsurgery_medicines')
                    ->where('postsurgery_prescription_id', $latestPs->id)
                    ->get();
                foreach ($meds as $m) {
                    $postSurgeryMedicines[] = [
                        'medicine_name'   => $m->medicine_name ?? '',
                        'strength'        => $m->strength      ?? '',
                        'dose'            => $m->dose          ?? '',
                        'route'           => $m->route         ?? '',
                        'frequency'       => $m->frequency     ?? '',
                        'duration'        => $m->duration      ?? '',
                        'timing'          => $m->timing        ?? '',
                        'prescription_id' => $latestPs->id,
                    ];
                }
            }
        }
        return $postSurgeryMedicines;
    }

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
                        $m->dose      ?? null, $m->route     ?? null,
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
                'id'    => $row->id,
                'date'  => $row->rx_date ?? $row->admission_date ?? $row->created_at,
                'doctor'=> null, 'type' => 'on_admission',
                'lines' => array_values($lines),
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
                        $m->medicine_name ?? null, $m->dose ?? null,
                        $m->route ?? null, $m->frequency ?? null,
                        !empty($m->duration) ? ('× ' . $m->duration) : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
            }
            if (empty($lines) && !empty($row->rx_text)) {
                $lines = array_filter(preg_split('/\r\n|\r|\n/', trim((string) $row->rx_text)));
            }
            $result[] = [
                'id'    => $row->id,
                'date'  => $row->prescription_date ?? $row->created_at,
                'doctor'=> $row->doctor_name ?? null, 'type' => 'fresh',
                'lines' => array_values($lines),
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
            ->limit(1)
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
                        $m->medicine_name ?? null, $m->strength ?? null,
                        $m->dose ?? null, $m->route ?? null, $m->frequency ?? null,
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
                'id'    => $row->id,
                'date'  => $row->prescription_date ?? $row->created_at,
                'doctor'=> null, 'type' => 'post-surgery',
                'lines' => array_values($lines),
            ];
        }
        return $result;
    }
}