@extends('adminlte::page')

@section('title', 'Create Invoice')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0" style="font-size:18px; font-weight:600; color:#222;">
        Create Invoice
    </h1>
    <ol class="breadcrumb float-sm-right mb-0" style="font-size:12px;">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('billing.create-invoice.index') }}">Billing</a></li>
        <li class="breadcrumb-item active">Create Invoice</li>
    </ol>
</div>
@stop

@section('content')

{{-- Patient Info Bar --}}
<div style="background:#fff; border:2px solid #00bfa5; border-radius:6px; padding:10px 16px;
            display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
        <div>
            <span style="font-size:10px; color:#888; display:block;">Patient Name</span>
            <span style="font-size:14px; font-weight:700; color:#1a3c5e;">{{ $patient->patientname }}</span>
        </div>
        <div>
            <span style="font-size:10px; color:#888; display:block;">Patient Code</span>
            <span style="font-size:13px; font-weight:600; color:#333;">{{ $patient->patientcode }}</span>
        </div>
        <div>
            <span style="font-size:10px; color:#888; display:block;">Age</span>
            <span style="font-size:13px; color:#333;">{{ $patient->age ?? '—' }}</span>
        </div>
        <div>
            <span style="font-size:10px; color:#888; display:block;">Gender</span>
            <span style="font-size:13px; color:#333;">{{ ucfirst($patient->gender ?? '—') }}</span>
        </div>
        <div>
            <span style="font-size:10px; color:#888; display:block;">Mobile</span>
            <span style="font-size:13px; color:#333;">{{ $patient->mobile_no ?? '—' }}</span>
        </div>
        @if($admission)
        <div>
            <span style="font-size:10px; color:#888; display:block;">Admission</span>
            <span style="font-size:11px; background:#e3f2fd; color:#1565c0; padding:2px 8px;
                         border-radius:10px; font-weight:600;">#{{ $admission->id }}</span>
        </div>
        @endif
    </div>
    <a href="{{ route('billing.create-invoice.index') }}"
       style="background:#f5f5f5; border:1px solid #ddd; color:#555; border-radius:4px;
              padding:5px 14px; font-size:12px; text-decoration:none; white-space:nowrap;">
        ? Back
    </a>
</div>

<div style="display:flex; gap:14px; align-items:flex-start;">

    {{-- ================================================
         LEFT — Add Service
    ================================================= --}}
    <div style="flex:0 0 38%;">

        {{-- Add Service Card --}}
        <div style="background:#fff; border:2px solid #00bfa5; border-radius:6px;
                    overflow:hidden; margin-bottom:14px;">
            <div style="padding:9px 14px; border-bottom:1px solid #e0e0e0;
                        font-size:14px; font-weight:600; color:#222; background:#f9fffe;">
                <i class="fas fa-plus-circle mr-1" style="color:#00bfa5;"></i> Add Service
            </div>
            <div style="padding:12px 14px; display:flex; flex-direction:column; gap:9px;">

                <div class="ci-row">
                    <div class="ci-label">Main Category</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <select id="mainCatSelect" class="ci-select">
                            <option value="">— Select —</option>
                        </select>
                    </div>
                </div>

                <div class="ci-row">
                    <div class="ci-label">Sub Category</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <select id="subCatSelect" class="ci-select" disabled>
                            <option value="">— Select main first —</option>
                        </select>
                    </div>
                </div>

                <div class="ci-row">
                    <div class="ci-label">Amount (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="serviceAmount" class="ci-input"
                               placeholder="0" min="0" step="1">
                    </div>
                </div>

                <div style="padding-top:2px;">
                    <button type="button" id="btnAddService"
                            style="width:100%; background:#00bfa5; color:#fff; border:none;
                                   border-radius:4px; padding:8px 0; font-size:13px;
                                   font-weight:600; cursor:pointer; letter-spacing:.3px;">
                        <i class="fas fa-plus mr-1"></i> Add to Invoice
                    </button>
                </div>

            </div>
        </div>

        {{-- Payment Summary Card --}}
        <div style="background:#fff; border:2px solid #00bfa5; border-radius:6px; overflow:hidden;">
            <div style="padding:9px 14px; border-bottom:1px solid #e0e0e0;
                        font-size:14px; font-weight:600; color:#222; background:#f9fffe;">
                <i class="fas fa-money-bill-wave mr-1" style="color:#00bfa5;"></i> Payment Info
            </div>
            <div style="padding:12px 14px; display:flex; flex-direction:column; gap:9px;">

                {{-- Total (readonly) --}}
                <div class="ci-row">
                    <div class="ci-label">Total Bill (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="totalBillInput" class="ci-input"
                               readonly placeholder="0"
                               style="background:#f5f5f5; font-weight:700; color:#1a3c5e;">
                    </div>
                </div>

                {{-- Discount --}}
                <div class="ci-row">
                    <div class="ci-label">Discount (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="discountInput" class="ci-input"
                               placeholder="0" min="0" step="1" value="0">
                    </div>
                </div>

                {{-- Net Amount --}}
                <div class="ci-row">
                    <div class="ci-label">Net Amount (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="netAmountInput" class="ci-input"
                               readonly placeholder="0"
                               style="background:#e8f5e9; font-weight:700; color:#2e7d32;">
                    </div>
                </div>

                {{-- Paid Amount --}}
                <div class="ci-row">
                    <div class="ci-label">Paid Amount (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="paidAmountInput" class="ci-input"
                               placeholder="0" min="0" step="1">
                        <div id="paidError" style="display:none; color:#e53935; font-size:11px; margin-top:2px;"></div>
                        <div id="minRequiredHint" style="display:none; font-size:11px; color:#e65100; margin-top:2px;"></div>
                    </div>
                </div>

                {{-- Due (readonly) --}}
                <div class="ci-row">
                    <div class="ci-label">Due Amount (?)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="dueAmountInput" class="ci-input"
                               readonly placeholder="0"
                               style="background:#fff3e0; font-weight:700; color:#e65100;">
                    </div>
                </div>

                {{-- Payment Date --}}
                <div class="ci-row">
                    <div class="ci-label">Payment Date</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="date" id="paymentDateInput" class="ci-input"
                               value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="ci-row">
                    <div class="ci-label">Pay Method</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <select id="paymentMethodInput" class="ci-select">
                            <option value="cash">Cash</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>

                {{-- Collected By --}}
                <div class="ci-row">
                    <div class="ci-label">Collected By</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="text" id="collectedByInput" class="ci-input"
                               placeholder="Staff name">
                    </div>
                </div>

                {{-- Confirm Button --}}
                <div style="padding-top:4px;">
                    <button type="button" id="btnConfirmInvoice" disabled
                            style="width:100%; background:#1a3c5e; color:#fff; border:none;
                                   border-radius:4px; padding:10px 0; font-size:14px;
                                   font-weight:700; cursor:pointer; letter-spacing:.4px;">
                        <i class="fas fa-check-circle mr-1"></i> Confirm Invoice
                    </button>
                    <div style="text-align:center; font-size:11px; color:#888; margin-top:5px;">
                        Minimum 25% payment required to confirm
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- /LEFT --}}


    {{-- ================================================
         RIGHT — Added Services Table
    ================================================= --}}
    <div style="flex:1; background:#fff; border:2px solid #00bfa5; border-radius:6px; overflow:hidden;">

        <div style="padding:9px 14px; border-bottom:1px solid #e0e0e0;
                    display:flex; align-items:center; justify-content:space-between; background:#f9fffe;">
            <div style="font-size:14px; font-weight:600; color:#222;">
                <i class="fas fa-list mr-1" style="color:#00bfa5;"></i> Added Services
                <span id="serviceCount"
                      style="background:#00bfa5; color:#fff; font-size:11px;
                             padding:1px 8px; border-radius:10px; margin-left:6px;">0</span>
            </div>
            <button type="button" id="btnClearAll"
                    style="background:none; border:1px solid #e57373; color:#e57373;
                           border-radius:3px; font-size:11px; padding:3px 10px; cursor:pointer;">
                <i class="fas fa-trash mr-1"></i> Clear All
            </button>
        </div>

        {{-- Empty state --}}
        <div id="emptyState"
             style="text-align:center; padding:50px 20px; color:#aaa;">
            <i class="fas fa-file-invoice" style="font-size:36px; margin-bottom:10px; display:block; color:#ddd;"></i>
            <div style="font-size:13px;">No services added yet.</div>
            <div style="font-size:12px; margin-top:4px;">Select a category and add services from the left panel.</div>
        </div>

        {{-- Table --}}
        <div id="serviceTableWrap" style="display:none;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="background:#f5f5f5;">
                        <th class="inv-th" style="width:32px; text-align:center;">#</th>
                        <th class="inv-th">Main Category</th>
                        <th class="inv-th">Service / Sub Category</th>
                        <th class="inv-th" style="text-align:right; width:100px;">Amount (?)</th>
                        <th class="inv-th" style="text-align:center; width:60px;">Remove</th>
                    </tr>
                </thead>
                <tbody id="serviceTableBody"></tbody>
                <tfoot>
                    <tr style="background:#e8f5e9;">
                        <td colspan="3"
                            style="border:1px solid #c8e6c9; padding:8px 10px;
                                   font-weight:700; font-size:13px; color:#1a3c5e; text-align:right;">
                            Total
                        </td>
                        <td style="border:1px solid #c8e6c9; padding:8px 10px;
                                   font-weight:700; font-size:14px; color:#1a3c5e; text-align:right;">
                            ? <span id="footerTotal">0</span>
                        </td>
                        <td style="border:1px solid #c8e6c9;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>{{-- /RIGHT --}}

</div>

{{-- Hidden fields --}}
<input type="hidden" id="patientId"    value="{{ $patient->id }}">
<input type="hidden" id="patientCode"  value="{{ $patient->patientcode }}">
<input type="hidden" id="patientName"  value="{{ $patient->patientname }}">
<input type="hidden" id="patientAge"   value="{{ $patient->age }}">
<input type="hidden" id="mobileNo"     value="{{ $patient->mobile_no }}">
<input type="hidden" id="admissionId"  value="{{ $admission->id ?? '' }}">

@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .content-wrapper { background:#f4f6f8 !important; }

    .ci-row    { display:flex; align-items:center; min-height:28px; }
    .ci-label  { flex:0 0 120px; font-size:12px; color:#555; }
    .ci-colon  { flex:0 0 12px; color:#888; font-size:12px; }
    .ci-control{ flex:1; }

    .ci-select, .ci-input {
        width:100%; border:1px solid #ccc; border-radius:4px;
        padding:5px 8px; font-size:12px; color:#222;
        background:#fff; height:30px; outline:none;
        transition:border-color .15s;
    }
    .ci-select:focus, .ci-input:focus { border-color:#00bfa5; box-shadow:0 0 0 2px rgba(0,191,165,.12); }
    .ci-select:disabled { background:#f5f5f5; color:#aaa; }

    .inv-th {
        border:1px solid #ccc; padding:7px 10px;
        font-weight:600; font-size:11px; color:#333;
    }
    #serviceTableBody td {
        border:1px solid #ddd; padding:6px 10px;
        font-size:12px; color:#222; vertical-align:middle;
    }

    #btnConfirmInvoice:disabled {
        background:#90a4ae !important;
        cursor:not-allowed !important;
    }
    #btnConfirmInvoice:not(:disabled):hover {
        background:#0d2d4a !important;
    }
    #btnAddService:hover { background:#00897b !important; }

    .btn-remove-row {
        background:none; border:1px solid #e57373; color:#e57373;
        border-radius:3px; padding:2px 8px; font-size:11px;
        cursor:pointer; transition:all .15s;
    }
    .btn-remove-row:hover { background:#e57373; color:#fff; }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true };
$.ajaxSetup({ headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' } });
</script>

<script>
(function(){
'use strict';

// -- State ----------------------------------------------------------
const patientId   = $('#patientId').val();
const patientCode = $('#patientCode').val();
const patientName = $('#patientName').val();
const patientAge  = $('#patientAge').val();
const mobileNo    = $('#mobileNo').val();
const admissionId = $('#admissionId').val();

let tmpItems   = [];   // local cache of tbl_bill_tmp rows
let mainCats   = [];   // main category list
let subCats    = [];   // sub category list for selected main

// -- 1. Load main categories ----------------------------------------
$.get('{{ route("billing.create-invoice.main-categories") }}', function(data){
    mainCats = data;
    const $sel = $('#mainCatSelect').empty().append('<option value="">— Select —</option>');
    data.forEach(function(c){
        $sel.append(`<option value="${c.Code}">${c.Name}</option>`);
    });
});

// -- 2. Main category change ? load sub categories -----------------
$('#mainCatSelect').on('change', function(){
    const mainCode = $(this).val();
    $('#subCatSelect').empty().append('<option value="">— Loading… —</option>').prop('disabled', true);
    $('#serviceAmount').val('');

    if (!mainCode) {
        $('#subCatSelect').empty().append('<option value="">— Select main first —</option>');
        return;
    }

    $.get('{{ route("billing.create-invoice.sub-categories") }}', { main_code: mainCode }, function(data){
        subCats = data;
        const $sub = $('#subCatSelect').empty().append('<option value="">— Select —</option>');
        data.forEach(function(c){
            $sub.append(`<option value="${c.Code}" data-amount="${c.Amount}">${c.Name}</option>`);
        });
        $sub.prop('disabled', false);
    });
});

// -- 3. Sub category change ? fill amount --------------------------
$('#subCatSelect').on('change', function(){
    const amount = $('option:selected', this).data('amount') || '';
    $('#serviceAmount').val(amount);
});

// -- 4. Add service to tmp -----------------------------------------
$('#btnAddService').on('click', function(){
    const mainCode = $('#mainCatSelect').val();
    const subCode  = $('#subCatSelect').val();
    const amount   = parseFloat($('#serviceAmount').val()) || 0;

    if (!mainCode) { toastr.warning('Please select a main category.'); return; }
    if (!subCode)  { toastr.warning('Please select a sub category.');  return; }
    if (amount <= 0){ toastr.warning('Please enter a valid amount.');   return; }

    const mainName = $('#mainCatSelect option:selected').text();
    const subName  = $('#subCatSelect option:selected').text();

    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Adding…');

    $.ajax({
        url    : '{{ route("billing.create-invoice.add-tmp") }}',
        method : 'POST',
        data   : {
            patient_code : patientCode,
            main_code    : mainCode,
            sub_code     : subCode,
            amount       : amount,
        },
        success: function(res){
            if (res.success) {
                tmpItems.push({
                    tmp_id    : res.tmp_id,
                    main_code : mainCode,
                    main_name : mainName,
                    sub_code  : subCode,
                    sub_name  : subName,
                    amount    : amount,
                });
                renderTable();
                recalcTotals();
                // reset sub select
                $('#subCatSelect').val('');
                $('#serviceAmount').val('');
                toastr.success(`${subName} added.`);
            } else {
                toastr.error(res.message || 'Failed to add.');
            }
        },
        error: function(){
            toastr.error('Server error. Could not add service.');
        },
        complete: function(){
            $btn.prop('disabled', false).html('<i class="fas fa-plus mr-1"></i> Add to Invoice');
        }
    });
});

// -- 5. Render table -----------------------------------------------
function renderTable(){
    const $body = $('#serviceTableBody').empty();

    if (!tmpItems.length) {
        $('#emptyState').show();
        $('#serviceTableWrap').hide();
        $('#serviceCount').text('0');
        return;
    }

    $('#emptyState').hide();
    $('#serviceTableWrap').show();
    $('#serviceCount').text(tmpItems.length);

    tmpItems.forEach(function(item, idx){
        $body.append(`
            <tr>
                <td style="text-align:center; color:#888;">${idx + 1}</td>
                <td style="color:#555;">${item.main_name}</td>
                <td style="font-weight:500;">${item.sub_name}</td>
                <td style="text-align:right; font-weight:600; color:#1a3c5e;">
                    ? ${parseInt(item.amount).toLocaleString()}
                </td>
                <td style="text-align:center;">
                    <button class="btn-remove-row" data-tmp-id="${item.tmp_id}" data-idx="${idx}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

// -- 6. Remove row -------------------------------------------------
$(document).on('click', '.btn-remove-row', function(){
    const tmpId = $(this).data('tmp-id');
    const idx   = $(this).data('idx');

    $.ajax({
        url    : '{{ route("billing.create-invoice.remove-tmp") }}',
        method : 'DELETE',
        data   : { tmp_id: tmpId },
        success: function(){
            tmpItems.splice(idx, 1);
            renderTable();
            recalcTotals();
        },
        error: function(){
            toastr.error('Could not remove item.');
        }
    });
});

// -- 7. Clear all --------------------------------------------------
$('#btnClearAll').on('click', function(){
    if (!tmpItems.length) return;
    if (!confirm('Remove all services?')) return;

    $.ajax({
        url    : '{{ route("billing.create-invoice.clear-tmp") }}',
        method : 'POST',
        data   : { patient_code: patientCode },
        success: function(){
            tmpItems = [];
            renderTable();
            recalcTotals();
            toastr.info('All services cleared.');
        }
    });
});

// -- 8. Recalculate totals -----------------------------------------
function recalcTotals(){
    const total    = tmpItems.reduce(function(s, i){ return s + parseFloat(i.amount); }, 0);
    const discount = parseFloat($('#discountInput').val()) || 0;
    const net      = Math.max(0, total - discount);
    const paid     = parseFloat($('#paidAmountInput').val()) || 0;
    const due      = Math.max(0, net - paid);

    $('#totalBillInput').val(total   || '');
    $('#netAmountInput').val(net     || '');
    $('#dueAmountInput').val(due     || '');
    $('#footerTotal').text(parseInt(total).toLocaleString());

    // Min 25%
    const minRequired = Math.ceil(net * 0.25);
    if (net > 0) {
        $('#minRequiredHint').text('Min 25% = ? ' + parseInt(minRequired).toLocaleString()).show();
    } else {
        $('#minRequiredHint').hide();
    }

    validateConfirm();
}

$('#discountInput').on('input', recalcTotals);
$('#paidAmountInput').on('input', function(){
    recalcTotals();
    validatePaid();
});

function validatePaid(){
    const net    = parseFloat($('#netAmountInput').val()) || 0;
    const paid   = parseFloat($('#paidAmountInput').val()) || 0;
    const minReq = Math.ceil(net * 0.25);

    $('#paidError').hide();
    if (net > 0 && paid > net) {
        $('#paidError').text('Cannot exceed net amount ? ' + parseInt(net).toLocaleString()).show();
    }
}

function validateConfirm(){
    const net    = parseFloat($('#netAmountInput').val()) || 0;
    const paid   = parseFloat($('#paidAmountInput').val()) || 0;
    const minReq = Math.ceil(net * 0.25);

    const hasItems  = tmpItems.length > 0;
    const validPaid = paid >= minReq && paid <= net && net > 0;

    $('#btnConfirmInvoice').prop('disabled', !(hasItems && validPaid));
}

// -- 9. Confirm Invoice --------------------------------------------
$('#btnConfirmInvoice').on('click', function(){
    const total    = parseFloat($('#totalBillInput').val()) || 0;
    const discount = parseFloat($('#discountInput').val())  || 0;
    const paid     = parseFloat($('#paidAmountInput').val())|| 0;
    const date     = $('#paymentDateInput').val();
    const method   = $('#paymentMethodInput').val();

    if (!tmpItems.length) { toastr.warning('No services added.'); return; }
    if (!date)            { toastr.warning('Select payment date.'); return; }

    // Build items payload
    const items = tmpItems.map(function(item){
        return {
            category      : item.main_code,
            category_name : item.main_name,
            service_name  : item.sub_name,
            unit_price    : item.amount,
            quantity      : 1,
            amount        : item.amount,
        };
    });

    const payload = {
        patient_id     : patientId,
        patient_code   : patientCode,
        patient_name   : patientName,
        patient_age    : patientAge,
        mobile_no      : mobileNo,
        admission_id   : admissionId || null,
        total_bill     : total,
        discount       : discount,
        paid_amount    : paid,
        payment_date   : date,
        payment_method : method,
        collected_by   : $('#collectedByInput').val(),
        items          : items,
    };

    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing…');

    $.ajax({
        url         : '{{ route("billing.create-invoice.store") }}',
        method      : 'POST',
        contentType : 'application/json',
        data        : JSON.stringify(payload),
        success: function(res){
            if (res.success) {
                toastr.success('Invoice ' + res.bill_no + ' created!');
                tmpItems = [];
                renderTable();
                recalcTotals();
                // Open print in new tab
                window.open('{{ url("Billing/CreateInvoice") }}/' + res.payment_id + '/print', '_blank');
                // Reset form
                $('#discountInput').val('0');
                $('#paidAmountInput').val('');
                $('#collectedByInput').val('');
                $('#paymentDateInput').val('{{ date("Y-m-d") }}');
            } else {
                toastr.error(res.message || 'Failed to create invoice.');
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-check-circle mr-1"></i> Confirm Invoice');
            }
        },
        error: function(xhr){
            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error.';
            toastr.error(msg);
            $btn.prop('disabled', false)
                .html('<i class="fas fa-check-circle mr-1"></i> Confirm Invoice');
        }
    });
});

// -- 10. Load existing tmp items on page load ----------------------
$.get('{{ route("billing.create-invoice.get-tmp") }}', { patient_code: patientCode }, function(data){
    if (!data.length) return;

    // We need category names — build a lookup if needed
    // For simplicity, reload names from sub category data
    // Since we don't have names here, fetch all mains first
    $.get('{{ route("billing.create-invoice.main-categories") }}', function(mains){
        const mainMap = {};
        mains.forEach(function(m){ mainMap[m.Code] = m.Name; });

        // For each tmp item, fetch sub name
        let pending = data.length;
        data.forEach(function(row){
            $.get('{{ route("billing.create-invoice.sub-categories") }}', { main_code: row.MainCode }, function(subs){
                const sub = subs.find(function(s){ return s.Code == row.SubCode; });
                tmpItems.push({
                    tmp_id    : row.ID,
                    main_code : row.MainCode,
                    main_name : mainMap[row.MainCode] || 'Unknown',
                    sub_code  : row.SubCode,
                    sub_name  : sub ? sub.Name : ('Service #' + row.SubCode),
                    amount    : parseFloat(row.Amount) || 0,
                });
                pending--;
                if (pending === 0) {
                    renderTable();
                    recalcTotals();
                }
            });
        });
    });
});

})();
</script>
@stop