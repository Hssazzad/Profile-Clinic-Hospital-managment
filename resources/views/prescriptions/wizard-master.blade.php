{{-- resources/views/prescriptions/wizard-master.blade.php --}}
@extends('adminlte::page')

@section('title','Prescription Wizard') 

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css">
<style>
    /* Wizard Styling */
    .tabs { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
    .tab-btn { padding: .7rem 1.2rem; border: 1px solid #dbe3f0; background: #fff; border-radius: .5rem; text-decoration: none; color: #4b5563; font-weight: 600; transition: 0.3s; }
    .tab-btn.active { background: #0ea5e9; color: #fff; border-color: #0ea5e9; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.3); }
    .tab-btn:hover:not(.active) { background: #f1f5f9; }

    /* Layout Adjustments */
    .wizard-container { display: flex; gap: 1.5rem; align-items: flex-start; }
    .main-content { flex: 1; min-width: 0; }
    
    /* Sticky Patient Info Sidebar */
    .patient-sidebar { width: 300px; position: sticky; top: 20px; z-index: 100; }
    .vital-card { border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; }
    .vital-header { background: #1e293b; color: #fff; padding: 12px; font-weight: bold; text-align: center; }
    .vital-item { padding: 10px 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; }
    .vital-item:last-child { border-bottom: none; }
    .vital-label { color: #64748b; font-size: 0.85rem; font-weight: 600; }
    .vital-value { color: #1e293b; font-weight: 700; }
    
    .badge-vital { padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; }
    .bg-critical { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .bg-normal { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

    .card-main { background: #fff; border: 1px solid #e5eaf3; border-radius: .75rem; padding: 1.5rem; min-height: 500px; }
    
    @media (max-width: 992px) {
        .wizard-container { flex-direction: column; }
        .patient-sidebar { width: 100%; position: relative; top: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid pt-3">
    
    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <b>Please check errors:</b>
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    @php
        $active = $tab ?? 'patients';
        $patientId = $patientId ?? request('patient');
        
        // Navigation Helper
        $nav = fn($t) => route('rx.wizard', [
            'tab'     => $t,
            'id'      => $pid ?? request('id'),
            'patient' => $patientId
        ]);

        // Mocking Vitals - Replace these variables with actual DB data if available
        // Example: $weight = $selectedPatient->latestVitals->weight ?? '--';
    @endphp

    {{-- Step Indicator --}}
    <div class="tabs" role="tablist">
        <a class="tab-btn {{ $active==='patients'?'active':'' }}" href="{{ $nav('patients') }}">1. Patient Selection</a>
        <a class="tab-btn {{ $active==='complain'?'active':'' }}" href="{{ $nav('complain') }}">2. Complaints</a>
        <a class="tab-btn {{ $active==='investigations'?'active':'' }}" href="{{ $nav('investigations') }}">3. Tests</a>
        <a class="tab-btn {{ $active==='diagnosis'?'active':'' }}" href="{{ $nav('diagnosis') }}">4. Diagnosis</a>
        <a class="tab-btn {{ $active==='medicine'?'active':'' }}" href="{{ $nav('medicine') }}">5. Medicine</a>
        <a class="tab-btn {{ $active==='doctor'?'active':'' }}" href="{{ $nav('doctor') }}">6. Doctor</a>
        <a class="tab-btn {{ $active==='preview'?'active':'' }}" href="{{ $nav('preview') }}">7. Preview</a>
        <a class="tab-btn {{ $active==='finish'?'active':'' }}" href="{{ $nav('finish') }}">8. Finish</a>
    </div>
	
	

    <div class="wizard-container">
        {{-- MAIN CONTENT AREA --}}
        <div class="main-content">
            <div class="card-main shadow-sm">
                @include('prescriptions.tabs.'.$active)
            </div>
        </div>

       {{-- SIDEBAR: PATIENT INFO & VITALS --}}
<div class="patient-sidebar">
    <div class="card vital-card">
        <div class="vital-header">
            <i class="fas fa-user-circle mr-2"></i> Current Patient
        </div>
        <div class="card-body p-0">
            <div class="vital-item bg-light text-center py-3">
                <div class="w-100">
                    <h6 class="mb-0 font-weight-bold text-primary" id="sidePatientName">
                        {{-- পেশেন্টের নাম চেক করা হচ্ছে --}}
                        @if(isset($vitals) && $vitals->patientcode)
                            {{ $vitals->patientcode }}
                        @elseif($patientId)
                            ID: {{ $patientId }}
                        @else
                            No Patient Selected
                        @endif
                    </h6>
                    <small class="text-muted">
                        Last Assessment: 
                        {{ isset($vitals->created_at) ? $vitals->created_at : 'N/A' }}
                    </small>
                </div>
            </div>
            
            {{-- BP Section --}}
            <div class="vital-item">
                <span class="vital-label">BP (Systolic/Dia)</span>
                @php
                    $isBpHigh = isset($vitals->bp_sys) && ($vitals->bp_sys > 140 || $vitals->bp_sys < 90);
                @endphp
                <span class="vital-value {{ $isBpHigh ? 'text-danger' : '' }}" id="sideBP">
                    {{ $vitals->bp_sys ?? '--' }}/{{ $vitals->bp_dia ?? '--' }}
                </span>
            </div>

            {{-- SpO2 Section --}}
            <div class="vital-item">
                <span class="vital-label">SpO2 (Oxygen)</span>
                <span class="vital-value {{ (isset($vitals->spo2) && $vitals->spo2 < 95) ? 'text-danger' : 'text-info' }}" id="sideSpO2">
                    {{ isset($vitals->spo2) ? $vitals->spo2.'%' : '--' }}
                </span>
            </div>

            {{-- Weight / Height Section --}}
            <div class="vital-item">
                <span class="vital-label">Weight / Height</span>
                <span class="vital-value" id="sideWH">
                    {{ $vitals->weight ?? '--' }} kg / {{ $vitals->height ?? '--' }} cm
                </span>
            </div>

            {{-- BMI Calculation --}}
            @php
                $bmi = null;
                $bmiClass = 'bg-normal';
                if(isset($vitals->weight) && isset($vitals->height) && $vitals->height > 0){
                    $heightInMeters = $vitals->height / 100;
                    $bmi = round($vitals->weight / ($heightInMeters * $heightInMeters), 1);
                    if($bmi >= 25) $bmiClass = 'bg-critical';
                }
            @endphp

            <div class="vital-item">
                <span class="vital-label">BMI Status</span>
                <span id="sideBMI" class="badge-vital {{ $bmiClass }}">
                    @if($bmi)
                        {{ $bmi }} ({{ $bmi < 18.5 ? 'Underweight' : ($bmi < 25 ? 'Normal' : 'Overweight') }})
                    @else
                        N/A
                    @endif
                </span>
            </div>

            {{-- Temperature Section --}}
            <div class="vital-item">
                <span class="vital-label">Temperature</span>
                <span class="vital-value" id="sideTemp">
                    {{ isset($vitals->temp) ? $vitals->temp.'°F' : '--' }}
                </span>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                @if(isset($vitals))
                    Vitals loaded from pre-assessment
                @else
                    Please select a patient with assessment
                @endif
            </small>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Cache Buster for PDF
    document.addEventListener('click', function(e){
        const a = e.target.closest('a.tab-btn');
        if(!a) return;
        if(a.getAttribute('href').includes('tab=preview')){
            setTimeout(() => {
                const frame = document.getElementById('rxPdfFrame');
                if(frame){
                    frame.src = frame.src.split('?')[0] + '?t=' + Date.now();
                }
            }, 200);
        }
    });

    // logic: Pre-fill Sidebar from Local Storage or AJAX
    // If you have vital data in the session or PHP, you can echo it here
    function updateSidebarVitals() {
        // Example: Static update. Real app should fetch via AJAX based on Patient ID
        // $('#sideBP').text('145/90').addClass('text-danger'); 
    }
    updateSidebarVitals();
});
</script>
@endpush