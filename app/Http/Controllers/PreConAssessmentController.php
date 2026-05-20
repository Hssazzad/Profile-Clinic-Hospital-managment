<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreConAssessment;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreConAssessmentController extends Controller
{
    /**
     * Show Pre-Con Assessment Form
     */
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
            
            // Latest 10 records only
            $assessmentHistory = PreConAssessment::where('patientcode', $patientcode)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(); 
        }

        return view('preconassessment.create', compact(
            'patients', 'q', 'assessmentHistory', 'patientcode', 'selectedPatient'
        ));
    }

    /**
     * Store Assessment Record
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patientcode' => ['required', 'string'],
            'weight'      => ['nullable', 'numeric', 'min:0'],
            'height'      => ['nullable', 'numeric', 'min:0'],
            'temp'        => ['nullable', 'numeric', 'min:0'],
            'bp_sys'      => ['nullable', 'integer', 'min:0'],
            'bp_dia'      => ['nullable', 'integer', 'min:0'],
            'pulse'       => ['nullable', 'integer', 'min:0'],
            'spo2'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'rr'          => ['nullable', 'integer', 'min:0'],
            'notes'       => ['nullable', 'string'],
        ]);

        try {
            // Get patient ID
            $patient = Patient::where('patientcode', $validatedData['patientcode'])->first();
            
            $validatedData['patient_id'] = $patient ? $patient->id : null;

            $record = PreConAssessment::create($validatedData);

            if ($record) {
                return redirect()
                    ->route('prescriptions.preconassessment', ['patientcode' => $validatedData['patientcode']])
                    ->with('success', 'Vitals recorded successfully for Patient: ' . $validatedData['patientcode']);
            }

            throw new \Exception("Failed to save record");

        } catch (\Exception $e) {
            Log::error('PreConAssessment Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get Assessment History via AJAX
     */
    public function getHistory($patientcode)
    {
        try {
            $history = PreConAssessment::where('patientcode', $patientcode)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function($record) {
                    return [
                        'id' => $record->id,
                        'date' => $record->created_at->format('d M Y'),
                        'time' => $record->created_at->format('h:i A'),
                        'datetime' => $record->created_at->format('d M Y, h:i A'),
                        'weight' => $record->weight ?? '--',
                        'height' => $record->height ?? '--',
                        'bmi' => $record->weight && $record->height 
                            ? number_format(($record->weight / (($record->height/100) ** 2)), 1)
                            : '--',
                        'temp' => $record->temp ?? '--',
                        'bp' => ($record->bp_sys ?? '--') . '/' . ($record->bp_dia ?? '--'),
                        'bp_sys' => $record->bp_sys ?? '--',
                        'bp_dia' => $record->bp_dia ?? '--',
                        'pulse' => $record->pulse ?? '--',
                        'spo2' => $record->spo2 ?? '--',
                        'rr' => $record->rr ?? '--',
                        'notes' => $record->notes ?? 'No notes',
                    ];
                });

            return response()->json([
                'success' => true,
                'total' => $history->count(),
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
?>