<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReleaseApprovalController extends Controller
{
    /* ------------------------------------------
       INDEX — Manager approval list
       ? ???? release_pending status ??????
    ------------------------------------------ */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('patients')
            ->join('nursing_admissions', 'patients.id', '=', 'nursing_admissions.patient_id')
            ->where('nursing_admissions.admission_type', 'on_admission')
            ->where('nursing_admissions.status', 'release_pending') // ? ???? pending
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
                DB::raw('MAX(nursing_admissions.status) as admission_status'),
                // ? submitted_at — release_requested_at column ????? ??????
                DB::raw('MAX(nursing_admissions.release_requested_at) as submitted_at')
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

        $patients = $query->orderBy('admission_id', 'desc')->paginate(20)->withQueryString();

        return view('nursing.release-approval', compact('patients', 'search'));
    }

    /* ------------------------------------------
       APPROVE — Manager release approve ????
       ? release_pending ? released
    ------------------------------------------ */
    public function approve(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|integer',
            'patient_id'   => 'required|integer',
        ]);

        try {
            $updateData = [
                'status'     => 'released',
                'updated_at' => now(),
            ];

            // ? approved_by column ????? save ???
            if (Schema::hasColumn('nursing_admissions', 'release_approved_by')) {
                $updateData['release_approved_by'] = auth()->id();
            }
            if (Schema::hasColumn('nursing_admissions', 'release_approved_at')) {
                $updateData['release_approved_at'] = now();
            }

            $updated = DB::table('nursing_admissions')
                ->where('id', $request->admission_id)
                ->where('patient_id', $request->patient_id)
                ->where('status', 'release_pending') // ? ???? pending ???? approve ???
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found or already processed.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Patient release approved successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ------------------------------------------
       REJECT — Manager release reject ????
       ? release_pending ? discharged (nurse ?? ???? ???? ????)
    ------------------------------------------ */
    public function reject(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|integer',
            'patient_id'   => 'required|integer',
            'reason'       => 'nullable|string|max:500',
        ]);

        try {
            $updateData = [
                'status'     => 'discharged', // ? Discharge list ? ???? ????
                'updated_at' => now(),
            ];

            // ? rejection reason save ??? ??? column ????
            if (Schema::hasColumn('nursing_admissions', 'notes') && $request->reason) {
                $updateData['notes'] = 'Release rejected: ' . $request->reason;
            }

            // ? approved_by clear ???
            if (Schema::hasColumn('nursing_admissions', 'release_approved_by')) {
                $updateData['release_approved_by'] = null;
            }
            if (Schema::hasColumn('nursing_admissions', 'release_approved_at')) {
                $updateData['release_approved_at'] = null;
            }

            $updated = DB::table('nursing_admissions')
                ->where('id', $request->admission_id)
                ->where('patient_id', $request->patient_id)
                ->where('status', 'release_pending') // ? ???? pending ???? reject ???
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found or already processed.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Release rejected. Patient returned to discharge list.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}