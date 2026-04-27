@extends('adminlte::page')

@section('title', 'Billing > Pay Now')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0" style="font-size:18px; font-weight:600; color:#222;">
        Billing > Pay Now
    </h1>
    <ol class="breadcrumb float-sm-right mb-0" style="font-size:12px;">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Pay Now</li>
    </ol>
</div>
@stop

@section('content')
<div class="paynow-layout">

    <div class="paynow-left">
        <div class="pay-card">
            <div class="pay-card__header">Pay Due Amount</div>

            <div class="pay-card__body">

                <div class="pn-row">
                    <div class="pn-label">Search Patient</div>
                    <div class="pn-colon">:</div>
                    <div class="pn-control">
                        <select id="patientSelect" style="width:100%;">
                            <option value="">- Type patient name / code / mobile -</option>
                        </select>
                    </div>
                </div>


                <div id="selectedInvoiceBox" class="invoice-box" style="display:none;">
                    <div class="invoice-box__top">
                        <div>
                            <span class="invoice-chip" id="infoBillNo">-</span>
                            <strong id="infoPatientName" class="invoice-patient-name">-</strong>
                        </div>
                        <button type="button" id="btnClearInvoice" class="btn-chip-clear">&times; Clear</button>
                    </div>

                    <div class="invoice-metrics">
                        <div class="metric-card">
                            <div class="metric-title">Total Bill</div>
                            <div class="metric-value" id="infoTotal">&#2547; 0</div>
                        </div>

                        <div class="metric-card metric-card--paid">
                            <div class="metric-title">Paid</div>
                            <div class="metric-value" id="infoPaid">&#2547; 0</div>
                        </div>

                        <div class="metric-card metric-card--due">
                            <div class="metric-title">Due</div>
                            <div class="metric-value" id="infoDue">&#2547; 0</div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="selectedInvoiceId">

                {{-- Payment fields always visible --}}
                <div id="paymentFields" class="payment-fields">
                    <div class="pn-row">
                        <div class="pn-label">Paying Amount (&#2547;)</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <input type="number" id="payingAmountInput" class="pn-input" placeholder="0" min="1" step="1">
                            <div id="payingError" class="pay-error" style="display:none;"></div>
                        </div>
                    </div>

                    <div class="pn-row">
                        <div class="pn-label">Payment Date</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <input type="date" id="payDateInput" class="pn-input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="pn-row">
                        <div class="pn-label">Pay Method</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <select id="payMethodInput" class="pn-input">
                                <option value="cash">Cash</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="pn-row">
                        <div class="pn-label">Collected By</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <input type="text" id="collectedByInput" class="pn-input" placeholder="Staff name">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pay-card__footer">
                <button type="button" id="btnPayNow" disabled>Confirm Payment</button>
            </div>
        </div>
    </div>

    <div class="paynow-right">
        <div class="due-card-panel">
            <div class="due-card-panel__header">
                <div>
                    <div class="due-card-panel__title">Due Invoice Cards</div>
                    <div class="due-card-panel__sub" id="duePanelSub">Select a patient to load due invoices.</div>
                </div>
            </div>

            <div id="dueCardsWrap" class="due-cards-wrap">
                <div class="due-empty-state">
                    <i class="fas fa-user-injured"></i>
                    <div>Select a patient from the left side.</div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<style>
    .content-wrapper { background:#f4f6f8 !important; }

    .paynow-layout {
        display:flex;
        gap:16px;
        align-items:flex-start;
        flex-wrap:wrap;
        padding:4px 0 12px;
    }

    .paynow-left {
        flex:0 0 520px;
        max-width:520px;
        width:100%;
    }

    .paynow-right {
        flex:1;
        min-width:320px;
    }

    .pay-card,
    .due-card-panel {
        background:#fff;
        border:2px solid #11b8a6;
        border-radius:8px;
        overflow:hidden;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
    }

    .pay-card__header,
    .due-card-panel__header {
        padding:12px 16px;
        border-bottom:1px solid #e7ecef;
    }

    .pay-card__header {
        font-size:15px;
        font-weight:600;
        color:#222;
    }

    .pay-card__body {
        padding:14px 16px;
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .pay-card__footer {
        padding:4px 16px 16px;
    }

    .due-card-panel__title {
        font-size:15px;
        font-weight:600;
        color:#222;
    }

    .due-card-panel__sub {
        margin-top:3px;
        font-size:12px;
        color:#6d7a82;
    }

    .pn-row {
        display:flex;
        align-items:center;
        gap:8px;
    }

    .pn-label {
        flex:0 0 138px;
        font-size:12px;
        color:#52616b;
    }

    .pn-colon {
        flex:0 0 10px;
        text-align:center;
        font-size:12px;
        color:#7d8a92;
    }

    .pn-control {
        flex:1;
    }

    .pn-input {
        width:100%;
        height:36px;
        border:1px solid #d7dee2;
        border-radius:5px;
        padding:7px 10px;
        font-size:12px;
        color:#222;
        background:#fff;
        outline:none;
        box-sizing:border-box;
    }

    .pn-input:focus {
        border-color:#11b8a6;
        box-shadow:0 0 0 2px rgba(17,184,166,.12);
    }

    .notice-box {
        background:#fff7e7;
        border:1px solid #ffd69a;
        color:#b87900;
        border-radius:5px;
        padding:8px 10px;
        font-size:12px;
    }

    .patient-box,
    .invoice-box {
        border-radius:6px;
        padding:7px 10px;
    }

    .patient-box {
        background:#eef9f6;
        border:1px solid #d7efe8;
    }

    .invoice-box {
        background:#eef8ef;
        border:1px solid #d9ead8;
    }

    .patient-box__top,
    .invoice-box__top {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        flex-wrap:wrap;
        margin-bottom:7px;
    }

    .patient-chip,
    .invoice-chip {
        display:inline-block;
        background:#11b8a6;
        color:#fff;
        font-size:10px;
        font-weight:700;
        padding:1px 7px;
        border-radius:10px;
        line-height:1.4;
    }

    .patient-name,
    .invoice-patient-name {
        margin-left:6px;
        font-size:12px;
        font-weight:600;
        color:#222;
    }

    .patient-box__meta {
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        font-size:11px;
        color:#55626b;
    }

    .btn-chip-clear {
        background:#fff;
        border:1px solid #e8a39d;
        color:#d65d53;
        border-radius:4px;
        font-size:10px;
        padding:2px 8px;
        cursor:pointer;
    }

    .invoice-metrics {
        display:flex;
        gap:6px;
        flex-wrap:wrap;
    }

    .metric-card {
        flex:1;
        min-width:80px;
        background:#fff;
        border:1px solid #dce6dc;
        border-radius:5px;
        padding:5px 6px;
        text-align:center;
    }

    .metric-card--paid .metric-value {
        color:#2f7d32;
    }

    .metric-card--due {
        background:#fff4df;
        border-color:#ffd28c;
    }

    .metric-card--due .metric-title,
    .metric-card--due .metric-value {
        color:#d77900;
    }

    .metric-title {
        font-size:10px;
        color:#7a8790;
        margin-bottom:3px;
    }

    .metric-value {
        font-size:16px;
        font-weight:700;
        color:#222;
        line-height:1.1;
    }

    .payment-fields {
        display:flex;
        flex-direction:column;
        gap:10px;
    }

    .pay-error {
        margin-top:4px;
        font-size:11px;
        color:#d93025;
    }

    #btnPayNow {
        width:100%;
        border:none;
        border-radius:5px;
        background:#11b8a6;
        color:#fff;
        font-size:14px;
        font-weight:600;
        padding:11px 14px;
        cursor:pointer;
    }

    #btnPayNow:disabled {
        background:#90d8d0;
        cursor:not-allowed;
    }

    .due-cards-wrap {
        padding:10px 12px;
        display:flex;
        flex-direction:column;
        gap:8px;
        min-height:80px;
    }

    .due-empty-state {
        grid-column:1 / -1;
        min-height:160px;
        border:1px dashed #c8d7db;
        border-radius:8px;
        background:#fbfcfd;
        color:#75828a;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:8px;
        text-align:center;
        font-size:13px;
    }

    .due-empty-state i {
        font-size:22px;
        color:#9aabb2;
    }

    .due-invoice-card {
        border:1px solid #dbe5e8;
        border-radius:7px;
        background:#fff;
        padding:9px 12px;
        cursor:pointer;
        display:flex;
        align-items:center;
        gap:10px;
    }

    .due-invoice-card:hover {
        border-color:#11b8a6;
        box-shadow:0 3px 8px rgba(0,0,0,.06);
    }

    .due-invoice-card.active {
        border-color:#11b8a6;
        box-shadow:0 0 0 2px rgba(17,184,166,.12);
        background:#f8fffd;
    }

    .due-card-top {
        display:flex;
        flex-direction:column;
        gap:3px;
        flex:0 0 auto;
        min-width:90px;
    }

    .due-bill-chip {
        display:inline-block;
        background:#0fa897;
        color:#fff;
        font-size:10px;
        font-weight:700;
        padding:2px 7px;
        border-radius:10px;
        white-space:nowrap;
    }

    .due-card-date {
        font-size:10px;
        color:#7c8991;
        white-space:nowrap;
    }

    .due-card-patient {
        font-size:12px;
        font-weight:600;
        color:#24323a;
        flex:1;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .due-card-meta {
        font-size:10px;
        color:#718088;
        display:none;
    }

    .due-mini-grid {
        display:flex;
        gap:6px;
        flex:0 0 auto;
    }

    .due-mini {
        border-radius:5px;
        padding:4px 8px;
        text-align:center;
        background:#f8fafb;
        border:1px solid #e5edef;
        min-width:52px;
    }

    .due-mini--paid .due-mini-value {
        color:#2f7d32;
    }

    .due-mini--due {
        background:#fff4df;
        border-color:#ffd28c;
    }

    .due-mini--due .due-mini-label,
    .due-mini--due .due-mini-value {
        color:#d77900;
    }

    .due-mini-label {
        font-size:9px;
        color:#7d8890;
        margin-bottom:2px;
    }

    .due-mini-value {
        font-size:11px;
        font-weight:700;
        color:#1f2b33;
        line-height:1.2;
        white-space:nowrap;
    }

    .due-card-footer {
        display:flex;
        align-items:center;
        gap:6px;
        flex:0 0 auto;
    }

    .due-type-badge {
        font-size:9px;
        font-weight:700;
        padding:2px 6px;
        border-radius:8px;
        background:#e7f7f4;
        color:#0f6c63;
    }

    .due-card-action {
        font-size:10px;
        font-weight:700;
        color:#11b8a6;
        white-space:nowrap;
    }

    .select2-container { width:100% !important; }

    .select2-container--default .select2-selection--single {
        height:36px !important;
        border:1px solid #d7dee2 !important;
        border-radius:5px !important;
        background:#fff !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height:34px !important;
        padding-left:10px !important;
        padding-right:28px !important;
        font-size:12px !important;
        color:#222 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height:34px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color:#11b8a6 !important;
        box-shadow:0 0 0 2px rgba(17,184,166,.12) !important;
    }

    .select2-dropdown {
        border:1px solid #b9e5df !important;
        border-radius:6px !important;
        box-shadow:0 8px 22px rgba(0,0,0,.08) !important;
        z-index:99999 !important;
    }

    .select2-results__option {
        padding:8px 10px !important;
        font-size:12px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background:#e8f8f5 !important;
        color:#0f6b63 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border:1px solid #d7dee2 !important;
        border-radius:5px !important;
        padding:7px 8px !important;
        font-size:12px !important;
    }

    @media (max-width: 991px) {
        .paynow-left,
        .paynow-right {
            flex:1 1 100%;
            max-width:100%;
        }
    }

    @media (max-width: 575px) {
        .pn-row {
            flex-direction:column;
            align-items:flex-start;
            gap:4px;
        }

        .pn-label,
        .pn-colon,
        .pn-control {
            flex:none;
            width:100%;
        }

        .pn-colon {
            display:none;
        }

        .metric-value {
            font-size:20px;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
toastr.options = {
    positionClass: 'toast-top-right',
    timeOut: 3000,
    progressBar: true
};

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

(function () {
    'use strict';

    let selectedPatient = null;
    let selectedInvoice = null;
    let loadedInvoices = [];

    const dueInvoiceBaseUrl = @json(url('Billing/Paynow/ajax/due-invoices'));
    const printBaseUrl = @json(url('Billing/Paynow'));

    function formatAmount(value) {
        return (parseFloat(value) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function invoiceNetBill(invoice) {
        return parseFloat(invoice.NetBill || 0);
    }

    function clearAmountError() {
        $('#payingError').hide().text('');
    }

    function showAmountError(message) {
        $('#payingError').text(message).show();
        $('#btnPayNow').prop('disabled', true);
    }

    function validateAmount() {
        if (!selectedInvoice) {
            clearAmountError();
            $('#btnPayNow').prop('disabled', true);
            return false;
        }

        const due = parseFloat(selectedInvoice.DueAmount || 0);
        const paying = parseFloat($('#payingAmountInput').val()) || 0;

        if (paying <= 0) {
            showAmountError('Enter a valid amount.');
            return false;
        }

        if (paying > due) {
            showAmountError('Cannot exceed due Tk ' + formatAmount(due));
            return false;
        }

        clearAmountError();
        $('#btnPayNow').prop('disabled', false);
        return true;
    }

    function resetSelectedInvoice(noticeText) {
        selectedInvoice = null;
        $('#selectedInvoiceId').val('');
        $('#selectedInvoiceBox').hide();
        // payment fields সব সময় visible — hide করা হবে না
        $('#payingAmountInput').val('');
        clearAmountError();
        $('#btnPayNow').prop('disabled', true);

        if (noticeText) {
            $('#invoiceRequiredNotice').show().html('<i class="fas fa-info-circle mr-1"></i> ' + noticeText);
        }
    }

    function resetAll() {
        selectedPatient = null;
        loadedInvoices = [];
        resetSelectedInvoice('Please select a patient. Due invoices will appear on the right side.');

        $('#duePanelSub').text('Select a patient to load due invoices.');
        $('#dueCardsWrap').html(
            '<div class="due-empty-state">' +
                '<i class="fas fa-user-injured"></i>' +
                '<div>Select a patient from the left side.</div>' +
            '</div>'
        );
    }

    function updatePatientBoxWithInvoices() {
    const totalBill = loadedInvoices.reduce((sum, inv) => sum + invoiceNetBill(inv), 0);
    const totalPaid = loadedInvoices.reduce((sum, inv) => sum + (parseFloat(inv.PaidAmount) || 0), 0);
    const totalDue  = loadedInvoices.reduce((sum, inv) => sum + (parseFloat(inv.DueAmount)  || 0), 0);
    const count = loadedInvoices.length;
	 $('#duePanelSub').text(
        count
        ? (selectedPatient.patientname + ' - ' + count + ' due invoice')
        : (selectedPatient.patientname + ' - no due invoice found')
    );
	
	if (count) {
        $('#infoBillNo').text('All Invoices');
        $('#infoPatientName').text(selectedPatient.patientname);
        $('#infoTotal').html('&#2547; ' + formatAmount(totalBill));
        $('#infoPaid').html('&#2547; ' + formatAmount(totalPaid));
        $('#infoDue').html('&#2547; ' + formatAmount(totalDue));
        $('#selectedInvoiceBox').stop(true, true).slideDown(180);
    }
}
	function fillPatientBox(patient) {
   
}
	
    function fillInvoiceBox(invoice) {
        const total = invoiceNetBill(invoice);
        const paid = parseFloat(invoice.PaidAmount || 0);
        const due = parseFloat(invoice.DueAmount || 0);

        $('#selectedInvoiceId').val(invoice.ID);
        $('#infoBillNo').text(invoice.BillNo || '-');
        $('#infoPatientName').text(invoice.PatientName || '-');
        $('#infoTotal').html('&#2547; ' + formatAmount(total));
        $('#infoPaid').html('&#2547; ' + formatAmount(paid));
        $('#infoDue').html('&#2547; ' + formatAmount(due));

        $('#selectedInvoiceBox').show();
        $('#invoiceRequiredNotice').hide();

        $('#payingAmountInput').val(Math.round(due)).attr('max', Math.round(due));
        // payment fields already visible — show() call unnecessary but harmless
        validateAmount();
    }

    function renderDueCards() {
        const $wrap = $('#dueCardsWrap');
        $wrap.empty();

        if (!selectedPatient) {
            $wrap.html(
                '<div class="due-empty-state">' +
                    '<i class="fas fa-user-injured"></i>' +
                    '<div>Select a patient from the left side.</div>' +
                '</div>'
            );
            return;
        }

        if (!loadedInvoices.length) {
            $wrap.html(
                '<div class="due-empty-state">' +
                    '<i class="fas fa-file-invoice-dollar"></i>' +
                    '<div>No due invoice found for this patient.</div>' +
                '</div>'
            );
            return;
        }

        loadedInvoices.forEach(function (invoice) {
            const activeClass = selectedInvoice && String(selectedInvoice.ID) === String(invoice.ID) ? 'active' : '';
            const typeBadge = invoice.InvoiceType
                ? '<span class="due-type-badge">' + escapeHtml(invoice.InvoiceType) + '</span>'
                : '<span class="due-type-badge">Due Invoice</span>';

            const html = '' +
                '<div class="due-invoice-card ' + activeClass + '" data-id="' + escapeHtml(String(invoice.ID)) + '">' +
                    '<div class="due-card-top">' +
                        '<span class="due-bill-chip">' + escapeHtml(invoice.BillNo || '-') + '</span>' +
                        '<span class="due-card-date">' + escapeHtml(invoice.PaymentDate || '-') + '</span>' +
                    '</div>' +
                    '<div class="due-card-patient">' + escapeHtml(invoice.PatientName || '-') + '</div>' +
                    '<div class="due-mini-grid">' +
                        '<div class="due-mini">' +
                            '<div class="due-mini-label">Total</div>' +
                            '<div class="due-mini-value">&#2547; ' + formatAmount(invoiceNetBill(invoice)) + '</div>' +
                        '</div>' +
                        '<div class="due-mini due-mini--paid">' +
                            '<div class="due-mini-label">Paid</div>' +
                            '<div class="due-mini-value">&#2547; ' + formatAmount(invoice.PaidAmount || 0) + '</div>' +
                        '</div>' +
                        '<div class="due-mini due-mini--due">' +
                            '<div class="due-mini-label">Due</div>' +
                            '<div class="due-mini-value">&#2547; ' + formatAmount(invoice.DueAmount || 0) + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="due-card-footer">' +
                        typeBadge +
                        '<span class="due-card-action">Click to pay</span>' +
                    '</div>' +
                '</div>';

            $wrap.append(html);
        });
    }

function setSelectedInvoice(invoiceId) {
    const invoice = loadedInvoices.find(function (item) {
        return String(item.ID) === String(invoiceId);
    });

    if (!invoice) {
        renderDueCards();
        return;
    }

    selectedInvoice = invoice;
   

    $('#dueCardsWrap .due-invoice-card').removeClass('active');
    $('#dueCardsWrap .due-invoice-card[data-id="' + invoice.ID + '"]').addClass('active');
}

    function loadDueInvoices(preferredInvoiceId) {
        if (!selectedPatient) {
            return;
        }

        resetSelectedInvoice('Loading due invoices...');
        $('#dueCardsWrap').html(
            '<div class="due-empty-state">' +
                '<i class="fas fa-spinner fa-spin"></i>' +
                '<div>Loading due invoices...</div>' +
            '</div>'
        );

        $.get(dueInvoiceBaseUrl + '/' + encodeURIComponent(selectedPatient.id), {
            patient_code: selectedPatient.patientcode || '',
            per_page: 100
        }, function (response) {
            loadedInvoices = response.data || [];
            updatePatientBoxWithInvoices();

            if (!loadedInvoices.length) {
                renderDueCards();
                resetSelectedInvoice('No due invoice found for this patient.');
                return;
            }

            const preferred = loadedInvoices.find(function (item) {
                return String(item.ID) === String(preferredInvoiceId);
            });

            renderDueCards();
            setSelectedInvoice(preferred ? preferred.ID : loadedInvoices[0].ID);
        }).fail(function (xhr) {
            loadedInvoices = [];
            renderDueCards();
            resetSelectedInvoice('Failed to load due invoices.');
            toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to load due invoices.');
        });
    }

    $('#patientSelect').select2({
        placeholder: 'Type patient name / code / mobile...',
        allowClear: true,
        minimumInputLength: 0,
        width: '100%',
        dropdownParent: $('#patientSelect').parent(),
        ajax: {
            url: '{{ route("billing.paynow.searchPatient") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (response) {
                return {
                    results: (response.data || []).map(function (patient) {
                        return {
                            id: patient.id,
                            text: patient.patientname || '',
                            data: patient
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function (item) {
            if (item.loading) {
                return $('<span style="font-size:12px;color:#0f6b63;">Searching...</span>');
            }

            if (!item.data) {
                return item.text;
            }

            const patient = item.data;
            const meta = [
                patient.patientcode || '',
                patient.mobile_no || '',
                patient.age ? (patient.age + ' yrs') : '',
                patient.gender || ''
            ].filter(Boolean).join(' / ');

            return $(
                '<div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;">' +
                    '<div>' +
                        '<div style="font-size:12px;font-weight:700;color:#173b57;">' + escapeHtml(patient.patientname || '-') + '</div>' +
                        '<div style="font-size:11px;color:#61717a;margin-top:2px;">' + escapeHtml(meta || '-') + '</div>' +
                    '</div>' +
                '</div>'
            );
        },
        templateSelection: function (item) {
            if (!item.data) {
                return item.text || '';
            }

            return (item.data.patientname || '') + (item.data.patientcode ? ' - ' + item.data.patientcode : '');
        },
        language: {
            noResults: function () { return 'No patient found'; },
            searching: function () { return 'Searching...'; }
        }
    });

    $('#patientSelect').on('select2:select', function (e) {
        selectedPatient = e.params.data.data;
        fillPatientBox(selectedPatient);
        loadDueInvoices();
    });

    $('#patientSelect').on('select2:clear select2:unselect', function () {
        resetAll();
    });


    $('#btnClearInvoice').on('click', function () {
        resetSelectedInvoice('Please select a due invoice card from the right side.');
        renderDueCards();
    });

    $(document).on('click', '.due-invoice-card', function () {
        setSelectedInvoice($(this).data('id'));
    });

    $('#payingAmountInput').on('input', function () {
        validateAmount();
    });

    $('#btnPayNow').on('click', function () {
        if (!selectedInvoice) {
            toastr.warning('Please select a due invoice card.');
            return;
        }

        if (!validateAmount()) {
            return;
        }

        const payload = {
            invoice_id: selectedInvoice.ID,
            paying_amount: parseFloat($('#payingAmountInput').val()) || 0,
            payment_date: $('#payDateInput').val(),
            payment_method: $('#payMethodInput').val(),
            collected_by: $('#collectedByInput').val()
        };

        const previousInvoiceId = selectedInvoice.ID;
        const $btn = $(this);

        $btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: '{{ route("billing.paynow.store") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
                if (!response.success) {
                    toastr.error(response.message || 'Payment failed.');
                    return;
                }

                toastr.success('Payment confirmed successfully.');
                window.open(printBaseUrl + '/' + response.invoice_id + '/print', '_blank');

                loadDueInvoices(previousInvoiceId);
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error.');
            },
            complete: function () {
                $btn.text('Confirm Payment');
                if (selectedInvoice) {
                    validateAmount();
                } else {
                    $btn.prop('disabled', true);
                }
            }
        });
    });

    resetAll();
})();
</script>
@stop