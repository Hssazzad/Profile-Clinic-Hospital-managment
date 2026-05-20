<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TemplateMedicine;

class PreSurgeryController extends Controller
{
    /* ------------------------------------------
       INDEX — Patient list
       ???? status = 'moved_to_postsurgery'
       (On Admission ???? ??? patients)
    ------------------------------------------ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // -- Active patients (pre-surgery queue) ------------------------------
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
                DB::raw('MAX(nursing_admissions.rx_date) as rx_date'),
                DB::raw('MAX(nursing_admissions.ot_time) as ot_time')
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

        $patients = $query->orderBy('admission_id', 'desc')->paginate(20)->withQueryString();

        // -- Past Pre-Surgery prescriptions -----------------------------------
        $PreSurgeryPatients = DB::table('nursing_presurgery_prescriptions')
            ->join('patients', 'nursing_presurgery_prescriptions.patient_id', '=', 'patients.id')
            ->select(
                'nursing_presurgery_prescriptions.*',
                'patients.patientname',
                'patients.patientcode',
                'patients.age',
                'patients.gender',
                'patients.mobile_no',
                'patients.address',
                'patients.upozila',
                'patients.blood_group'
            )
            ->whereRaw('nursing_presurgery_prescriptions.id IN (
                SELECT MAX(id)
                FROM nursing_presurgery_prescriptions
                GROUP BY patient_id
            )')
            ->orderBy('nursing_presurgery_prescriptions.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // -- Available medicines (common_medicine) ----------------------------
        $medicines = DB::table('common_medicine')
            ->where('active', 1)
            ->select('id', 'name', 'code', 'GroupName', 'strength')
            ->orderBy('GroupName')
            ->orderBy('name')
            ->get();

        // -- Templates --------------------------------------------------------
        $templates = DB::table('tbl_template')
            ->where('status', 1)
            ->orderBy('title')
            ->get();

        return view('nursing.presurgery', compact(
            'patients',
            'PreSurgeryPatients',
            'search',
            'medicines',
            'templates'
        ));
    }

    /* ------------------------------------------
       GET PATIENT ADMISSION DATA (AJAX)
       GET /nursing/presurgery/patient-admission/{patientId}
    ------------------------------------------ */
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
            'success'                => true,
            'admission'              => $admission,
            'medicines'              => $medicines,
            'previous_prescriptions' => $this->getPreviousAdmissionPrescriptions($patientId),
            'presurgery_prescriptions' => $this->getPreSurgeryPrescriptions($patientId),
            'message'                => $admission
                                        ? 'Admission record found.'
                                        : 'No admission record found.',
        ]);
    }

    /* ------------------------------------------
       GET PRE-OPERATION MEDICINES (AJAX)
       GET /nursing/presurgery/preorder-medicines
       order_type = 'preorder'
    ------------------------------------------ */
    public function getPreOrderMedicines()
    {
        try {
            $meds = TemplateMedicine::where('order_type', 'preorder')
                ->where('active', 1)
                ->orderBy('group')
                ->orderBy('name')
                ->get();

            $rows = $meds->map(fn($med) => [
                'id'         => $med->id,
                'name'       => $med->name,
                'dose'       => $med->dose,
                'route'      => $med->route,
                'frequency'  => $med->frequency,
                'duration'   => $med->duration,
                'timing'     => $med->timing,
                'order_type' => $med->order_type,
                'templateid' => $med->templeteid,
            ])->values()->toArray();

            return response()->json([
                'success' => true,
                'count'   => count($rows),
                'rows'    => $rows,
                'message' => count($rows) . ' Pre-Order medicines found',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ------------------------------------------
       STORE PRE-SURGERY PRESCRIPTION (AJAX)
       POST /nursing/presurgery/store
    ------------------------------------------ */
    public function storePrescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'        => 'required|integer',
            'patient_name'      => 'nullable|string|max:255',
            'patient_age'       => 'nullable|string|max:100',
            'prescription_date' => 'nullable|date',
            'ot_time'           => 'nullable',
            'notes'             => 'nullable|string',
            'medicines'         => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $rxId = DB::table('nursing_presurgery_prescriptions')->insertGetId([
                'patient_id'        => $request->patient_id,
                'patient_name'      => $request->patient_name,
                'patient_age'       => $request->patient_age,
                'prescription_date' => $request->prescription_date ?: now()->toDateString(),
                'ot_time'           => $request->ot_time           ?: null,
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
                    'presurgery_prescription_id' => $rxId,
                    'medicine_name'              => $name,
                    'dose'                       => $m['dose']      ?? null,
                    'route'                      => $m['route']     ?? null,
                    'frequency'                  => $m['frequency'] ?? null,
                    'duration'                   => $m['duration']  ?? null,
                    'timing'                     => $m['timing']    ?? null,
                    'note'                       => $m['note']      ?? null,
                    'created_at'                 => now(),
                    'updated_at'                 => now(),
                ];
            }

            if (!empty($rows)) {
                DB::table('nursing_presurgery_medicines')->insert($rows);
            }

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Pre-surgery prescription saved successfully.',
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

    /* ------------------------------------------
       APPLY TEMPLATE (AJAX POST)
       POST /nursing/presurgery/apply-template
    ------------------------------------------ */
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

        $tplCode = !empty($template->templateid) ? $template->templateid : $templateId;

        // preorder medicines only
        $templateMedicines = DB::table('template_medicine')
            ->where('templeteid', $tplCode)
            ->where('active', 1)
            ->where('order_type', 'preorder')
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $commonMedicines = DB::table('common_medicine')
            ->where('active', 1)
            ->select('id', 'name', 'code', 'GroupName', 'strength')
            ->get();

        $medicineMap = $commonMedicines->keyBy(fn($item) => strtolower(trim($item->name)));

        $mappedMedicines = $templateMedicines->map(function ($med) use ($medicineMap) {
            $medicineName = trim($med->name ?? $med->group ?? '');
            if (empty($medicineName)) return null;

            $commonMed = $medicineMap->get(strtolower($medicineName));

            return [
                'medicine_name'      => $medicineName,
                'dose'               => $med->dose      ?? '',
                'route'              => $med->route      ?? '',
                'frequency'          => $med->frequency  ?? '',
                'duration'           => $med->duration   ?? '',
                'timing'             => $med->timing     ?? '',
                'remarks'            => $med->note       ?? '',
                'common_medicine_id' => $commonMed->id   ?? null,
                'is_from_common'     => !is_null($commonMed),
            ];
        })->filter()->values();

        return response()->json([
            'success'             => true,
            'message'             => 'Template applied successfully',
            'template'            => $template,
            'medicines'           => $mappedMedicines,
            'available_medicines' => $commonMedicines,
        ]);
    }

    /* ------------------------------------------
       DETAIL — prescription details (AJAX)
       GET /nursing/presurgery/detail/{id}
    ------------------------------------------ */
    public function detail($id)
    {
        $prescription = DB::table('nursing_presurgery_prescriptions')->where('id', $id)->first();

        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Pre-surgery prescription not found.'], 404);
        }

        $medicines = DB::table('nursing_presurgery_medicines')
            ->where('presurgery_prescription_id', $id)
            ->get();

        $patient = DB::table('patients')->where('id', $prescription->patient_id)->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $prescription->id,
                'patient_name'      => $prescription->patient_name,
                'patient_age'       => $prescription->patient_age,
                'prescription_date' => $prescription->prescription_date,
                'ot_time'           => $prescription->ot_time,
                'notes'             => $prescription->notes,
                'created_at'        => $prescription->created_at,
                'medicines'         => $medicines,
                'patient'           => $patient,
            ],
        ]);
    }

    /* ------------------------------------------
       PRIVATE HELPERS
    ------------------------------------------ */

    private function getPreviousAdmissionPrescriptions($patientId): array
    {
        $rows = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];
            $meds  = DB::table('nursing_admission_medicines')
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

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', trim((string) $row->notes))
                ));
            }

            $result[] = [
                'id'    => $row->id,
                'date'  => $row->rx_date ?? $row->admission_date ?? $row->created_at,
                'doctor'=> null,
                'type'  => 'on_admission',
                'lines' => array_values($lines),
            ];
        }

        return $result;
    }

    private function getPreSurgeryPrescriptions($patientId): array
    {
        $rows = DB::table('nursing_presurgery_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];
            $meds  = DB::table('nursing_presurgery_medicines')
                        ->where('presurgery_prescription_id', $row->id)
                        ->get();

            foreach ($meds as $m) {
                $parts = array_filter([
                    $m->medicine_name ?? null,
                    $m->dose      ?? null,
                    $m->route     ?? null,
                    $m->frequency ?? null,
                    !empty($m->duration) ? ('× ' . $m->duration) : null,
                    !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                ]);
                if (!empty($parts)) $lines[] = implode(' ', $parts);
            }

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', trim((string) $row->notes))
                ));
            }

            $result[] = [
                'id'    => $row->id,
                'date'  => $row->prescription_date ?? $row->created_at,
                'doctor'=> null,
                'type'  => 'pre-surgery',
                'lines' => array_values($lines),
            ];
        }

        return $result;
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