@extends('adminlte::page')

@section('title', 'Billing � Payment')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0" style="font-size:18px; font-weight:600; color:#222;">
        Billing � Payment
    </h1>
    <ol class="breadcrumb float-sm-right mb-0" style="font-size:12px;">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Payment</li>
    </ol>
</div>
@stop

@section('content')

{{-- ============================================================
     TOP ROW � Pay Due Amount Form (LEFT) + Summary Cards (RIGHT)
============================================================= --}}
<div class="top-row-wrap">

    {{-- LEFT � Pay Due Amount --}}
    <div class="pay-form-card">

        <div class="card-header-bar">
            Pay Due Amount
        </div>

        <div class="card-body-pad">

            {{-- Invoice Search --}}
            <div class="ci-row">
                <div class="ci-label">Search Invoice</div>
                <div class="ci-colon">:</div>
                <div class="ci-control">
                    <select id="invoiceSelect" style="width:100%;">
                        <option value="">� Type bill no / name / mobile �</option>
                    </select>
                </div>
            </div>

            {{-- Invoice Info Box --}}
            <div id="invoiceInfoBox" style="display:none; background:#e8f5e9; border-radius:5px; padding:8px 10px; font-size:12px;">
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:4px; margin-bottom:6px;">
                    <div>
                        <span style="background:#00bfa5;color:#fff;font-size:11px;padding:1px 7px;border-radius:10px;font-weight:600;" id="infoBillNo">�</span>
                        <strong id="infoPatientName" style="margin-left:6px; font-size:13px;">�</strong>
                        <span id="infoTypeBadge" style="margin-left:6px;font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;display:inline-block;"></span>
                    </div>
                    <button type="button" id="btnClearInvoice"
                            style="background:none;border:1px solid #e57373;color:#e57373;border-radius:3px;font-size:11px;padding:1px 8px;cursor:pointer;">
                        &times; Clear
                    </button>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="flex:1;min-width:70px;background:#fff;border:1px solid #c8e6c9;border-radius:4px;padding:6px 8px;text-align:center;">
                        <div style="font-size:10px;color:#888;">Total Bill</div>
                        <div style="font-size:14px;font-weight:700;color:#222;" id="infoTotal">&#2547; 0</div>
                    </div>
                    <div style="flex:1;min-width:70px;background:#fff;border:1px solid #c8e6c9;border-radius:4px;padding:6px 8px;text-align:center;">
                        <div style="font-size:10px;color:#888;">Paid</div>
                        <div style="font-size:14px;font-weight:700;color:#2e7d32;" id="infoPaid">&#2547; 0</div>
                    </div>
                    <div style="flex:1;min-width:70px;background:#fff3e0;border:1px solid #ffcc80;border-radius:4px;padding:6px 8px;text-align:center;">
                        <div style="font-size:10px;color:#e65100;">Due</div>
                        <div style="font-size:14px;font-weight:700;color:#e65100;" id="infoDue">&#2547; 0</div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="selectedInvoiceId">

            <div id="invoiceRequiredNotice"
                 style="background:#fff3e0;border:1px solid #ffcc80;border-radius:4px;padding:6px 10px;font-size:12px;color:#e65100;">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Please select an invoice with due amount.
            </div>

            {{-- Payment Fields --}}
            <div id="paymentFields" style="display:none; flex-direction:column; gap:9px;">

                <div class="ci-row">
                    <div class="ci-label">Paying Amount (&#2547;)</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="number" id="payingAmountInput" class="ci-input" placeholder="0" min="1" step="1">
                        <div id="payingError" style="display:none;color:#e53935;font-size:11px;margin-top:2px;"></div>
                    </div>
                </div>

                <div class="ci-row">
                    <div class="ci-label">Payment Date</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="date" id="payDateInput" class="ci-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="ci-row">
                    <div class="ci-label">Pay Method</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <select id="payMethodInput" class="ci-select">
                            <option value="cash">Cash</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="ci-row">
                    <div class="ci-label">Collected By</div>
                    <div class="ci-colon">:</div>
                    <div class="ci-control">
                        <input type="text" id="collectedByInput" class="ci-input" placeholder="Staff name">
                    </div>
                </div>

            </div>
        </div>

        <div style="padding:4px 14px 14px;">
            <button type="button" id="btnPayNow" disabled
                    style="width:100%;background:#00bfa5;color:#fff;border:none;border-radius:4px;
                           padding:9px 0;font-size:13px;font-weight:600;cursor:pointer;letter-spacing:.3px;">
                Confirm Payment
            </button>
        </div>
    </div>{{-- /LEFT --}}


    {{-- RIGHT � Summary Card + Search --}}
    <div class="right-col">

        <div style="display:flex; gap:12px;">
            <div class="summary-card" style="border-left:4px solid #e65100;">
                <div class="summary-icon" style="background:#fff3e0; color:#e65100;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="summary-label">Total Due</div>
                    <div class="summary-value" id="sumTotalDue">&#2547; 0</div>
                    <div class="summary-count" id="sumTotalCount">0 invoices</div>
                </div>
            </div>
        </div>

        {{-- Search bar --}}
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <input type="text" id="listSearchInput" class="ci-input"
                   placeholder="Search name / bill no / mobile..."
                   style="flex:1; min-width:160px; height:32px;">
            <label style="font-size:12px;color:#555;display:flex;align-items:center;gap:4px;cursor:pointer;white-space:nowrap;">
                <input type="checkbox" id="showAllChk" style="cursor:pointer;">
                Show Paid Also
            </label>
        </div>

    </div>{{-- /RIGHT --}}

</div>{{-- /TOP ROW --}}


{{-- ============================================================
     BOTTOM � Due List Table
============================================================= --}}
<div style="border:2px solid #00bfa5; border-radius:6px; background:#fff; overflow:hidden;">

    {{-- Export Toolbar --}}
    <div class="export-toolbar">
        <div style="font-size:12px; color:#555;">
            Due List
            <span id="activeTabDueSummary" style="margin-left:8px; color:#e65100; font-weight:600;"></span>
        </div>
        <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <button id="btnExportExcel"
                    style="background:#217346;color:#fff;border:none;border-radius:4px;
                           padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </button>
            <button id="btnExportCsv"
                    style="background:#e65100;color:#fff;border:none;border-radius:4px;
                           padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;">
                <i class="fas fa-file-csv mr-1"></i> CSV
            </button>
            <button id="btnPrintList"
                    style="background:#1565c0;color:#fff;border:none;border-radius:4px;
                           padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:600px;" id="dueTable">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th class="inv-th" style="width:28px;">#</th>
                    <th class="inv-th">Bill No</th>
                    <th class="inv-th">Patient</th>
                    <th class="inv-th">Date</th>
                    <th class="inv-th" style="text-align:right;">Total (&#2547;)</th>
                    <th class="inv-th" style="text-align:right;">Paid (&#2547;)</th>
                    <th class="inv-th" style="text-align:right; color:#e65100;">Due (&#2547;)</th>
                    <th class="inv-th" style="text-align:center;">Status</th>
                    <th class="inv-th" style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody id="dueTableBody">
                <tr>
                    <td colspan="9" style="text-align:center;color:#aaa;padding:30px;font-size:12px;border:1px solid #ddd;">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                    </td>
                </tr>
            </tbody>
            <tfoot id="dueTableFoot" style="display:none;">
                <tr style="background:#e8f5e9; font-weight:700;">
                    <td colspan="4" class="inv-th" style="text-align:right;color:#555;">Total (this page):</td>
                    <td class="inv-th" style="text-align:right;" id="footTotal">&#2547; 0</td>
                    <td class="inv-th" style="text-align:right;color:#2e7d32;" id="footPaid">&#2547; 0</td>
                    <td class="inv-th" style="text-align:right;color:#e65100;font-weight:700;" id="footDue">&#2547; 0</td>
                    <td colspan="2" class="inv-th"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Pagination --}}
    <div id="paginationWrap" style="padding:8px 14px; font-size:12px; color:#555;"></div>

</div>

@stop


@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    .content-wrapper { background: #f4f6f8 !important; }

    /* -- Layout -- */
    .top-row-wrap {
        display: flex;
        gap: 16px;
        padding: 4px 0 14px;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .pay-form-card {
        flex: 0 0 38%;
        min-width: 280px;
        border: 2px solid #00bfa5;
        border-radius: 6px;
        background: #fff;
        overflow: hidden;
    }
    .right-col {
        flex: 1;
        min-width: 240px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .card-header-bar {
        padding: 10px 14px;
        border-bottom: 1px solid #e0e0e0;
        font-size: 15px;
        font-weight: 600;
        color: #222;
    }
    .card-body-pad {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }
    .export-toolbar {
        padding: 8px 14px;
        background: #fafafa;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* -- Select2 -- */
    .select2-container--default .select2-selection--single {
        border:1px solid #ccc !important; border-radius:4px !important; height:30px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height:28px !important; font-size:12px !important; padding-left:8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:28px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
        border-color:#00bfa5 !important; box-shadow:0 0 0 2px rgba(0,191,165,.15) !important;
    }
    .select2-dropdown { border:1px solid #b2dfdb !important; border-radius:5px !important; font-size:12px !important; z-index:99999 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:#e0f2f1 !important; color:#004d40 !important; }
    .select2-results__option { padding:6px 10px !important; }

    /* -- Form rows -- */
    .ci-row    { display:flex; align-items:center; min-height:28px; flex-wrap:wrap; gap:4px; }
    .ci-label  { flex:0 0 120px; font-size:12px; color:#555; }
    .ci-colon  { flex:0 0 12px; color:#888; font-size:12px; }
    .ci-control{ flex:1; min-width:140px; }
    .ci-select, .ci-input {
        width:100%; border:1px solid #ccc; border-radius:4px;
        padding:5px 8px; font-size:12px; color:#222; background:#fff;
        height:30px; outline:none; transition:border-color .15s;
        box-sizing:border-box;
    }
    .ci-select:focus, .ci-input:focus { border-color:#00bfa5; }
    #paymentFields { display:flex; }
    #btnPayNow:disabled { background:#80cbc4 !important; cursor:not-allowed !important; }

    /* -- Summary cards -- */
    .summary-card {
        flex:1; background:#fff; border-radius:6px; padding:12px 14px;
        display:flex; align-items:center; gap:12px;
        box-shadow:0 1px 4px rgba(0,0,0,.08);
    }
    .summary-icon {
        width:40px; height:40px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-size:17px; flex-shrink:0;
    }
    .summary-label { font-size:11px; color:#888; }
    .summary-value { font-size:17px; font-weight:700; color:#222; line-height:1.3; }
    .summary-count { font-size:10px; color:#aaa; }

    /* -- Table -- */
    .inv-th { border:1px solid #ccc; padding:6px 8px; font-weight:600; font-size:11px; color:#333; white-space:nowrap; }
    #dueTableBody td { border:1px solid #ddd; padding:5px 8px; font-size:12px; color:#222; vertical-align:middle; }
    #dueTableFoot td { border:1px solid #ccc; padding:5px 8px; font-size:12px; }

    /* -- Status badges -- */
    .status-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; white-space:nowrap; }
    .status-paid      { background:#e8f5e9; color:#2e7d32; }
    .status-partial   { background:#fff8e1; color:#f57f17; }
    .status-due       { background:#fce4ec; color:#c62828; }
    .status-confirmed { background:#e3f2fd; color:#1565c0; }

    /* -- Action buttons -- */
    .btn-select-inv {
        background:#2979ff;color:#fff;border:none;border-radius:3px;
        padding:3px 10px;font-size:11px;cursor:pointer;font-weight:600;white-space:nowrap;
    }
    .btn-print-inv {
        background:#00bfa5;color:#fff;border:none;border-radius:3px;
        padding:3px 8px;font-size:11px;cursor:pointer;
    }

    /* -- Pagination -- */
    .page-btn { border:1px solid #ddd;background:#fff;padding:3px 9px;border-radius:4px;font-size:12px;cursor:pointer;color:#333; }
    .page-btn.active { background:#00bfa5;color:#fff;border-color:#00bfa5;font-weight:600; }
    .page-btn:hover:not(.active) { background:#f5f5f5; }
    #paginationWrap { overflow-x:auto; }

    /* -- Mobile: stack columns -- */
    @media (max-width: 768px) {
        .top-row-wrap { flex-direction: column; }
        .pay-form-card { flex: none; width: 100%; }
        .right-col { width: 100%; }
        .summary-value { font-size: 15px; }
        .export-toolbar { flex-direction: column; align-items: flex-start; }
        .ci-label { flex: 0 0 105px; }
    }

    @media (max-width: 480px) {
        .ci-row { flex-direction: column; align-items: flex-start; }
        .ci-colon { display: none; }
        .ci-label { flex: none; font-weight: 600; margin-bottom: 2px; }
        .ci-control { width: 100%; }
    }

    /* -- Print -- */
    @media print {
        .content-header, .main-sidebar, .main-header,
        #btnExportExcel, #btnExportCsv, #btnPrintList,
        .btn-select-inv, .btn-print-inv, #paginationWrap,
        .pay-form-card, .right-col { display:none !important; }
        body, .content-wrapper { background:#fff !important; }
        #dueTableFoot { display:table-footer-group !important; }
    }
</style>
@stop


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true };
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
</script>

<script>
(function(){
'use strict';

// -- State ------------------------------------------------
let currentPage     = 1;
let searchTimer     = null;
let selectedInvoice = null;
let allLoadedRows   = [];

// ----------------------------------------------------------
// 1. Invoice Select2 � search due invoices
// ----------------------------------------------------------
$('#invoiceSelect').select2({
    placeholder        : 'Type bill no / name / mobile...',
    allowClear         : true,
    minimumInputLength : 0,
    width              : '100%',
    dropdownParent     : $('#invoiceSelect').parent(),
    ajax: {
        url      : '{{ route("billing.invoice.list") }}',
        dataType : 'json',
        delay    : 250,
        data     : function(p){ return { q: p.term || '', page: p.page || 1, per_page: 10, show_all: 1 }; },
        processResults: function(data){
            const due = (data.data || []).filter(i => parseFloat(i.DueAmount) > 0);
            const meta = data.meta || {};
            const hasMore = meta.current_page < meta.last_page;
            return {
                results: due.map(function(inv){
                    return { id: inv.ID, text: inv.BillNo + ' – ' + inv.PatientName, data: inv };
                }),
                pagination: {
                    more: hasMore
                }
            };
        },
        cache: false,
    },
    templateResult: function(item){
        if (item.loading) return $('<span style="color:#00897b;font-size:12px;">Searching...</span>');
        if (!item.data)   return item.text;
        const inv = item.data;
        const due = parseFloat(inv.DueAmount) || 0;
        return $(
            `<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:2px 0;flex-wrap:wrap;">
                <div>
                    <span style="font-weight:600;color:#1a3c5e;font-size:12px;">${inv.BillNo}</span>
                    <span style="color:#555;font-size:11px;margin-left:6px;">${inv.PatientName}</span>
                    <span style="background:#e0f2f1;color:#00695c;font-size:10px;font-weight:600;
                                 padding:1px 6px;border-radius:8px;margin-left:4px;">
                        ${inv.InvoiceType || ''}
                    </span>
                </div>
                <span style="background:#fce4ec;color:#c62828;font-size:10px;font-weight:700;
                             padding:1px 7px;border-radius:10px;white-space:nowrap;">
                    Due &#2547;${parseInt(due).toLocaleString()}
                </span>
            </div>`
        );
    },
    templateSelection: function(item){
        if (!item.data) return item.text;
        return item.data.BillNo + ' � ' + item.data.PatientName;
    },
    language:{ noResults:()=>'No due invoices found', searching:()=>'Searching...' },
});

$('#invoiceSelect').on('select2:select', function(e){ selectInvoice(e.params.data.data); });
$('#invoiceSelect').on('select2:unselect select2:clear', clearInvoice);
$('#btnClearInvoice').on('click', function(){ $('#invoiceSelect').val(null).trigger('change'); });

// -- Select invoice --
function selectInvoice(inv){
    selectedInvoice = inv;
    $('#selectedInvoiceId').val(inv.ID);

    const net  = parseFloat(inv.TotalBill) - parseFloat(inv.Discount || 0);
    const paid = parseFloat(inv.PaidAmount) || 0;
    const due  = parseFloat(inv.DueAmount)  || 0;

    $('#infoBillNo').text(inv.BillNo);
    $('#infoPatientName').text(inv.PatientName);
    $('#infoTypeBadge').text(inv.InvoiceType || '').css({ background: '#e0f2f1', color: '#00695c' });
    $('#infoTotal').html('&#2547; ' + parseInt(net).toLocaleString());
    $('#infoPaid').html('&#2547; '  + parseInt(paid).toLocaleString());
    $('#infoDue').html('&#2547; '   + parseInt(due).toLocaleString());

    $('#invoiceInfoBox').slideDown(180);
    $('#invoiceRequiredNotice').hide();

    if (due > 0) {
        $('#payingAmountInput').val(parseInt(due)).attr('max', parseInt(due));
        $('#paymentFields').show().css({ display:'flex', 'flex-direction':'column', gap:'9px' });
        $('#btnPayNow').prop('disabled', false);
    } else {
        $('#paymentFields').hide();
        $('#btnPayNow').prop('disabled', true);
        toastr.info('This invoice is already fully paid.');
    }
}

// -- Clear invoice --
function clearInvoice(){
    selectedInvoice = null;
    $('#selectedInvoiceId').val('');
    $('#invoiceInfoBox').slideUp(180);
    $('#invoiceRequiredNotice').show();
    $('#paymentFields').hide();
    $('#btnPayNow').prop('disabled', true);
    $('#payingAmountInput').val('');
    $('#payingError').hide();
}

// ----------------------------------------------------------
// 2. Paying amount validation
// ----------------------------------------------------------
$('#payingAmountInput').on('input', function(){
    if (!selectedInvoice) return;
    const due    = parseFloat(selectedInvoice.DueAmount) || 0;
    const paying = parseFloat($(this).val()) || 0;
    if (paying > due) {
        $('#payingError').html('Cannot exceed due &#2547;' + parseInt(due).toLocaleString()).show();
        $('#btnPayNow').prop('disabled', true);
    } else if (paying <= 0) {
        $('#payingError').text('Enter a valid amount.').show();
        $('#btnPayNow').prop('disabled', true);
    } else {
        $('#payingError').hide();
        $('#btnPayNow').prop('disabled', false);
    }
});

// ----------------------------------------------------------
// 3. Confirm Payment AJAX
// ----------------------------------------------------------
$('#btnPayNow').on('click', function(){
    if (!selectedInvoice) return;
    const paying = parseFloat($('#payingAmountInput').val()) || 0;
    const due    = parseFloat(selectedInvoice.DueAmount) || 0;
    if (paying <= 0 || paying > due) { toastr.error('Invalid amount.'); return; }

    const payload = {
        invoice_id     : selectedInvoice.ID,
        paying_amount  : paying,
        payment_date   : $('#payDateInput').val(),
        payment_method : $('#payMethodInput').val(),
        collected_by   : $('#collectedByInput').val(),
    };

    const $btn = $(this);
    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url         : '{{ route("billing.payment.store") }}',
        method      : 'POST',
        contentType : 'application/json',
        data        : JSON.stringify(payload),
        success: function(res){
            if (res.success) {
                toastr.success('Payment confirmed!');
                window.open('{{ url("Billing/payment") }}/' + res.invoice_id + '/print', '_blank');
                clearInvoice();
                $('#invoiceSelect').val(null).trigger('change');
                loadDueList();
                loadSummaryCards();
            } else {
                toastr.error(res.message || 'Failed.');
            }
        },
        error: function(xhr){
            toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Server error.');
        },
        complete: function(){
            $btn.prop('disabled', false).text('Confirm Payment');
        }
    });
});

// ----------------------------------------------------------
// 4. Load Due List
// ----------------------------------------------------------
function loadDueList(page){
    page = page || 1;
    currentPage = page;

    const q       = $('#listSearchInput').val().trim();
    const showAll = $('#showAllChk').is(':checked') ? 1 : 0;
    const $body   = $('#dueTableBody');

    $body.html(`<tr><td colspan="9" style="text-align:center;color:#aaa;padding:20px;border:1px solid #ddd;">
        <i class="fas fa-spinner fa-spin mr-1"></i> Loading...</td></tr>`);
    $('#dueTableFoot').hide();

    $.get('{{ route("billing.invoice.list") }}', {
        q,
        show_all : showAll,
        page,
        per_page : 20,
    }, function(res){
        const rows = res.data || [];
        const meta = res.meta || {};
        allLoadedRows = rows;

        $body.empty();

        if (!rows.length) {
            $body.html(`<tr><td colspan="9"
                style="text-align:center;color:#aaa;padding:30px;border:1px solid #ddd;font-size:12px;">
                No due invoices found.</td></tr>`);
            $('#paginationWrap').empty();
            $('#activeTabDueSummary').text('');
            return;
        }

        let sumTotal = 0, sumPaid = 0, sumDue = 0;

        rows.forEach(function(inv, idx){
            const net    = parseFloat(inv.TotalBill) - parseFloat(inv.Discount || 0);
            const due    = parseFloat(inv.DueAmount)  || 0;
            const paid   = parseFloat(inv.PaidAmount) || 0;
            const status = (inv.Status || 'due').toLowerCase();
            sumTotal += net; sumPaid += paid; sumDue += due;

            const statusMap = {
                paid      : '<span class="status-badge status-paid">Paid</span>',
                partial   : '<span class="status-badge status-partial">Partial</span>',
                confirmed : '<span class="status-badge status-confirmed">Confirmed</span>',
                due       : '<span class="status-badge status-due">Due</span>',
            };
            const badge  = statusMap[status] || `<span class="status-badge status-due">${inv.Status}</span>`;
            const rowNum = ((meta.current_page || 1) - 1) * (meta.per_page || 20) + idx + 1;
            const invEsc = JSON.stringify(inv).replace(/'/g, '&#39;');

            $body.append(`
                <tr>
                    <td style="color:#888;text-align:center;">${rowNum}</td>
                    <td style="font-weight:600;white-space:nowrap;">${inv.BillNo}</td>
                    <td>
                        <div style="font-weight:500;">${inv.PatientName}</div>
                        <div style="font-size:11px;color:#888;">
                            ${inv.PatientCode || ''}
                            ${inv.MobileNo ? ' &middot; ' + inv.MobileNo : ''}
                        </div>
                    </td>
                    <td style="color:#666;white-space:nowrap;">${inv.PaymentDate || '�'}</td>
                    <td style="text-align:right;white-space:nowrap;">&#2547; ${parseInt(net).toLocaleString()}</td>
                    <td style="text-align:right;color:#2e7d32;font-weight:600;white-space:nowrap;">&#2547; ${parseInt(paid).toLocaleString()}</td>
                    <td style="text-align:right;color:#e65100;font-weight:700;white-space:nowrap;">&#2547; ${parseInt(due).toLocaleString()}</td>
                    <td style="text-align:center;">${badge}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;">
                            ${due > 0
                                ? `<button class="btn-select-inv" data-inv='${invEsc}'>Pay</button>`
                                : ''}
                            <button class="btn-print-inv"
                                    onclick="window.open('{{ url('Billing/payment') }}/${inv.ID}/print','_blank')">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        $('#footTotal').html('&#2547; ' + parseInt(sumTotal).toLocaleString());
        $('#footPaid').html('&#2547; '  + parseInt(sumPaid).toLocaleString());
        $('#footDue').html('&#2547; '   + parseInt(sumDue).toLocaleString());
        $('#dueTableFoot').show();

        $('#activeTabDueSummary').text(
            '� Page Due: \u09f3' + parseInt(sumDue).toLocaleString()
            + ' (' + rows.length + ' records)'
        );

        renderPagination(meta);

    }).fail(function(){
        toastr.error('Failed to load invoices.');
    });
}

// ----------------------------------------------------------
// 5. Summary Card
// ----------------------------------------------------------
function loadSummaryCards(){
    $.get('{{ route("billing.invoice.list") }}', {
        show_all : 0,
        per_page : 9999,
        page     : 1,
    }, function(res){
        const rows     = res.data || [];
        const meta     = res.meta || {};
        const totalDue = rows.reduce((s,i) => s + (parseFloat(i.DueAmount)||0), 0);
        $('#sumTotalDue').html('&#2547; ' + parseInt(totalDue).toLocaleString());
        $('#sumTotalCount').text((meta.total || rows.length) + ' invoices');
    });
}

// ----------------------------------------------------------
// 6. Pagination
// ----------------------------------------------------------
function renderPagination(meta){
    const $wrap = $('#paginationWrap');
    $wrap.empty();
    if (!meta.last_page || meta.last_page <= 1) return;

    let html = `<div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap;">`;
    if (meta.current_page > 1)
        html += `<button class="page-btn" data-page="${meta.current_page - 1}">&lsaquo; Prev</button>`;
    for (let i = 1; i <= meta.last_page; i++) {
        if (i === meta.current_page)
            html += `<button class="page-btn active" data-page="${i}">${i}</button>`;
        else if (i === 1 || i === meta.last_page || Math.abs(i - meta.current_page) <= 1)
            html += `<button class="page-btn" data-page="${i}">${i}</button>`;
        else if (Math.abs(i - meta.current_page) === 2)
            html += `<span style="color:#aaa;">&hellip;</span>`;
    }
    if (meta.current_page < meta.last_page)
        html += `<button class="page-btn" data-page="${meta.current_page + 1}">Next &rsaquo;</button>`;
    html += `<span style="margin-left:8px;color:#888;">Showing ${meta.from}&ndash;${meta.to} of ${meta.total}</span></div>`;
    $wrap.html(html);
}

$(document).on('click', '.page-btn', function(){
    loadDueList(parseInt($(this).data('page')));
});

// ----------------------------------------------------------
// 7. Row "Pay" button
// ----------------------------------------------------------
$(document).on('click', '.btn-select-inv', function(){
    selectInvoice($(this).data('inv'));
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ----------------------------------------------------------
// 8. Search & filter
// ----------------------------------------------------------
$('#listSearchInput').on('input', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function(){ loadDueList(1); }, 300);
});
$('#showAllChk').on('change', function(){ loadDueList(1); });

// ----------------------------------------------------------
// 9. Export helpers
// ----------------------------------------------------------
function buildExportRows(){
    return allLoadedRows.map(function(inv, idx){
        const net = parseFloat(inv.TotalBill) - parseFloat(inv.Discount || 0);
        return {
            '#'            : idx + 1,
            'Bill No'      : inv.BillNo       || '',
            'Patient Name' : inv.PatientName  || '',
            'Patient Code' : inv.PatientCode  || '',
            'Mobile'       : inv.MobileNo     || '',
            'Date'         : inv.PaymentDate  || '',
            'Category'     : inv.InvoiceType  || '',
            'Total (BDT)'  : parseInt(net),
            'Paid (BDT)'   : parseInt(parseFloat(inv.PaidAmount) || 0),
            'Due (BDT)'    : parseInt(parseFloat(inv.DueAmount)  || 0),
            'Status'       : inv.Status       || '',
        };
    });
}

function getFileName(ext){
    return 'DueList_All_' + new Date().toISOString().slice(0,10) + '.' + ext;
}

// -- Excel --
$('#btnExportExcel').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data on this page to export.'); return; }
    const data = buildExportRows();
    const ws   = XLSX.utils.json_to_sheet(data);
    const wb   = XLSX.utils.book_new();
    ws['!cols'] = [
        {wch:4},{wch:14},{wch:24},{wch:14},{wch:14},
        {wch:12},{wch:16},{wch:12},{wch:12},{wch:12},{wch:10}
    ];
    XLSX.utils.book_append_sheet(wb, ws, 'Due List');
    XLSX.writeFile(wb, getFileName('xlsx'));
    toastr.success('Excel downloaded!');
});

// -- CSV --
$('#btnExportCsv').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data on this page to export.'); return; }
    const data  = buildExportRows();
    const heads = Object.keys(data[0]);
    const csv   = [
        heads.join(','),
        ...data.map(row =>
            heads.map(h => '"' + String(row[h]).replace(/"/g,'""') + '"').join(',')
        )
    ].join('\r\n');
    const blob = new Blob(['\uFEFF' + csv], { type:'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = getFileName('csv'); a.click();
    URL.revokeObjectURL(url);
    toastr.success('CSV downloaded!');
});

// -- Print --
$('#btnPrintList').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data on this page to print.'); return; }
    const data  = buildExportRows();
    const today = new Date().toLocaleDateString('en-GB');

    const totalDue  = data.reduce((s,r) => s + r['Due (BDT)'],  0);
    const totalPaid = data.reduce((s,r) => s + r['Paid (BDT)'], 0);
    const totalBill = data.reduce((s,r) => s + r['Total (BDT)'],0);

    const rows = data.map(r => `
        <tr>
            <td>${r['#']}</td>
            <td><strong>${r['Bill No']}</strong></td>
            <td>
                ${r['Patient Name']}
                <br><small>${r['Patient Code']}${r['Mobile'] ? ' &middot; ' + r['Mobile'] : ''}</small>
            </td>
            <td>${r['Date']}</td>
            <td style="text-align:right;">&#2547; ${r['Total (BDT)'].toLocaleString()}</td>
            <td style="text-align:right;color:#2e7d32;">&#2547; ${r['Paid (BDT)'].toLocaleString()}</td>
            <td style="text-align:right;color:#c62828;font-weight:700;">&#2547; ${r['Due (BDT)'].toLocaleString()}</td>
            <td>${r['Status']}</td>
        </tr>`).join('');

    const win = window.open('', '_blank', 'width=960,height=720');
    win.document.write(`<!DOCTYPE html><html><head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Due List</title>
        <style>
            * { box-sizing:border-box; margin:0; padding:0; }
            body  { font-family:Arial,sans-serif; font-size:12px; padding:20px; color:#222; }
            .hdr  { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
            .hdr h2 { font-size:16px; }
            .hdr .sub { font-size:11px; color:#888; margin-top:3px; }
            .hdr .badge { font-size:11px; font-weight:700; padding:3px 10px; border-radius:12px;
                          background:#fff3e0; color:#e65100; }
            table { width:100%; border-collapse:collapse; margin-top:4px; }
            th { background:#f5f5f5; border:1px solid #ccc; padding:6px 8px; font-size:11px; text-align:left; }
            td { border:1px solid #ddd; padding:5px 8px; }
            tfoot td { background:#e8f5e9; font-weight:700; border:1px solid #ccc; }
            small { font-size:10px; color:#888; }
            @media print { body { padding:10px; } }
        </style>
    </head><body>
        <div class="hdr">
            <div>
                <h2>Due Invoice List</h2>
                <div class="sub">Printed: ${today} &nbsp;|&nbsp; Records: ${data.length}</div>
            </div>
            <span class="badge">Due List</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Bill No</th><th>Patient</th><th>Date</th>
                    <th style="text-align:right;">Total (&#2547;)</th>
                    <th style="text-align:right;">Paid (&#2547;)</th>
                    <th style="text-align:right;">Due (&#2547;)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">Total:</td>
                    <td style="text-align:right;">&#2547; ${totalBill.toLocaleString()}</td>
                    <td style="text-align:right;color:#2e7d32;">&#2547; ${totalPaid.toLocaleString()}</td>
                    <td style="text-align:right;color:#c62828;">&#2547; ${totalDue.toLocaleString()}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <script>window.onload = function(){ window.print(); window.close(); };<\/script>
    </body></html>`);
    win.document.close();
});

// ----------------------------------------------------------
// INIT
// ----------------------------------------------------------
loadDueList();
loadSummaryCards();

})();
</script>
@stop
