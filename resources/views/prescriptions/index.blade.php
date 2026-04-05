@extends('adminlte::page')

@section('title', 'Prescriptions List')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-file-medical"></i> Prescriptions</h1>
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
            <h5 class="mb-0"><i class="fas fa-list"></i> All Prescriptions</h5>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-light">
                <i class="fas fa-plus"></i> New Prescription
            </a>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('prescriptions.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="patient_id" class="form-control">
                                    <option value="">All Patients</option>
                                    @foreach($patients ?? [] as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->patientname }} ({{ $patient->patientcode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ request('date_from') }}" placeholder="From Date">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ request('date_to') }}" placeholder="To Date">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Prescriptions Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">Prescription No</th>
                            <th width="15%">Patient</th>
                            <th width="12%">Date</th>
                            <th width="20%">Diagnosis</th>
                            <th width="15%">Doctor</th>
                            <th width="8%">Medicines</th>
                            <th width="13%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($prescriptions) && $prescriptions->count() > 0)
                            @foreach($prescriptions as $index => $prescription)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $prescription->prescription_no }}</strong></td>
                                    <td>
                                        {{ $prescription->patient->patientname }}<br>
                                        <small class="text-muted">{{ $prescription->patient->patientcode }}</small>
                                    </td>
                                    <td>{{ $prescription->prescribed_on->format('d M Y') }}</td>
                                    <td>
                                        @if($prescription->diagnosis)
                                            <span class="text-truncate d-block" style="max-width: 200px;"
                                                  title="{{ $prescription->diagnosis }}">
                                                {{ Str::limit($prescription->diagnosis, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $prescription->doctor_name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $prescription->items->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('prescriptions.show', $prescription->id) }}"
                                               class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('prescriptions.pdf', $prescription->id) }}"
                                               class="btn btn-outline-success" title="PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('prescriptions.create', ['patient' => $prescription->patient_id]) }}"
                                               class="btn btn-outline-info" title="New Prescription">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    No prescriptions found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($prescriptions) && $prescriptions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.table th {
    font-weight: 600;
    font-size: 13px;
}
.table td {
    font-size: 13px;
    vertical-align: middle;
}
.badge {
    font-size: 11px;
}
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection
