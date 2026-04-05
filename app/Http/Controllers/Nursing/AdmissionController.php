<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AdmissionController extends Controller
{
    /* ══════════════════════════════════════════
       INDEX — On Admission patient list
       ✅ শুধু সেই patients দেখাবে যাদের
          moved_to_postsurgery, post_surgery_done
          বা fresh status হয়নি
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

        $patients       = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        
        // Query for past nursing prescriptions (latest admission per patient)
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
            ->orderBy('nursing_admissions.created_at', 'desc');
        
        $NursingPatients = $nursingQuery->paginate(20)->withQueryString();
        
        $investigations = DB::table('template_investigations')->orderBy('id')->get();
        $medicines      = DB::table('template_medicine')->orderBy('group')->get();
        $templates      = DB::table('tbl_template')
                            ->where('status', 1)
                            ->orderBy('title')
                            ->get();

        return view('nursing.onaddmission', [
            'patients'       => $patients,
            'NursingPatients'       => $NursingPatients,
            'search'         => $search,
            'investigations' => $investigations,
            'medicines'      => $medicines,
            'templates'      => $templates,
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

        // ✅ Fetch all patient prescriptions
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
       ✅ Prescription save হলে automatically
          status = 'moved_to_postsurgery' হবে
          এবং OnAdmission list থেকে সরে যাবে
    ══════════════════════════════════════════ */
    public function store(Request $request)
    {
        // Auto-create status column if not exists
        $this->ensureStatusColumn();

        // Basic validation
        $request->validate([
            'patient_id'     => 'required|integer',
            'admission_type' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Insert main admission row
            // ✅ status = 'moved_to_postsurgery' সেট করা হচ্ছে
            $admissionId = DB::table('nursing_admissions')->insertGetId([
                'patient_id'      => $request->patient_id,
                'patient_name'    => $request->patient_name,
                'patient_age'     => $request->patient_age,
                'patient_code'    => $request->patient_code,
                'pulse'           => $request->pulse,
                'bp'              => $request->bp,
                'rx_date'         => $request->rx_date         ?: now()->toDateString(),
                'admission_date'  => $request->admission_date,
                'admission_time'  => $request->admission_time,
                'ot_time'         => $request->ot_time,
                'pregnancy_weeks' => $request->pregnancy_weeks,
                'baby_sex'        => $request->baby_sex,
                'baby_weight'     => $request->baby_weight,
                'baby_time'       => $request->baby_time,
                'notes'           => $request->notes,
                'admission_type'  => $request->admission_type ?? 'on_admission',
                'status'          => 'moved_to_postsurgery', // ✅ automatically move to Post Surgery
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 2. Insert medicines
            $medicines = $request->input('medicines', []);
            if (!empty($medicines) && is_array($medicines)) {
                $medRows = [];
                foreach ($medicines as $med) {
                    if (empty(trim($med['medicine_name'] ?? ''))) continue;
                    $medRows[] = [
                        'nursing_admission_id' => $admissionId,
                        'medicine_name'        => $med['medicine_name']  ?? null,
                        'dose'                 => $med['dose']           ?? null,
                        'route'                => $med['route']          ?? null,
                        'frequency'            => $med['frequency']      ?? null,
                        'duration'             => $med['duration']       ?? null,
                        'timing'               => $med['timing']         ?? null,
                        'remarks'              => $med['remarks']        ?? null,
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

        // ✅ Fetch all patient prescriptions
        $patientPrescriptions = DB::table('nursing_admissions')
            ->where('patient_id', $admission->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nursing.admission-show', compact('admission', 'medicines', 'patientPrescriptions'));
    }

    /* ══════════════════════════════════════════
       DETAIL — Show admission details
       GET /admission/detail/{id}
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Admission record not found.'], 404);
        }

        $medicines = DB::table('nursing_admission_medicines')
            ->where('nursing_admission_id', $id)
            ->get();

        // Get patient information
        $patient = DB::table('patients')->where('id', $admission->patient_id)->first();

        // Get all patient prescriptions for history
        $patientPrescriptions = DB::table('nursing_admissions')
            ->where('patient_id', $admission->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admission->id,
                'patient_name' => $admission->patient_name,
                'patient_age' => $admission->patient_age,
                'patient_code' => $admission->patient_code,
                'p_code' => $admission->patient_code,
                'admission_date' => $admission->admission_date,
                'rx_date' => $admission->rx_date,
                'admission_time' => $admission->admission_time,
                'ot_time' => $admission->ot_time,
                'pulse' => $admission->pulse,
                'bp' => $admission->bp,
                'pregnancy_weeks' => $admission->pregnancy_weeks,
                'baby_sex' => $admission->baby_sex,
                'baby_weight' => $admission->baby_weight,
                'baby_time' => $admission->baby_time,
                'notes' => $admission->notes,
                'medicines' => $medicines,
                'patient' => $patient,
                'patientPrescriptions' => $patientPrescriptions
            ]
        ]);
    }

    /* ══════════════════════════════════════════
       EDIT
    ══════════════════════════════════════════ */
    public function edit($id)
    {
        $admission = DB::table('nursing_admissions')->where('id', $id)->first();
        if (!$admission) abort(404);

        $medicines = DB::table('nursing_admission_medicines')
            ->where('nursing_admission_id', $id)
            ->get();

        // ✅ Fetch all patient prescriptions
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

            // Re-insert medicines
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

        $medicines      = $this->safeFetch('template_medicine',      $tplCode, 'group');
        $investigations = $this->safeFetch('template_investigations', $tplCode, 'id');
        $diagnoses      = $this->safeFetch('template_diagnosis',      $tplCode, 'id');

        return response()->json([
            'success'        => true,
            'template'       => $template,
            'medicines'      => $medicines,
            'investigations' => $investigations,
            'diagnoses'      => $diagnoses,
        ]);
    }

    /* ══════════════════════════════════════════
       APPLY TEMPLATE (AJAX POST)
       POST /nursing/admission/apply-template
       ★ Used by On Admission blade to load template medicines
    ══════════════════════════════════════════ */
    public function applyTemplate(Request $request)
    {
        $templateId = $request->get('template_id');
        if (!$templateId) {
            return response()->json(['success' => false, 'message' => 'Template ID is required'], 422);
        }

        // Find template row in tbl_template
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

        // Fetch medicines matching on templeteid
        $medicines = DB::table('template_medicine')
            ->where('templeteid', $tplCode)
            ->where('active', 1)
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        if ($medicines->isEmpty()) {
            $medicines = DB::table('template_medicine')
                ->where('templeteid', $tplCode)
                ->orderBy('group')
                ->orderBy('name')
                ->get();
        }

        if ($medicines->isEmpty() && $tplCode != $templateId) {
            $medicines = DB::table('template_medicine')
                ->where('templeteid', $templateId)
                ->where('active', 1)
                ->orderBy('group')
                ->orderBy('name')
                ->get();
        }

        // Map DB columns to the keys JS expects
        $mappedMedicines = $medicines
            ->map(function ($med) {
                $mName = trim($med->name ?? '');
                if ($mName === '') {
                    $mName = trim($med->group ?? '');
                }
                if ($mName === '') return null;

                return [
                    'medicine_name' => $mName,
                    'dose'          => $med->dose      ?? '',
                    'route'         => $med->route     ?? '',
                    'frequency'     => $med->frequency ?? '',
                    'duration'      => $med->duration  ?? '',
                    'timing'        => $med->timing    ?? '',
                    'remarks'       => $med->note      ?? '',
                ];
            })
            ->filter()
            ->values();

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
            'success'        => true,
            'message'        => 'Template applied successfully',
            'template'       => $template,
            'medicines'      => $mappedMedicines,
            'investigations' => $investigations,
            'diagnoses'      => $diagnoses,
            'debug_info'     => [
                'template_id'            => $templateId,
                'template_templateid'    => $template->templateid,
                'tplCode_used'           => $tplCode,
                'raw_medicines_count'    => $medicines->count(),
                'mapped_medicines_count' => $mappedMedicines->count(),
            ],
        ]);
    }

    /* ══════════════════════════════════════════
       DEBUG METHODS (TEMPORARY - Remove in production)
    ══════════════════════════════════════════ */

    /**
     * DEBUG: Get all templates with their medicine counts
     * GET /nursing/admission/debug/templates
     */
    public function debugTemplates()
    {
        $templates = DB::table('tbl_template')
            ->where('status', 1)
            ->orderBy('title')
            ->get();

        $result = [];
        foreach ($templates as $template) {
            $medicines = DB::table('template_medicine')
                ->where('templeteid', $template->templateid)
                ->get();

            $result[] = [
                'id'             => $template->ID,
                'templateid'     => $template->templateid,
                'title'          => $template->title,
                'medicine_count' => $medicines->count(),
                'medicines'      => $medicines->map(function ($m) {
                    return [
                        'id'         => $m->id,
                        'name'       => $m->name,
                        'active'     => $m->active,
                        'templeteid' => $m->templeteid,
                    ];
                }),
            ];
        }

        return response()->json([
            'total_templates'    => $templates->count(),
            'templates'          => $result,
            'all_medicines_count' => DB::table('template_medicine')->count(),
            'unique_templeteids' => DB::table('template_medicine')
                ->select('templeteid')
                ->distinct()
                ->pluck('templeteid')
                ->toArray(),
        ]);
    }

    /**
     * DEBUG: Get specific template details with medicines
     * GET /nursing/admission/debug/template/{id}
     */
    public function debugTemplate($id)
    {
        $template = DB::table('tbl_template')->where('ID', $id)->first()
                 ?? DB::table('tbl_template')->where('templateid', $id)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found with ID: ' . $id], 404);
        }

        $medicines    = DB::table('template_medicine')->where('templeteid', $template->templateid)->get();
        $medicinesById = DB::table('template_medicine')->where('templeteid', (string) $id)->get();
        $allMedicines = DB::table('template_medicine')->limit(10)->get();

        return response()->json([
            'debug_info' => [
                'searched_id'      => $id,
                'searched_id_type' => gettype($id),
            ],
            'template' => [
                'ID'         => $template->ID,
                'templateid' => $template->templateid,
                'title'      => $template->title,
                'status'     => $template->status,
                'created_at' => $template->created_at,
            ],
            'medicines_by_templateid' => [
                'count' => $medicines->count(),
                'data'  => $medicines->map(function ($m) {
                    return [
                        'id'         => $m->id,
                        'name'       => $m->name,
                        'group'      => $m->group,
                        'dose'       => $m->dose,
                        'route'      => $m->route,
                        'frequency'  => $m->frequency,
                        'duration'   => $m->duration,
                        'timing'     => $m->timing,
                        'active'     => $m->active,
                        'templeteid' => $m->templeteid,
                    ];
                }),
            ],
            'medicines_by_id_as_string' => [
                'count' => $medicinesById->count(),
                'data'  => $medicinesById->map(function ($m) {
                    return [
                        'id'         => $m->id,
                        'name'       => $m->name,
                        'templeteid' => $m->templeteid,
                    ];
                }),
            ],
            'sample_all_medicines' => $allMedicines->map(function ($m) {
                return [
                    'id'         => $m->id,
                    'name'       => $m->name,
                    'templeteid' => $m->templeteid,
                    'active'     => $m->active,
                ];
            }),
            'unique_templeteids_in_db' => DB::table('template_medicine')
                ->select('templeteid')
                ->distinct()
                ->pluck('templeteid')
                ->toArray(),
            'total_medicines_in_db' => DB::table('template_medicine')->count(),
        ]);
    }

    /**
     * DEBUG: Check all template_medicine records
     * GET /nursing/admission/debug/all-medicines
     */
    public function debugAllMedicines()
    {
        $medicines = DB::table('template_medicine')
            ->orderBy('templeteid')
            ->orderBy('name')
            ->get();

        return response()->json([
            'total_count' => $medicines->count(),
            'medicines'   => $medicines->map(function ($m) {
                return [
                    'id'         => $m->id,
                    'templeteid' => $m->templeteid,
                    'name'       => $m->name,
                    'group'      => $m->group,
                    'active'     => $m->active,
                    'dose'       => $m->dose,
                    'route'      => $m->route,
                    'frequency'  => $m->frequency,
                    'duration'   => $m->duration,
                ];
            }),
        ]);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════ */

    private function ensureStatusColumn(): void
    {
        try {
            if (!Schema::hasColumn('nursing_admissions', 'status')) {
                DB::statement("
                    ALTER TABLE nursing_admissions
                    ADD COLUMN status VARCHAR(50) DEFAULT 'on_admission'
                    AFTER admission_type
                ");
            }
        } catch (\Exception $e) {
            // Column already exists or other issue — ignore
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