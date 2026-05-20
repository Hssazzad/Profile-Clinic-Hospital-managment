@extends('adminlte::page')

@section('title', 'Patient Medicine')

@section('content_header')
    <h1 class="text-primary font-weight-bold">
        Medicine List Post Medicine- Patient Code: {{ $patientcode }}
        @if(isset($admission) && $admission)
            @php
                $stageNames = [1=>'Admit Medicine',2=>'Pre Surgery',3=>'Post Surgery',4=>'Round Patient',5=>'Fresh Prescription',6=>'Release Patient'];
                $s = (int)($admission->status ?? 3);
            @endphp
            <span class="badge badge-info float-right" style="font-size:14px;">
                Stage: {{ $s }} - {{ $stageNames[$s] ?? '' }}
            </span>
        @endif
    </h1>
    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm mt-2">
        Back
    </a>
@stop

@section('content')

<div id="ajaxMessage"></div>

<input type="hidden" id="patientcode" value="{{ $patientcode }}">
<input type="hidden" id="print_patientname" value="{{ $patient->patientname ?? '' }}">
<input type="hidden" id="print_mobile" value="{{ $patient->mobile_no ?? '' }}">
<input type="hidden" id="print_admissiondate" value="{{ $admission ? date('d-m-Y', strtotime($admission->created_at)) : date('d-m-Y') }}">

<div class="card card-outline card-primary shadow">
    <div class="card-header">
        <button type="button" class="btn btn-success btn-sm" id="addMedicineBtn">
            Add Medicine
        </button>
        <div class="card-tools">
            <button type="button" onclick="savePrintNext()" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> Save & Print & Next
            </button>
        </div>
    </div>
    <div class="card-body" id="medicineTableArea">
        @include('admission.postmedicine.postmedicine_rows')
    </div>
</div>

<div class="modal fade" id="medicineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="medicineForm">
            @csrf
            <input type="hidden" name="id" id="medicine_id">
            <input type="hidden" name="patientcode" id="modal_patientcode" value="{{ $patientcode }}">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Medicine</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Medicine Name</label>
                            <div class="form-group">
                                <label><b>Select Medicine <span class="text-danger">*</span></b></label>
                                <select name="name" id="name" class="form-control select2" required>
                                    <option value="">-- Search by Name</option>
                                    @foreach($medicine as $m)
                                        <option value="{{ $m->name }}">
                                            {{ $m->name }} - {{ $m->name ?? '' }} ({{ $m->name ?? 'No Name' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Strength</label>
                            <input type="text" name="strength" id="strength" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Dose</label>
                            <input type="text" name="dose" id="dose" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Morning</label>
                            <input type="text" name="morning" id="morning" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Noon</label>
                            <input type="text" name="noon" id="noon" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Night</label>
                            <input type="text" name="night" id="night" class="form-control">
                        </div>
                        <input type="hidden" name="order_type" id="order_type" value="postorder">
                        <div class="col-md-6 form-group">
                            <label>Template ID</label>
                            <input type="text" name="templeteid" id="templeteid" class="form-control" value="TPL-000001">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
var logoUrl = '{{ config('app.logo_url') }}';

function printSlip() {
    var rows = '';
    var i = 1;
    $('#medicineTableArea table tbody tr').each(function () {
        var tds = $(this).find('td');
        if (tds.length > 0) {
            rows += '<tr>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + i + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;">' + tds.eq(1).text().trim() + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + tds.eq(2).text().trim() + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + tds.eq(3).text().trim() + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + tds.eq(4).text().trim() + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + tds.eq(5).text().trim() + '</td>';
            rows += '<td style="border:1px solid #000;padding:6px;text-align:center;">' + tds.eq(6).text().trim() + '</td>';
            rows += '</tr>';
            i++;
        }
    });

    var patientname   = $('#print_patientname').val();
    var mobile        = $('#print_mobile').val();
    var admissiondate = $('#print_admissiondate').val();
    var patientcode   = $('#patientcode').val();

    var printWindow = window.open('', '_blank', 'width=1000,height=700');
    printWindow.document.open();
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Post Surgery Medicine Prescription</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 13px; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                table th, table td { border: 1px solid #000; padding: 6px; }
                th { background: #f8f9fa; }
                @media print { @page { size: A4; margin: 10mm; } }
            </style>
        </head>
        <body>
            <div style="text-align:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:20px;">
                ${logoUrl ? '<div style="margin-bottom:8px;"><img src="'+logoUrl+'" alt="Logo" style="max-height:60px;"></div>' : ''}
                <h2 style="margin:0; font-size:28px; font-weight:bold;">Professor Clinic</h2>
                <p style="margin:2px 0; font-size:14px;">মাঝিড়া, শাজাহানপুর, বগুড়া</p>
                <p style="margin:2px 0; font-size:14px;">Mobile: 01720-039005</p>
            </div>
            <div style="text-align:center; font-size:22px; font-weight:bold; margin:15px 0; text-decoration:underline;">
                POST SURGERY MEDICINE PRESCRIPTION
            </div>
            <div style="background:#e9ecef; border:1px solid #000; padding:8px; font-size:16px; font-weight:bold; margin-top:15px;">
                Patient Information
            </div>
            <table style="margin-top:10px;">
                <tr>
                    <th style="width:20%;">Patient Code</th>
                    <td>${patientcode}</td>
                    <th style="width:20%;">Patient Name</th>
                    <td>${patientname}</td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td>${mobile}</td>
                    <th>Admission Date</th>
                    <td>${admissiondate}</td>
                </tr>
            </table>
            <div style="background:#e9ecef; border:1px solid #000; padding:8px; font-size:16px; font-weight:bold; margin-top:15px;">
                Medicine List
            </div>
            <table style="margin-top:10px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Strength</th>
                        <th>Dose</th>
                        <th>Morning</th>
                        <th>Noon</th>
                        <th>Night</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            <div style="margin-top:80px; width:100%;">
                <div style="width:32%; display:inline-block; text-align:center; vertical-align:top;">
                    <div style="border-top:1px solid #000; margin-top:40px; padding-top:5px;">Patient / Guardian</div>
                </div>
                <div style="width:32%; display:inline-block; text-align:center; vertical-align:top;">
                    <div style="border-top:1px solid #000; margin-top:40px; padding-top:5px;">Prepared By</div>
                </div>
                <div style="width:32%; display:inline-block; text-align:center; vertical-align:top;">
                    <div style="border-top:1px solid #000; margin-top:40px; padding-top:5px;">Authorized Signature</div>
                </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    var now = new Date();
    var today = now.toLocaleDateString('en-GB').replace(/\//g, '-');
    var time = now.getHours().toString().padStart(2,'0') + '-' + now.getMinutes().toString().padStart(2,'0') + '-' + now.getSeconds().toString().padStart(2,'0');
    printWindow.document.title = 'PostSurgery_Medicine_' + patientname + '_' + today + '_' + time;
    printWindow.onload = function () {
        printWindow.focus();
        printWindow.print();
        setTimeout(function () { printWindow.close(); }, 500);
    };
}

function saveAllPostorder() {
    $.ajax({
        url: "{{ route('admission.postmedicineSaveAllPostorder') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
        success: function (response) {
            if (response.success) {
                $('#ajaxMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                if (typeof loadMedicineTable === 'function') {
                    loadMedicineTable();
                }
                $.ajax({
                    url: "{{ route('admission.nextStage') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
                    success: function (res) {
                        if (res.success) {
                            $('#ajaxMessage').append('<br><span class="text-success">' + res.message + '</span>');
                            setTimeout(function(){ location.reload(); }, 1500);
                        }
                    }
                });
            } else {
                $('#ajaxMessage').html('<div class="alert alert-danger">' + (response.message || 'Save failed.') + '</div>');
            }
        },
        error: function (xhr) {
            var msg = 'Save failed.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            $('#ajaxMessage').html('<div class="alert alert-danger">' + msg + '</div>');
        }
    });
}

function savePrintNext() {
    $.ajax({
        url: "{{ route('admission.postmedicineSaveAllPostorder') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
        success: function (response) {
            if (response.success) {
                printSlip();
                $.ajax({
                    url: "{{ route('admission.nextStage') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
                    success: function (res) {
                        if (res.success) {
                            $('#ajaxMessage').html('<div class="alert alert-success">Saved & Printed. ' + res.message + '</div>');
                            setTimeout(function(){ location.reload(); }, 2000);
                        }
                    }
                });
            } else {
                $('#ajaxMessage').html('<div class="alert alert-danger">' + (response.message || 'Save failed.') + '</div>');
            }
        },
        error: function () {
            $('#ajaxMessage').html('<div class="alert alert-danger">Save failed.</div>');
        }
    });
}
</script>
@endsection

@push('js')
<script>
function loadMedicineTable() {
    $.ajax({
        url: "{{ route('admission.postmedicineList.list') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", patientcode: $('#patientcode').val() },
        success: function (response) {
            $('#medicineTableArea').html(response);
        }
    });
}

$(document).ready(function () {

    $('#addMedicineBtn').on('click', function () {
        $('#medicineForm')[0].reset();
        $('#medicine_id').val('');
        $('#modal_patientcode').val($('#patientcode').val());
        $('#templeteid').val('TPL-000001');
        $('#order_type').val('postorder');
        $('#modalTitle').text('Add Medicine');
        $('#medicineModal').modal('show');
    });

    $('#medicineForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admission.postmedicineSave.save') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                $('#medicineModal').modal('hide');
                $('#ajaxMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                loadMedicineTable();
            },
            error: function () {
                $('#ajaxMessage').html('<div class="alert alert-danger">Something went wrong.</div>');
            }
        });
    });

    $(document).on('click', '.editMedicineBtn', function () {
        $('#medicine_id').val($(this).data('id'));
        $('#name').val($(this).data('name'));
        $('#strength').val($(this).data('strength'));
        $('#dose').val($(this).data('dose'));
        $('#morning').val($(this).data('morning'));
        $('#noon').val($(this).data('noon'));
        $('#night').val($(this).data('night'));
        $('#templeteid').val($(this).data('templeteid'));
        $('#order_type').val('postorder');
        $('#modalTitle').text('Edit Medicine');
        $('#medicineModal').modal('show');
    });

    $(document).on('click', '.deleteMedicineBtn', function () {
        if (!confirm('Are you sure you want to delete this medicine?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admission.postmedicine.delete') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function (response) {
                $('#ajaxMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                loadMedicineTable();
            },
            error: function () {
                $('#ajaxMessage').html('<div class="alert alert-danger">Delete failed.</div>');
            }
        });
    });

});
</script>
@endpush

@push('js')
@if(isset($activeMenu))
<script>
$(document).ready(function () {
    let menu = $('a[href*="{{ $activeMenu }}"]');
    menu.addClass('active');
    menu.closest('.nav-item').addClass('menu-open');
    menu.closest('.nav-treeview').css('display', 'block');
});
</script>
@endif
@endpush