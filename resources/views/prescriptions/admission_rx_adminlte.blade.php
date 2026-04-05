@extends('adminlte::page')

@section('title', $prescriptionType == 'discharge' ? 'Discharge Prescription' : ucfirst($prescriptionType) . ' Prescription')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-{{ getPrescriptionColor($prescriptionType) }}">
                        <i class="fas fa-{{ getPrescriptionIcon($prescriptionType) }}"></i> 
                        {{ $prescriptionType == 'discharge' ? 'Discharge Prescription' : ucfirst($prescriptionType) . ' Prescription' }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('prescriptions.dashboard') }}">Prescription Center</a></li>
                        <li class="breadcrumb-item active">{{ ucfirst($prescriptionType) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
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

    <!-- Patient Selection Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-injured mr-2"></i>Patient Information
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('prescriptions.admission.rx', ['type' => $prescriptionType]) }}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Select Admitted Patient</label>
                            <select name="patient_id" class="form-control select2" required>
                                <option value="">-- Select Patient --</option>
                                @if(isset($admittedPatients))
                                    @foreach($admittedPatients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->patientname }} ({{ $patient->patientcode }}) - 
                                            Bed: {{ $patient->bed_no ?? 'N/A' }} - 
                                            Admitted: {{ \Carbon\Carbon::parse($patient->admit_date)->format('d M Y') }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-{{ getPrescriptionColor($prescriptionType) }}">
                                    <i class="fas fa-search"></i> Load Patient
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($selectedPatient))
        <!-- Patient Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Current Patient Details
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Name</span>
                                <span class="info-box-number">{{ $selectedPatient->patientname }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary elevation-1">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Code</span>
                                <span class="info-box-number">{{ $selectedPatient->patientcode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-bed"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ward/Bed</span>
                                <span class="info-box-number">{{ $selectedPatient->ward ?? 'N/A' }} / {{ $selectedPatient->bed_no ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1">
                                <i class="fas fa-user-md"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Consultant</span>
                                <span class="info-box-number">{{ $selectedPatient->consultant ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted">Age/Gender:</small> {{ $selectedPatient->age ?? 'N/A' }} / {{ $selectedPatient->gender ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Mobile:</small> {{ $selectedPatient->mobile_no }}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Admission Date:</small> {{ \Carbon\Carbon::parse($selectedPatient->admit_date)->format('d M Y') }}
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Status:</small> 
                        <span class="badge bg-{{ $selectedPatient->status == 'admitted' ? 'success' : 'warning' }}">
                            {{ ucfirst($selectedPatient->status ?? 'Unknown') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Form -->
        <form method="POST" action="{{ route('prescriptions.admission.rx.store', ['type' => $prescriptionType, 'patient_id' => $selectedPatient->id]) }}">
            @csrf
            
            <!-- Prescription Header -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-medical mr-2"></i> 
                        {{ $prescriptionType == 'discharge' ? 'Discharge' : ucfirst($prescriptionType) }} Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prescription Date</label>
                                <input type="date" name="prescription_date" class="form-control" 
                                       value="{{ old('prescription_date', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Doctor Name</label>
                                <input type="text" name="doctor_name" class="form-control" 
                                       value="{{ old('doctor_name', auth()->user()->name) }}" required>
                            </div>
                        </div>
                    </div>

                    @if($prescriptionType == 'discharge')
                        <!-- Discharge Specific Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discharge Date</label>
                                    <input type="date" name="discharge_date" class="form-control" 
                                           value="{{ old('discharge_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discharge Type</label>
                                    <select name="discharge_type" class="form-control" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="normal">Normal Discharge</option>
                                        <option value="lama">Left Against Medical Advice</option>
                                        <option value="referred">Referred to Other Hospital</option>
                                        <option value="death">Expired</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Clinical Sections -->
            <div class="row">
                <!-- Diagnosis -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-stethoscope mr-2"></i>Diagnosis
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Primary Diagnosis</label>
                                <textarea name="primary_diagnosis" rows="3" class="form-control" 
                                          placeholder="Enter primary diagnosis">{{ old('primary_diagnosis') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Secondary Diagnosis</label>
                                <textarea name="secondary_diagnosis" rows="2" class="form-control" 
                                          placeholder="Enter secondary diagnosis if any">{{ old('secondary_diagnosis') }}</textarea>
                            </div>
                            @if($prescriptionType == 'discharge')
                                <div class="form-group">
                                    <label>Final Diagnosis</label>
                                    <textarea name="final_diagnosis" rows="3" class="form-control" 
                                              placeholder="Enter final diagnosis at discharge">{{ old('final_diagnosis') }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Investigations -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-microscope mr-2"></i>Investigations
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Lab Investigations</label>
                                <textarea name="lab_investigations" rows="3" class="form-control" 
                                          placeholder="CBC, RFT, LFT, etc.">{{ old('lab_investigations') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Radiology</label>
                                <textarea name="radiology" rows="2" class="form-control" 
                                          placeholder="X-ray, USG, CT scan, etc.">{{ old('radiology') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Other Investigations</label>
                                <textarea name="other_investigations" rows="2" class="form-control" 
                                          placeholder="Any other investigations">{{ old('other_investigations') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treatment Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-pills mr-2"></i>Treatment & Medications
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Medications (One per line)</label>
                        <textarea name="medications" rows="6" class="form-control" 
                                  placeholder="Tab. Napa 500mg - 1+0+1 - 5 days&#10;Cap. Omeprazole 20mg - 0+1+0 - 7 days&#10;Syr. Paracetamol 250mg/5ml - 5ml SOS">{{ old('medications') }}</textarea>
                        <small class="text-muted">Format: Medicine name - Dose - Duration</small>
                    </div>

                    @if($prescriptionType == 'pre-op')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pre-operative Instructions</label>
                                    <textarea name="pre_op_instructions" rows="3" class="form-control" 
                                              placeholder="NPO from midnight, Skin preparation, etc.">{{ old('pre_op_instructions') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Anesthesia Clearance</label>
                                    <select name="anesthesia_clearance" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="fit">Fit for Surgery</option>
                                        <option value="high-risk">High Risk</option>
                                        <option value="optimized">Optimized Needed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($prescriptionType == 'post-op')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Post-operative Instructions</label>
                                    <textarea name="post_op_instructions" rows="3" class="form-control" 
                                              placeholder="Wound care, Mobilization, Diet, etc.">{{ old('post_op_instructions') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Follow-up</label>
                                    <textarea name="follow_up" rows="2" class="form-control" 
                                              placeholder="Follow-up after 7 days with reports">{{ old('follow_up') }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($prescriptionType == 'discharge')
                        <div class="form-group">
                            <label>Discharge Advice</label>
                            <textarea name="discharge_advice" rows="4" class="form-control" 
                                      placeholder="Complete course of medications, Diet restrictions, Activity restrictions, When to seek immediate medical attention">{{ old('discharge_advice') }}</textarea>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-notes-medical mr-2"></i>Additional Notes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Doctor's Notes</label>
                        <textarea name="doctor_notes" rows="3" class="form-control" 
                                  placeholder="Any additional instructions or notes">{{ old('doctor_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-{{ getPrescriptionColor($prescriptionType) }} btn-lg">
                                <i class="fas fa-save"></i> Save Prescription
                            </button>
                            <a href="{{ route('prescriptions.dashboard') }}" class="btn btn-default btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary btn-lg" onclick="previewPrescription()">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button type="button" class="btn btn-info btn-lg" onclick="printPrescription()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

@push('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});

function previewPrescription() {
    // Open preview in new window
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    window.open('{{ route('prescriptions.admission.rx.preview', ['type' => $prescriptionType]) }}?' + params.toString(), 
                '_blank', 'width=800,height=600');
}

function printPrescription() {
    window.print();
}
</script>
@endpush

@php
function getPrescriptionIcon($type) {
    $icons = [
        'pre-op' => 'clipboard-check',
        'post-op' => 'band-aid',
        'fresh' => 'pills',
        'discharge' => 'sign-out-alt'
    ];
    return $icons[$type] ?? 'file-medical';
}

function getPrescriptionColor($type) {
    $colors = [
        'pre-op' => 'warning',
        'post-op' => 'info',
        'fresh' => 'secondary',
        'discharge' => 'danger'
    ];
    return $colors[$type] ?? 'primary';
}
@endphp

@section('css')
<style>
.info-box {
    cursor: pointer;
    transition: all 0.3s ease;
}
.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.card {
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}
.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.form-group {
    margin-bottom: 1rem;
}
.btn {
    margin-right: 0.5rem;
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
