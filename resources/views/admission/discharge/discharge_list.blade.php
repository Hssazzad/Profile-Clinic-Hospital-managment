@extends('adminlte::page')
@section('title', 'Discharge Patient')
@section('content_header')
    <h1 class="text-primary font-weight-bold">
        Discharge Patient
        <span class="badge badge-info float-right" style="font-size:14px;">Stage: 6 - Release Patient</span>
    </h1>
@stop
@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card card-outline card-danger shadow">
    <div class="card-header">
        <h5>Patients Ready for Discharge</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="bg-danger text-white">
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Patient Code</th>
                    <th>Mobile</th>
                    <th>Admission Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $i => $p)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $p->patientname }}</td>
                        <td>{{ $p->patientcode }}</td>
                        <td>{{ $p->mobile_no ?? 'N/A' }}</td>
                        <td>{{ date('d-m-Y', strtotime($p->created_at)) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admission.showdischargeMedicine') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="patientcode" value="{{ $p->patientcode }}">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-sign-out-alt"></i> Discharge
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No patients ready for discharge.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection