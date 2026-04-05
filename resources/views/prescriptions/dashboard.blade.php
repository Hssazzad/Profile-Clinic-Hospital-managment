@extends('adminlte::page')

@section('title', 'Prescription Center')

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
                        <li class="breadcrumb-item active">Prescription Center</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- AdminLTE Info Box Row -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-calendar-day"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Prescriptions</span>
                    <span class="info-box-number">{{ $todayPrescriptions ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-clinic-medical"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Outdoor Chamber</span>
                    <span class="info-box-number">{{ $outdoorPrescriptions ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-hospital"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Indoor Patients</span>
                    <span class="info-box-number">{{ $indoorPrescriptions ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Discharges</span>
                    <span class="info-box-number">{{ $pendingDischarges ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Prescription Menu -->
    <div class="row">
        <!-- Outdoor Chamber Section -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-stethoscope mr-2"></i>Outdoor Chamber
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('prescriptions.create') }}" class="btn btn-app bg-primary">
                                <i class="fas fa-plus"></i> New Prescription
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('prescriptions.search') }}" class="btn btn-app bg-info">
                                <i class="fas fa-search"></i> Search Prescriptions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indoor Patients Section -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hospital-alt mr-2"></i>Indoor Patients
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admission.list') }}" class="btn btn-app bg-success">
                                <i class="fas fa-bed"></i> Admitted Patients
                            </a>
                        </div>
                        <div class="col-6">
                            <button onclick="showOperationMenu()" class="btn btn-app bg-warning">
                                <i class="fas fa-procedures"></i> Operations
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operation Prescription Types -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md mr-2"></i>Operation Prescriptions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('prescriptions.admission.rx', ['type' => 'pre-op']) }}" class="btn btn-app bg-primary">
                                <i class="fas fa-clipboard-check"></i> Pre-Operative Order
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('prescriptions.admission.rx', ['type' => 'post-op']) }}" class="btn btn-app bg-success">
                                <i class="fas fa-band-aid"></i> Post-Operative Order
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('prescriptions.admission.rx', ['type' => 'fresh']) }}" class="btn btn-app bg-info">
                                <i class="fas fa-pills"></i> Fresh Order
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <button onclick="showDischargeModal()" class="btn btn-app bg-danger">
                                <i class="fas fa-sign-out-alt"></i> Discharge
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>Recent Prescriptions
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentPrescriptions) && $recentPrescriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped projects">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">#</th>
                                        <th>Prescription No</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Doctor</th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPrescriptions as $index => $prescription)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a>
                                                    <strong>{{ $prescription->prescription_no ?? 'RX-' . $prescription->id }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    Created {{ $prescription->created_at->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td>{{ $prescription->patient->patientname ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ getPrescriptionTypeColor($prescription->type ?? 'outdoor') }}">
                                                    {{ ucfirst($prescription->type ?? 'Outdoor') }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($prescription->created_at)->format('d M Y H:i') }}</td>
                                            <td>{{ $prescription->doctor_name ?? 'N/A' }}</td>
                                            <td class="project-actions text-right">
                                                <a class="btn btn-primary btn-sm" href="{{ route('prescriptions.show', $prescription->id) }}">
                                                    <i class="fas fa-folder"></i> View
                                                </a>
                                                <a class="btn btn-info btn-sm" href="{{ route('prescriptions.pdf', $prescription->id) }}" target="_blank">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent prescriptions found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Clearance Modal -->
<div class="modal fade" id="dischargeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Patient Discharge
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <strong>Important:</strong> Patient can only be discharged after account clearance. Please check the account status first.
                </div>

                <div class="form-group">
                    <label>Select Patient</label>
                    <select id="dischargePatient" class="form-control select2">
                        <option value="">-- Select Admitted Patient --</option>
                        @if(isset($admittedPatients))
                            @foreach($admittedPatients as $patient)
                                <option value="{{ $patient->id }}" data-balance="{{ $patient->account_balance ?? 0 }}">
                                    {{ $patient->patientname }} ({{ $patient->patientcode }}) - Bed: {{ $patient->bed_no ?? 'N/A' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div id="accountStatus" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Bill</span>
                                            <span class="info-box-number" id="totalBill">৳0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fas fa-money-check-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Paid Amount</span>
                                            <span class="info-box-number" id="paidAmount">৳0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger elevation-1">
                                            <i class="fas fa-exclamation"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Due Amount</span>
                                            <span class="info-box-number text-danger" id="dueAmount">৳0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-{{ getPrescriptionTypeColor('clearance') }} elevation-1">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Status</span>
                                            <span class="info-box-number" id="clearanceStatus">Checking...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <div>
                    <button type="button" id="checkAccountBtn" class="btn btn-warning" onclick="checkAccountStatus()">
                        <i class="fas fa-calculator"></i> Check Account
                    </button>
                    <button type="button" id="proceedDischargeBtn" class="btn btn-success" onclick="proceedToDischarge()" disabled>
                        <i class="fas fa-check-circle"></i> Proceed to Discharge
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function toggleQuickStats() {
    const statsDiv = document.getElementById('quickStats');
    statsDiv.style.display = statsDiv.style.display === 'none' ? 'flex' : 'none';
}

function showOperationMenu() {
    // Scroll to operation section
    document.querySelector('.card-title:contains("Operation")').scrollIntoView({ behavior: 'smooth' });
}

function showDischargeModal() {
    $('#dischargeModal').modal('show');
}

function checkAccountStatus() {
    const patientId = document.getElementById('dischargePatient').value;
    if (!patientId) {
        alert('Please select a patient first');
        return;
    }

    // Show loading state
    document.getElementById('checkAccountBtn').disabled = true;
    document.getElementById('checkAccountBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

    // Simulate API call (replace with actual AJAX call)
    setTimeout(() => {
        // Mock data - replace with actual API response
        const mockData = {
            total_bill: 5000,
            paid_amount: 3000,
            due_amount: 2000,
            clearance_status: 'pending'
        };

        document.getElementById('totalBill').textContent = '৳' + mockData.total_bill;
        document.getElementById('paidAmount').textContent = '৳' + mockData.paid_amount;
        document.getElementById('dueAmount').textContent = '৳' + mockData.due_amount;

        const statusElement = document.getElementById('clearanceStatus');
        const proceedBtn = document.getElementById('proceedDischargeBtn');

        if (mockData.due_amount > 0) {
            statusElement.textContent = 'Pending';
            statusElement.className = 'info-box-number text-warning';
            proceedBtn.disabled = true;
        } else {
            statusElement.textContent = 'Cleared';
            statusElement.className = 'info-box-number text-success';
            proceedBtn.disabled = false;
        }

        document.getElementById('accountStatus').classList.remove('d-none');
        document.getElementById('checkAccountBtn').disabled = false;
        document.getElementById('checkAccountBtn').innerHTML = '<i class="fas fa-calculator"></i> Check Account';
    }, 1500);
}

function proceedToDischarge() {
    const patientId = document.getElementById('dischargePatient').value;
    const patientName = document.getElementById('dischargePatient').options[document.getElementById('dischargePatient').selectedIndex].text;

    if (confirm(`Are you sure you want to proceed with discharge for: ${patientName}?`)) {
        // Redirect to discharge prescription page
        window.location.href = `/admission/${patientId}/rx-discharge`;
    }
}

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});
</script>

@php
function getPrescriptionTypeColor($type) {
    $colors = [
        'outdoor' => 'primary',
        'indoor' => 'success',
        'pre-op' => 'warning',
        'post-op' => 'info',
        'fresh' => 'secondary',
        'discharge' => 'danger',
        'clearance' => 'success'
    ];
    return $colors[$type] ?? 'primary';
}
@endphp
@endsection

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
.btn-app {
    margin-bottom: 10px;
    min-height: 60px;
    transition: all 0.3s ease;
}
.btn-app:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.02);
}
.project-actions .btn {
    margin: 0 2px;
}
</style>
@endsection
