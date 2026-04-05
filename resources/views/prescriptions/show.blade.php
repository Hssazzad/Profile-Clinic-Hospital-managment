@extends('adminlte::page')

@section('title', 'Prescription')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary mb-0">Prescription Details</h1>
        <div class="d-print-none">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                Print
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
    </div>
@stop

@section('content')
@php
    // Expect: $prescription with ->patient and ->items eager-loaded
    // Controller example:
    // $prescription->load('patient','items');
@endphp

<div class="container mt-3">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Header: Clinic / Prescription Info --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4 class="mb-1">Prescription #{{ $prescription->prescription_no }}</h4>
                    <div>Prescribed On:
                        <strong>{{ optional($prescription->prescribed_on)->format('d M Y') }}</strong>
                    </div>
                    @if($prescription->doctor_name)
                        <div>Doctor:
                            <strong>{{ $prescription->doctor_name }}</strong>
                            @if($prescription->doctor_reg_no)
                                <small class="text-muted">({{ $prescription->doctor_reg_no }})</small>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-right">
                    <div class="text-muted">Created: {{ $prescription->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            <hr>

            {{-- Patient Info --}}
            <h5 class="mb-2">Patient</h5>
            <div class="table-responsive">
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" style="width:160px;">Name</td>
                            <td style="width:10px;">:</td>
                            <td>{{ $prescription->patient->patientname ?? '—' }}</td>
                            <td class="font-weight-bold" style="width:160px;">Patient Code</td>
                            <td style="width:10px;">:</td>
                            <td>{{ $prescription->patient->patientcode ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Age</td>
                            <td>:</td>
                            <td>{{ $prescription->patient->age ?? '—' }}</td>
                            <td class="font-weight-bold">Gender</td>
                            <td>:</td>
                            <td>{{ $prescription->patient->gender ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Mobile</td>
                            <td>:</td>
                            <td>{{ $prescription->patient->mobile_no ?? '—' }}</td>
                            <td class="font-weight-bold">NID</td>
                            <td>:</td>
                            <td>{{ $prescription->patient->nid_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Address</td>
                            <td>:</td>
                            <td colspan="4">{{ $prescription->patient->address ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Complaint + Diagnosis --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-uppercase text-muted mb-1">Chief Complaint</h6>
                    <div class="border rounded p-2" style="min-height:70px;">
                        {!! nl2br(e($prescription->chief_complaint ?? '—')) !!}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-uppercase text-muted mb-1">Diagnosis</h6>
                    <div class="border rounded p-2" style="min-height:70px;">
                        {!! nl2br(e($prescription->diagnosis ?? '—')) !!}
                    </div>
                </div>
            </div>

            {{-- Medicines --}}
            <h5 class="mt-2 mb-2">Medicines</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Medicine</th>
                            <th>Strength</th>
                            <th>Dose</th>
                            <th>Route</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Timing</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prescription->items as $it)
                            <tr>
                                <td>{{ $it->medicine_name }}</td>
                                <td>{{ $it->strength }}</td>
                                <td>{{ $it->dose }}</td>
                                <td>{{ $it->route }}</td>
                                <td>{{ $it->frequency }}</td>
                                <td>{{ $it->duration }}</td>
                                <td>{{ $it->timing }}</td>
                                <td>{{ $it->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted text-center">No medicines listed.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Investigations & Advices --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-uppercase text-muted mb-1">Investigations</h6>
                    <div class="border rounded p-2" style="min-height:70px;">
                        {!! nl2br(e($prescription->investigations ?? '—')) !!}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-uppercase text-muted mb-1">Advices</h6>
                    <div class="border rounded p-2" style="min-height:70px;">
                        {!! nl2br(e($prescription->advices ?? '—')) !!}
                    </div>
                </div>
            </div>

            {{-- Next Appointment --}}
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted mb-1">Next Appointment</h6>
                    <div class="border rounded p-2">
                        @if($prescription->next_appointment)
                            {{ \Illuminate\Support\Carbon::parse($prescription->next_appointment)->format('d M Y') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Print tweaks */
@media print {
    .content-header, .main-header, .main-footer, .d-print-none { display: none !important; }
    .content-wrapper { background: #fff; }
    .card { border: none; box-shadow: none; }
}
</style>
@endsection
