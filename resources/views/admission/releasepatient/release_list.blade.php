@extends('adminlte::page')
@section('title', 'Release Patient')
@section('content_header')
    <h1 class="text-primary font-weight-bold">
        Release Patient
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
<div class="card card-outline card-primary shadow">
    <div class="card-header">
        <h5>Select Patient for Release</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admission.showdischargeMedicine') }}">
            @csrf
            <div class="form-group">
                <label><b>Select Patient <span class="text-danger">*</span></b></label>
                <select name="patientcode" id="patientcode" class="form-control select2" required>
                    <option value="">-- Search by Name or Mobile --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->patientcode }}">
                            {{ $p->patientname }} - {{ $p->patientcode ?? '' }} ({{ $p->mobile_no ?? 'No Mobile' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-right"></i> Release
            </button>
        </form>
    </div>
</div>
@endsection
@push('js')
<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>
@endpush