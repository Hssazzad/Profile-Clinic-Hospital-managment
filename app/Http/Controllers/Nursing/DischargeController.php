<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DischargeController extends Controller
{
    /* ------------------------------------------
       INDEX — Discharge patient list
       ? ??? patients ????? status fresh_done
          ???? ???? active status ??? —
          ?????? discharged / released ???
    ------------------------------------------ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // ? ???? ?? statuses ???? Discharge list ? ??????
        $activeStatuses = [
            'fresh_done',
            'post_surgery_done', // Fresh skip ??? ?????? Discharge ? ???
            'admitted',
            'on_ward',
        ];

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->whereIn('nursing_admissions.status', $activeStatuses) // ? ???????? ?????
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
        $medicines = DB::table('template_medicine')->orderBy('name')->get();
        $doctors   = DB::table('doctors')
                        ->whereIn('active', [1, '1', true, 'yes', 'active'])
                        ->orderBy('name')
                        ->get();

        return view('nursing.discharge', compact('patients', 'search', 'medicines', 'doctors'));
    }

    /* ------------------------------------------
       GET PATIENT DATA (AJAX)
    ------------------------------------------ */
    public function getPatientData($patientId)
    {
        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderBy('id', 'desc')
            ->first();

        $patient = DB::table('patients')->where('id', $patientId)->first();

        return response()->json([
            'success'             => true,
            'patient'             => $patient,
            'admission'           => $admission,
            'fresh_prescriptions' => $this->getFreshPrescriptions($patientId),
            'round_prescriptions' => $this->getRoundPrescriptions($patientId),
            'message'             => $admission ? 'Record found.' : 'No admission record found.',
        ]);
    }

    /* ------------------------------------------
       STORE — Mark patient as discharged
    ------------------------------------------ */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'     => 'required|integer',
            'admission_id'   => 'required|integer',
            'discharge_date' => 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        try {
            DB::table('nursing_admissions')
                ->where('id', $request->admission_id)
                ->where('patient_id', $request->patient_id)
                ->update([
                    'status'         => 'discharged',
                    'discharge_date' => $request->discharge_date ?: now()->toDateString(),
                    'notes'          => $request->notes,
                    'updated_at'     => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient discharged successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Discharge failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ------------------------------------------
       PRIVATE HELPERS
    ------------------------------------------ */

    private function getFreshPrescriptions($patientId): array
    {
        if (!Schema::hasTable('nursing_fresh_prescriptions')) return [];

        $rows = DB::table('nursing_fresh_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(5)
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
                        $m->dose          ?? null,
                        $m->route         ?? null,
                        $m->frequency     ?? null,
                        !empty($m->duration) ? ('x ' . $m->duration) : null,
                        !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
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
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
            }
            $result[] = [
                'id'    => $row->id,
                'date'  => $row->prescription_date ?? $row->created_at,
                'doctor'=> $row->doctor_name ?? null,
                'type'  => 'round',
                'lines' => array_values($lines),
            ];
        }
        return $result;
    }
}