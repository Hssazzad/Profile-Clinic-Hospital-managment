@extends('adminlte::page')

@section('title', 'Prescription Center - World Class')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-primary">
                        <i class="fas fa-file-medical-alt"></i> Prescription Center
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Prescription</li>
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

    <!-- Patient Selection -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-injured mr-2"></i>Patient Selection
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="patientSearch">Search Patient</label>
                        <div class="input-group">
                            <input type="text" id="patientSearch" class="form-control" placeholder="Enter patient name or code...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchPatientBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="patientSelect">Select Patient</label>
                        <select id="patientSelect" class="form-control select2">
                            <option value="">-- Select Patient --</option>
                            @if(isset($patients))
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" data-code="{{ $patient->patientcode }}" data-name="{{ $patient->patientname }}">
                                        {{ $patient->patientname }} ({{ $patient->patientcode }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" id="loadPatientBtn" class="btn btn-primary btn-block" disabled>
                            <i class="fas fa-user-check"></i> Load Patient
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Info Display -->
    <div id="patientInfoCard" class="card" style="display: none;">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-user mr-2"></i>Patient Information
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
                            <span class="info-box-number" id="patientName">-</span>
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
                            <span class="info-box-number" id="patientCode">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-birthday-cake"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Age/Gender</span>
                            <span class="info-box-number" id="patientAgeGender">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-phone"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Mobile</span>
                            <span class="info-box-number" id="patientMobile">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Prescription Form -->
    <form id="prescriptionForm" method="POST" action="{{ route('prescriptions.worldclass.store') }}">
        @csrf
        <input type="hidden" id="patientId" name="patient_id" value="">
        <input type="hidden" id="prescriptionType" name="prescription_type" value="outdoor">

        <!-- Tabbed Navigation -->
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs nav-tabs-primary" id="prescriptionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="outdoor-tab" data-toggle="tab" href="#outdoor" role="tab" data-type="outdoor">
                            <i class="fas fa-stethoscope mr-2"></i>Outdoor (Rx)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="admission-tab" data-toggle="tab" href="#admission" role="tab" data-type="admission">
                            <i class="fas fa-hospital mr-2"></i>Admission Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="preop-tab" data-toggle="tab" href="#preop" role="tab" data-type="pre-op">
                            <i class="fas fa-procedures mr-2"></i>Pre-Operative
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="postop-tab" data-toggle="tab" href="#postop" role="tab" data-type="post-op">
                            <i class="fas fa-band-aid mr-2"></i>Post-Operative
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fresh-tab" data-toggle="tab" href="#fresh" role="tab" data-type="fresh">
                            <i class="fas fa-pills mr-2"></i>Fresh Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="discharge-tab" data-toggle="tab" href="#discharge" role="tab" data-type="discharge">
                            <i class="fas fa-sign-out-alt mr-2"></i>Discharge Summary
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="prescriptionTabContent">
                    <!-- Outdoor Tab -->
                    <div class="tab-pane fade show active" id="outdoor" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="outdoorDiagnosis">Diagnosis</label>
                                    <textarea name="outdoor_diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admission Orders Tab -->
                    <div class="tab-pane fade" id="admission" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="admissionNotes">Admission Notes</label>
                                    <textarea name="admission_notes" class="form-control" rows="3" placeholder="Enter admission notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pre-Operative Tab -->
                    <div class="tab-pane fade" id="preop" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preopDiagnosis">Pre-Op Diagnosis</label>
                                    <textarea name="preop_diagnosis" class="form-control" rows="3" placeholder="Enter pre-operative diagnosis..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preopInstructions">Pre-Op Instructions</label>
                                    <textarea name="preop_instructions" class="form-control" rows="3" placeholder="NPO from midnight, Skin preparation, etc..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Post-Operative Tab -->
                    <div class="tab-pane fade" id="postop" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postopDiagnosis">Post-Op Diagnosis</label>
                                    <textarea name="postop_diagnosis" class="form-control" rows="3" placeholder="Enter post-operative diagnosis..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postopInstructions">Post-Op Instructions</label>
                                    <textarea name="postop_instructions" class="form-control" rows="3" placeholder="Wound care, Mobilization, Diet, etc..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fresh Orders Tab -->
                    <div class="tab-pane fade" id="fresh" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="freshNotes">Fresh Order Notes</label>
                                    <textarea name="fresh_notes" class="form-control" rows="3" placeholder="Enter fresh order notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discharge Summary Tab -->
                    <div class="tab-pane fade" id="discharge" role="tabpanel">
                        <div id="dischargeWarning" class="alert alert-warning" style="display: none;">
                            <h5><i class="fas fa-exclamation-triangle"></i> Account Clearance Required</h5>
                            <strong>Account clearance pending. Discharge locked.</strong><br>
                            Please ensure all account balances are cleared before proceeding with discharge.
                        </div>

                        <div id="dischargeContent">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dischargeDiagnosis">Final Diagnosis</label>
                                        <textarea name="discharge_diagnosis" class="form-control" rows="3" placeholder="Enter final diagnosis..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dischargeAdvice">Discharge Advice</label>
                                        <textarea name="discharge_advice" class="form-control" rows="3" placeholder="Enter discharge advice..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vitals Section -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">
                            <i class="fas fa-heartbeat mr-2"></i>Patient Vitals
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="bpSystolic">BP (Systolic)</label>
                                    <div class="input-group">
                                        <input type="number" name="bp_systolic" class="form-control" placeholder="120" min="60" max="200">
                                        <div class="input-group-append">
                                            <span class="input-group-text">mmHg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="bpDiastolic">BP (Diastolic)</label>
                                    <div class="input-group">
                                        <input type="number" name="bp_diastolic" class="form-control" placeholder="80" min="40" max="120">
                                        <div class="input-group-append">
                                            <span class="input-group-text">mmHg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="pulse">Pulse</label>
                                    <div class="input-group">
                                        <input type="number" name="pulse" class="form-control" placeholder="72" min="40" max="200">
                                        <div class="input-group-append">
                                            <span class="input-group-text">bpm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="spo2">SpO2</label>
                                    <div class="input-group">
                                        <input type="number" name="spo2" class="form-control" placeholder="98" min="70" max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="temperature">Temperature</label>
                                    <div class="input-group">
                                        <input type="number" name="temperature" class="form-control" placeholder="37.0" min="35" max="42" step="0.1">
                                        <div class="input-group-append">
                                            <span class="input-group-text">°C</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="respiration">Respiration</label>
                                    <div class="input-group">
                                        <input type="number" name="respiration" class="form-control" placeholder="16" min="8" max="40">
                                        <div class="input-group-append">
                                            <span class="input-group-text">rpm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Medicine Builder -->
                <div class="card mt-3">
                    <div class="card-header bg-success">
                        <h3 class="card-title">
                            <i class="fas fa-pills mr-2"></i>Medications
                            <button type="button" class="btn btn-sm btn-light float-right" id="addMedicineBtn">
                                <i class="fas fa-plus"></i> Add Medicine
                            </button>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="medicinesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Medicine</th>
                                        <th width="25%">Dosage</th>
                                        <th width="20%">Duration</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="medicinesBody">
                                    <tr id="medicineRow1">
                                        <td>1</td>
                                        <td>
                                            <select name="medicines[0][medicine]" class="form-control medicine-select">
                                                <option value="">-- Select Medicine --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="dosage-buttons">
                                                <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+0+0">1+0+0</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="0+1+0">0+1+0</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="0+0+1">0+0+1</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+0+1">1+0+1</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+1+1">1+1+1</button>
                                                <input type="text" name="medicines[0][dosage]" class="form-control mt-2 dosage-input" placeholder="Custom dosage...">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="medicines[0][duration]" class="form-control" placeholder="e.g., 5 days, 1 week">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-medicine" data-row="1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card mt-3">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">
                            <i class="fas fa-notes-medical mr-2"></i>Additional Notes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="doctorNotes">Doctor's Notes</label>
                            <textarea name="doctor_notes" class="form-control" rows="4" placeholder="Enter any additional notes or instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Prescription
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-outline-info btn-lg" onclick="previewPrescription()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="button" class="btn btn-outline-success btn-lg" onclick="generatePDF()">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    let medicineRowCount = 1;
    let currentPatient = null;

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Patient selection
    $('#patientSelect').on('change', function() {
        const patientId = $(this).val();
        $('#loadPatientBtn').prop('disabled', !patientId);
    });

    $('#loadPatientBtn').on('click', function() {
        const patientId = $('#patientSelect').val();
        if (patientId) {
            loadPatientData(patientId);
        }
    });

    // Tab switching
    $('#prescriptionTabs a').on('click', function(e) {
        e.preventDefault();
        const tabType = $(this).data('type');

        // Check discharge lock
        if (tabType === 'discharge') {
            checkDischargeLock();
        } else {
            $(this).tab('show');
            $('#prescriptionType').val(tabType);
        }
    });

    // Medicine management
    $('#addMedicineBtn').on('click', function() {
        addMedicineRow();
    });

    $(document).on('click', '.remove-medicine', function() {
        const rowId = $(this).data('row');
        $(`#medicineRow${rowId}`).remove();
        renumberMedicineRows();
    });

    // Dosage quick buttons
    $(document).on('click', '.dosage-btn', function() {
        const dosage = $(this).data('dosage');
        const row = $(this).closest('tr');
        row.find('.dosage-input').val(dosage);
    });

    // Load medicines for Select2
    loadMedicines();

    function loadPatientData(patientId) {
        // Make AJAX call to get patient data
        $.get(`{{ route('patients.worldclass.data', ':patientId') }}`.replace(':patientId', patientId))
            .done(function(response) {
                if (response.patient) {
                    currentPatient = response.patient;
                    $('#patientId').val(patientId);
                    $('#patientName').text(response.patient.patientname);
                    $('#patientCode').text(response.patient.patientcode);
                    $('#patientAgeGender').text(`${response.patient.age || 'N/A'} / ${response.patient.gender || 'N/A'}`);
                    $('#patientMobile').text(response.patient.mobile_no || 'N/A');
                    $('#patientInfoCard').show();

                    // Check discharge lock
                    checkDischargeLock();
                }
            })
            .fail(function() {
                alert('Error loading patient data');
            });
    }

    function checkDischargeLock() {
        if (!currentPatient) return;

        // Simulate account clearance check - replace with actual API call
        const accountClearance = currentPatient.account_clearance || 'pending';

        if (accountClearance !== 'paid') {
            $('#dischargeWarning').show();
            $('#dischargeContent').hide();
            $('#discharge-tab').addClass('disabled').attr('data-toggle', '');
        } else {
            $('#dischargeWarning').hide();
            $('#dischargeContent').show();
            $('#discharge-tab').removeClass('disabled').attr('data-toggle', 'tab');
        }
    }

    function addMedicineRow() {
        medicineRowCount++;
        const newRow = `
            <tr id="medicineRow${medicineRowCount}">
                <td>${medicineRowCount}</td>
                <td>
                    <select name="medicines[${medicineRowCount-1}][medicine]" class="form-control medicine-select">
                        <option value="">-- Select Medicine --</option>
                    </select>
                </td>
                <td>
                    <div class="dosage-buttons">
                        <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+0+0">1+0+0</button>
                        <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="0+1+0">0+1+0</button>
                        <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="0+0+1">0+0+1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+0+1">1+0+1</button>
                        <button type="button" class="btn btn-sm btn-outline-primary dosage-btn" data-dosage="1+1+1">1+1+1</button>
                        <input type="text" name="medicines[${medicineRowCount-1}][dosage]" class="form-control mt-2 dosage-input" placeholder="Custom dosage...">
                    </div>
                </td>
                <td>
                    <input type="text" name="medicines[${medicineRowCount-1}][duration]" class="form-control" placeholder="e.g., 5 days, 1 week">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-medicine" data-row="${medicineRowCount}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#medicinesBody').append(newRow);

        // Initialize Select2 for new row
        $(`#medicineRow${medicineRowCount} .medicine-select`).select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Load medicines for new row
        loadMedicinesForRow($(`#medicineRow${medicineRowCount} .medicine-select`));
    }

    function renumberMedicineRows() {
        $('#medicinesBody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
            $(this).attr('id', `medicineRow${index + 1}`);
            $(this).find('.remove-medicine').data('row', index + 1);
        });
        medicineRowCount = $('#medicinesBody tr').length;
    }

    function loadMedicines() {
        $('.medicine-select').each(function() {
            loadMedicinesForRow($(this));
        });
    }

    function loadMedicinesForRow(selectElement) {
        // Make AJAX call to get medicines for specific row
        $.get('{{ route('medicines.worldclass.search') }}')
            .done(function(medicines) {
                medicines.forEach(medicine => {
                    selectElement.append(`<option value="${medicine.id}">${medicine.name}</option>`);
                });
            })
            .fail(function() {
                console.error('Error loading medicines');
            });
    }

    function previewPrescription() {
        // Implement preview functionality
        alert('Preview functionality would be implemented here');
    }

    function generatePDF() {
        // Implement PDF generation
        alert('PDF generation would be implemented here');
    }
});
</script>
@endpush

@section('css')
<style>
.dosage-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.dosage-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.dosage-input {
    width: 100%;
}

.nav-tabs .nav-link.disabled {
    color: #6c757d;
    pointer-events: none;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

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
