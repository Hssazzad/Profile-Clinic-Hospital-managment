<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientAdmitController extends Controller
{
    /**
     * Show all admissions (optional list).
     */
    public function index()
    {
        // join with patients to show names
        $admissions = DB::table('admissions')
            ->join('patients', 'patients.id', '=', 'admissions.patient_id')
            ->select(
                'admissions.*',
                'patients.patientname',
                'patients.mobile_no',
                'patients.mobileno'
            )
            ->orderByDesc('admissions.id')
            ->paginate(20);

        return view('admissions.index', compact('admissions'));
    }

    /**
     * Show admit form for a patient.
     */
    public function create($id)
    {
        $patient = DB::table('patients')->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Optional: Check if already admitted and not discharged
        $currentAdmission = DB::table('admissions')
            ->where('patient_id', $id)
            ->whereNull('discharge_date')
            ->where('status', 1)
            ->first();

        return view('patients.admit', compact('patient', 'currentAdmission'));
    }

    /**
     * Save admit info.
     */
    public function store(Request $request, $id)
    {
        $patient = DB::table('patients')->find($id);

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient not found.');
        }

        $data = $request->validate([
            'admit_date' => 'required|date',
            'ward'       => 'required|string|max:100',
            'bed_no'     => 'required|string|max:50',
            'reason'     => 'nullable|string',
        ]);

        // Prevent double-admission without discharge (optional)
        $already = DB::table('admissions')
            ->where('patient_id', $id)
            ->whereNull('discharge_date')
            ->where('status', 1)
            ->exists();

        if ($already) {
            return redirect()
                ->back()
                ->with('error', 'This patient is already admitted and not discharged yet.');
        }

        DB::table('admissions')->insert([
            'patient_id'    => $id,
            'admit_date'    => $data['admit_date'],
            'ward'          => $data['ward'],
            'bed_no'        => $data['bed_no'],
            'reason'        => $data['reason'] ?? null,
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()
            ->route('admissions.index')
            ->with('success', 'Patient admitted successfully.');
    }

    /**
     * Discharge patient (simple).
     */
    public function discharge(Request $request, $id)
    {
        $admission = Admission::find($id);

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission record not found.');
        }

        $request->validate([
            'discharge_date' => 'required|date',
        ]);

        $admission->discharge_date = $request->discharge_date;
        $admission->status = 2; // Discharged
        $admission->save();

        return redirect()
            ->back()
            ->with('success', 'Patient discharged successfully.');
    }
}
