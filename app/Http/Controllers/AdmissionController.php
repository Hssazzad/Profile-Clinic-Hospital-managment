<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdmissionController extends Controller
{
    /* ══════════════════════════════════════════
        INDEX — Patient list for On Admission
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
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
                'patients.upozila'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patients.patientname', 'LIKE', "%{$search}%")
                  ->orWhere('patients.patientcode', 'LIKE', "%{$search}%")
                  ->orWhere('patients.mobile_no', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('patients.id', 'desc')->paginate(20)->withQueryString();

        $NursingPatients = DB::table('nursing_admissions')
            ->join('patients', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->select(
                'nursing_admissions.id as admission_id',
                'nursing_admissions.admission_date',
                'nursing_admissions.created_at',
                'patients.patientname as patient_name',
                'patients.patientcode as p_code',
                'patients.age as patient_age',
                'patients.gender',
                'patients.mobile_no',
                'patients.blood_group'
            )
            ->orderBy('nursing_admissions.id', 'desc')
            ->paginate(15, ['*'], 'rx_page')
            ->withQueryString();

        $medicines      = DB::table('template_medicine')->orderBy('name')->get();
        $investigations = DB::table('template_investigations')->orderBy('id')->get();
        $templates      = DB::table('tbl_template')->where('status', 1)->orderBy('title')->get();

        return view('nursing.on_admission', compact(
            'patients',
            'search',
            'medicines',
            'investigations',
            'templates',
            'NursingPatients'
        ));
    }

    /* ══════════════════════════════════════════
        STORE — Save On Admission Prescription
    ══════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'   => 'required|integer',
            'patient_name' => 'nullable|string|max:255',
            'patient_age'  => 'nullable|string|max:100',
            'patient_code' => 'nullable|string|max:100',
        ]);

        if (!Schema::hasTable('nursing_admissions')) {
            return response()->json([
                'success' => false,
                'message' => 'Table nursing_admissions not found.',
            ], 500);
        }

        DB::beginTransaction();
        try {
            $admissionData = [
                'patient_id'       => $request->patient_id,
                'patient_name'     => $request->patient_name,
                'patient_age'      => $request->patient_age,
                'patient_code'     => $request->patient_code  ?? null,
                'admission_type'   => $request->admission_type ?? 'on_admission',
                'admission_date'   => $request->admission_date ?? now()->toDateString(),
                'admission_time'   => $request->admission_time ?? null,
                'rx_date'          => $request->rx_date        ?? now()->toDateString(),
                'pulse'            => $request->pulse          ?? null,
                'bp'               => $request->bp             ?? null,
                'ot_time'          => $request->ot_time        ?? null,
                'pregnancy_weeks'  => $request->pregnancy_weeks ?? null,
                'baby_sex'         => $request->baby_sex       ?? null,
                'baby_weight'      => $request->baby_weight    ?? null,
                'baby_time'        => $request->baby_time      ?? null,
                'notes'            => $request->notes          ?? null,
                'status'           => 'admitted',
                'created_by'       => auth()->id(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $admissionId = DB::table('nursing_admissions')->insertGetId($admissionData);

            if (Schema::hasTable('nursing_admission_medicines')) {
                $medRows = [];
                foreach ((array) $request->input('medicines', []) as $m) {
                    $name = trim((string) ($m['name'] ?? $m['medicine_name'] ?? ''));
                    if ($name === '') continue;
                    $medRows[] = [
                        'nursing_admission_id' => $admissionId,
                        'medicine_name'        => $name,
                        'dose'                 => $m['dose']      ?? null,
                        'route'                => $m['route']     ?? null,
                        'frequency'            => $m['frequency'] ?? null,
                        'duration'             => $m['duration']  ?? null,
                        'timing'               => $m['timing']    ?? null,
                        'remarks'              => $m['remarks']   ?? null,
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

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Save failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
        DETAIL — Get single admission for modal (AJAX)
        (Fixes the BadMethodCallException)
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        try {
            $admission = DB::table('nursing_admissions')->where('id', $id)->first();

            if (!$admission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admission record not found.',
                ], 404);
            }

            $medicines = [];
            if (Schema::hasTable('nursing_admission_medicines')) {
                $medicines = DB::table('nursing_admission_medicines')
                    ->where('nursing_admission_id', $id)
                    ->get()
                    ->toArray();
            }

            $data = (array) $admission;
            $data['medicines'] = $medicines;
            
            // Fetch patient details to supplement the data
            $patient = DB::table('patients')->where('id', $admission->patient_id)->first();
            if ($patient) {
                $data['patient_name'] = $data['patient_name'] ?? $patient->patientname;
                $data['patient_age']  = $data['patient_age']  ?? $patient->age;
                $data['p_code']       = $data['patient_code'] ?? $patient->patientcode;
                $data['mobile_no']    = $patient->mobile_no;
                $data['gender']       = $patient->gender;
                $data['blood_group']  = $patient->blood_group;
            }

            return response()->json([
                'success' => true,
                'data'    => $data,
                'message' => 'Record found.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════
        SHOW — Admission detail page (web)
    ══════════════════════════════════════════ */
    public function show($id)
    {
        $admission = DB::table('nursing_admissions')
            ->join('patients', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.id', $id)
            ->select('nursing_admissions.*', 'patients.patientname', 'patients.patientcode', 'patients.mobile_no')
            ->first();

        if (!$admission) {
            abort(404, 'Admission not found.');
        }

        return response()->json(['success' => true, 'data' => $admission]);
    }

    /* ══════════════════════════════════════════
        GET ADMISSION DATA (AJAX)
    ══════════════════════════════════════════ */
    public function getAdmissionData($patientId)
    {
        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderBy('id', 'desc')
            ->first();

        $medicines = [];
        if ($admission && Schema::hasTable('nursing_admission_medicines')) {
            $medicines = DB::table('nursing_admission_medicines')
                ->where('nursing_admission_id', $admission->id)
                ->get()
                ->toArray();
        }

        return response()->json([
            'success'   => true,
            'admission' => $admission,
            'medicines' => $medicines,
            'message'   => $admission ? 'Record found.' : 'No record.',
        ]);
    }

    /* ══════════════════════════════════════════
        APPLY TEMPLATE (AJAX)
    ══════════════════════════════════════════ */
   public function applyTemplate(Request $request)
{
    $templateId = $request->input('template_id');

    if (!$templateId) {
        return response()->json(['success' => false, 'message' => 'No template ID provided.'], 400);
    }

    // tbl_template থেকে template খোঁজো — ID বা id যেটাই হোক
    $template = DB::table('tbl_template')->where('id', $templateId)->first();
    if (!$template) {
        $template = DB::table('tbl_template')->whereRaw('ID = ?', [$templateId])->first();
    }
    if (!$template) {
        return response()->json([
            'success' => false,
            'message' => 'Template not found. ID: ' . $templateId,
        ], 404);
    }

    // ✅ template_medicine table-এর সব column name খোঁজো
    $medicines = collect();
    $debugInfo = [];

    if (Schema::hasTable('template_medicine')) {
        $columns = Schema::getColumnListing('template_medicine');
        $debugInfo['template_medicine_columns'] = $columns;

        // template_id column আছে কিনা দেখো
        if (in_array('template_id', $columns)) {
            $medicines = DB::table('template_medicine')
                ->where('template_id', $templateId)
                ->get();
            $debugInfo['found_by'] = 'template_medicine.template_id';
        }
        // tbl_template_id column আছে কিনা দেখো
        elseif (in_array('tbl_template_id', $columns)) {
            $medicines = DB::table('template_medicine')
                ->where('tbl_template_id', $templateId)
                ->get();
            $debugInfo['found_by'] = 'template_medicine.tbl_template_id';
        }
        // অন্য সব column try করো যেগুলোতে 'template' আছে
        else {
            foreach ($columns as $col) {
                if (stripos($col, 'template') !== false) {
                    $result = DB::table('template_medicine')->where($col, $templateId)->get();
                    if ($result->isNotEmpty()) {
                        $medicines = $result;
                        $debugInfo['found_by'] = 'template_medicine.' . $col;
                        break;
                    }
                }
            }
        }

        $debugInfo['medicine_count'] = $medicines->count();
    }

    // tbl_template_medicines table-ও try করো
    if ($medicines->isEmpty() && Schema::hasTable('tbl_template_medicines')) {
        $medicines = DB::table('tbl_template_medicines')->where('template_id', $templateId)->get();
        $debugInfo['found_by'] = 'tbl_template_medicines.template_id';
    }

    $normalizedMeds = $medicines->map(function ($m) {
        $arr = (array) $m;
        // medicine name বিভিন্ন column-এ থাকতে পারে
        $name = $arr['medicine_name'] ?? $arr['name'] ?? $arr['medicine'] ?? '';
        return [
            'medicine_name' => $name,
            'dose'          => $arr['dose']      ?? '',
            'route'         => $arr['route']     ?? '',
            'frequency'     => $arr['frequency'] ?? '',
            'duration'      => $arr['duration']  ?? '',
            'timing'        => $arr['timing']    ?? '',
            'remarks'       => $arr['remarks']   ?? $arr['note'] ?? '',
        ];
    })->filter(function($m) {
        return !empty($m['medicine_name']);
    })->values()->toArray();

    return response()->json([
        'success'   => true,
        'template'  => $template,
        'medicines' => $normalizedMeds,
        'debug'     => $debugInfo,   // ✅ এটা দেখে সমস্যা বুঝবেন
        'message'   => count($normalizedMeds) . ' medicine(s) found.',
    ]);
}
    /* ══════════════════════════════════════════
        GET TEMPLATE DATA (by ID) — AJAX
    ══════════════════════════════════════════ */
    public function getTemplateData($id = null)
    {
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'No template ID.'], 400);
        }

        $template = DB::table('tbl_template')->where('id', $id)->orWhere('ID', $id)->first();

        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found.'], 404);
        }

        $medicines = collect();
        if (Schema::hasTable('tbl_template_medicines')) {
            $medicines = DB::table('tbl_template_medicines')->where('template_id', $id)->get();
        }
        if ($medicines->isEmpty() && Schema::hasTable('template_medicine')) {
            $medicines = DB::table('template_medicine')->where('template_id', $id)->get();
        }

        $normalizedMeds = $medicines->map(function ($m) {
            return [
                'medicine_name' => $m->medicine_name ?? $m->name ?? '',
                'dose'          => $m->dose      ?? '',
                'route'         => $m->route     ?? '',
                'frequency'     => $m->frequency ?? '',
                'duration'      => $m->duration  ?? '',
                'timing'        => $m->timing    ?? '',
                'remarks'       => $m->remarks   ?? $m->note ?? '',
            ];
        })->values()->toArray();

        return response()->json([
            'success'   => true,
            'template'  => $template,
            'medicines' => $normalizedMeds,
        ]);
    }

    public function template()
    {
        $templates = DB::table('tbl_template')->where('status', 1)->orderBy('title')->get();
        return view('nursing.templates', compact('templates'));
    }

    public function selectPatient()
    {
        $patients = DB::table('patients')->orderBy('patientname')->get();
        return view('nursing.select_patient', compact('patients'));
    }

    public function create()
    {
        $patients  = DB::table('patients')->orderBy('patientname')->get(['id', 'patientname', 'patientcode']);
        $templates = DB::table('tbl_template')->orderBy('title')->get();
        return view('nursing.on_admission', compact('patients', 'templates'));
    }

    public function edit($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) abort(404);

        $medicines = Schema::hasTable('nursing_admission_medicines')
            ? DB::table('nursing_admission_medicines')->where('nursing_admission_id', $id)->get()
            : collect();

        return response()->json([
            'success'   => true,
            'admission' => $admission,
            'medicines' => $medicines,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::table('nursing_admissions')
                ->where('id', $id)
                ->update([
                    'notes'      => $request->notes,
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Updated successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('nursing_admissions')->where('id', $id)->delete();
            if (Schema::hasTable('nursing_admission_medicines')) {
                DB::table('nursing_admission_medicines')->where('nursing_admission_id', $id)->delete();
            }
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}