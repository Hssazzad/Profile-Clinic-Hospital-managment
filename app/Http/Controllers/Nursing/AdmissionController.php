<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OnAdmissionMedicineService;

class AdmissionController extends Controller
{
    protected $medicineService;

    public function __construct(OnAdmissionMedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    /* ══════════════════════════════════════════
       INDEX — On Admission patient list
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('nursing_admissions')
                  ->whereColumn('nursing_admissions.patient_id', 'patients.id')
                  ->where('nursing_admissions.status', 'moved_to_postsurgery');
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patientname',  'LIKE', "%{$search}%")
                  ->orWhere('patientcode', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_no',   'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $nursingQuery = DB::table('nursing_admissions')
            ->join('patients', 'nursing_admissions.patient_id', '=', 'patients.id')
            ->select(
                'nursing_admissions.*',
                'patients.patientname',
                'patients.patientcode',
                'patients.age',
                'patients.gender',
                'patients.mobile_no',
                'patients.address',
                'patients.upozila',
                'patients.blood_group'
            )
            ->whereRaw('nursing_admissions.id IN (
                SELECT MAX(id)
                FROM nursing_admissions
                GROUP BY patient_id
            )')
            ->where('nursing_admissions.status', '=', 'moved_to_postsurgery')
            ->orderBy('nursing_admissions.created_at', 'desc');

        $NursingPatients = $nursingQuery->paginate(20)->withQueryString();

        $investigations = DB::table('template_investigations')->orderBy('id')->get();
        $medicines      = $this->medicineService->getAvailableMedicinesForAdmission();
        $templates      = DB::table('tbl_template')
                            ->where('status', 1)
                            ->orderBy('title')
                            ->get();

        return view('nursing.onaddmission', [
            'patients'        => $patients,
            'NursingPatients' => $NursingPatients,
            'search'          => $search,
            'investigations'  => $investigations,
            'medicines'       => $medicines,
            'templates'       => $templates,
        ]);
    }

    /* ══════════════════════════════════════════
       SELECT PATIENT (Step 1)
    ══════════════════════════════════════════ */
    public function selectPatient(Request $request)
    {
        return $this->index($request);
    }

    /* ══════════════════════════════════════════
       CREATE FORM (Step 2)
    ══════════════════════════════════════════ */
    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patient   = DB::table('patients')->where('id', $patientId)->first();

        if (!$patient) {
            return redirect()->route('nursing.on_admission')
                ->with('error', 'Patient not found.');
        }

        $prescriptions = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nursing.admission-create', compact('patient', 'prescriptions'));
    }

    /* ══════════════════════════════════════════
       STORE — Save prescription via AJAX
       POST /nursing/admission/store
       Returns JSON
    ══════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'     => 'required|integer',
            'admission_type' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $admissionId = DB::table('nursing_admissions')->insertGetId([
                'patient_id'      => $request->patient_id,
                'patient_name'    => $request->patient_name,
                'patient_age'     => $request->patient_age,
                'patient_code'    => $request->patient_code,
                'pulse'           => $request->pulse,
                'bp'              => $request->bp,
                'rx_date'         => $request->rx_date        ?: now()->toDateString(),
                'admission_date'  => $request->admission_date,
                'admission_time'  => $request->admission_time,
                'ot_time'         => $request->ot_time,
                'pregnancy_weeks' => $request->pregnancy_weeks,
                'baby_sex'        => $request->baby_sex,
                'baby_weight'     => $request->baby_weight,
                'baby_time'       => $request->baby_time,
                'notes'           => $request->notes,
                'admission_type'  => $request->admission_type ?? 'on_admission',
                'status'          => 'moved_to_postsurgery',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $medicines = $request->input('medicines', []);
            if (!empty($medicines) && is_array($medicines)) {
                $medRows = [];
                foreach ($medicines as $med) {
                    if (empty(trim($med['medicine_name'] ?? ''))) continue;
                    $medRows[] = [
                        'nursing_admission_id' => $admissionId,
                        'medicine_name'        => $med['medicine_name'] ?? null,
                        'dose'                 => $med['dose']          ?? null,
                        'route'                => $med['route']         ?? null,
                        'frequency'            => $med['frequency']     ?? null,
                        'duration'             => $med['duration']      ?? null,
                        'timing'               => $med['timing']        ?? null,
                        'remarks'              => $med['remarks']       ?? null,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ];
                }
                if (!empty($medRows)) {
                    DB::table('nursing_admission_medicines')->insert($medRows);
                }
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Prescription saved successfully.',
                'admission_id' => $admissionId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Save failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
       SHOW saved admission
    ══════════════════════════════════════════ */
    public function show($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) abort(404);

        $medicines = DB::table('nursing_admission_medicines')
            ->where('nursing_admission_id', $id)
            ->get();

        $patientPrescriptions = DB::table('nursing_admissions')
            ->where('patient_id', $admission->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nursing.admission-show', compact('admission', 'medicines', 'patientPrescriptions'));
    }

    /* ══════════════════════════════════════════
       DETAIL — Show admission details (AJAX)
       GET /admission/detail/{id}
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Admission record not found.'], 404);
        }

        $medicines            = DB::table('nursing_admission_medicines')
                                    ->where('nursing_admission_id', $id)
                                    ->get();
        $patient              = DB::table('patients')->where('id', $admission->patient_id)->first();
        $patientPrescriptions = DB::table('nursing_admissions')
                                    ->where('patient_id', $admission->patient_id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $admission->id,
                'patient_name'     => $admission->patient_name,
                'patient_age'      => $admission->patient_age,
                'patient_code'     => $admission->patient_code,
                'p_code'           => $admission->patient_code,
                'admission_date'   => $admission->admission_date,
                'rx_date'          => $admission->rx_date,
                'admission_time'   => $admission->admission_time,
                'ot_time'          => $admission->ot_time,
                'pulse'            => $admission->pulse,
                'bp'               => $admission->bp,
                'pregnancy_weeks'  => $admission->pregnancy_weeks,
                'baby_sex'         => $admission->baby_sex,
                'baby_weight'      => $admission->baby_weight,
                'baby_time'        => $admission->baby_time,
                'notes'            => $admission->notes,
                'medicines'        => $medicines,
                'patient'          => $patient,
                'patientPrescriptions' => $patientPrescriptions,
            ],
        ]);
    }

    /* ══════════════════════════════════════════
       EDIT
    ══════════════════════════════════════════ */
    public function edit($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) abort(404);

        $medicines            = DB::table('nursing_admission_medicines')
                                    ->where('nursing_admission_id', $id)
                                    ->get();
        $patientPrescriptions = DB::table('nursing_admissions')
                                    ->where('patient_id', $admission->patient_id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return view('nursing.admission-edit', compact('admission', 'medicines', 'id', 'patientPrescriptions'));
    }

    /* ══════════════════════════════════════════
       UPDATE
    ══════════════════════════════════════════ */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('nursing_admissions')->where('id', $id)->update([
                'patient_name'    => $request->patient_name,
                'patient_age'     => $request->patient_age,
                'pulse'           => $request->pulse,
                'bp'              => $request->bp,
                'rx_date'         => $request->rx_date,
                'admission_date'  => $request->admission_date,
                'admission_time'  => $request->admission_time,
                'ot_time'         => $request->ot_time,
                'pregnancy_weeks' => $request->pregnancy_weeks,
                'baby_sex'        => $request->baby_sex,
                'baby_weight'     => $request->baby_weight,
                'baby_time'       => $request->baby_time,
                'notes'           => $request->notes,
                'updated_at'      => now(),
            ]);

            DB::table('nursing_admission_medicines')
                ->where('nursing_admission_id', $id)
                ->delete();

            $medicines = $request->input('medicines', []);
            if (!empty($medicines)) {
                $medRows = [];
                foreach ($medicines as $med) {
                    if (empty(trim($med['medicine_name'] ?? ''))) continue;
                    $medRows[] = [
                        'nursing_admission_id' => $id,
                        'medicine_name'        => $med['medicine_name'] ?? null,
                        'dose'                 => $med['dose']          ?? null,
                        'route'                => $med['route']         ?? null,
                        'frequency'            => $med['frequency']     ?? null,
                        'duration'             => $med['duration']      ?? null,
                        'timing'               => $med['timing']        ?? null,
                        'remarks'              => $med['remarks']       ?? null,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ];
                }
                if (!empty($medRows)) {
                    DB::table('nursing_admission_medicines')->insert($medRows);
                }
            }

            DB::commit();
            return redirect()->route('nursing.index')->with('success', 'Updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /* ══════════════════════════════════════════
       DESTROY
    ══════════════════════════════════════════ */
    public function destroy($id)
    {
        DB::table('nursing_admissions')->where('id', $id)->delete();
        return redirect()->route('nursing.index')->with('success', 'Deleted successfully.');
    }

    /* ══════════════════════════════════════════
       TEMPLATE DATA (AJAX GET)
       GET /nursing/admission/template/data/{id}
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

    /* ══════════════════════════════════════════
       APPLY TEMPLATE (AJAX POST)
       POST /nursing/admission/apply-template
    ══════════════════════════════════════════ */
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

        $templateData       = $this->medicineService->applyTemplateWithCommonMedicines($tplCode);
        $mappedMedicines    = $templateData['template_medicines'];
        $availableMedicines = $templateData['available_medicines'];

        $investigations = DB::table('template_investigations')
            ->where(function ($q) use ($tplCode, $templateId) {
                $q->where('templateid', $tplCode)
                  ->orWhere('templateid', $templateId);
            })
            ->orderBy('id')
            ->get();

        $diagnoses = DB::table('template_diagnosis')
            ->where(function ($q) use ($tplCode, $templateId) {
                $q->where('templateid', $tplCode)
                  ->orWhere('templateid', $templateId);
            })
            ->orderBy('id')
            ->get();

        return response()->json([
            'success'             => true,
            'message'             => 'Template applied successfully',
            'template'            => $template,
            'medicines'           => $mappedMedicines,
            'available_medicines' => $availableMedicines,
            'investigations'      => $investigations,
            'diagnoses'           => $diagnoses,
        ]);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════ */

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