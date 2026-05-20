<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateMedicine;
use App\Models\TemplateDiagnosis;
use App\Models\TemplateInvestigation;
use App\Models\TemplateAdvice;
use App\Models\TemplateDischarge;
use App\Models\TemplateComplain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SurgeryPrescriptionController extends Controller
{
    /**
     * Display list of surgery prescriptions
     * Route: GET /prescriptions/SurgeryPrescription
     */
    public function index()
    {
        try {
            Log::info('===== SURGERY PRESCRIPTIONS LIST START =====');
            
            // Get available columns to avoid errors
            $availableColumns = DB::getSchemaBuilder()->getColumnListing('patients');
            
            $selectFields = [
                'sp.*',
                'p.patientname as patient_name',
                'p.patientcode as patient_code',
                'p.age as patient_age',
                'p.gender as patient_gender'
            ];
            
            if (in_array('mobile_no', $availableColumns)) {
                $selectFields[] = 'p.mobile_no as patient_phone';
            }
            if (in_array('blood_group', $availableColumns)) {
                $selectFields[] = 'p.blood_group as patient_blood_group';
            }
            if (in_array('emergency_contact', $availableColumns)) {
                $selectFields[] = 'p.emergency_contact as patient_emergency_contact';
            }
            if (in_array('allergies', $availableColumns)) {
                $selectFields[] = 'p.allergies as patient_allergies';
            }
            
            $prescriptions = DB::table('surgery_prescriptions as sp')
                ->leftJoin('patients as p', 'sp.patient_id', '=', 'p.id')
                ->select($selectFields)
                ->orderBy('sp.created_at', 'desc')
                ->paginate(15);

            return view('prescriptions.surgery.index', compact('prescriptions'));

        } catch (\Exception $e) {
            Log::error('Prescriptions list error: ' . $e->getMessage());
            return view('prescriptions.surgery.index')->with('error', 'Failed to load prescriptions');
        }
    }

    /**
     * Show multi-step form
     * Route: GET /prescriptions/SurgeryPrescription/create
     */
    public function create(Request $request)
    {
        try {
            Log::info('===== SURGERY PRESCRIPTION CREATE START =====');
            
            $templates = Template::where('status', 1)
                ->orderBy('title')
                ->get(['id', 'templateid', 'title', 'description']);
            
            Log::info('Templates found: ' . $templates->count());
            
            $doctors = $this->getDoctorsList();
            
            $selectedPatientId = $request->get('patient_id');
            $selectedPatient = null;
            
            if ($selectedPatientId) {
                $availableColumns = DB::getSchemaBuilder()->getColumnListing('patients');
                
                $selectFields = [
                    'id', 'patientcode as patient_id', 'patientname as name', 'age', 'gender'
                ];
                
                if (in_array('mobile_no', $availableColumns)) {
                    $selectFields[] = 'mobile_no as phone';
                }
                if (in_array('blood_group', $availableColumns)) {
                    $selectFields[] = 'blood_group';
                }
                if (in_array('emergency_contact', $availableColumns)) {
                    $selectFields[] = 'emergency_contact';
                }
                if (in_array('allergies', $availableColumns)) {
                    $selectFields[] = 'allergies';
                }
                
                $selectedPatient = DB::table('patients')
                    ->where('id', $selectedPatientId)
                    ->select($selectFields)
                    ->first();
            }
            
            return view('surgery-prescriptions.create', compact('templates', 'doctors', 'selectedPatient'));
            
        } catch (\Exception $e) {
            Log::error('===== SURGERY CREATE ERROR =====');
            Log::error('Error message: ' . $e->getMessage());
            
            return view('surgery-prescriptions.create')
                ->with('error', 'Could not load form. Please check database connection.');
        }
    }

    /**
     * API: টেমপ্লেট লিস্ট
     * Route: GET /prescriptions/SurgeryPrescription/get-templates
     */
    public function getTemplates()
    {
        try {
            $templates = Template::where('status', 1)
                ->orderBy('title')
                ->get(['id', 'templateid', 'title', 'description']);
            
            return response()->json(['success' => true, 'data' => $templates]);
            
        } catch (\Exception $e) {
            Log::error('Get templates error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load templates: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: ডাক্তার লিস্ট লোড
     * Route: GET /prescriptions/SurgeryPrescription/get-doctors
     */
    public function getDoctors()
    {
        try {
            $doctors = DB::table('doctors')
                ->where('active', 1)
                ->orderBy('doctor_name')
                ->get(['id', 'reg_no', 'doctor_name', 'name', 'speciality', 'contact', 'Posting']);
            
            return response()->json(['success' => true, 'data' => $doctors]);
            
        } catch (\Exception $e) {
            Log::error('Get doctors error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load doctors: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: টেমপ্লেট ডাটা লোড
     * Route: GET /prescriptions/SurgeryPrescription/get-template-data/{id}
     */
    public function getTemplateData($templateId)
    {
        try {
            Log::info('===== GET TEMPLATE DATA START =====');
            Log::info('Template ID: ' . $templateId);
            
            $template = Template::find($templateId);
            
            if (!$template) {
                return response()->json(['success' => false, 'message' => 'Template not found with ID: ' . $templateId], 404);
            }
            
            // ============ 1. MEDICINES ============
            $medicines = [];
            try {
                if (DB::getSchemaBuilder()->hasTable('template_medicine')) {
                    $medicinesData = TemplateMedicine::where('templeteid', $template->templateid)
                        ->where('active', 1)
                        ->orderBy('order_type')
                        ->orderBy('id')
                        ->get();
                    
                    $medicines = $medicinesData->map(function($med) {
                        return [
                            'id'                   => $med->id,
                            'name'                 => $med->name,
                            'dosage'               => $med->dose ?? '1+0+1',
                            'duration'             => $med->duration ?? '7 days',
                            'order_type'           => $this->convertOrderType($med->order_type),
                            'strength'             => $med->strength ?? '',
                            'brand'                => $med->brand ?? 'Generic',
                            'instructions'         => $med->note ?? '',
                            'template_medicine_id' => $med->id,
                            'route'                => $med->route ?? 'Oral',
                            'frequency'            => $med->frequency ?? '',
                            'medicine_type'        => $med->medicine_type ?? 'Tablet',
                        ];
                    })->values()->toArray();
                }
            } catch (\Exception $e) {
                Log::error('Error loading medicines: ' . $e->getMessage());
            }
            
            // ============ 2. DIAGNOSES ============
            $diagnoses = [];
            try {
                if (DB::getSchemaBuilder()->hasTable('template_diagnosis')) {
                    $diagnoses = TemplateDiagnosis::where('templateid', $template->templateid)
                        ->where('active', 1)
                        ->orderBy('id')
                        ->get()
                        ->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'note' => $d->note ?? ''])
                        ->values()->toArray();
                }
            } catch (\Exception $e) {
                Log::error('Error loading diagnoses: ' . $e->getMessage());
            }
            
            // ============ 3. INVESTIGATIONS ============
            $investigations = [];
            try {
                if (DB::getSchemaBuilder()->hasTable('template_investigations')) {
                    $investigations = TemplateInvestigation::where('templateid', $template->templateid)
                        ->orderBy('id')
                        ->get()
                        ->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'note' => $i->note ?? ''])
                        ->values()->toArray();
                }
            } catch (\Exception $e) {
                Log::error('Error loading investigations: ' . $e->getMessage());
            }
            
            // ============ 4. ADVICES ============
            $advices = [];
            try {
                if (DB::getSchemaBuilder()->hasTable('template_advice')) {
                    $advices = TemplateAdvice::where('templateid', $template->templateid)
                        ->where('active', 1)
                        ->orderBy('id')
                        ->get()
                        ->map(fn($a) => ['id' => $a->id, 'advice' => $a->advice])
                        ->values()->toArray();
                }
            } catch (\Exception $e) {
                Log::error('Error loading advices: ' . $e->getMessage());
            }
            
            // ============ 5. DISCHARGE ============
            $discharge = null;
            try {
                if (DB::getSchemaBuilder()->hasTable('template_discharge')) {
                    $d = TemplateDischarge::where('templateid', $template->id)->where('active', 1)->first();
                    if ($d) {
                        $discharge = ['treatment' => $d->treatment ?? '', 'condition' => $d->condition ?? '', 'follow_up' => $d->follow_up ?? ''];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error loading discharge: ' . $e->getMessage());
            }
            
            // ============ 6. COMPLAINS ============
            $complains = [];
            try {
                if (DB::getSchemaBuilder()->hasTable('template_complain') && class_exists('App\Models\TemplateComplain')) {
                    $complains = TemplateComplain::where('templateid', $template->templateid)
                        ->where('active', 1)
                        ->orderBy('id')
                        ->get()
                        ->map(fn($c) => ['id' => $c->id, 'name' => $c->name ?? $c->complain, 'note' => $c->note ?? ''])
                        ->values()->toArray();
                }
            } catch (\Exception $e) {
                Log::error('Error loading complains: ' . $e->getMessage());
            }
            
            return response()->json([
                'success'        => true,
                'template'       => [
                    'id'           => $template->id,
                    'templateid'   => $template->templateid,
                    'title'        => $template->title,
                    'description'  => $template->description,
                    'surgery_type' => $template->surgery_type ?? '',
                ],
                'medicines'      => $medicines,
                'diagnoses'      => $diagnoses,
                'investigations' => $investigations,
                'advices'        => $advices,
                'complains'      => $complains,
                'discharge'      => $discharge,
                'counts'         => [
                    'medicines'      => count($medicines),
                    'diagnoses'      => count($diagnoses),
                    'investigations' => count($investigations),
                    'advices'        => count($advices),
                    'complains'      => count($complains),
                    'discharge'      => $discharge ? 1 : 0,
                    'total'          => count($medicines) + count($diagnoses) + count($investigations) + count($advices) + count($complains) + ($discharge ? 1 : 0),
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('GET TEMPLATE DATA ERROR: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load template data: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    //  PATIENT & MEDICINE SEARCH
    // ============================================================

    /**
     * Search patients (AJAX)
     * Route: GET /prescriptions/SurgeryPrescription/search-patients
     */
    public function searchPatients(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            $query = DB::table('patients');
            
            // Get available columns to avoid errors
            $availableColumns = DB::getSchemaBuilder()->getColumnListing('patients');
            
            if (!empty($search)) {
                $query->where(function ($q) use ($search, $availableColumns) {
                    $q->where('patientname', 'LIKE', "%{$search}%")
                      ->orWhere('patientcode',  'LIKE', "%{$search}%");
                    
                    // Only add phone search if columns exist
                    if (in_array('mobile_no', $availableColumns)) {
                        $q->orWhere('mobile_no', 'LIKE', "%{$search}%");
                    }
                    if (in_array('phone', $availableColumns)) {
                        $q->orWhere('phone', 'LIKE', "%{$search}%");
                    }
                    
                    // Only add new fields if they exist in database
                    if (in_array('blood_group', $availableColumns)) {
                        $q->orWhere('blood_group', 'LIKE', "%{$search}%");
                    }
                    if (in_array('emergency_contact', $availableColumns)) {
                        $q->orWhere('emergency_contact', 'LIKE', "%{$search}%");
                    }
                    if (in_array('allergies', $availableColumns)) {
                        $q->orWhere('allergies', 'LIKE', "%{$search}%");
                    }
                });
            }
            
            // Build select array with only available columns
            $selectFields = [
                'id', 'patientcode', 'patientname', 'age', 'gender'
            ];
            
            if (in_array('mobile_no', $availableColumns)) {
                $selectFields[] = 'mobile_no';
            }
            if (in_array('blood_group', $availableColumns)) {
                $selectFields[] = 'blood_group';
            }
            if (in_array('emergency_contact', $availableColumns)) {
                $selectFields[] = 'emergency_contact';
            }
            if (in_array('allergies', $availableColumns)) {
                $selectFields[] = 'allergies';
            }
            
            $patients = $query->select($selectFields)
                ->limit(50)
                ->get();

            return response()->json(['success' => true, 'data' => $patients]);

        } catch (\Exception $e) {
            Log::error('Patient search error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Search medicines (AJAX for Select2)
     * Route: GET /prescriptions/SurgeryPrescription/search-medicines
     */
    /**
     * Search medicines — robust version
     * Priority: template_medicine → common_medicine → []
     * NEVER returns 500.
     * Route: GET /prescriptions/SurgeryPrescription/search-medicines
     */
    public function searchMedicines(Request $request)
    {
        try {
            $search     = trim($request->get('q', ''));
            $templateId = $request->get('template_id');
            $formatted  = collect();

            // ===== SOURCE 1: template_medicine =====
            if (DB::getSchemaBuilder()->hasTable('template_medicine')) {
                try {
                    $columns = DB::getSchemaBuilder()->getColumnListing('template_medicine');
                    $query   = DB::table('template_medicine');

                    if (in_array('active', $columns)) {
                        $query->where('active', 1);
                    }

                    if (!empty($search)) {
                        $query->where(function ($q) use ($search, $columns) {
                            $q->where('name', 'LIKE', "%{$search}%");
                            if (in_array('brand',    $columns)) $q->orWhere('brand',    'LIKE', "%{$search}%");
                            if (in_array('strength', $columns)) $q->orWhere('strength', 'LIKE', "%{$search}%");
                        });
                    }

                    if ($templateId && in_array('templeteid', $columns)) {
                        $tid = is_numeric($templateId)
                            ? optional(Template::find($templateId))->templateid
                            : $templateId;
                        if ($tid) $query->where('templeteid', $tid);
                    }

                    $available = array_values(array_intersect(
                        ['id', 'name', 'dose', 'duration', 'order_type', 'strength', 'brand', 'route', 'frequency', 'medicine_type'],
                        $columns
                    ));

                    $rows = $query->select($available)->orderBy('name')->limit(30)->get();

                    $formatted = $rows->map(function ($med) {
                        $name     = $med->name ?? '';
                        $strength = property_exists($med, 'strength') ? ($med->strength ?? '') : '';
                        $brand    = property_exists($med, 'brand')    ? ($med->brand    ?? '') : '';

                        $text = $name;
                        if (!empty($strength)) $text .= ' - ' . $strength;
                        if (!empty($brand) && $brand !== 'Generic') $text .= ' (' . $brand . ')';

                        return [
                            'id'            => $med->id,
                            'text'          => $text,
                            'name'          => $name,
                            'dosage'        => property_exists($med, 'dose')          ? ($med->dose          ?? '1+0+1') : '1+0+1',
                            'duration'      => property_exists($med, 'duration')      ? ($med->duration      ?? '7 days'): '7 days',
                            'order_type'    => $this->convertOrderType(property_exists($med, 'order_type') ? $med->order_type : null),
                            'strength'      => $strength,
                            'brand'         => !empty($brand) ? $brand : 'Generic',
                            'route'         => property_exists($med, 'route')         ? ($med->route         ?? 'Oral')  : 'Oral',
                            'frequency'     => property_exists($med, 'frequency')     ? ($med->frequency     ?? '')      : '',
                            'medicine_type' => property_exists($med, 'medicine_type') ? ($med->medicine_type ?? 'Tablet'): 'Tablet',
                        ];
                    });

                    Log::info('searchMedicines (template_medicine): ' . $formatted->count() . ' results');

                } catch (\Exception $inner) {
                    Log::error('searchMedicines template_medicine query error: ' . $inner->getMessage());
                    $formatted = collect();
                }
            }

            // ===== SOURCE 2: common_medicine fallback =====
            if ($formatted->isEmpty() && DB::getSchemaBuilder()->hasTable('common_medicine')) {
                try {
                    $columns = DB::getSchemaBuilder()->getColumnListing('common_medicine');
                    $query   = DB::table('common_medicine');

                    if (!empty($search)) {
                        $query->where(function ($q) use ($search, $columns) {
                            $q->where('name', 'LIKE', "%{$search}%");
                            if (in_array('strength',  $columns)) $q->orWhere('strength',  'LIKE', "%{$search}%");
                            if (in_array('GroupName', $columns)) $q->orWhere('GroupName', 'LIKE', "%{$search}%");
                        });
                    }

                    $available = array_values(array_intersect(['id', 'name', 'strength', 'GroupName'], $columns));
                    $rows      = $query->select($available)->orderBy('name')->limit(30)->get();

                    $formatted = $rows->map(function ($med) {
                        $strength = property_exists($med, 'strength') ? ($med->strength ?? '') : '';
                        $text     = $med->name . (!empty($strength) ? ' - ' . $strength : '');

                        return [
                            'id'            => $med->id,
                            'text'          => $text,
                            'name'          => $med->name,
                            'dosage'        => '1+0+1',
                            'duration'      => '7 days',
                            'order_type'    => 'post-op',
                            'strength'      => $strength,
                            'brand'         => 'Generic',
                            'route'         => 'Oral',
                            'frequency'     => '',
                            'medicine_type' => 'Tablet',
                        ];
                    });

                    Log::info('searchMedicines (common_medicine fallback): ' . $formatted->count() . ' results');

                } catch (\Exception $inner) {
                    Log::error('searchMedicines common_medicine query error: ' . $inner->getMessage());
                    $formatted = collect();
                }
            }

            return response()->json([
                'success' => true,
                'data'    => $formatted->values(),
            ]);

        } catch (\Exception $e) {
            Log::error('searchMedicines fatal: ' . $e->getMessage() . ' L:' . $e->getLine());
            return response()->json(['success' => true, 'data' => [], 'warning' => $e->getMessage()]);
        }
    }

    // ============================================================
    //  EXISTING DATA SEARCH ROUTES (NEW - blade panel গুলোর জন্য)
    // ============================================================

    /**
     * Search diagnoses from existing records
     * Route: GET /prescriptions/SurgeryPrescription/search-diagnoses
     */
    public function searchDiagnoses(Request $request)
    {
        try {
            $search     = $request->get('q', '');
            $templateId = $request->get('template_id');

            $query = TemplateDiagnosis::where('active', 1);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('note', 'LIKE', "%{$search}%");
                });
            }

            if ($templateId) {
                $tid = is_numeric($templateId) ? optional(Template::find($templateId))->templateid : $templateId;
                if ($tid) $query->where('templateid', $tid);
            }

            $data = $query->select('id', 'name', 'note')
                ->orderBy('name')->limit(20)->get()
                ->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'note' => $d->note ?? ''])
                ->values();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('searchDiagnoses error: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search investigations from existing records
     * Route: GET /prescriptions/SurgeryPrescription/search-investigations
     */
    public function searchInvestigations(Request $request)
    {
        try {
            $search     = $request->get('q', '');
            $templateId = $request->get('template_id');

            $query = TemplateInvestigation::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('note', 'LIKE', "%{$search}%");
                });
            }

            if ($templateId) {
                $tid = is_numeric($templateId) ? optional(Template::find($templateId))->templateid : $templateId;
                if ($tid) $query->where('templateid', $tid);
            }

            $data = $query->select('id', 'name', 'note')
                ->orderBy('name')->limit(20)->get()
                ->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'note' => $i->note ?? ''])
                ->values();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('searchInvestigations error: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search advices from existing records
     * Route: GET /prescriptions/SurgeryPrescription/search-advices
     */
    public function searchAdvices(Request $request)
    {
        try {
            $search     = $request->get('q', '');
            $templateId = $request->get('template_id');

            $query = TemplateAdvice::where('active', 1);

            if (!empty($search)) {
                $query->where('advice', 'LIKE', "%{$search}%");
            }

            if ($templateId) {
                $tid = is_numeric($templateId) ? optional(Template::find($templateId))->templateid : $templateId;
                if ($tid) $query->where('templateid', $tid);
            }

            $data = $query->select('id', 'advice')
                ->orderBy('advice')->limit(20)->get()
                ->map(fn($a) => ['id' => $a->id, 'advice' => $a->advice])
                ->values();

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            Log::error('searchAdvices error: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search fresh prescriptions from existing records
     * Route: GET /prescriptions/SurgeryPrescription/search-fresh-prescriptions
     *
     * NOTE: যদি আপনার DB-তে dedicated fresh_prescriptions table থাকে তাহলে
     *       নিচের hardcoded array-টি DB query দিয়ে replace করুন।
     */
    public function searchFreshPrescriptions(Request $request)
    {
        try {
            $search = $request->get('q', '');

            // ---- Option A: DB table থাকলে এটি uncomment করুন ----
            // $query = DB::table('template_fresh_prescriptions');
            // if (!empty($search)) {
            //     $query->where('name', 'LIKE', "%{$search}%")
            //           ->orWhere('details', 'LIKE', "%{$search}%");
            // }
            // $data = $query->select('id', 'name', 'details')->limit(20)->get();
            // return response()->json(['success' => true, 'data' => $data]);

            // ---- Option B: Static fallback (DB table না থাকলে) ----
            $items = collect([
                ['id' => 1, 'name' => 'Continue current medications', 'details' => ''],
                ['id' => 2, 'name' => 'Start new antibiotics',        'details' => ''],
                ['id' => 3, 'name' => 'Pain management',              'details' => ''],
                ['id' => 4, 'name' => 'Wound care instructions',      'details' => ''],
                ['id' => 5, 'name' => 'Dietary modifications',        'details' => ''],
                ['id' => 6, 'name' => 'Physical therapy',             'details' => ''],
                ['id' => 7, 'name' => 'Follow-up appointment',        'details' => ''],
            ]);

            if (!empty($search)) {
                $items = $items->filter(fn($i) => stripos($i['name'], $search) !== false)->values();
            }

            return response()->json(['success' => true, 'data' => $items]);

        } catch (\Exception $e) {
            Log::error('searchFreshPrescriptions error: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Search discharge summaries from existing records
     * Route: GET /prescriptions/SurgeryPrescription/search-discharge-summaries
     *
     * NOTE: যদি template_discharge table থেকে data আনতে চান তাহলে
     *       নিচে Option A uncomment করুন।
     */
    public function searchDischargeSummaries(Request $request)
    {
        try {
            $search = $request->get('q', '');

            // ---- Option A: template_discharge table থেকে ----
            if (DB::getSchemaBuilder()->hasTable('template_discharge')) {
                $query = TemplateDischarge::where('active', 1);

                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('treatment', 'LIKE', "%{$search}%")
                          ->orWhere('condition', 'LIKE', "%{$search}%")
                          ->orWhere('follow_up', 'LIKE', "%{$search}%");
                    });
                }

                $data = $query->select('id', 'treatment', 'condition', 'follow_up')
                    ->orderBy('id', 'desc')->limit(20)->get()
                    ->map(fn($d) => [
                        'id'        => $d->id,
                        'treatment' => $d->treatment ?? '',
                        'condition' => $d->condition ?? '',
                        'follow_up' => $d->follow_up ?? '',
                    ])->values();

                return response()->json(['success' => true, 'data' => $data]);
            }

            // ---- Option B: Static fallback ----
            $items = collect([
                ['id' => 1, 'treatment' => 'Surgery completed successfully',    'condition' => 'Stable and improving',    'follow_up' => 'Follow up in 1 week'],
                ['id' => 2, 'treatment' => 'Laparoscopic procedure performed',  'condition' => 'Afebrile and comfortable','follow_up' => 'Follow up in 2 weeks'],
                ['id' => 3, 'treatment' => 'Open surgery completed',            'condition' => 'Pain well controlled',    'follow_up' => 'Follow up in 6 weeks'],
                ['id' => 4, 'treatment' => 'Emergency surgery performed',       'condition' => 'Vitals stable',           'follow_up' => 'Return to ER if fever'],
                ['id' => 5, 'treatment' => 'Elective surgery completed',        'condition' => 'Wound clean and dry',     'follow_up' => 'Follow up as needed'],
            ]);

            if (!empty($search)) {
                $items = $items->filter(function ($i) use ($search) {
                    return stripos($i['treatment'], $search) !== false
                        || stripos($i['condition'], $search) !== false
                        || stripos($i['follow_up'], $search) !== false;
                })->values();
            }

            return response()->json(['success' => true, 'data' => $items]);

        } catch (\Exception $e) {
            Log::error('searchDischargeSummaries error: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    //  LEGACY / BACKWARD-COMPAT SEARCH METHODS
    //  (পুরনো route name দিয়ে call হলে নতুন method-এ delegate করে)
    // ============================================================

    /** @deprecated  use searchDiagnoses() */
    public function searchDiagnosis(Request $request)
    {
        return $this->searchDiagnoses($request);
    }

    /** @deprecated  use searchInvestigations() */
    // searchInvestigations already exists — no duplicate needed

    /** @deprecated  use searchAdvices() */
    public function searchAdvice(Request $request)
    {
        return $this->searchAdvices($request);
    }

    /** @deprecated  use searchFreshPrescriptions() */
    // searchFreshPrescriptions already exists — no duplicate needed

    // ============================================================
    //  STORE
    // ============================================================

    /**
     * Store surgery prescription
     * Route: POST /prescriptions/SurgeryPrescription/store
     */
    public function store(Request $request)
    {
        try {
            Log::info('===== SURGERY PRESCRIPTION STORE START =====');
            
            DB::beginTransaction();
            
            $validator = \Validator::make($request->all(), [
                'patient_id'  => 'required|exists:patients,id',
                'surgery_name'=> 'required|string',
                'surgery_date'=> 'required|date',
                'medicines'   => 'required|array|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            
            $patient = DB::table('patients')->where('id', $request->patient_id)->first();
            
            $year  = date('Y');
            $month = date('m');
            $count = DB::table('surgery_prescriptions')->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
            $prescriptionNo = 'SUR-' . $year . $month . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            
            $prescriptionId = DB::table('surgery_prescriptions')->insertGetId([
                'prescription_no' => $prescriptionNo,
                'patient_id'      => $request->patient_id,
                'patient_name'    => $patient->patientname ?? 'Unknown',
                'patient_age'     => $patient->age ?? null,
                'patient_gender'  => $patient->gender ?? null,
                'patient_contact' => $patient->mobile_no ?? null,
                'doctor_id'       => auth()->id() ?? 1,
                'surgery_name'    => $request->surgery_name,
                'surgery_date'    => $request->surgery_date,
                'template_id'     => $request->template_id ?? null,
                'pre_op_notes'    => $request->pre_op_notes ?? null,
                'post_op_notes'   => $request->post_op_notes ?? null,
                'instructions'    => $request->instructions,
                'status'          => 'active',
                'surgeon_name'    => $request->surgeon_name ?? optional(auth()->user())->name ?? 'Dr. Admin',
                'anesthesia_type' => $request->anesthesia_type ?? null,
                'admission_date'  => $request->admission_date ?? null,
                'discharge_date'  => $request->discharge_date ?? null,
                'ward_bed'        => $request->ward_bed ?? null,
                'bp'              => $request->bp ?? null,
                'pulse'           => $request->pulse ?? null,
                'temperature'     => $request->temperature ?? null,
                'weight'          => $request->weight ?? null,
                'rbs'             => $request->rbs ?? null,
                'created_by'      => auth()->id() ?? 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            
            Log::info('Prescription inserted with ID: ' . $prescriptionId);
            
            // ---- 1. Medicines ----
            if ($request->has('medicines') && is_array($request->medicines)) {
                foreach ($request->medicines as $medicine) {
                    DB::table('surgery_prescription_medicines')->insert([
                        'prescription_id'      => $prescriptionId,
                        'medicine_name'        => $medicine['name'],
                        'strength'             => $medicine['strength'] ?? null,
                        'dose'                 => $medicine['dosage'],
                        'dosage'               => $medicine['dosage'],
                        'duration'             => $medicine['duration'],
                        'order_type'           => $medicine['order_type'],
                        'template_medicine_id' => $medicine['template_medicine_id'] ?? null,
                        'route'                => $medicine['route'] ?? 'Oral',
                        'frequency'            => $medicine['frequency'] ?? '',
                        'medicine_type'        => $medicine['medicine_type'] ?? 'Tablet',
                        'instruction'          => $medicine['instructions'] ?? null,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                }
                Log::info('Medicines inserted: ' . count($request->medicines));
            }
            
            // ---- 2. Diagnoses ----
            if ($request->has('diagnoses') && is_array($request->diagnoses) && count($request->diagnoses) > 0) {
                foreach ($request->diagnoses as $diagnosis) {
                    DB::table('surgery_prescription_diagnoses')->insert([
                        'surgery_prescription_id' => $prescriptionId,
                        'diagnosis_name'          => $diagnosis['name'],
                        'notes'                   => $diagnosis['note'] ?? null,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]);
                }
                Log::info('Diagnoses inserted: ' . count($request->diagnoses));
            }
            
            // ---- 3. Investigations ----
            if ($request->has('investigations') && is_array($request->investigations) && count($request->investigations) > 0) {
                foreach ($request->investigations as $investigation) {
                    DB::table('surgery_investigations')->insert([
                        'prescription_id'    => $prescriptionId,
                        'investigation_name' => $investigation['name'],
                        'remarks'            => $investigation['note'] ?? null,
                        'status'             => 'pending',
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
                Log::info('Investigations inserted: ' . count($request->investigations));
            }

            // ---- 4. Advices ----
            if ($request->has('advices') && is_array($request->advices) && count($request->advices) > 0) {
                foreach ($request->advices as $advice) {
                    // table name আপনার actual table দিয়ে replace করুন
                    if (DB::getSchemaBuilder()->hasTable('surgery_prescription_advices')) {
                        DB::table('surgery_prescription_advices')->insert([
                            'prescription_id' => $prescriptionId,
                            'advice'          => $advice['advice'],
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
                Log::info('Advices inserted: ' . count($request->advices));
            }

            // ---- 5. Fresh Prescriptions ----
            if ($request->has('fresh_prescriptions') && is_array($request->fresh_prescriptions) && count($request->fresh_prescriptions) > 0) {
                foreach ($request->fresh_prescriptions as $fresh) {
                    if (DB::getSchemaBuilder()->hasTable('surgery_prescription_fresh')) {
                        DB::table('surgery_prescription_fresh')->insert([
                            'prescription_id' => $prescriptionId,
                            'name'            => $fresh['name'],
                            'details'         => $fresh['details'] ?? null,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
                Log::info('Fresh prescriptions inserted: ' . count($request->fresh_prescriptions));
            }

            // ---- 6. Discharge ----
            if ($request->has('discharge') && is_array($request->discharge)) {
                $d = $request->discharge;
                if (!empty($d['treatment']) || !empty($d['condition']) || !empty($d['follow_up'])) {
                    if (DB::getSchemaBuilder()->hasTable('surgery_prescription_discharge')) {
                        DB::table('surgery_prescription_discharge')->insert([
                            'prescription_id' => $prescriptionId,
                            'treatment'       => $d['treatment'] ?? null,
                            'condition'       => $d['condition'] ?? null,
                            'follow_up'       => $d['follow_up'] ?? null,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
            }
            
            DB::commit();
            Log::info('Transaction committed successfully');

            return response()->json([
                'success'         => true,
                'id'              => $prescriptionId,
                'prescription_no' => $prescriptionNo,
                'message'         => 'Prescription saved successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SURGERY STORE ERROR: ' . $e->getMessage() . ' Line:' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Failed to save: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    //  PDF
    // ============================================================

    /**
     * View PDF inline
     * Route: GET /prescriptions/SurgeryPrescription/{id}/pdf
     */
    public function viewPDF($id)
    {
        try {
            $prescription = DB::table('surgery_prescriptions')->where('id', $id)->firstOrFail();

            $medicines      = DB::table('surgery_prescription_medicines')->where('prescription_id', $id)->get();
            $diagnoses      = DB::table('surgery_prescription_diagnoses')->where('surgery_prescription_id', $id)->get();
            $investigations = DB::table('surgery_investigations')->where('prescription_id', $id)->get();

            $pdf = Pdf::loadView('prescriptions.surgery.pdf', compact('prescription', 'medicines', 'diagnoses', 'investigations'))
                ->setPaper('A4', 'portrait');

            return $pdf->stream('prescription-' . $prescription->prescription_no . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF view error: ' . $e->getMessage());
            abort(500, 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF
     * Route: GET /prescriptions/SurgeryPrescription/{id}/download
     */
    public function generatePDF($id)
    {
        try {
            $prescription = DB::table('surgery_prescriptions')->where('id', $id)->firstOrFail();

            $medicines      = DB::table('surgery_prescription_medicines')->where('prescription_id', $id)->get();
            $diagnoses      = DB::table('surgery_prescription_diagnoses')->where('surgery_prescription_id', $id)->get();
            $investigations = DB::table('surgery_investigations')->where('prescription_id', $id)->get();

            $pdf = Pdf::loadView('prescriptions.surgery.pdf', compact('prescription', 'medicines', 'diagnoses', 'investigations'))
                ->setPaper('A4', 'portrait');

            return $pdf->download('prescription-' . $prescription->prescription_no . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF download error: ' . $e->getMessage());
            abort(500, 'Failed to download PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    //  HELPERS
    // ============================================================

    /**
     * Convert template order_type to prescription order_type
     */
    private function convertOrderType($templateOrderType)
    {
        if (!$templateOrderType) return 'post-op';

        switch (strtolower(trim($templateOrderType))) {
            case 'admit':      return 'admission';
            case 'preorder':   return 'pre-op';
            case 'postorder':  return 'post-op';
            default:           return 'post-op';
        }
    }

    /**
     * Get doctors list
     */
    private function getDoctorsList()
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('doctors')) {
                return DB::table('doctors')->where('active', 1)->orderBy('doctor_name')->get(['id', 'doctor_name as name', 'reg_no']);
            }
            return collect();
        } catch (\Exception $e) {
            Log::error('Error getting doctors: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Test endpoint
     * Route: GET /prescriptions/SurgeryPrescription/test
     */
    public function test() 
    {
        try {
            $tables = collect([
                'templates', 'template_medicine', 'template_diagnosis',
                'template_investigations', 'template_advice', 'template_complain',
                'template_discharge', 'surgery_prescriptions',
                'surgery_prescription_medicines', 'surgery_prescription_diagnoses',
                'surgery_investigations',
            ])->mapWithKeys(fn($t) => [$t => DB::getSchemaBuilder()->hasTable($t)]);

            return response()->json([
                'success' => true,
                'message' => 'Surgery prescription controller is working',
                'data'    => [
                    'tables_exist' => $tables,
                    'counts'       => [
                        'templates'      => Template::where('status', 1)->count(),
                        'medicines'      => TemplateMedicine::where('active', 1)->count(),
                        'diagnoses'      => TemplateDiagnosis::count(),
                        'investigations' => TemplateInvestigation::count(),
                        'advices'        => TemplateAdvice::count(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Test failed: ' . $e->getMessage()], 500);
        }
    }
} 