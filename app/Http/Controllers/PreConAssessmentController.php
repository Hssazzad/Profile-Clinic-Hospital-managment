<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreConAssessment;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreConAssessmentController extends Controller
{
    public function create(Request $request)
    {
        $q = $request->get('q'); 
        $patientcode = $request->get('patientcode');

        $patients = Patient::query()
            ->when($q, function ($query) use ($q) {
                $query->where('patientname', 'like', "%{$q}%")
                      ->orWhere('patientcode', 'like', "%{$q}%")
                      ->orWhere('mobile_no', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $selectedPatient = null;
        $assessmentHistory = collect();

        if ($patientcode) {
            $selectedPatient = Patient::where('patientcode', $patientcode)->first();
            $assessmentHistory = PreConAssessment::where('patientcode', $patientcode)
                ->orderByDesc('created_at')
                ->get(); 
        }

        return view('preconassessment.create', compact(
            'patients', 'q', 'assessmentHistory', 'patientcode', 'selectedPatient'
        ));
    }

    public function store(Request $request)
    {
        // ১. ভ্যালিডেশন
        $validatedData = $request->validate([
            'patientcode' => ['required', 'string'],
            'weight'      => ['nullable', 'numeric'],
            'height'      => ['nullable', 'numeric'],
            'temp'        => ['nullable', 'numeric'],
            'bp_sys'      => ['nullable', 'numeric'],
            'bp_dia'      => ['nullable', 'numeric'],
            'pulse'       => ['nullable', 'numeric'],
            'spo2'        => ['nullable', 'numeric'],
            'rr'          => ['nullable', 'numeric'],
            'notes'       => ['nullable', 'string'],
        ]);

        try {
            /** * ২. সমাধান: 
             * আপনার DB লগে দেখা যাচ্ছে 'code' এবং 'value' কলামে ডিফল্ট ভ্যালু নেই।
             * তাই আমরা অ্যারেতে এই দুটি কি (key) যোগ করে দিচ্ছি।
             */
            $dataToSave = array_merge($validatedData, [
                'code'  => 'VITAL_SHEET', // ডাটাবেসকে সন্তুষ্ট করার জন্য
                'value' => 'RECORDED'     // ডাটাবেসকে সন্তুষ্ট করার জন্য
            ]);

            $record = PreConAssessment::create($dataToSave);

            if ($record) {
                return redirect()
                    ->route('prescriptions.preconassessment', ['patientcode' => $request->patientcode])
                    ->with('success', 'Full assessment record saved successfully for Patient ID: ' . $request->patientcode);
            }

            throw new \Exception("Execution failed unexpectedly.");

        } catch (\Exception $e) {
            Log::error('PreConAssessment Save Error: ' . $e->getMessage());
            
            // ব্যবহারকারীকে আসল কারণ দেখানো
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
}