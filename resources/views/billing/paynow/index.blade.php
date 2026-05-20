@extends('adminlte::page')

@section('title', 'Billing — Pay Now')

@section('content_header')
<div class="pn-page-header">
    <div class="pn-page-header__left">
        <div class="pn-page-header__icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
            <h1 class="pn-page-header__title">Pay Now</h1>
            <p class="pn-page-header__sub">Billing &mdash; Pay Due Amount</p>
        </div>
    </div>
    <ol class="breadcrumb pn-breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
        <li class="breadcrumb-item"><a href="#">Billing</a></li>
        <li class="breadcrumb-item active">Pay Now</li>
    </ol>
</div>
@stop

@section('content')

<div class="paynow-layout">

    {{-- ── LEFT: Payment Form ────────────────────────────────── --}}
    <div class="paynow-left">
        <div class="pay-card">

            <div class="pay-card__header">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Pay Due Amount
            </div>

            <div class="pay-card__body">

                {{-- Patient Search --}}
                <div class="pn-row">
                    <div class="pn-label">Search Patient</div>
                    <div class="pn-colon">:</div>
                    <div class="pn-control">
                        <select id="patientSelect" style="width:100%;">
                            <option value="">— Type patient name / code / mobile —</option>
                        </select>
                    </div>
                </div>

                <div id="invoiceRequiredNotice" class="notice-box" style="display:none;"></div>

                {{-- Selected Invoice Summary Box --}}
                <div id="selectedInvoiceBox" class="invoice-box" style="display:none;">
                    <div class="invoice-box__top">
                        <div>
                            <span class="invoice-chip" id="infoBillNo">-</span>
                            <strong id="infoPatientName" class="invoice-patient-name">-</strong>
                        </div>
                        <button type="button" id="btnClearInvoice" class="btn-chip-clear"
                                aria-label="Clear selected invoice">
                            &times; Clear
                        </button>
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

                {{-- Payment Fields --}}
                <div id="paymentFields" class="payment-fields">

                    <div class="pn-row">
                        <div class="pn-label">Paying Amount (&#2547;)</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <input type="number" id="payingAmountInput"
                                   class="pn-input"
                                   placeholder="0"
                                   min="1"
                                   step="1"
                                   autocomplete="off">
                            <div id="payingError" class="pay-error" style="display:none;"
                                 role="alert" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="pn-row">
                        <div class="pn-label">Payment Date</div>
                        <div class="pn-colon">:</div>
                        <div class="pn-control">
                            <input type="date" id="payDateInput"
                                   class="pn-input"
                                   value="{{ date('Y-m-d') }}">
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
                            <input type="text" id="collectedByInput"
                                   class="pn-input"
                                   placeholder="Staff name"
                                   autocomplete="off">
                        </div>
                    </div>

                </div>
            </div>{{-- /.pay-card__body --}}

            <div class="pay-card__footer">
                <button type="button" id="btnPayNow" disabled
                        aria-label="Confirm payment">
                    <i class="fas fa-check-circle mr-1"></i> Confirm Payment
                </button>
            </div>

        </div>
    </div>{{-- /.paynow-left --}}

    {{-- ── RIGHT: Due Invoice Table (Bulk) ───────────────────── --}}
    <div class="paynow-right">
        <div class="due-card-panel">

            <div class="due-card-panel__header">
                <div class="due-header-row">
                    <div>
                        <div class="due-card-panel__title">
                            <i class="fas fa-file-invoice mr-2"></i>Due Invoices
                        </div>
                        <div class="due-card-panel__sub" id="duePanelSub">
                            Select a patient to load due invoices.
                        </div>
                    </div>
                    {{-- Pay All Due Button --}}
                    <button type="button" id="btnPayAllDue" class="btn-pay-all" style="display:none;" disabled>
                        <i class="fas fa-layer-group mr-1"></i>
                        Pay All Due
                        <span class="pay-all-badge" id="payAllBadge">৳ 0</span>
                    </button>
                </div>
            </div>

            {{-- Due Invoice Table --}}
            <div id="dueTableWrap">
                <div class="due-empty-state">
                    <i class="fas fa-user-injured"></i>
                    <div>Select a patient from the left side.</div>
                </div>
            </div>

        </div>
    </div>{{-- /.paynow-right --}}

</div>{{-- /.paynow-layout --}}


{{-- ══════════ BULK PAYMENT MODAL ══════════════════════════════════ --}}
<div id="bulkPayModal" class="bp-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="bpModalTitle">
    <div class="bp-modal">

        <div class="bp-modal__header">
            <div>
                <div class="bp-modal__title" id="bpModalTitle">
                    <i class="fas fa-layer-group mr-2"></i>Pay All Due
                </div>
                <div class="bp-modal__sub" id="bpModalSub">—</div>
            </div>
            <button type="button" class="bp-modal__close" id="btnBpClose" aria-label="Close">&times;</button>
        </div>

        <div class="bp-modal__body">

            {{-- Summary bar --}}
            <div class="bp-summary-bar">
                <div class="bp-summary-item">
                    <div class="bp-summary-label">Total Invoices</div>
                    <div class="bp-summary-val" id="bpTotalCount">0</div>
                </div>
                <div class="bp-summary-item bp-summary-item--due">
                    <div class="bp-summary-label">Total Due</div>
                    <div class="bp-summary-val" id="bpTotalDue">৳ 0</div>
                </div>
                <div class="bp-summary-item bp-summary-item--green">
                    <div class="bp-summary-label">You Are Paying</div>
                    <div class="bp-summary-val" id="bpPayingPreview">৳ 0</div>
                </div>
                <div class="bp-summary-item bp-summary-item--remain">
                    <div class="bp-summary-label">Remaining Due</div>
                    <div class="bp-summary-val" id="bpRemainingPreview">৳ 0</div>
                </div>
            </div>

            {{-- Amount input row --}}
            <div class="bp-amount-row">
                <div class="bp-amount-label">Enter Amount (&#2547;)</div>
                <div class="bp-amount-inputs">
                    <input type="number" id="bpAmountInput"
                           class="pn-input bp-amount-field"
                           placeholder="Enter amount to pay"
                           min="1" step="1" autocomplete="off">
                    <button type="button" id="btnBpPayFull" class="btn-bp-pay-full">
                        Pay Full ৳ <span id="bpPayFullAmt">0</span>
                    </button>
                </div>
                <div id="bpAmountError" class="pay-error" style="display:none;" role="alert"></div>
            </div>

            {{-- Distribution preview table --}}
            <div class="bp-table-wrap">
                <div class="bp-table-label">
                    <i class="fas fa-info-circle mr-1" style="color:var(--pn-teal);"></i>
                    Amount will be distributed oldest invoice first
                </div>
                <table class="bp-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill No</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Allocating</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody id="bpTableBody">
                        <tr><td colspan="7" class="bp-table-empty">—</td></tr>
                    </tbody>
                </table>
            </div>

        </div>{{-- /.bp-modal__body --}}

        <div class="bp-modal__footer">
            <button type="button" id="btnBpCancel" class="btn-bp-cancel">Cancel</button>
            <button type="button" id="btnBpConfirm" class="btn-bp-confirm" disabled>
                <i class="fas fa-check-circle mr-1"></i>
                Confirm Bulk Payment
            </button>
        </div>

    </div>
</div>


{{-- ══════════ BULK RECEIPT MODAL ══════════════════════════════════ --}}
<div id="bulkReceiptModal" class="bp-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="brModalTitle">
    <div class="bp-modal bp-modal--wide">

        <div class="bp-modal__header">
            <div>
                <div class="bp-modal__title" id="brModalTitle">
                    <i class="fas fa-print mr-2"></i>Bulk Payment Receipt
                </div>
                <div class="bp-modal__sub" id="brModalSub">—</div>
            </div>
            <button type="button" class="bp-modal__close" id="btnBrClose" aria-label="Close">&times;</button>
        </div>

        <div class="bp-modal__body" id="brReceiptBody">
            {{-- filled by JS --}}
        </div>

        <div class="bp-modal__footer">
            <button type="button" id="btnBrClose2" class="btn-bp-cancel">Close</button>
            <button type="button" id="btnBrPrint" class="btn-bp-confirm">
                <i class="fas fa-print mr-1"></i> Print Receipt
            </button>
        </div>

    </div>
</div>

@stop


{{-- ═══════════════════════════════════ CSS ═══════════════════════════════════ --}}
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<style>
/* ROOT */
:root {
    --pn-teal      : #11b8a6;
    --pn-teal-lt   : #e8f8f5;
    --pn-teal-dk   : #0d9485;
    --pn-green     : #2f7d32;
    --pn-green-lt  : #eef9f6;
    --pn-amber     : #d77900;
    --pn-amber-lt  : #fff4df;
    --pn-red       : #d93025;
    --pn-border    : #d7dee2;
    --pn-text      : #222;
    --pn-muted     : #6d7a82;
    --pn-bg        : #f4f6f8;
    --pn-white     : #ffffff;
    --pn-shadow    : 0 2px 10px rgba(0,0,0,.06);
    --pn-radius    : 6px;
}

.content-wrapper { background: var(--pn-bg) !important; }

/* PAGE HEADER */
.pn-page-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:2px 0 6px; }
.pn-page-header__left { display:flex;align-items:center;gap:12px; }
.pn-page-header__icon { width:40px;height:40px;background:var(--pn-teal);border-radius:var(--pn-radius);display:flex;align-items:center;justify-content:center;color:var(--pn-white);font-size:17px;flex-shrink:0;box-shadow:var(--pn-shadow); }
.pn-page-header__title { font-size:17px;font-weight:700;color:#1a2433;margin:0;line-height:1.3; }
.pn-page-header__sub { font-size:11px;color:var(--pn-muted);margin:0; }
.pn-breadcrumb { font-size:11px;background:transparent !important;padding:0 !important; }

/* LAYOUT */
.paynow-layout { display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;padding:4px 0 12px; }
.paynow-left   { flex:0 0 520px;max-width:520px;width:100%; }
.paynow-right  { flex:1;min-width:340px; }

/* CARDS */
.pay-card,
.due-card-panel {
    background: var(--pn-white);
    border: 2px solid var(--pn-teal);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--pn-shadow);
}

.pay-card__header,
.due-card-panel__header {
    padding: 11px 16px;
    border-bottom: 1px solid #e7ecef;
    font-size: 14px;
    font-weight: 600;
    color: var(--pn-text);
}

.pay-card__body    { padding:14px 16px;display:flex;flex-direction:column;gap:12px; }
.pay-card__footer  { padding:4px 16px 16px; }

/* DUE HEADER ROW */
.due-header-row { display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap; }
.due-card-panel__title { font-size:14px;font-weight:600;color:var(--pn-text); }
.due-card-panel__sub   { margin-top:3px;font-size:12px;color:var(--pn-muted); }

/* PAY ALL DUE BUTTON */
.btn-pay-all {
    display:flex;align-items:center;gap:6px;
    background: linear-gradient(135deg,#11b8a6,#0d9485);
    color:#fff;border:none;border-radius:6px;
    font-size:12px;font-weight:700;padding:7px 12px;
    cursor:pointer;white-space:nowrap;
    box-shadow:0 2px 8px rgba(17,184,166,.3);
    transition:opacity .15s,transform .1s;
}
.btn-pay-all:hover:not(:disabled) { opacity:.9;transform:translateY(-1px); }
.btn-pay-all:disabled { background:#90d8d0;box-shadow:none;cursor:not-allowed;transform:none; }
.pay-all-badge {
    background:rgba(255,255,255,.25);
    border-radius:10px;
    padding:1px 7px;
    font-size:11px;
}

/* FORM ROWS */
.pn-row    { display:flex;align-items:center;gap:8px; }
.pn-label  { flex:0 0 138px;font-size:12px;color:#52616b; }
.pn-colon  { flex:0 0 10px;text-align:center;font-size:12px;color:#7d8a92; }
.pn-control { flex:1; }

.pn-input {
    width:100%;
    height:36px;
    border:1px solid var(--pn-border);
    border-radius:5px;
    padding:7px 10px;
    font-size:12px;
    color:var(--pn-text);
    background:var(--pn-white);
    outline:none;
    box-sizing:border-box;
    transition:border-color .15s,box-shadow .15s;
}
.pn-input:focus { border-color:var(--pn-teal);box-shadow:0 0 0 2px rgba(17,184,166,.12); }

/* NOTICE BOX */
.notice-box {
    background:#fff7e7;
    border:1px solid #ffd69a;
    color:#b87900;
    border-radius:5px;
    padding:8px 10px;
    font-size:12px;
}

/* PATIENT / INVOICE INFO BOX */
.patient-box,
.invoice-box  { border-radius:var(--pn-radius);padding:7px 10px; }
.patient-box  { background:var(--pn-green-lt);border:1px solid #d7efe8; }
.invoice-box  { background:#eef8ef;border:1px solid #d9ead8; }

.patient-box__top,
.invoice-box__top { display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:7px; }

.patient-chip,
.invoice-chip { display:inline-block;background:var(--pn-teal);color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;line-height:1.4; }

.patient-name,
.invoice-patient-name { margin-left:6px;font-size:12px;font-weight:600;color:var(--pn-text); }

.patient-box__meta { display:flex;gap:10px;flex-wrap:wrap;font-size:11px;color:#55626b; }

.btn-chip-clear { background:var(--pn-white);border:1px solid #e8a39d;color:#d65d53;border-radius:4px;font-size:10px;padding:2px 8px;cursor:pointer;transition:background .1s; }
.btn-chip-clear:hover { background:#fdf0ef; }

/* METRIC CARDS */
.invoice-metrics { display:flex;gap:6px;flex-wrap:wrap; }
.metric-card     { flex:1;min-width:80px;background:var(--pn-white);border:1px solid #dce6dc;border-radius:5px;padding:5px 6px;text-align:center; }
.metric-card--paid .metric-value { color:var(--pn-green); }
.metric-card--due  { background:var(--pn-amber-lt);border-color:#ffd28c; }
.metric-card--due .metric-title,
.metric-card--due .metric-value { color:var(--pn-amber); }
.metric-title { font-size:10px;color:#7a8790;margin-bottom:3px; }
.metric-value { font-size:16px;font-weight:700;color:var(--pn-text);line-height:1.1; }

/* PAYMENT FIELDS */
.payment-fields { display:flex;flex-direction:column;gap:10px; }
.pay-error      { margin-top:4px;font-size:11px;color:var(--pn-red); }

/* CONFIRM BUTTON */
#btnPayNow {
    width:100%;border:none;border-radius:5px;
    background:var(--pn-teal);color:#fff;
    font-size:14px;font-weight:600;padding:11px 14px;
    cursor:pointer;transition:background .15s;
}
#btnPayNow:hover:not(:disabled) { background:var(--pn-teal-dk); }
#btnPayNow:disabled { background:#90d8d0;cursor:not-allowed; }

/* DUE TABLE WRAP */
#dueTableWrap { padding:12px; }

.due-empty-state {
    min-height:160px;border:1px dashed #c8d7db;border-radius:8px;
    background:#fbfcfd;color:#75828a;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:8px;text-align:center;font-size:13px;
}
.due-empty-state i { font-size:22px;color:#9aabb2; }

/* DUE INVOICE TABLE */
.due-inv-table { width:100%;border-collapse:collapse;font-size:12px; }
.due-inv-table thead th {
    background:#f0f5f7;color:#4a5a63;font-weight:700;font-size:11px;
    padding:7px 8px;text-align:left;border-bottom:2px solid #dce5e8;
    white-space:nowrap;
}
.due-inv-table tbody tr { border-bottom:1px solid #eaeff2;transition:background .1s; }
.due-inv-table tbody tr:hover { background:var(--pn-teal-lt); }
.due-inv-table tbody tr.active-row { background:#d4f5f2;border-color:#b0e8e3; }
.due-inv-table td { padding:7px 8px;vertical-align:middle;color:var(--pn-text); }

.inv-bill-chip { display:inline-block;background:#0fa897;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px; }
.inv-status-partial { display:inline-block;background:var(--pn-amber-lt);color:var(--pn-amber);font-size:10px;font-weight:700;padding:2px 6px;border-radius:8px; }
.inv-status-due     { display:inline-block;background:#fce8e7;color:#c0392b;font-size:10px;font-weight:700;padding:2px 6px;border-radius:8px; }
.inv-due-val        { color:var(--pn-amber);font-weight:700; }

.due-inv-table td.td-pay-btn { text-align:center; }
.btn-inv-pay {
    background:var(--pn-teal-lt);color:var(--pn-teal-dk);
    border:1px solid #a8ddd8;border-radius:4px;
    font-size:10px;font-weight:700;padding:3px 9px;
    cursor:pointer;white-space:nowrap;transition:background .12s;
}
.btn-inv-pay:hover { background:var(--pn-teal);color:#fff; }

/* SELECT2 OVERRIDES */
.select2-container { width:100% !important; }
.select2-container--default .select2-selection--single { height:36px !important;border:1px solid var(--pn-border) !important;border-radius:5px !important;background:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:34px !important;padding-left:10px !important;padding-right:28px !important;font-size:12px !important;color:var(--pn-text) !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:34px !important; }
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open  .select2-selection--single { border-color:var(--pn-teal) !important;box-shadow:0 0 0 2px rgba(17,184,166,.12) !important; }
.select2-dropdown { border:1px solid #b9e5df !important;border-radius:var(--pn-radius) !important;box-shadow:0 8px 22px rgba(0,0,0,.08) !important;z-index:99999 !important; }
.select2-results__option { padding:8px 10px !important;font-size:12px !important; }
.select2-container--default .select2-results__option--highlighted[aria-selected] { background:var(--pn-teal-lt) !important;color:#0f6b63 !important; }
.select2-container--default .select2-search--dropdown .select2-search__field { border:1px solid var(--pn-border) !important;border-radius:5px !important;padding:7px 8px !important;font-size:12px !important; }

/* ══════════ BULK PAYMENT MODAL ══════════════════════════════ */
.bp-modal-overlay {
    position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;
    display:flex;align-items:center;justify-content:center;padding:16px;
}
.bp-modal {
    background:var(--pn-white);border-radius:10px;
    box-shadow:0 12px 40px rgba(0,0,0,.18);
    width:100%;max-width:700px;max-height:90vh;
    display:flex;flex-direction:column;overflow:hidden;
}
.bp-modal--wide { max-width:820px; }

.bp-modal__header {
    display:flex;align-items:flex-start;justify-content:space-between;gap:10px;
    padding:14px 18px;border-bottom:1px solid #e5edef;flex-shrink:0;
}
.bp-modal__title { font-size:15px;font-weight:700;color:var(--pn-text); }
.bp-modal__sub   { font-size:12px;color:var(--pn-muted);margin-top:2px; }
.bp-modal__close {
    width:28px;height:28px;border:none;background:#f0f4f6;color:#5a6872;
    border-radius:50%;font-size:16px;line-height:1;cursor:pointer;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;transition:background .1s;
}
.bp-modal__close:hover { background:#e0e7ea;color:#2c3e50; }

.bp-modal__body { flex:1;overflow-y:auto;padding:16px 18px;display:flex;flex-direction:column;gap:14px; }
.bp-modal__footer {
    padding:12px 18px;border-top:1px solid #e5edef;flex-shrink:0;
    display:flex;justify-content:flex-end;gap:10px;
}

/* SUMMARY BAR */
.bp-summary-bar { display:flex;gap:8px;flex-wrap:wrap; }
.bp-summary-item {
    flex:1;min-width:110px;background:#f7fafb;border:1px solid #e0e9ec;
    border-radius:7px;padding:8px 10px;text-align:center;
}
.bp-summary-item--due   { background:var(--pn-amber-lt);border-color:#ffd28c; }
.bp-summary-item--green { background:#eef9f6;border-color:#c8eade; }
.bp-summary-item--remain { background:#fce8e7;border-color:#f5c6c4; }
.bp-summary-label { font-size:10px;color:#7a8790;margin-bottom:4px; }
.bp-summary-val   { font-size:15px;font-weight:700;color:var(--pn-text);line-height:1.1; }
.bp-summary-item--due .bp-summary-val   { color:var(--pn-amber); }
.bp-summary-item--green .bp-summary-val { color:var(--pn-green); }
.bp-summary-item--remain .bp-summary-val { color:#c0392b; }

/* AMOUNT ROW */
.bp-amount-row { display:flex;flex-direction:column;gap:6px; }
.bp-amount-label { font-size:12px;font-weight:600;color:#4a5a63; }
.bp-amount-inputs { display:flex;gap:8px;align-items:center; }
.bp-amount-field { flex:1;height:40px;font-size:14px !important;font-weight:600; }
.btn-bp-pay-full {
    white-space:nowrap;height:40px;padding:0 14px;
    background:var(--pn-amber-lt);color:var(--pn-amber);
    border:1px solid #ffd28c;border-radius:5px;font-size:12px;font-weight:700;
    cursor:pointer;transition:background .12s;
}
.btn-bp-pay-full:hover { background:#ffecc8; }

/* BP TABLE */
.bp-table-wrap { overflow-x:auto; }
.bp-table-label { font-size:11px;color:#556672;margin-bottom:6px; }
.bp-table { width:100%;border-collapse:collapse;font-size:12px; }
.bp-table thead th {
    background:#f0f5f7;color:#4a5a63;font-weight:700;font-size:11px;
    padding:7px 8px;text-align:right;border-bottom:2px solid #dce5e8;white-space:nowrap;
}
.bp-table thead th:nth-child(1),
.bp-table thead th:nth-child(2) { text-align:left; }
.bp-table tbody tr { border-bottom:1px solid #eaeff2; }
.bp-table td { padding:7px 8px;text-align:right;vertical-align:middle; }
.bp-table td:nth-child(1),
.bp-table td:nth-child(2) { text-align:left; }
.bp-table-empty { text-align:center !important;color:#8a9ba3;padding:20px !important; }

.bp-alloc-full  { color:var(--pn-teal-dk);font-weight:700; }
.bp-alloc-part  { color:var(--pn-amber);font-weight:700; }
.bp-alloc-zero  { color:#c8d4d8; }
.bp-remain-zero { color:#c0392b;font-weight:700; }
.bp-remain-part { color:var(--pn-amber); }
.bp-remain-none { color:#c8d4d8; }
.bp-row-covered { background:#edfaf7 !important; }
.bp-row-partial { background:#fffaed !important; }

/* FOOTER BUTTONS */
.btn-bp-cancel {
    border:1px solid #cdd6da;border-radius:5px;
    background:#f8fafb;color:#556672;
    font-size:13px;font-weight:600;padding:9px 20px;cursor:pointer;
    transition:background .12s;
}
.btn-bp-cancel:hover { background:#eef3f5; }
.btn-bp-confirm {
    border:none;border-radius:5px;
    background:var(--pn-teal);color:#fff;
    font-size:13px;font-weight:700;padding:9px 20px;cursor:pointer;
    transition:background .15s;box-shadow:0 2px 8px rgba(17,184,166,.25);
}
.btn-bp-confirm:hover:not(:disabled) { background:var(--pn-teal-dk); }
.btn-bp-confirm:disabled { background:#90d8d0;cursor:not-allowed;box-shadow:none; }

/* RECEIPT */
.br-receipt-header { text-align:center;margin-bottom:12px; }
.br-receipt-title  { font-size:16px;font-weight:700;color:var(--pn-text); }
.br-receipt-sub    { font-size:12px;color:var(--pn-muted);margin-top:2px; }
.br-receipt-meta   { display:flex;gap:16px;flex-wrap:wrap;background:#f7fafb;border:1px solid #e2eaed;border-radius:6px;padding:10px 14px;font-size:12px;margin-bottom:10px; }
.br-meta-item strong { color:#3a4a53; }
.br-receipt-table { width:100%;border-collapse:collapse;font-size:12px;margin-top:4px; }
.br-receipt-table th { background:#f0f5f7;color:#4a5a63;padding:7px 8px;font-weight:700;font-size:11px;border-bottom:2px solid #d8e4e8;text-align:right; }
.br-receipt-table th:nth-child(1),
.br-receipt-table th:nth-child(2) { text-align:left; }
.br-receipt-table td { padding:7px 8px;border-bottom:1px solid #ecf1f3;text-align:right;vertical-align:middle; }
.br-receipt-table td:nth-child(1),
.br-receipt-table td:nth-child(2) { text-align:left; }
.br-receipt-table tfoot td { font-weight:700;background:#f0f5f7;font-size:12px; }
.br-status-paid    { display:inline-block;background:#edf9f0;color:#2e7d32;font-size:10px;font-weight:700;padding:2px 7px;border-radius:8px; }
.br-status-partial { display:inline-block;background:var(--pn-amber-lt);color:var(--pn-amber);font-size:10px;font-weight:700;padding:2px 7px;border-radius:8px; }
.br-receipt-sign   { margin-top:24px;display:flex;justify-content:space-between;font-size:11px;color:#7a8790; }

/* ══════════ PRINT STYLES ══════════════════════════════════════ */
@media print {
    /* Hide everything first */
    body * {
        visibility: hidden !important;
    }

    /* Show only the bulk receipt modal and all its children */
    #bulkReceiptModal,
    #bulkReceiptModal * {
        visibility: visible !important;
    }

    /* Position the modal to fill the page */
    #bulkReceiptModal {
        position: fixed !important;
        inset: 0 !important;
        background: #fff !important;
        padding: 20px !important;
        z-index: 99999 !important;
        overflow: visible !important;
        display: flex !important;
        align-items: flex-start !important;
        justify-content: center !important;
    }

    /* Clean up modal chrome for print */
    #bulkReceiptModal .bp-modal {
        box-shadow: none !important;
        max-height: none !important;
        border-radius: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        border: none !important;
    }

    #bulkReceiptModal .bp-modal__header,
    #bulkReceiptModal .bp-modal__footer {
        display: none !important;
        visibility: hidden !important;
    }

    #bulkReceiptModal .bp-modal__body {
        overflow: visible !important;
        padding: 0 !important;
        flex: none !important;
    }

    #bulkReceiptModal .bp-modal-overlay {
        background: none !important;
        position: static !important;
    }
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .paynow-left,
    .paynow-right { flex:1 1 100%;max-width:100%; }
}
@media (max-width: 575px) {
    .pn-row { flex-direction:column;align-items:flex-start;gap:4px; }
    .pn-label,.pn-colon,.pn-control { flex:none;width:100%; }
    .pn-colon { display:none; }
    .metric-value { font-size:20px; }
    .bp-amount-inputs { flex-direction:column; }
    .btn-bp-pay-full { width:100%; }
}
</style>
@stop


{{-- ═══════════════════════════════════ JS ════════════════════════════════════ --}}
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true, closeButton:true };
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

const PN_URLS = {
    searchPatient  : '{{ route("billing.paynow.searchPatient") }}',
    dueInvoices    : '{{ url("Billing/Paynow/ajax/due-invoices") }}',
    store          : '{{ route("billing.paynow.store") }}',
    storeBulk      : '{{ route("billing.paynow.store-bulk") }}',
    print          : '{{ url("Billing/Paynow") }}',
};

(function () {
'use strict';

// ── State ───────────────────────────────────────────────────────
let selectedPatient = null;
let selectedInvoice = null;
let loadedInvoices  = [];
let activeXhr       = null;

// ── Helpers ─────────────────────────────────────────────────────
function fmt(value) {
    return (parseFloat(value) || 0).toLocaleString('en-US', { minimumFractionDigits:0, maximumFractionDigits:0 });
}
function esc(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
}
function invoiceNet(inv) {
    return parseFloat(inv.NetBill ?? ((inv.TotalBill || 0) - (inv.Discount || 0))) || 0;
}
function totalDue() {
    return loadedInvoices.reduce((s, inv) => s + (parseFloat(inv.DueAmount) || 0), 0);
}

// ── Amount validation (single invoice) ──────────────────────────
function clearAmountError() { $('#payingError').hide().text(''); }
function showAmountError(msg) {
    $('#payingError').text(msg).show();
    $('#btnPayNow').prop('disabled', true);
}
function validateAmount() {
    if (!selectedInvoice) { clearAmountError(); $('#btnPayNow').prop('disabled', true); return false; }
    const due    = parseFloat(selectedInvoice.DueAmount || 0);
    const paying = parseFloat($('#payingAmountInput').val()) || 0;
    if (paying <= 0)    { showAmountError('একটি valid amount লিখুন।'); return false; }
    if (paying > due)   { showAmountError('Due-এর বেশি দেওয়া যাবে না: ৳ ' + fmt(due)); return false; }
    clearAmountError();
    $('#btnPayNow').prop('disabled', false);
    return true;
}

// ── Reset helpers ─────────────────────────────────────────────────
function resetSelectedInvoice(noticeText) {
    selectedInvoice = null;
    $('#selectedInvoiceId').val('');
    $('#selectedInvoiceBox').hide();
    $('#payingAmountInput').val('').removeAttr('max');
    clearAmountError();
    $('#btnPayNow').prop('disabled', true);
    if (noticeText) {
        $('#invoiceRequiredNotice').show().html('<i class="fas fa-info-circle mr-1"></i>' + esc(noticeText));
    } else {
        $('#invoiceRequiredNotice').hide();
    }
}
function resetAll() {
    selectedPatient = null;
    loadedInvoices  = [];
    resetSelectedInvoice('Please select a patient. Due invoices will appear on the right side.');
    $('#duePanelSub').text('Select a patient to load due invoices.');
    $('#dueTableWrap').html(emptyState('fa-user-injured', 'Select a patient from the left side.'));
    $('#btnPayAllDue').hide();
}
function emptyState(icon, text) {
    return '<div class="due-empty-state"><i class="fas ' + esc(icon) + '"></i><div>' + esc(text) + '</div></div>';
}

// ── Invoice box (single) ─────────────────────────────────────────
function fillInvoiceBox(invoice) {
    $('#selectedInvoiceId').val(invoice.ID);
    $('#infoBillNo').text(invoice.BillNo || '-');
    $('#infoPatientName').text(invoice.PatientName || '-');
    $('#infoTotal').html('&#2547; ' + fmt(invoiceNet(invoice)));
    $('#infoPaid').html('&#2547; '  + fmt(invoice.PaidAmount || 0));
    $('#infoDue').html('&#2547; '   + fmt(invoice.DueAmount  || 0));
    $('#selectedInvoiceBox').show();
    $('#invoiceRequiredNotice').hide();
    const due = parseFloat(invoice.DueAmount || 0);
    $('#payingAmountInput').val(Math.round(due)).attr('max', Math.round(due));
    validateAmount();
}

// ── Patient summary info ──────────────────────────────────────────
function updatePatientInfo() {
    const count = loadedInvoices.length;
    const name  = selectedPatient ? (selectedPatient.patientname || '') : '';
    $('#duePanelSub').text(
        count
            ? (name + ' — ' + count + ' due invoice' + (count > 1 ? 's' : ''))
            : (name + ' — no due invoice found')
    );

    if (count) {
        const due = totalDue();
        $('#btnPayAllDue')
            .show()
            .prop('disabled', false);
        $('#payAllBadge').text('৳ ' + fmt(due));

        // Summary in left box
        const totalBill = loadedInvoices.reduce((s, inv) => s + invoiceNet(inv), 0);
        const totalPaid = loadedInvoices.reduce((s, inv) => s + (parseFloat(inv.PaidAmount) || 0), 0);
        $('#infoBillNo').text('All Invoices');
        $('#infoPatientName').text(name);
        $('#infoTotal').html('&#2547; ' + fmt(totalBill));
        $('#infoPaid').html('&#2547; '  + fmt(totalPaid));
        $('#infoDue').html('&#2547; '   + fmt(due));
        $('#selectedInvoiceBox').stop(true, true).slideDown(180);
        $('#invoiceRequiredNotice').hide();
    } else {
        $('#btnPayAllDue').hide();
    }
}

// ── Render due invoices TABLE ────────────────────────────────────
function renderDueTable() {
    const $wrap = $('#dueTableWrap');
    $wrap.empty();

    if (!selectedPatient) {
        $wrap.html(emptyState('fa-user-injured', 'Select a patient from the left side.'));
        return;
    }
    if (!loadedInvoices.length) {
        $wrap.html(emptyState('fa-file-invoice-dollar', 'No due invoice found for this patient.'));
        return;
    }

    let rows = '';
    loadedInvoices.forEach(function (inv, idx) {
        const activeClass = (selectedInvoice && String(selectedInvoice.ID) === String(inv.ID)) ? 'active-row' : '';
        const statusBadge = parseFloat(inv.PaidAmount || 0) > 0
            ? '<span class="inv-status-partial">Partial</span>'
            : '<span class="inv-status-due">Unpaid</span>';
        rows += '<tr class="' + activeClass + '" data-id="' + esc(String(inv.ID)) + '">' +
            '<td>' + (idx+1) + '</td>' +
            '<td><span class="inv-bill-chip">' + esc(inv.BillNo || '-') + '</span></td>' +
            '<td>' + esc(inv.PatientName || '-') + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(invoiceNet(inv)) + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(inv.PaidAmount || 0) + '</td>' +
            '<td style="text-align:right" class="inv-due-val">৳ ' + fmt(inv.DueAmount || 0) + '</td>' +
            '<td style="text-align:center">' + statusBadge + '</td>' +
            '<td class="td-pay-btn"><button class="btn-inv-pay" data-id="' + esc(String(inv.ID)) + '">Pay</button></td>' +
            '</tr>';
    });

    const table = '<div style="overflow-x:auto"><table class="due-inv-table">' +
        '<thead><tr>' +
        '<th>#</th><th>Bill No</th><th>Patient</th>' +
        '<th style="text-align:right">Total</th>' +
        '<th style="text-align:right">Paid</th>' +
        '<th style="text-align:right">Due</th>' +
        '<th style="text-align:center">Status</th>' +
        '<th style="text-align:center">Action</th>' +
        '</tr></thead>' +
        '<tbody>' + rows + '</tbody>' +
        '</table></div>';

    $wrap.html(table);
}

// ── Set active invoice ────────────────────────────────────────────
function setSelectedInvoice(invoiceId) {
    const invoice = loadedInvoices.find(item => String(item.ID) === String(invoiceId));
    if (!invoice) { renderDueTable(); return; }
    selectedInvoice = invoice;
    fillInvoiceBox(invoice);
    $('#dueTableWrap tr').removeClass('active-row');
    $('#dueTableWrap tr[data-id="' + invoice.ID + '"]').addClass('active-row');
}

// ── Load due invoices ─────────────────────────────────────────────
function loadDueInvoices(preferredInvoiceId) {
    if (!selectedPatient) return;
    if (activeXhr) { activeXhr.abort(); activeXhr = null; }
    resetSelectedInvoice('Loading due invoices...');
    $('#dueTableWrap').html(emptyState('fa-spinner fa-spin', 'Loading due invoices...'));
    $('#btnPayAllDue').hide();

    activeXhr = $.get(
        PN_URLS.dueInvoices + '/' + encodeURIComponent(selectedPatient.id),
        { patient_code: selectedPatient.patientcode || '', per_page: 100 },
        function (res) {
            activeXhr = null;
            if (res && res.success === false) {
                toastr.error(res.message || 'Failed to load due invoices.');
                loadedInvoices = [];
                renderDueTable(); resetSelectedInvoice('Failed to load due invoices.'); return;
            }
            loadedInvoices = res.data || [];
            updatePatientInfo();
            if (!loadedInvoices.length) {
                renderDueTable(); resetSelectedInvoice('No due invoice found for this patient.'); return;
            }
            renderDueTable();
            const preferred = loadedInvoices.find(item => String(item.ID) === String(preferredInvoiceId));
            setSelectedInvoice(preferred ? preferred.ID : loadedInvoices[0].ID);
        }
    ).fail(function (xhr, status) {
        if (status === 'abort') return;
        activeXhr = null;
        loadedInvoices = [];
        renderDueTable();
        resetSelectedInvoice('Failed to load due invoices.');
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to load due invoices.');
    });
}

// ═══════════════════════════════════════════════════════════════
// BULK PAYMENT MODAL LOGIC
// ═══════════════════════════════════════════════════════════════

// Distribute amount oldest-first (same logic as backend — for preview)
function distributePreview(invoices, payingAmount) {
    let remaining = payingAmount;
    return invoices.map(function (inv) {
        const due      = parseFloat(inv.DueAmount || 0);
        const allocate = Math.min(remaining, due);
        remaining      = Math.max(0, remaining - allocate);
        return {
            id        : inv.ID,
            bill_no   : inv.BillNo || '-',
            total     : invoiceNet(inv),
            paid      : parseFloat(inv.PaidAmount || 0),
            due       : due,
            allocate  : allocate,
            newDue    : Math.max(0, due - allocate),
        };
    });
}

function renderBpTable(amount) {
    const rows = distributePreview(loadedInvoices, amount);
    let html = '';
    rows.forEach(function (r, idx) {
        let allocClass, remainClass, rowClass = '';
        if (r.allocate >= r.due)        { allocClass = 'bp-alloc-full';  remainClass = 'bp-remain-zero'; rowClass = 'bp-row-covered'; }
        else if (r.allocate > 0)        { allocClass = 'bp-alloc-part';  remainClass = 'bp-remain-part'; rowClass = 'bp-row-partial'; }
        else                            { allocClass = 'bp-alloc-zero';  remainClass = 'bp-remain-none'; }

        html += '<tr class="' + rowClass + '">' +
            '<td>' + (idx+1) + '</td>' +
            '<td>' + esc(r.bill_no) + '</td>' +
            '<td>৳ ' + fmt(r.total)    + '</td>' +
            '<td>৳ ' + fmt(r.paid)     + '</td>' +
            '<td>৳ ' + fmt(r.due)      + '</td>' +
            '<td class="' + allocClass   + '">৳ ' + fmt(r.allocate) + '</td>' +
            '<td class="' + remainClass  + '">৳ ' + fmt(r.newDue)   + '</td>' +
            '</tr>';
    });
    $('#bpTableBody').html(html || '<tr><td colspan="7" class="bp-table-empty">—</td></tr>');
}

function updateBpSummary() {
    const amount    = parseFloat($('#bpAmountInput').val()) || 0;
    const totalDueV = totalDue();
    const remaining = Math.max(0, totalDueV - amount);
    $('#bpPayingPreview').text('৳ ' + fmt(amount));
    $('#bpRemainingPreview').text('৳ ' + fmt(remaining));
    renderBpTable(amount);

    // Validate
    if (amount <= 0) {
        $('#bpAmountError').show().text('একটি valid amount লিখুন।');
        $('#btnBpConfirm').prop('disabled', true);
    } else if (amount > totalDueV) {
        $('#bpAmountError').show().text('Total due-এর বেশি দেওয়া যাবে না: ৳ ' + fmt(totalDueV));
        $('#btnBpConfirm').prop('disabled', true);
    } else {
        $('#bpAmountError').hide();
        $('#btnBpConfirm').prop('disabled', false);
    }
}

function openBulkModal() {
    if (!selectedPatient || !loadedInvoices.length) return;

    const name     = selectedPatient.patientname || '';
    const code     = selectedPatient.patientcode || '';
    const totalDueV = totalDue();
    const count    = loadedInvoices.length;

    $('#bpModalSub').text(name + (code ? ' — ' + code : '') + ' | ' + count + ' invoice(s)');
    $('#bpTotalCount').text(count);
    $('#bpTotalDue').text('৳ ' + fmt(totalDueV));
    $('#bpPayingPreview').text('৳ 0');
    $('#bpRemainingPreview').text('৳ ' + fmt(totalDueV));
    $('#bpPayFullAmt').text(fmt(totalDueV));
    $('#bpAmountInput').val('');
    $('#bpAmountError').hide();
    $('#btnBpConfirm').prop('disabled', true);
    $('#bpTableBody').html('<tr><td colspan="7" class="bp-table-empty">Enter amount above to preview distribution.</td></tr>');

    $('#bulkPayModal').fadeIn(160);
    setTimeout(function () { $('#bpAmountInput').focus(); }, 200);
}

function closeBulkModal() {
    $('#bulkPayModal').fadeOut(140);
}

// ── Open bulk modal
$('#btnPayAllDue').on('click', openBulkModal);
$('#btnBpClose, #btnBpCancel').on('click', closeBulkModal);
$('#bulkPayModal').on('click', function (e) {
    if ($(e.target).is('#bulkPayModal')) closeBulkModal();
});

// ── "Pay Full" shortcut
$('#btnBpPayFull').on('click', function () {
    $('#bpAmountInput').val(Math.round(totalDue()));
    updateBpSummary();
});

// ── Live preview on amount input
$('#bpAmountInput').on('input', updateBpSummary);

// ── Confirm bulk payment
$('#btnBpConfirm').on('click', function () {
    const amount = parseFloat($('#bpAmountInput').val()) || 0;
    if (amount <= 0 || amount > totalDue()) return;

    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

    $.ajax({
        url         : PN_URLS.storeBulk,
        method      : 'POST',
        contentType : 'application/json',
        data        : JSON.stringify({
            patient_code   : selectedPatient.patientcode || '',
            paying_amount  : amount,
            payment_date   : $('#payDateInput').val(),
            payment_method : $('#payMethodInput').val(),
            collected_by   : $('#collectedByInput').val().trim(),
            invoice_ids    : loadedInvoices.map(inv => inv.ID),
        }),
        success: function (res) {
            if (!res.success) {
                toastr.error(res.message || 'Bulk payment failed.');
                return;
            }
            closeBulkModal();
            toastr.success(res.invoice_count + ' invoice(s) updated successfully!');
            openBulkReceipt(res);
            loadDueInvoices();
        },
        error: function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Server error. Please try again.');
        },
        complete: function () {
            $btn.html('<i class="fas fa-check-circle mr-1"></i> Confirm Bulk Payment');
            updateBpSummary();
        },
    });
});

// ═══════════════════════════════════════════════════════════════
// BULK RECEIPT MODAL
// ═══════════════════════════════════════════════════════════════
function openBulkReceipt(res) {
    const name   = selectedPatient ? (selectedPatient.patientname || '-') : '-';
    const code   = selectedPatient ? (selectedPatient.patientcode || '-') : '-';
    const mobile = selectedPatient ? (selectedPatient.mobile_no   || '-') : '-';
    const date   = $('#payDateInput').val();
    const method = $('#payMethodInput').val().replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
    const by     = $('#collectedByInput').val().trim() || '-';

    let rows = '';
    res.invoices.forEach(function (inv, idx) {
        const statusHtml = inv.status === 'paid'
            ? '<span class="br-status-paid">PAID</span>'
            : '<span class="br-status-partial">PARTIAL</span>';
        rows += '<tr>' +
            '<td>' + (idx+1) + '</td>' +
            '<td>' + esc(inv.bill_no) + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(inv.total_bill - (inv.discount || 0)) + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(inv.allocated) + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(inv.new_paid)  + '</td>' +
            '<td style="text-align:right">৳ ' + fmt(inv.new_due)   + '</td>' +
            '<td style="text-align:center">' + statusHtml + '</td>' +
            '</tr>';
    });

    const totalAllocated = res.invoices.reduce((s, inv) => s + inv.allocated, 0);
    const totalNewDue    = res.invoices.reduce((s, inv) => s + inv.new_due,   0);

    const html =
        '<div class="br-receipt-header">' +
            '<div class="br-receipt-title">Bulk Payment Receipt</div>' +
            '<div class="br-receipt-sub">' + esc(name) + ' — ' + esc(code) + '</div>' +
        '</div>' +
        '<div class="br-receipt-meta">' +
            '<div class="br-meta-item"><strong>Date:</strong> ' + esc(date) + '</div>' +
            '<div class="br-meta-item"><strong>Patient:</strong> ' + esc(name) + '</div>' +
            '<div class="br-meta-item"><strong>Code:</strong> ' + esc(code) + '</div>' +
            '<div class="br-meta-item"><strong>Mobile:</strong> ' + esc(mobile) + '</div>' +
            '<div class="br-meta-item"><strong>Method:</strong> ' + esc(method) + '</div>' +
            '<div class="br-meta-item"><strong>Collected By:</strong> ' + esc(by) + '</div>' +
            '<div class="br-meta-item"><strong>Invoices Paid:</strong> ' + res.invoice_count + '</div>' +
        '</div>' +
        '<div style="overflow-x:auto">' +
        '<table class="br-receipt-table">' +
            '<thead><tr>' +
            '<th>#</th><th>Bill No</th>' +
            '<th style="text-align:right">Net Bill</th>' +
            '<th style="text-align:right">Allocated</th>' +
            '<th style="text-align:right">Total Paid</th>' +
            '<th style="text-align:right">Remaining Due</th>' +
            '<th style="text-align:center">Status</th>' +
            '</tr></thead>' +
            '<tbody>' + rows + '</tbody>' +
            '<tfoot><tr>' +
            '<td colspan="3" style="text-align:right">Total</td>' +
            '<td style="text-align:right">৳ ' + fmt(totalAllocated) + '</td>' +
            '<td></td>' +
            '<td style="text-align:right">৳ ' + fmt(totalNewDue) + '</td>' +
            '<td></td>' +
            '</tr></tfoot>' +
        '</table></div>' +
        '<div class="br-receipt-sign">' +
            '<div>Patient Signature: ___________________</div>' +
            '<div>Cashier: ' + esc(by) + '</div>' +
        '</div>';

    $('#brReceiptBody').html(html);
    $('#brModalSub').text(esc(name) + ' — ৳ ' + fmt(totalAllocated) + ' paid across ' + res.invoice_count + ' invoice(s)');
    $('#bulkReceiptModal').fadeIn(180);
}

$('#btnBrClose, #btnBrClose2').on('click', function () { $('#bulkReceiptModal').fadeOut(140); });
$('#bulkReceiptModal').on('click', function (e) {
    if ($(e.target).is('#bulkReceiptModal')) $('#bulkReceiptModal').fadeOut(140);
});
$('#btnBrPrint').on('click', function () { window.print(); });

// ═══════════════════════════════════════════════════════════════
// SELECT2 — Patient search
// ═══════════════════════════════════════════════════════════════
$('#patientSelect').select2({
    placeholder        : 'Type patient name / code / mobile...',
    allowClear         : true,
    minimumInputLength : 0,
    width              : '100%',
    dropdownParent     : $('#patientSelect').parent(),
    ajax: {
        url      : PN_URLS.searchPatient,
        dataType : 'json',
        delay    : 250,
        data     : params => ({ q: params.term || '' }),
        processResults: function (res) {
            return {
                results: (res.data || []).map(p => ({ id: p.id, text: p.patientname || '', data: p })),
            };
        },
        cache: true,
    },
    templateResult: function (item) {
        if (item.loading) return $('<span style="font-size:12px;color:#0f6b63;">Searching...</span>');
        if (!item.data) return item.text;
        const p    = item.data;
        const meta = [p.patientcode||'', p.mobile_no||'', p.age?(p.age+' yrs'):'', p.gender||''].filter(Boolean).join(' / ');
        return $('<div style="display:flex;gap:10px;align-items:flex-start;">' +
            '<div><div style="font-size:12px;font-weight:700;color:#173b57;">' + esc(p.patientname||'-') + '</div>' +
            '<div style="font-size:11px;color:#61717a;margin-top:2px;">' + esc(meta||'-') + '</div></div></div>');
    },
    templateSelection: function (item) {
        if (!item.data) return item.text || '';
        const p = item.data;
        return (p.patientname||'') + (p.patientcode ? ' — ' + p.patientcode : '');
    },
    language: { noResults: () => 'No patient found', searching: () => 'Searching...' },
});

$('#patientSelect').on('select2:select', function (e) {
    selectedPatient = e.params.data.data;
    loadDueInvoices();
});
$('#patientSelect').on('select2:clear select2:unselect', function () { resetAll(); });

// ── Events ───────────────────────────────────────────────────────
$('#btnClearInvoice').on('click', function () {
    resetSelectedInvoice('Please select a due invoice row from the right side.');
    renderDueTable();
});

// Click on table row → select invoice
$(document).on('click', '#dueTableWrap tr[data-id]', function () {
    setSelectedInvoice($(this).data('id'));
});
// Click on Pay button in row
$(document).on('click', '.btn-inv-pay', function (e) {
    e.stopPropagation();
    setSelectedInvoice($(this).data('id'));
    $('#payingAmountInput').focus();
});

$('#payingAmountInput').on('input', validateAmount);

// ── SINGLE PAYMENT SUBMIT ─────────────────────────────────────────
$('#btnPayNow').on('click', function () {
    if (!selectedInvoice) { toastr.warning('Please select a due invoice row.'); return; }
    if (!validateAmount()) return;

    const previousInvoiceId = selectedInvoice.ID;
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');

    $.ajax({
        url         : PN_URLS.store,
        method      : 'POST',
        contentType : 'application/json',
        data        : JSON.stringify({
            invoice_id    : selectedInvoice.ID,
            paying_amount : parseFloat($('#payingAmountInput').val()) || 0,
            payment_date  : $('#payDateInput').val(),
            payment_method: $('#payMethodInput').val(),
            collected_by  : $('#collectedByInput').val().trim(),
        }),
        success: function (res) {
            if (!res.success) { toastr.error(res.message || 'Payment failed.'); return; }
            toastr.success('Payment confirmed successfully!');
            window.open(PN_URLS.print + '/' + res.invoice_id + '/print', '_blank');
            loadDueInvoices(previousInvoiceId);
        },
        error: function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Server error. Please try again.');
        },
        complete: function () {
            $btn.html('<i class="fas fa-check-circle mr-1"></i> Confirm Payment');
            if (selectedInvoice) { validateAmount(); } else { $btn.prop('disabled', true); }
        },
    });
});

// ── Init ──────────────────────────────────────────────────────────
resetAll();

})();
</script>
@stop