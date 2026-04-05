<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class RoundPrescriptionController extends Controller
{
    /* ══════════════════════════════════════════
       INDEX — Round Prescription patient list
       ✅ শুধু সেই patients যারা currently
          admitted (discharge হয়নি)
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->whereNotIn('nursing_admissions.status', ['discharged', 'released'])
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
                DB::raw('MAX(nursing_admissions.status) as admission_status')
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
                $q->where('patients.patientname', 'LIKE', "%{$search}%")
                  ->orWhere('patients.patientcode', 'LIKE', "%{$search}%")
                  ->orWhere('patients.mobile_no', 'LIKE', "%{$search}%");
            });
        }

        $patients  = $query->orderBy('admission_id', 'desc')->paginate(20)->withQueryString();
        
        // Query for past round prescriptions (latest per patient)
        $roundQuery = DB::table('nursing_round_prescriptions')
            ->join('patients', 'nursing_round_prescriptions.patient_id', '=', 'patients.id')
            ->select(
                'nursing_round_prescriptions.*',
                'patients.patientname',
                'patients.patientcode',
                'patients.age',
                'patients.gender',
                'patients.mobile_no',
                'patients.address',
                'patients.upozila',
                'patients.blood_group'
            )
            ->whereRaw('nursing_round_prescriptions.id IN (
                SELECT MAX(id) 
                FROM nursing_round_prescriptions 
                GROUP BY patient_id
            )')
            ->orderBy('nursing_round_prescriptions.created_at', 'desc');
        
        $RoundPatients = $roundQuery->paginate(20)->withQueryString();
        
        $medicines = DB::table('template_medicine')->orderBy('name')->get();
        $doctors   = DB::table('doctors')
                        ->whereIn('active', [1, '1', true, 'yes', 'active'])
                        ->orderBy('name')
                        ->get();
        $templates = DB::table('tbl_template')->where('status', 1)->orderBy('title')->get();

        return view('nursing.roundprescription', compact(
            'patients',
            'RoundPatients',
            'search',
            'medicines',
            'doctors',
            'templates'
        ));
    }

    /* ══════════════════════════════════════════
       GET PATIENT ADMISSION DATA (AJAX)
       ✅ Patient select করলে তার admission,
          previous prescriptions load হবে
    ══════════════════════════════════════════ */
    public function getPatientAdmissionData($patientId)
    {
        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'success'                => true,
            'admission'              => $admission,
            'medicines'              => $this->fetchAdmissionMedicines($admission),
            'round_prescriptions'    => $this->getRoundPrescriptions($patientId),
            'previous_prescriptions' => $this->getPreviousAdmissionPrescriptions($patientId),
            'message'                => $admission ? 'Admission record found.' : 'No admission record found.',
        ]);
    }

    /* ══════════════════════════════════════════
       STORE ROUND PRESCRIPTION (AJAX)
       ✅ Medicine add করে save করা যাবে
    ══════════════════════════════════════════ */
    public function store(Request $request)
    {
        // Debug log
        Log::info('Round prescription store attempt', [
            'request_data' => $request->all(),
            'has_table' => Schema::hasTable('nursing_round_prescriptions')
        ]);

        $request->validate([
            'patient_id'        => 'required|integer',
            'patient_name'      => 'nullable|string|max:255',
            'patient_age'       => 'nullable|string|max:100',
            'patient_code'      => 'nullable|string|max:100',
            'doctor_name'       => 'nullable|string|max:255',
            'prescription_date' => 'nullable|date',
            'notes'             => 'nullable|string',
            'medicines'         => 'nullable|array',
        ]);

        if (!Schema::hasTable('nursing_round_prescriptions')) {
            Log::error('Table nursing_round_prescriptions not found');
            return response()->json([
                'success' => false,
                'message' => 'Table nursing_round_prescriptions not found. Please run SQL first.',
                'debug' => 'Table missing'
            ], 500);
        }

        Log::info('Table exists, proceeding with save');

        DB::beginTransaction();
        try {
            Log::info('Inserting main prescription record', [
                'patient_id' => $request->patient_id,
                'patient_name' => $request->patient_name
            ]);
            
            $roundId = DB::table('nursing_round_prescriptions')->insertGetId([
                'patient_id'        => $request->patient_id,
                'patient_name'      => $request->patient_name,
                'patient_age'       => $request->patient_age,
                'patient_code'      => $request->patient_code,
                'doctor_name'       => $request->doctor_name,
                'prescription_date' => $request->prescription_date ?: now()->toDateString(),
                'notes'             => $request->notes,
                'created_by'        => auth()->id(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            if (Schema::hasTable('nursing_round_medicines')) {
                $rows = [];
                foreach ((array) $request->input('medicines', []) as $m) {
                    $name = trim((string) ($m['name'] ?? $m['medicine_name'] ?? ''));
                    if ($name === '') continue;

                    $rows[] = [
                        'round_prescription_id' => $roundId,
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
                    DB::table('nursing_round_medicines')->insert($rows);
                }
            }

            DB::commit();

            Log::info('Round prescription saved successfully', ['round_id' => $roundId]);

            return response()->json([
                'success'         => true,
                'message'         => 'Round prescription saved successfully.',
                'prescription_id' => $roundId,
                'debug'          => 'Saved successfully'
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
       GET DATA (AJAX — patient list for JSON)
    ══════════════════════════════════════════ */
    public function getData(Request $request)
    {
        $patients = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->whereNotIn('nursing_admissions.status', ['discharged', 'released'])
            ->select(
                'patients.id as patient_id',
                'patients.patientname as patient_name',
                'patients.patientcode',
                'patients.mobile_no',
                'patients.age',
                'patients.gender',
                DB::raw('MAX(nursing_admissions.id) as admission_id'),
                DB::raw('MAX(nursing_admissions.admission_date) as admission_date')
            )
            ->groupBy(
                'patients.id',
                'patients.patientname',
                'patients.patientcode',
                'patients.mobile_no',
                'patients.age',
                'patients.gender'
            )
            ->orderBy('admission_id', 'desc')
            ->get();

        return response()->json([
            'ok'       => true,
            'patients' => $patients,
        ]);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════ */

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

    private function getRoundPrescriptions($patientId): array
    {
        if (!Schema::hasTable('nursing_round_prescriptions')) return [];

        $rows = DB::table('nursing_round_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];

            if (Schema::hasTable('nursing_round_medicines')) {
                $meds = DB::table('nursing_round_medicines')
                    ->where('round_prescription_id', $row->id)
                    ->get();

                foreach ($meds as $m) {
                    $parts = array_filter([
                        $m->medicine_name ?? null,
                        $m->dose          ?? null,
                        $m->route         ?? null,
                        $m->frequency     ?? null,
                        !empty($m->duration) ? ('x ' . $m->duration) : null,
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
                'doctor' => $row->doctor_name ?? null,
                'type'   => 'round',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
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
                        $m->dose          ?? null,
                        $m->route         ?? null,
                        $m->frequency     ?? null,
                        !empty($m->duration) ? ('x ' . $m->duration) : null,
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

    /* ══════════════════════════════════════════
       DETAIL — Show round prescription details
       GET /roundprescription/detail/{id}
    ══════════════════════════════════════════ */
    public function detail($id)
    {
        $prescription = DB::table('nursing_round_prescriptions')->where('id', $id)->first();
        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Round prescription not found.'], 404);
        }

        $medicines = DB::table('nursing_round_medicines')
            ->where('round_prescription_id', $id)
            ->get();

        // Get patient information
        $patient = DB::table('patients')->where('id', $prescription->patient_id)->first();

        // Get all patient round prescriptions for history
        $patientPrescriptions = DB::table('nursing_round_prescriptions')
            ->where('patient_id', $prescription->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $prescription->id,
                'patient_name' => $prescription->patient_name,
                'patient_age' => $prescription->patient_age,
                'patient_code' => $prescription->patient_code,
                'doctor_name' => $prescription->doctor_name,
                'prescription_date' => $prescription->prescription_date,
                'notes' => $prescription->notes,
                'created_at' => $prescription->created_at,
                'medicines' => $medicines,
                'patient' => $patient,
                'patientPrescriptions' => $patientPrescriptions
            ]
        ]);
    }

    /* ══════════════════════════════════════════
       PATIENT HISTORY — Get patient's round prescription history
       GET /roundprescription/patient-history/{patientId}
    ══════════════════════════════════════════ */
    public function patientHistory($patientId)
    {
        // Get patient information
        $patient = DB::table('patients')->where('id', $patientId)->first();
        
        // Get all round prescriptions for this patient
        $prescriptions = DB::table('nursing_round_prescriptions')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        $prescriptionsData = [];

        foreach ($prescriptions as $prescription) {
            // Get medicines for this prescription
            $medicines = DB::table('nursing_round_medicines')
                ->where('round_prescription_id', $prescription->id)
                ->get();

            $prescriptionsData[] = [
                'id' => $prescription->id,
                'prescription_date' => $prescription->prescription_date,
                'round_time' => $prescription->round_time,
                'doctor_name' => $prescription->doctor_name,
                'notes' => $prescription->notes,
                'created_at' => $prescription->created_at,
                'medicines' => $medicines,
            ];
        }

        return response()->json([
            'success' => true,
            'patient' => [
                'id' => $patient->id ?? null,
                'name' => $patient->patientname ?? $patient->patient_name ?? '—',
                'age' => $patient->age ?? '—',
                'code' => $patient->patientcode ?? $patient->patient_code ?? '—',
                'mobile' => $patient->mobile_no ?? '—',
                'blood_group' => $patient->blood_group ?? '',
            ],
            'prescriptions' => $prescriptionsData,
        ]);
    }
}