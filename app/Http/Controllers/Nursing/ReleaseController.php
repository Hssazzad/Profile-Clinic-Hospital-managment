<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReleaseController extends Controller
{
    /* ------------------------------------------
       INDEX — Release patient list
       ✅ Nurse দেখবে — status = 'discharged'
    ------------------------------------------ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->where('nursing_admissions.status', 'discharged') // ✅ শুধু discharged দেখাবে
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
        $doctors   = DB::table('doctors')->select('id', 'name')->orderBy('name')->get();

        return view('nursing.releasepatients', compact('patients', 'search', 'medicines', 'doctors'));
    }

    /* ------------------------------------------
       GET PATIENT DATA (AJAX)
       ✅ শুধু discharged admission খুঁজবে
    ------------------------------------------ */
    public function getPatientData($patientId)
    {
        $patient = DB::table('patients')->where('id', $patientId)->first();

        if (!$patient) {
            return response()->json([
                'success' => true,
                'message' => 'Patient release submitted for manager approval.',
                'status'  => 'pending_approval'
            ], 404);
        }

        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->where('status', 'discharged') // ✅ শুধু discharged
            ->orderBy('id', 'desc')
            ->first();

        $doctors = DB::table('doctors')->select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'success'             => true,
            'patient'             => $patient,
            'admission'           => $admission,
            'doctors'             => $doctors,
            'fresh_prescriptions' => $this->getFreshPrescriptions($patientId),
            'round_prescriptions' => $this->getRoundPrescriptions($patientId),
            'message'             => $admission ? 'Record found.' : 'No admission record found.',
        ]);
    }

    /* ------------------------------------------
       STORE — Nurse submit করলে release_pending
       ✅ Manager approve না করলে released হবে না
    ------------------------------------------ */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'   => 'required|integer',
            'admission_id' => 'required|integer',
            'release_date' => 'nullable|date',
            'notes'        => 'nullable|string|max:1000',
            'bill_items'   => 'nullable|array',
            'bill_grand'   => 'nullable|numeric',
        ]);

        try {
            // Update admission status to PENDING (not released yet)
            $updateData = [
                'status'     => 'release_pending', // Changed from 'released'
                'updated_at' => now(),
            ];
            
            // Add release_date if column exists
            if (Schema::hasColumn('nursing_admissions', 'release_date')) {
                $updateData['release_date'] = $request->release_date ?: now()->toDateString();
            }
            
            // Add notes if column exists
            if (Schema::hasColumn('nursing_admissions', 'notes')) {
                $updateData['notes'] = $request->notes;
            }
            
            // Add pending tracking fields
            if (Schema::hasColumn('nursing_admissions', 'release_requested_by')) {
                $updateData['release_requested_by'] = auth()->id();
            }
            if (Schema::hasColumn('nursing_admissions', 'release_requested_at')) {
                $updateData['release_requested_at'] = now();
            }
            
            $updated = DB::table('nursing_admissions')
                ->where('id', $request->admission_id)
                ->where('patient_id', $request->patient_id)
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching admission record found.',
                ], 404);
            }

            if (!empty($request->bill_items) && Schema::hasTable('nursing_release_bills')) {
                DB::table('nursing_release_bills')->insert([
                    'patient_id'    => $request->patient_id,
                    'admission_id'  => $request->admission_id,
                    'bill_items'    => json_encode($request->bill_items),
                    'bill_subtotal' => $request->input('bill_subtotal', 0),
                    'bill_discount' => $request->input('bill_discount', 0),
                    'bill_grand'    => $request->bill_grand ?? 0,
                    'bill_advance'  => $request->input('bill_advance', 0),
                    'bill_due'      => $request->input('bill_due', 0),
                    'doctor_name'   => $request->input('doctor_name', ''),
                    'condition'     => $request->input('condition', ''),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Release request submitted. Waiting for manager approval.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Release failed: ' . $e->getMessage(),
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
            if (Schema::hasTable('nursing_fresh_prescription_medicines')) {
                $meds = DB::table('nursing_fresh_prescription_medicines')
                    ->where('prescription_id', $row->id)
                    ->orderBy('sort_order')
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
                        !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                    ]);
                    if (!empty($parts)) $lines[] = implode(' ', $parts);
                }
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
}