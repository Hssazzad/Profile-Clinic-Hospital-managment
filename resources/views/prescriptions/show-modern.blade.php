@extends('adminlte::page')

@section('title', 'Prescription Details')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-file-medical"></i> Prescription #{{ $prescription->prescription_no }}</h1>
@endsection

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-pills"></i> Prescription Details</h5>
            <div>
                <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('prescriptions.create', ['patient' => $prescription->patient_id]) }}" class="btn btn-success btn-sm ml-2">
                    <i class="fas fa-plus"></i> New Prescription
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Prescription Header -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-hospital"></i> Clinic Information</h6>
                    <p class="mb-1"><strong>Professional Clinic</strong></p>
                    <p class="mb-1">123 Medical Center Road</p>
                    <p class="mb-1">Phone: +880 1234-567890</p>
                    <p class="mb-0">Email: info@professionalclinic.com</p>
                </div>
                <div class="col-md-6 text-right">
                    <h6 class="text-primary"><i class="fas fa-file-medical"></i> Prescription Details</h6>
                    <p class="mb-1"><strong>Prescription No:</strong> {{ $prescription->prescription_no }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $prescription->prescribed_on->format('d M Y') }}</p>
                    <p class="mb-0"><strong>Doctor:</strong> {{ $prescription->doctor_name }} ({{ $prescription->doctor_reg_no }})</p>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Patient Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Patient Code:</strong> {{ $prescription->patient->patientcode }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Name:</strong> {{ $prescription->patient->patientname }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Age:</strong> {{ $prescription->patient->age ?? 'N/A' }} years</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Gender:</strong> {{ $prescription->patient->gender ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Mobile:</strong> {{ $prescription->patient->mobile_no ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Blood Group:</strong> {{ $prescription->patient->blood_group ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Address:</strong> {{ $prescription->patient->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis -->
            @if($prescription->diagnosis)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-stethoscope"></i> Diagnosis</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $prescription->diagnosis }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Medications -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-pills"></i> Medications</h6>
                        </div>
                        <div class="card-body">
                            @if($prescription->items->count() > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <a href="{{ route('prescriptions.pdf', $prescription->id) }}" class="btn btn-outline-success" title="PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="25%">Medicine Name</th>
                                                <th width="15%">Dosage</th>
                                                <th width="15%">Frequency</th>
                                                <th width="15%">Duration</th>
                                                <th width="25%">Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prescription->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $item->medicine_name }}</strong></td>
                                                <td>{{ $item->dose ?? 'N/A' }}</td>
                                                <td>{{ $item->frequency ?? 'N/A' }}</td>
                                                <td>{{ $item->duration ?? 'N/A' }}</td>
                                                <td>{{ $item->note ?? 'N/A' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No medications prescribed.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($prescription->advices)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-notes-medical"></i> Doctor's Advice</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $prescription->advices }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- QR Code Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-qrcode"></i> Verification</h6>
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-2">Scan QR code for verification</p>
                            <div class="d-inline-block p-3 border rounded">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ url('/prescriptions/' . $prescription->id) }}"
                                     alt="QR Code" class="img-fluid">
                            </div>
                            <p class="mt-2 mb-0 small text-muted">
                                Prescription ID: {{ $prescription->prescription_no }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
@media print {
    .card-header button, .card-header a {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }

    .table {
        font-size: 12px;
    }

    h6 {
        font-size: 14px !important;
    }

    p {
        font-size: 12px !important;
    }
}
</style>
@endsection

@section('js')
<script>
// Auto-print if URL parameter is present
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('print') === '1') {
        setTimeout(function() {
            window.print();
        }, 500);
    }
});
</script>
@endsection
