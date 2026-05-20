@extends('adminlte::page')
@section('title', 'Discharge Patient')
@section('content_header')
    <h1 class="text-primary font-weight-bold">
        Discharge Patient - {{ $patient->patientname ?? '' }} ({{ $patientcode }})
        @if(isset($admission) && $admission)
            @php
                $stageNames = [1=>'Admit Medicine',2=>'Pre Surgery',3=>'Post Surgery',4=>'Round Patient',5=>'Fresh Prescription',6=>'Release Patient'];
                $s = (int)($admission->status ?? 6);
            @endphp
            <span class="badge badge-info float-right" style="font-size:14px;">
                Stage: {{ $s }} - {{ $stageNames[$s] ?? '' }}
            </span>
        @endif
    </h1>
    <a href="{{ route('admission.discharge') }}" class="btn btn-secondary btn-sm mt-2">
        Back to Discharge List
    </a>
@stop
@section('content')
<div id="ajaxMessage"></div>
<input type="hidden" id="patientcode" value="{{ $patientcode }}">
<input type="hidden" id="print_patientname" value="{{ $patient->patientname ?? '' }}">
<input type="hidden" id="print_mobile" value="{{ $patient->mobile_no ?? '' }}">
<input type="hidden" id="print_admissiondate" value="{{ $admission ? date('d-m-Y', strtotime($admission->created_at)) : date('d-m-Y') }}">
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-danger shadow">
            <div class="card-header"><h5>Admit Medicines</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr><th>#</th><th>Medicine</th><th>Strength</th><th>Dose</th><th>Morning</th><th>Noon</th><th>Night</th></tr>
                    </thead>
                    <tbody>
                        @forelse($admitMedicines as $i => $m)
                            <tr><td>{{ $i+1 }}</td><td>{{ $m->name }}</td><td>{{ $m->strength }}</td><td>{{ $m->dose }}</td><td>{{ $m->morning }}</td><td>{{ $m->noon }}</td><td>{{ $m->night }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No admit medicines.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-outline card-warning shadow">
            <div class="card-header"><h5>Pre Surgery Medicines</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr><th>#</th><th>Medicine</th><th>Strength</th><th>Dose</th><th>Morning</th><th>Noon</th><th>Night</th></tr>
                    </thead>
                    <tbody>
                        @forelse($preMedicines as $i => $m)
                            <tr><td>{{ $i+1 }}</td><td>{{ $m->name }}</td><td>{{ $m->strength }}</td><td>{{ $m->dose }}</td><td>{{ $m->morning }}</td><td>{{ $m->noon }}</td><td>{{ $m->night }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No pre surgery medicines.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-outline card-info shadow">
            <div class="card-header"><h5>Post Surgery Medicines</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr><th>#</th><th>Medicine</th><th>Strength</th><th>Dose</th><th>Morning</th><th>Noon</th><th>Night</th></tr>
                    </thead>
                    <tbody>
                        @forelse($postMedicines as $i => $m)
                            <tr><td>{{ $i+1 }}</td><td>{{ $m->name }}</td><td>{{ $m->strength }}</td><td>{{ $m->dose }}</td><td>{{ $m->morning }}</td><td>{{ $m->noon }}</td><td>{{ $m->night }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No post surgery medicines.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-outline card-success shadow">
            <div class="card-header"><h5>Round Patient Medicines</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr><th>#</th><th>Medicine</th><th>Strength</th><th>Dose</th><th>Morning</th><th>Noon</th><th>Night</th></tr>
                    </thead>
                    <tbody>
                        @forelse($roundMedicines as $i => $m)
                            <tr><td>{{ $i+1 }}</td><td>{{ $m->name }}</td><td>{{ $m->strength }}</td><td>{{ $m->dose }}</td><td>{{ $m->morning }}</td><td>{{ $m->noon }}</td><td>{{ $m->night }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No round patient medicines.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-outline card-primary shadow">
            <div class="card-header"><h5>Fresh Prescription Medicines</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr><th>#</th><th>Medicine</th><th>Strength</th><th>Dose</th><th>Morning</th><th>Noon</th><th>Night</th></tr>
                    </thead>
                    <tbody>
                        @forelse($freshMedicines as $i => $m)
                            <tr><td>{{ $i+1 }}</td><td>{{ $m->name }}</td><td>{{ $m->strength }}</td><td>{{ $m->dose }}</td><td>{{ $m->morning }}</td><td>{{ $m->noon }}</td><td>{{ $m->night }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No fresh prescription medicines.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12 text-center">
        <button type="button" onclick="printSlip()" class="btn btn-warning btn-lg mr-2">
            <i class="fas fa-print"></i> Print Slip
        </button>
        <button type="button" onclick="doDischarge()" class="btn btn-danger btn-lg">
            <i class="fas fa-sign-out-alt"></i> Discharge Patient
        </button>
    </div>
</div>
@endsection
@section('js')
<script>
var logoUrl = '{{ config('app.logo_url') }}';

function printSlip() {
    var patientname = $('#print_patientname').val() || 'N/A';
    var mobile = $('#print_mobile').val() || 'N/A';
    var admissiondate = $('#print_admissiondate').val() || 'N/A';
    var printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <html>
        <head>
            <title>Discharge Slip</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h2 { text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                th { background: #f0f0f0; }
                .info { margin-bottom: 10px; }
            </style>
        </head>
        <body>
            ${logoUrl ? '<div style="text-align:center;margin-bottom:10px;"><img src="'+logoUrl+'" alt="Logo" style="max-height:60px;"></div>' : ''}
            <h2>Discharge Slip</h2>
            <div class="info">
                <p><strong>Patient:</strong> ${patientname}</p>
                <p><strong>Mobile:</strong> ${mobile}</p>
                <p><strong>Admission Date:</strong> ${admissiondate}</p>
                <p><strong>Discharge Date:</strong> ${new Date().toLocaleDateString()}</p>
            </div>
            <p><em>All stages completed. Patient discharged.</em></p>
        </body>
        </html>
    `);
    printWindow.document.close();
    setTimeout(function () { printWindow.print(); }, 500);
}
function doDischarge() {
    if (!confirm('Discharge this patient?')) return;
    $.ajax({
        url: "{{ route('admission.discharge.do') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
        success: function (response) {
            if (response.success) {
                $('#ajaxMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function(){ window.location.href = "{{ route('admission.discharge') }}"; }, 2000);
            } else {
                $('#ajaxMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function (xhr) {
            var msg = 'Discharge failed.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            $('#ajaxMessage').html('<div class="alert alert-danger">' + msg + '</div>');
        }
    });
}
</script>
@endsection