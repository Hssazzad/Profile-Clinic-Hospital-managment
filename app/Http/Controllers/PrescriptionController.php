<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
{
    // 1) SEARCH: সিলেক্ট পেশেন্ট → প্রেসক্রিপশন লিস্ট দেখা
    public function search(Request $request)
    {
        $patientId = $request->query('patient');

        $patients = DB::table('patients')
            ->orderBy('patientname')
            ->get();

        $selectedPatient = null;
        $prescriptions   = collect();

        if ($patientId) {
            $selectedPatient = DB::table('patients')
                ->where('id', $patientId)
                ->first();

            $prescriptions = DB::table('prescriptions')
                ->where('patient_id', $patientId)
                ->orderByDesc('id')
                ->get();
        }

        return view('prescriptions.search', compact(
            'patients',
            'selectedPatient',
            'prescriptions',
            'patientId'
        ));
    }

    // 2) AJAX PREVIEW: প্রেসক্রিপশন প্রিভিউ (HTML Fragment)
    public function previewAjax($id)
    {
        $rx = DB::table('prescriptions')->where('id', $id)->first();
        if (!$rx) {
            return response('Prescription not found', 404);
        }

        $patient = DB::table('patients')->where('id', $rx->patient_id)->first();

        $complaints = DB::table('prescriptions_complain')->where('prescription_id', $id)->get();
        $diagnoses = DB::table('prescriptions_diagnosis')->where('prescription_id', $id)->get();
        $investigations = DB::table('prescriptions_investigations')->where('prescription_id', $id)->get();
        $medicines = DB::table('prescriptions_medicine')->where('prescription_id', $id)->get();

        $clinicAddr  = 'আপনার ক্লিনিকের ঠিকানা এখানে দিন';
        $clinicPhone = 'মোবাইল: 01XXXXXXXXX';

        $html = view('prescriptions.tabs.preview', [
            'pid'            => $rx->id,
            'patientId'      => $patient->id ?? null,
            'rx'             => $rx,
            'patient'        => $patient,
            'complaints'     => $complaints,
            'diagnoses'      => $diagnoses,
            'investigations' => $investigations,
            'medicines'      => $medicines,
            'clinicAddr'     => $clinicAddr,
            'clinicPhone'    => $clinicPhone,
        ])->render();

        return response($html);
    }

    // 3) PDF GENERATION
    public function pdf($id)
    {
        $rx = DB::table('prescriptions')->where('id', $id)->first();
        if (!$rx) { abort(404); }

        $patient = DB::table('patients')->where('id', $rx->patient_id)->first();
        $complaints = DB::table('prescriptions_complain')->where('prescription_id', $id)->get();
        $diagnoses = DB::table('prescriptions_diagnosis')->where('prescription_id', $id)->get();
        $investigations = DB::table('prescriptions_investigations')->where('prescription_id', $id)->get();
        $medicines = DB::table('prescriptions_medicine')->where('prescription_id', $id)->get();

        $pdf = Pdf::loadView('prescriptions.pdf', [
            'rx'             => $rx,
            'patient'        => $patient,
            'complaints'     => $complaints,
            'diagnoses'      => $diagnoses,
            'investigations' => $investigations,
            'medicines'      => $medicines,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("prescription-$id.pdf");
    }

    // 4) WIZARD: পেশেন্ট সিলেকশন এবং ভাইটাল ডাটা লোড
    public function wizard(Request $request)
    {
        $tab = $request->query('tab', 'patients');
        $pid = $request->query('id'); // Prescription ID
        $patientId = $request->query('patient'); // Patient ID

        $vitals = null;
        if ($patientId) {
            // আপনার ডাটাবেস টেবিলের নাম 'patient_pre_assessments' হলে নিচের লাইনটি ঠিক আছে
            $vitals = DB::table('patient_pre_assessments') 
                ->where('patientcode', $patientId)
                ->latest()
                ->first();
        }

        $rx = null;
        if ($pid) {
            $rx = DB::table('prescriptions')->where('id', $pid)->first();
        }

        return view('prescriptions.wizard-master', compact('tab', 'pid', 'patientId', 'vitals', 'rx'));
    }

    // 5) PRE-CON ASSESSMENT: পেশেন্ট সার্চ এবং লিস্ট
    public function PreConAssessment(Request $request)
    {
        $q = $request->query('q');
        $patientcode = $request->query('patientcode');
        
        $patients = collect();
        if ($q) {
            $patients = DB::table('patients')
                ->where('patientname', 'like', "%$q%")
                ->orWhere('patientcode', 'like', "%$q%")
                ->orWhere('mobile_no', 'like', "%$q%")
                ->get();
        }

        // AJAX রিকোয়েস্টের জন্য
        if ($request->ajax()) {
            return response()->json(['ok' => true, 'patients' => $patients]);
        }

        return view('prescriptions.pre-assessment', compact('patients', 'q', 'patientcode'));
    }

    // ============ নতুন যোগ করা সার্জারি প্রেসক্রিপশন মেথড ============

    /**
     * Store surgery prescription
     */
    public function storeSurgeryPrescription(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|integer',
                'surgery_name' => 'required|string',
                'surgery_date' => 'required|date',
                'medicines' => 'required|array',
                'medicines.*.name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create surgery prescription
            $prescriptionId = DB::table('surgery_prescriptions')->insertGetId([
                'patient_id' => $request->patient_id,
                'surgery_name' => $request->surgery_name,
                'surgery_date' => $request->surgery_date,
                'anesthesia_type' => $request->anesthesia_type,
                'ward_bed' => $request->ward_bed,
                'bp' => $request->bp,
                'pulse' => $request->pulse,
                'temperature' => $request->temperature,
                'weight' => $request->weight,
                'instructions' => $request->instructions,
                'template_id' => $request->template_id,
                'created_by' => auth()->id(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Save medicines
            if ($request->has('medicines')) {
                foreach ($request->medicines as $medicine) {
                    DB::table('surgery_prescription_medicines')->insert([
                        'prescription_id' => $prescriptionId,
                        'medicine_id' => $medicine['template_medicine_id'] ?? null,
                        'name' => $medicine['name'],
                        'strength' => $medicine['strength'] ?? null,
                        'brand' => $medicine['brand'] ?? 'Generic',
                        'dosage' => $medicine['dosage'],
                        'duration' => $medicine['duration'],
                        'order_type' => $medicine['order_type'],
                        'route' => $medicine['route'] ?? 'Oral',
                        'frequency' => $medicine['frequency'] ?? null,
                        'medicine_type' => $medicine['medicine_type'] ?? 'Tablet',
                        'instructions' => $medicine['instructions'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Save diagnoses
            if ($request->has('diagnoses')) {
                foreach ($request->diagnoses as $diagnosis) {
                    DB::table('surgery_prescription_diagnoses')->insert([
                        'prescription_id' => $prescriptionId,
                        'name' => $diagnosis['name'],
                        'notes' => $diagnosis['note'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Save investigations
            if ($request->has('investigations')) {
                foreach ($request->investigations as $investigation) {
                    DB::table('surgery_prescription_investigations')->insert([
                        'prescription_id' => $prescriptionId,
                        'name' => $investigation['name'],
                        'notes' => $investigation['note'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Save advices
            if ($request->has('advices')) {
                foreach ($request->advices as $advice) {
                    DB::table('surgery_prescription_advices')->insert([
                        'prescription_id' => $prescriptionId,
                        'advice' => $advice['advice'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Save discharge summary
            if ($request->has('discharge')) {
                DB::table('surgery_prescription_discharges')->insert([
                    'prescription_id' => $prescriptionId,
                    'treatment' => $request->discharge['treatment'] ?? null,
                    'condition' => $request->discharge['condition'] ?? null,
                    'follow_up' => $request->discharge['follow_up'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Surgery prescription saved successfully',
                'data' => ['id' => $prescriptionId]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Surgery prescription store error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving prescription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medicines for surgery prescription
     */
    public function searchSurgeryMedicines(Request $request)
    {
        try {
            $query = $request->get('q') ?? $request->get('search');
            
            Log::info('Surgery medicine search query: ' . $query);
            
            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            // Try common_medicine table first
            if (DB::getSchemaBuilder()->hasTable('common_medicine')) {
                $medicines = DB::table('common_medicine')
                    ->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('strength', 'LIKE', "%{$query}%")
                    ->orWhere('GroupName', 'LIKE', "%{$query}%")
                    ->limit(20)
                    ->get();
                    
                $formatted = $medicines->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'strength' => $item->strength ?? '',
                        'brand' => $item->GroupName ?? 'Generic',
                        'medicine_type' => $item->medicine_type ?? 'Tablet',
                        'text' => $item->name . ' ' . ($item->strength ?? '') . ' (' . ($item->GroupName ?? 'Generic') . ')'
                    ];
                });
                
                return response()->json([
                    'success' => true,
                    'data' => $formatted
                ]);
            }
            
            // Try medicines table
            if (DB::getSchemaBuilder()->hasTable('medicines')) {
                $medicines = DB::table('medicines')
                    ->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('generic_name', 'LIKE', "%{$query}%")
                    ->orWhere('brand', 'LIKE', "%{$query}%")
                    ->limit(20)
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'data' => $medicines
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => []
            ]);
            
        } catch (\Exception $e) {
            Log::error('Surgery medicine search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching medicines',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get surgery prescription templates
     */
    public function getSurgeryTemplates()
    {
        try {
            $templates = [];
            
            if (DB::getSchemaBuilder()->hasTable('surgery_prescription_templates')) {
                $templates = DB::table('surgery_prescription_templates')
                    ->where('status', 'active')
                    ->orWhereNull('status')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get surgery templates error: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * Get surgery template data by ID
     */
    public function getSurgeryTemplateData($id)
    {
        try {
            $template = null;
            
            if (DB::getSchemaBuilder()->hasTable('surgery_prescription_templates')) {
                $template = DB::table('surgery_prescription_templates')->where('id', $id)->first();
            }
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }
            
            // Get related data
            $medicines = DB::table('template_medicines')
                        ->where('template_id', $id)
                        ->get() ?? [];
                        
            $diagnoses = DB::table('template_diagnoses')
                        ->where('template_id', $id)
                        ->get() ?? [];
                        
            $investigations = DB::table('template_investigations')
                            ->where('template_id', $id)
                            ->get() ?? [];
                            
            $advices = DB::table('template_advices')
                      ->where('template_id', $id)
                      ->get() ?? [];
            
            return response()->json([
                'success' => true,
                'template' => $template,
                'medicines' => $medicines,
                'diagnoses' => $diagnoses,
                'investigations' => $investigations,
                'advices' => $advices,
                'discharge' => null,
                'counts' => [
                    'medicines' => count($medicines),
                    'diagnoses' => count($diagnoses),
                    'investigations' => count($investigations),
                    'advices' => count($advices)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get surgery template data error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading template data'
            ], 500);
        }
    }

    /**
     * Search patients for surgery prescription
     */
    public function searchSurgeryPatients(Request $request)
    {
        try {
            $query = $request->get('q');
            
            $patients = [];
            
            if (DB::getSchemaBuilder()->hasTable('patients')) {
                $patientsQuery = DB::table('patients');
                
                if (!empty($query)) {
                    $patientsQuery->where(function($q) use ($query) {
                        $q->where('patientname', 'LIKE', "%{$query}%")
                          ->orWhere('patientcode', 'LIKE', "%{$query}%")
                          ->orWhere('mobile_no', 'LIKE', "%{$query}%")
                          ->orWhere('phone', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%");
                    });
                }
                
                $patients = $patientsQuery->limit(50)->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $patients
            ]);
            
        } catch (\Exception $e) {
            Log::error('Surgery patient search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching patients',
                'data' => []
            ], 500);
        }
    }

    /**
     * Search diagnosis for surgery prescription
     */
    public function searchSurgeryDiagnosis(Request $request)
    {
        try {
            $query = $request->get('search') ?? $request->get('q');
            $diagnoses = [];

            if (DB::getSchemaBuilder()->hasTable('template_diagnosis')) {
                $diagnosisQuery = DB::table('template_diagnosis');
                if (!empty($query)) {
                    $diagnosisQuery->where('name', 'LIKE', "%{$query}%")
                                   ->orWhere('description', 'LIKE', "%{$query}%");
                }
                $diagnoses = $diagnosisQuery->limit(20)->get();
            } elseif (DB::getSchemaBuilder()->hasTable('template_diagnoses')) {
                $diagnosisQuery = DB::table('template_diagnoses');
                if (!empty($query)) {
                    $diagnosisQuery->where('name', 'LIKE', "%{$query}%")
                                   ->orWhere('description', 'LIKE', "%{$query}%");
                }
                $diagnoses = $diagnosisQuery->limit(20)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $diagnoses
            ]);
        } catch (\Exception $e) {
            Log::error('Surgery diagnosis search error: ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => []]);
        }
    }

    /**
     * Search investigations for surgery prescription
     */
    public function searchSurgeryInvestigations(Request $request)
    {
        try {
            $query = $request->get('search') ?? $request->get('q');
            $investigations = [];

            if (DB::getSchemaBuilder()->hasTable('template_investigations')) {
                $investigationQuery = DB::table('template_investigations');
                if (!empty($query)) {
                    $investigationQuery->where('name', 'LIKE', "%{$query}%")
                                       ->orWhere('type', 'LIKE', "%{$query}%");
                }
                $investigations = $investigationQuery->limit(20)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $investigations
            ]);
        } catch (\Exception $e) {
            Log::error('Surgery investigations search error: ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => []]);
        }
    }

    /**
     * Search advice for surgery prescription
     */
    public function searchSurgeryAdvice(Request $request)
    {
        try {
            $query = $request->get('search') ?? $request->get('q');
            $advices = [];

            if (DB::getSchemaBuilder()->hasTable('template_advice')) {
                $adviceQuery = DB::table('template_advice');
                if (!empty($query)) {
                    $adviceQuery->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('advice', 'LIKE', "%{$query}%");
                }
                $advices = $adviceQuery->limit(20)->get();
            } elseif (DB::getSchemaBuilder()->hasTable('template_advices')) {
                $adviceQuery = DB::table('template_advices');
                if (!empty($query)) {
                    $adviceQuery->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('advice', 'LIKE', "%{$query}%");
                }
                $advices = $adviceQuery->limit(20)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $advices
            ]);
        } catch (\Exception $e) {
            Log::error('Surgery advice search error: ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => []]);
        }
    }

    /**
     * Search discharge for surgery prescription
     */
    public function searchSurgeryDischarge(Request $request)
    {
        try {
            $query = $request->get('search') ?? $request->get('q');
            $discharges = [];

            if (DB::getSchemaBuilder()->hasTable('template_discharge')) {
                $dischargeQuery = DB::table('template_discharge');
                if (!empty($query)) {
                    $dischargeQuery->where('name', 'LIKE', "%{$query}%")
                                   ->orWhere('notes', 'LIKE', "%{$query}%");
                }
                $discharges = $dischargeQuery->limit(20)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $discharges
            ]);
        } catch (\Exception $e) {
            Log::error('Surgery discharge search error: ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => []]);
        }
    }

    /**
     * Search complain for surgery prescription
     */
    public function searchSurgeryComplain(Request $request)
    {
        try {
            $query = $request->get('search') ?? $request->get('q');
            $complains = [];

            if (DB::getSchemaBuilder()->hasTable('template_complain')) {
                $complainQuery = DB::table('template_complain');
                if (!empty($query)) {
                    $complainQuery->where('name', 'LIKE', "%{$query}%")
                                  ->orWhere('complain', 'LIKE', "%{$query}%");
                }
                $complains = $complainQuery->limit(20)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $complains
            ]);
        } catch (\Exception $e) {
            Log::error('Surgery complain search error: ' . $e->getMessage());
            return response()->json(['success' => true, 'data' => []]);
        }
    }
}