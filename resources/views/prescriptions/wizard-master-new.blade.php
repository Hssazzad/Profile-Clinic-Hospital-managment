@extends('adminlte::page')

@section('title','Prescription Wizard')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css">
<style>
    /* Modern Hospital ERP Styling */
    .wizard-container { display: flex; gap: 1.5rem; align-items: flex-start; }
    .main-content { flex: 1; min-width: 0; }
    
    /* Patient Sidebar */
    .patient-sidebar { width: 320px; position: sticky; top: 20px; z-index: 100; }
    .vital-card { border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; }
    .vital-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px; font-weight: bold; text-align: center; font-size: 1.1rem; }
    .vital-item { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .vital-item:last-child { border-bottom: none; }
    .vital-label { color: #64748b; font-size: 0.85rem; font-weight: 600; margin-bottom: 0; }
    .vital-value { color: #1e293b; font-weight: 700; font-size: 0.95rem; }
    
    /* Compact Vitals Row */
    .compact-vitals { background: #f8f9fa; padding: 10px; border-radius: 8px; margin: 10px 0; }
    .vital-compact { text-align: center; padding: 8px; }
    .vital-compact .vital-label { font-size: 0.75rem; margin-bottom: 3px; }
    .vital-compact .vital-value { font-size: 0.85rem; }
    
    /* Main Content */
    .card-main { background: #fff; border: 1px solid #e5eaf3; border-radius: .75rem; padding: 1.5rem; min-height: 500px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    
    /* Responsive */
    @media (max-width: 992px) {
        .wizard-container { flex-direction: column; }
        .patient-sidebar { width: 100%; position: relative; top: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid pt-3">

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
    @endif

    <!-- Top Navigation Tabs -->
    <div class="card mb-3">
        <div class="card-body p-0">
            <ul class="nav nav-tabs" id="prescriptionTabs">
                <li class="nav-item">
                    <a class="nav-link {{ $active==='outdoor'?'active':'' }}" id="outdoor-tab" data-toggle="tab" href="#outdoor">
                        <i class="fas fa-stethoscope mr-2"></i>Outdoor (Rx)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active==='admission'?'active':'' }}" id="admission-tab" data-toggle="tab" href="#admission">
                        <i class="fas fa-hospital mr-2"></i>Admission Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active==='pre-op'?'active':'' }}" id="preop-tab" data-toggle="tab" href="#preop">
                        <i class="fas fa-procedures mr-2"></i>Pre-Operative
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active==='post-op'?'active':'' }}" id="postop-tab" data-toggle="tab" href="#postop">
                        <i class="fas fa-band-aid mr-2"></i>Post-Operative
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active==='fresh'?'active':'' }}" id="fresh-tab" data-toggle="tab" href="#fresh">
                        <i class="fas fa-pills mr-2"></i>Fresh Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $active==='discharge'?'active':'' }}" id="discharge-tab" data-toggle="tab" href="#discharge">
                        <i class="fas fa-sign-out-alt mr-2"></i>Discharge Summary
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="prescriptionTabContent">
        <!-- Outdoor Tab -->
        <div class="tab-pane fade {{ $active==='outdoor'?'show active':'' }}" id="outdoor" role="tabpanel">
            @include('prescriptions.tabs.outdoor')
        </div>
        
        <!-- Admission Tab -->
        <div class="tab-pane fade {{ $active==='admission'?'show active':'' }}" id="admission" role="tabpanel">
            @include('prescriptions.tabs.admission')
        </div>
        
        <!-- Pre-Op Tab -->
        <div class="tab-pane fade {{ $active==='pre-op'?'show active':'' }}" id="preop" role="tabpanel">
            @include('prescriptions.tabs.preop')
        </div>
        
        <!-- Post-Op Tab -->
        <div class="tab-pane fade {{ $active==='post-op'?'show active':'' }}" id="postop" role="tabpanel">
            @include('prescriptions.tabs.postop')
        </div>
        
        <!-- Fresh Order Tab -->
        <div class="tab-pane fade {{ $active==='fresh'?'show active':'' }}" id="fresh" role="tabpanel">
            @include('prescriptions.tabs.fresh')
        </div>
        
        <!-- Discharge Tab -->
        <div class="tab-pane fade {{ $active==='discharge'?'show active':'' }}" id="discharge" role="tabpanel">
            @include('prescriptions.tabs.discharge')
        </div>
    </div>

    <div class="wizard-container mt-3">
        <!-- SIDEBAR: PATIENT INFO & COMPACT VITALS -->
        <div class="patient-sidebar">
            <div class="card vital-card">
                <div class="vital-header">
                    <i class="fas fa-user-circle mr-2"></i> Current Patient
                </div>
                <div class="card-body p-0">
                    <!-- Patient Info -->
                    <div class="vital-item">
                        <div class="vital-label">Name:</div>
                        <div class="vital-value" id="sidePatientName">{{ $patient->patientname ?? 'No Patient Selected' }}</div>
                    </div>
                    <div class="vital-item">
                        <div class="vital-label">ID:</div>
                        <div class="vital-value" id="sidePatientId">{{ $patient->patientcode ?? '---' }}</div>
                    </div>
                    <div class="vital-item">
                        <div class="vital-label">Age/Gender:</div>
                        <div class="vital-value" id="sideAgeGender">{{ $patient->age ?? '--' }} / {{ $patient->gender ?? '--' }}</div>
                    </div>
                    <div class="vital-item">
                        <div class="vital-label">Mobile:</div>
                        <div class="vital-value" id="sideMobile">{{ $patient->mobile_no ?? '--' }}</div>
                    </div>
                </div>
                
                <!-- COMPACT VITALS ROW -->
                <div class="compact-vitals">
                    <div class="row">
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">BP</div>
                            <div class="vital-value text-danger">
                                {{ $vitals->bp_systolic ?? '--' }}<small>/</small>{{ $vitals->bp_diastolic ?? '--' }}
                                <small class="d-block text-muted">mmHg</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">Pulse</div>
                            <div class="vital-value text-info">
                                {{ $vitals->pulse ?? '--' }}
                                <small class="d-block text-muted">bpm</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">SpO2</div>
                            <div class="vital-value text-success">
                                {{ $vitals->spo2 ?? '--' }}%
                                <small class="d-block text-muted">Oxygen</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">Temp</div>
                            <div class="vital-value text-warning">
                                {{ $vitals->temperature ?? '--' }}°C
                                <small class="d-block text-muted">Body</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">Resp</div>
                            <div class="vital-value text-secondary">
                                {{ $vitals->respiration ?? '--' }}
                                <small class="d-block text-muted">rpm</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 vital-compact">
                            <div class="vital-label">BMI</div>
                            <div class="vital-value text-primary">
                                @php
                                    $bmi = null;
                                    if(isset($vitals->weight) && isset($vitals->height) && $vitals->height > 0){
                                        $heightInMeters = $vitals->height / 100;
                                        $bmi = round($vitals->weight / ($heightInMeters * $heightInMeters), 1);
                                    }
                                @endphp
                                {{ $bmi ?? '--' }}
                                <small class="d-block text-muted">Index</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="vital-item bg-light">
                    <div class="vital-label">Weight/Height</div>
                    <div class="vital-value">
                        {{ $vitals->weight ?? '--' }} kg / {{ $vitals->height ?? '--' }} cm
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="main-content">
            <div class="card-main shadow-sm">
                @include('prescriptions.tabs.'.$active)
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Tab switching
    $('#prescriptionTabs a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
</script>
@endpush
