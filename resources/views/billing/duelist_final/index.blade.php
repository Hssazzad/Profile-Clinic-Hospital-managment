@extends('adminlte::page')

@section('title', 'Due List — Professor Clinic')

@section('content_header')
<div class="dl-page-header">
    <div class="dl-page-header__left">
        <div class="dl-page-header__icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div>
            <h1 class="dl-page-header__title">Due Invoice List</h1>
            <p class="dl-page-header__sub">Due Invoice List &mdash; All Patients</p>
        </div>
    </div>
    <ol class="breadcrumb dl-breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
        <li class="breadcrumb-item"><a href="#">Billing</a></li>
        <li class="breadcrumb-item active">Due List</li>
    </ol>
</div>
@stop

@section('content')

{{-- SUMMARY STRIP --}}
<div class="dl-summary-strip">
    <div class="dl-summary-card dl-summary-card--due">
        <div class="dl-summary-card__icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Total Due</div>
            <div class="dl-summary-card__value" id="sumTotalDue">&#2547; 0</div>
            <div class="dl-summary-card__count" id="sumTotalCount">0 patients with due</div>
        </div>
    </div>
    <div class="dl-summary-card dl-summary-card--paid">
        <div class="dl-summary-card__icon"><i class="fas fa-check-circle"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Paid (this page)</div>
            <div class="dl-summary-card__value" id="sumTotalPaid">&#2547; 0</div>
            <div class="dl-summary-card__count">current page</div>
        </div>
    </div>
    <div class="dl-summary-card dl-summary-card--total">
        <div class="dl-summary-card__icon"><i class="fas fa-file-alt"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Total Bill (this page)</div>
            <div class="dl-summary-card__value" id="sumTotalBill">&#2547; 0</div>
            <div class="dl-summary-card__count">current page</div>
        </div>
    </div>
</div>

{{-- FILTER + TOOLBAR --}}
<div class="dl-toolbar">
    <div class="dl-toolbar__search">
        <span class="dl-toolbar__search-icon"><i class="fas fa-search"></i></span>
        <input type="text" id="listSearchInput"
               placeholder="Name / Bill No / Patient Code (min 3 char)"
               class="dl-search-input"
               autocomplete="off">
        <button class="dl-search-clear" id="btnClearSearch" style="display:none;" aria-label="Clear search">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="dl-toolbar__right">
        <label class="dl-toggle-label" title="Show all patients including paid">
            <input type="checkbox" id="showAllChk" class="dl-toggle-chk">
            <span class="dl-toggle-track"></span>
            <span class="dl-toggle-text">Show All</span>
        </label>
        <div class="dl-export-group">
            <button id="btnExportExcel" class="dl-btn dl-btn--excel" title="Export current page to Excel">
                <i class="fas fa-file-excel"></i><span>Excel (Page)</span>
            </button>
            <button id="btnExportCsv" class="dl-btn dl-btn--csv" title="Export current page to CSV">
                <i class="fas fa-file-csv"></i><span>CSV (Page)</span>
            </button>
        </div>
    </div>
</div>

{{-- TABLE PANEL --}}
<div class="dl-panel">
    <div class="dl-panel__header">
        <div class="dl-panel__title">
            <span class="dl-panel__dot"></span>
            Due Invoices
        </div>
        <div class="dl-panel__meta" id="activeTabDueSummary"></div>
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="dl-table-wrap">
        <table class="dl-table" id="dueTable">
            <thead>
                <tr>
                    <th class="dl-th dl-th--center dl-th--num">#</th>
                    <th class="dl-th">Bill No</th>
                    <th class="dl-th">Patient Info</th>
                    <th class="dl-th dl-th--center">Date</th>
                    <th class="dl-th dl-th--right">Total (&#2547;)</th>
                    <th class="dl-th dl-th--right">Paid (&#2547;)</th>
                    <th class="dl-th dl-th--right dl-th--due">Due (&#2547;)</th>
                    <th class="dl-th dl-th--center">Status</th>
                    <th class="dl-th dl-th--center">Action</th>
                </tr>
            </thead>
            <tbody id="dueTableBody">
                <tr>
                    <td colspan="9" class="dl-td--loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading
                    </td>
                </tr>
            </tbody>
            <tfoot id="dueTableFoot" style="display:none;">
                <tr class="dl-tfoot-row">
                    <td colspan="4" class="dl-tfoot-label">Page Total :</td>
                    <td class="dl-tfoot-val dl-tfoot-val--total" id="footTotal">&#2547; 0</td>
                    <td class="dl-tfoot-val dl-tfoot-val--paid"  id="footPaid">&#2547; 0</td>
                    <td class="dl-tfoot-val dl-tfoot-val--due"   id="footDue">&#2547; 0</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="dl-card-list" id="dueCardList" style="display:none;"></div>

    {{-- PAGINATION --}}
    <div class="dl-pagination" id="paginationWrap"></div>
</div>

{{-- PATIENT DETAILS MODAL --}}
<div class="modal fade" id="patientDetailsModal" tabindex="-1" role="dialog" aria-labelledby="patientModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content dl-modal-content">
            <div class="modal-header dl-modal-header">
                <h5 class="modal-title dl-modal-title" id="patientModalTitle">
                    <i class="fas fa-user-circle mr-2"></i>
                    <span id="modalPatientName">Patient Ledger &amp; Details</span>
                </h5>
                <button type="button" class="close dl-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body dl-modal-body">

                {{-- Bill List --}}
                <div class="dl-modal-section">
                    <h6 class="dl-modal-section__title">
                        <i class="fas fa-file-invoice mr-1"></i> Bill List
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm dl-modal-table">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th class="text-right">Total Bill</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right text-danger">Due</th>
                                    <th class="text-center dl-th-action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="modalBillBody">
                                <tr><td colspan="5" class="dl-modal-loading">Loading bills...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="dl-modal-total dl-modal-total--due">
                        Total Due: <span id="modalTotalDue">&#2547; 0</span>
                    </div>
                </div>

                {{-- Payment List --}}
                <div class="dl-modal-section">
                    <h6 class="dl-modal-section__title">
                        <i class="fas fa-money-bill-wave mr-1"></i> Payment List
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm dl-modal-table">
                            <thead>
                                <tr>
                                    <th>Payment Date</th>
                                    <th class="text-right">Payment Amount</th>
                                    <th>Received By</th>
                                    <th class="text-center dl-th-action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="modalPaymentBody">
                                <tr><td colspan="4" class="dl-modal-loading">Loading payments...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="dl-modal-total dl-modal-total--paid">
                        Total Payment: <span id="modalTotalPayment">&#2547; 0</span>
                    </div>
                </div>

            </div>

            <div class="modal-footer dl-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm dl-btn-close" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close &amp; Go Back
                </button>
            </div>
        </div>
    </div>
</div>

@stop


{{-- ═══════════════════════════════════════════════ --}}
{{-- CSS                                            --}}
{{-- ═══════════════════════════════════════════════ --}}
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<style>
/* ROOT TOKENS */
:root {
    --gov-navy      : #0a2342;
    --gov-navy-mid  : #163566;
    --gov-navy-lt   : #1e4a8a;
    --gov-amber     : #c8952a;
    --gov-amber-lt  : #f5c842;
    --gov-red       : #c0392b;
    --gov-green     : #1a7a4a;
    --gov-green-lt  : #e8f5ee;
    --gov-red-lt    : #fdf0ef;
    --gov-bg        : #f0f3f7;
    --gov-white     : #ffffff;
    --gov-border    : #cdd4de;
    --gov-text      : #1a2433;
    --gov-muted     : #5f6d7e;
    --gov-line      : #e2e8f0;
    --gov-shadow    : 0 2px 8px rgba(10,35,66,.10);
    --gov-shadow-lg : 0 4px 20px rgba(10,35,66,.15);
    --radius        : 4px;
    --radius-lg     : 6px;
    --font-mono     : 'JetBrains Mono', monospace;
}
.content-wrapper { background: var(--gov-bg) !important; }

/* PAGE HEADER */
.dl-page-header { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:2px 0 6px; }
.dl-page-header__left { display:flex;align-items:center;gap:12px; }
.dl-page-header__icon { width:42px;height:42px;background:var(--gov-navy);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;color:var(--gov-amber-lt);font-size:18px;flex-shrink:0;box-shadow:var(--gov-shadow); }
.dl-page-header__title { font-size:17px;font-weight:700;color:var(--gov-navy);margin:0;line-height:1.3; }
.dl-page-header__sub { font-size:11px;color:var(--gov-muted);margin:0;letter-spacing:.3px; }
.dl-breadcrumb { font-size:11px;background:transparent !important;padding:0 !important; }
.dl-breadcrumb .breadcrumb-item a { color:var(--gov-navy-lt); }
.dl-breadcrumb .breadcrumb-item.active { color:var(--gov-amber); }
.dl-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color:var(--gov-border); }

/* SUMMARY STRIP */
.dl-summary-strip { display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:14px; }
.dl-summary-card { background:var(--gov-white);border-radius:var(--radius-lg);padding:14px 16px;display:flex;align-items:center;gap:14px;box-shadow:var(--gov-shadow);border-top:3px solid transparent;transition:box-shadow .2s; }
.dl-summary-card:hover { box-shadow:var(--gov-shadow-lg); }
.dl-summary-card--due   { border-top-color:var(--gov-red); }
.dl-summary-card--paid  { border-top-color:var(--gov-green); }
.dl-summary-card--total { border-top-color:var(--gov-navy-lt); }
.dl-summary-card__icon { width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.dl-summary-card--due   .dl-summary-card__icon { background:var(--gov-red-lt);color:var(--gov-red); }
.dl-summary-card--paid  .dl-summary-card__icon { background:var(--gov-green-lt);color:var(--gov-green); }
.dl-summary-card--total .dl-summary-card__icon { background:#e8edf5;color:var(--gov-navy-lt); }
.dl-summary-card__label { font-size:11px;color:var(--gov-muted);margin-bottom:2px; }
.dl-summary-card__value { font-family:var(--font-mono);font-size:18px;font-weight:700;color:var(--gov-text);line-height:1.2; }
.dl-summary-card--due  .dl-summary-card__value { color:var(--gov-red); }
.dl-summary-card--paid .dl-summary-card__value { color:var(--gov-green); }
.dl-summary-card__count { font-size:10px;color:var(--gov-muted);margin-top:2px; }

/* TOOLBAR */
.dl-toolbar { background:var(--gov-white);border:1px solid var(--gov-border);border-radius:var(--radius-lg);padding:10px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;box-shadow:var(--gov-shadow); }
.dl-toolbar__search { flex:1;min-width:180px;position:relative;display:flex;align-items:center; }
.dl-toolbar__search-icon { position:absolute;left:10px;color:var(--gov-muted);font-size:12px;pointer-events:none; }
.dl-search-input { width:100%;height:34px;border:1px solid var(--gov-border);border-radius:var(--radius);padding:0 32px 0 30px;font-size:12px;color:var(--gov-text);background:#f8fafc;outline:none;transition:border-color .15s,box-shadow .15s; }
.dl-search-input:focus { border-color:var(--gov-navy-lt);box-shadow:0 0 0 3px rgba(30,74,138,.12);background:var(--gov-white); }
.dl-search-clear { position:absolute;right:8px;background:none;border:none;color:var(--gov-muted);cursor:pointer;font-size:11px;padding:0; }
.dl-toolbar__right { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }

/* Toggle */
.dl-toggle-label { display:flex;align-items:center;gap:7px;cursor:pointer;margin:0; }
.dl-toggle-chk { display:none; }
.dl-toggle-track { width:34px;height:18px;background:var(--gov-border);border-radius:9px;position:relative;transition:background .2s;flex-shrink:0; }
.dl-toggle-track::after { content:'';position:absolute;top:3px;left:3px;width:12px;height:12px;border-radius:50%;background:var(--gov-white);transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2); }
.dl-toggle-chk:checked + .dl-toggle-track { background:var(--gov-navy-lt); }
.dl-toggle-chk:checked + .dl-toggle-track::after { transform:translateX(16px); }
.dl-toggle-text { font-size:11px;color:var(--gov-muted);white-space:nowrap; }

.dl-export-group { display:flex;gap:6px; }
.dl-btn { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border:none;border-radius:var(--radius);font-size:11px;font-weight:600;cursor:pointer;transition:opacity .15s,transform .1s;white-space:nowrap; }
.dl-btn:active { transform:scale(.97); }
.dl-btn--excel { background:#1e6b3f;color:#fff; }
.dl-btn--csv   { background:var(--gov-amber);color:#fff; }

/* PANEL */
.dl-panel { background:var(--gov-white);border:1px solid var(--gov-border);border-radius:var(--radius-lg);box-shadow:var(--gov-shadow);overflow:hidden; }
.dl-panel__header { background:var(--gov-navy);padding:9px 14px;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap; }
.dl-panel__title { display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--gov-white); }
.dl-panel__dot { width:8px;height:8px;background:var(--gov-amber-lt);border-radius:50%;flex-shrink:0; }
.dl-panel__meta { font-size:11px;color:rgba(255,255,255,.7);font-family:var(--font-mono); }

/* TABLE */
.dl-table-wrap { overflow-x:auto; }
.dl-table { width:100%;border-collapse:collapse;font-size:12px; }
.dl-th { background:#f0f4f9;border:1px solid var(--gov-border);padding:8px 10px;font-size:11px;font-weight:700;color:var(--gov-navy);white-space:nowrap;letter-spacing:.2px;text-transform:uppercase; }
.dl-th--center { text-align:center; }
.dl-th--right  { text-align:right; }
.dl-th--num    { width:36px; }
.dl-th--due    { color:var(--gov-red) !important; }

#dueTableBody tr { transition:background .1s; }
#dueTableBody tr:hover { background:#f5f8fd; }
#dueTableBody tr:nth-child(even) { background:#fafbfd; }
#dueTableBody tr:nth-child(even):hover { background:#f0f4fa; }
#dueTableBody td { border:1px solid var(--gov-line);padding:7px 10px;font-size:12px;color:var(--gov-text);vertical-align:middle; }

.dl-td--loading { text-align:center;color:var(--gov-muted);padding:40px !important;border:1px solid var(--gov-line) !important;font-size:13px !important; }
.dl-td--empty   { text-align:center;color:var(--gov-muted);padding:50px !important;font-size:13px !important; }

.dl-tfoot-row { background:#f0f4fa; }
.dl-tfoot-label { border:1px solid var(--gov-border);padding:7px 10px;text-align:right;font-weight:700;font-size:11px;color:var(--gov-navy); }
.dl-tfoot-val { border:1px solid var(--gov-border);padding:7px 10px;text-align:right;font-family:var(--font-mono);font-size:12px;font-weight:700; }
.dl-tfoot-val--total { color:var(--gov-text); }
.dl-tfoot-val--paid  { color:var(--gov-green); }
.dl-tfoot-val--due   { color:var(--gov-red);background:var(--gov-red-lt); }

.dl-cell-billno { font-family:var(--font-mono);font-size:11px;font-weight:600;color:var(--gov-navy-lt);background:#e8edf7;padding:2px 7px;border-radius:3px;display:inline-block; }
.dl-cell-name   { font-weight:600;color:var(--gov-text);line-height:1.3; }
.dl-cell-sub    { font-size:10px;color:var(--gov-muted);margin-top:1px; }
.dl-cell-date   { font-size:11px;color:var(--gov-muted);white-space:nowrap;font-family:var(--font-mono); }
.dl-cell-money  { font-family:var(--font-mono);font-size:12px;text-align:right;display:block; }
.dl-cell-money--due  { color:var(--gov-red);font-weight:700; }
.dl-cell-money--paid { color:var(--gov-green);font-weight:600; }

.dl-badge { display:inline-block;font-size:10px;font-weight:700;padding:2px 9px;border-radius:3px;white-space:nowrap;letter-spacing:.2px; }
.dl-badge--paid      { background:var(--gov-green-lt);color:var(--gov-green);border:1px solid #a8d5bc; }
.dl-badge--partial   { background:#fff8e1;color:#b45309;border:1px solid #f5d98c; }
.dl-badge--due       { background:var(--gov-red-lt);color:var(--gov-red);border:1px solid #f0b8b3; }
.dl-badge--confirmed { background:#e8edf7;color:var(--gov-navy-lt);border:1px solid #b8c8e0; }

.dl-btn-print { background:var(--gov-navy);color:var(--gov-white);border:none;border-radius:3px;padding:4px 10px;font-size:11px;cursor:pointer;transition:background .15s; }
.dl-btn-print:hover { background:var(--gov-navy-lt); }

.dl-btn-view { background:#1e4a8a;color:var(--gov-white);border:none;border-radius:4px;padding:5px 12px;font-size:11px;font-weight:bold;cursor:pointer;transition:background .15s;margin-bottom:3px;display:inline-block; }
.dl-btn-view:hover { background:#163566; }

/* MOBILE CARDS */
.dl-card-list { padding:10px 12px;display:flex;flex-direction:column;gap:10px; }
.dl-card { border:1px solid var(--gov-border);border-radius:var(--radius-lg);overflow:hidden;background:var(--gov-white);box-shadow:0 1px 4px rgba(10,35,66,.07); }
.dl-card__head { background:var(--gov-navy);padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px; }
.dl-card__billno { font-family:var(--font-mono);font-size:11px;font-weight:600;color:var(--gov-amber-lt); }
.dl-card__body { padding:10px 12px; }
.dl-card__name { font-size:13px;font-weight:700;color:var(--gov-navy);margin-bottom:2px; }
.dl-card__info { font-size:11px;color:var(--gov-muted);margin-bottom:8px; }
.dl-card__amounts { display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px; }
.dl-card__amt { background:#f5f7fa;border:1px solid var(--gov-line);border-radius:var(--radius);padding:6px 8px;text-align:center; }
.dl-card__amt-label { font-size:10px;color:var(--gov-muted); }
.dl-card__amt-val   { font-family:var(--font-mono);font-size:13px;font-weight:700;color:var(--gov-text);margin-top:1px; }
.dl-card__amt--due  { background:var(--gov-red-lt);border-color:#f0b8b3; }
.dl-card__amt--due  .dl-card__amt-val { color:var(--gov-red); }
.dl-card__amt--paid .dl-card__amt-val { color:var(--gov-green); }
.dl-card__foot { display:flex;align-items:center;justify-content:space-between; }

/* PAGINATION */
.dl-pagination { padding:10px 14px;border-top:1px solid var(--gov-line);background:#fafbfc; }
.dl-pagination-inner { display:flex;gap:4px;align-items:center;flex-wrap:wrap; }
.dl-page-btn { border:1px solid var(--gov-border);background:var(--gov-white);padding:5px 12px;border-radius:var(--radius);font-size:12px;cursor:pointer;color:var(--gov-navy);transition:background .1s,color .1s; }
.dl-page-btn:hover:not(:disabled):not(.active) { background:#eef2f8; }
.dl-page-btn.active { background:var(--gov-navy);color:var(--gov-white);border-color:var(--gov-navy);font-weight:700; }
.dl-page-info { font-size:12px;color:var(--gov-muted);margin-left:10px;font-weight:500; }

.dl-modal-content  { border-radius:var(--radius-lg);overflow:hidden; }
.dl-modal-header   { background:var(--gov-navy);color:var(--gov-white);border-bottom:none;padding:12px 16px; }
.dl-modal-title    { font-size:15px;font-weight:600;margin:0; }
.dl-modal-close    { color:var(--gov-white);opacity:.8;text-shadow:none; }
.dl-modal-body     { background:#f8fafc;padding:20px; }
.dl-modal-footer   { background:#f0f4f9;border-top:1px solid var(--gov-border);padding:10px 16px; }
.dl-btn-close      { font-weight:600; }

.dl-modal-section  { background:var(--gov-white);border:1px solid var(--gov-border);border-radius:var(--radius);padding:14px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.05); }
.dl-modal-section:last-child { margin-bottom:0; }
.dl-modal-section__title { font-weight:700;color:var(--gov-navy);border-bottom:2px solid var(--gov-line);padding-bottom:8px;margin-bottom:12px;font-size:14px; }
.dl-modal-table    { font-size:12px; }
.dl-modal-table thead { background:#f0f4f9;color:var(--gov-navy); }
.dl-th-action      { width:80px; }
.dl-modal-loading  { text-align:center;color:var(--gov-muted);padding:12px; }
.dl-modal-total    { text-align:right;font-weight:700;font-size:14px;margin-top:8px; }
.dl-modal-total--due  { color:var(--gov-red); }
.dl-modal-total--paid { color:var(--gov-green); }

/* RESPONSIVE */
@media (max-width: 767px) {
    .dl-summary-strip    { grid-template-columns:1fr 1fr; }
    .dl-summary-card--total { display:none; }
    .dl-toolbar          { flex-direction:column;align-items:stretch;gap:8px; }
    .dl-toolbar__search  { min-width:unset; }
    .dl-toolbar__right   { justify-content:space-between; }
    .dl-export-group .dl-btn span { display:none; }
    .dl-export-group .dl-btn { padding:6px 10px; }
    .dl-table-wrap       { display:none; }
    .dl-card-list        { display:flex !important; }
    .dl-page-header      { flex-direction:column;align-items:flex-start; }
    .dl-breadcrumb       { display:none; }
}
@media (max-width: 420px) {
    .dl-summary-strip    { grid-template-columns:1fr; }
    .dl-summary-card--paid { display:none; }
    .dl-summary-card__value { font-size:16px; }
    .dl-card__amounts    { grid-template-columns:1fr 1fr; }
}
</style>
@stop


{{-- ═══════════════════════════════════════════════ --}}
{{-- JavaScript                                     --}}
{{-- ═══════════════════════════════════════════════ --}}
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true, closeButton:true };
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

const DL_URLS = {
    list           : '{{ route("billing.duelist.list") }}',
    summary        : '{{ route("billing.duelist.summary") }}',
    patientDetails : '{{ url("Billing/DuelistFinal/patient-details") }}',
    paymentPrint   : '{{ url("Billing/payment") }}',
};

(function () {
'use strict';

let currentPage   = 1;
let searchTimer   = null;
let allLoadedRows = [];
let activeRequest = null;

const fmt = n => '&#2547;\u00a0' + parseInt(parseFloat(n) || 0).toLocaleString('en-IN');

function esc(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#39;');
}

const statusBadge = (status) => {
    const map = {
        paid      : ['dl-badge--paid',      'Paid'],
        partial   : ['dl-badge--partial',   'Partial'],
        confirmed : ['dl-badge--confirmed', 'Confirmed'],
        due       : ['dl-badge--due',       'Due'],
    };
    const [cls, label] = map[status] || ['dl-badge--due', status || 'Due'];
    return `<span class="dl-badge ${cls}">${label}</span>`;
};

// -------------------------------------------------------
// 1. Due List Load
// -------------------------------------------------------
function loadDueList(page) {
    page        = page || 1;
    currentPage = page;

    const q       = $('#listSearchInput').val().trim();
    const showAll = $('#showAllChk').is(':checked') ? 1 : 0;

    if (activeRequest) {
        activeRequest.abort();
        activeRequest = null;
    }

    $('#dueTableBody').html(`<tr><td colspan="9" class="dl-td--loading"><i class="fas fa-spinner fa-spin"></i> Loading</td></tr>`);
    $('#dueTableFoot').hide();
    $('#dueCardList').html('<div style="text-align:center;padding:30px;color:#888;"><i class="fas fa-spinner fa-spin"></i> Loading</div>');

    activeRequest = $.ajax({
        url    : DL_URLS.list,
        method : 'GET',
        data   : { q, show_all: showAll, page, per_page: 20 },

        success: function (res) {
            activeRequest = null;

            const rows = res.data || [];
            const meta = res.meta || {};
            allLoadedRows = rows;

            if (! rows.length) {
                const empty = `<tr><td colspan="9" class="dl-td--empty">
                    <i class="fas fa-inbox" style="font-size:28px;display:block;margin:0 auto 8px;opacity:.3;"></i>
                    No due invoices found.</td></tr>`;
                $('#dueTableBody').html(empty);
                $('#dueCardList').html('<div style="text-align:center;padding:40px;color:#888;"><i class="fas fa-inbox" style="font-size:28px;display:block;margin:0 auto 8px;opacity:.3;"></i>No records found.</div>');
                $('#paginationWrap').empty();
                $('#activeTabDueSummary').text('');
                return;
            }

            let sumTotal = 0, sumPaid = 0, sumDue = 0;
            const rowParts  = [];
            const cardParts = [];

            rows.forEach(function (inv, idx) {
                const net    = parseFloat(inv.NetBill ?? (inv.TotalBill - (inv.Discount || 0))) || 0;
                const due    = parseFloat(inv.DueAmount)  || 0;
                const paid   = parseFloat(inv.PaidAmount) || 0;
                const status = (inv.Status || 'due').toLowerCase();
                const rowNum = ((meta.current_page || 1) - 1) * (meta.per_page || 20) + idx + 1;
                const ptId   = esc(String(inv.patient_id || inv.PatientId || inv.id || ''));

                sumTotal += net;
                sumPaid  += paid;
                sumDue   += due;

                rowParts.push(`
                <tr>
                    <td style="text-align:center;color:#aaa;font-size:11px;">${rowNum}</td>
                    <td><span class="dl-cell-billno">${esc(inv.BillNo)}</span></td>
                    <td>
                        <div class="dl-cell-name">${esc(inv.PatientName)}</div>
                        <div class="dl-cell-sub">${esc(inv.PatientCode)}${inv.MobileNo ? '&nbsp;&nbsp;' + esc(inv.MobileNo) : ''}</div>
                    </td>
                    <td><span class="dl-cell-date">${esc(inv.PaymentDate)}</span></td>
                    <td><span class="dl-cell-money">${fmt(net)}</span></td>
                    <td><span class="dl-cell-money dl-cell-money--paid">${fmt(paid)}</span></td>
                    <td><span class="dl-cell-money dl-cell-money--due">${fmt(due)}</span></td>
                    <td style="text-align:center;">${statusBadge(status)}</td>
                    <td style="text-align:center;white-space:nowrap;">
                        <button class="dl-btn-view" data-patient-id="${ptId}" title="Preview Details">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </td>
                </tr>`);

                cardParts.push(`
                <div class="dl-card">
                    <div class="dl-card__head">
                        <span class="dl-card__billno">${esc(inv.BillNo)}</span>
                        ${statusBadge(status)}
                    </div>
                    <div class="dl-card__body">
                        <div class="dl-card__name">${esc(inv.PatientName)}</div>
                        <div class="dl-card__info">
                            ${esc(inv.PatientCode)}${inv.MobileNo ? '&nbsp;&nbsp;' + esc(inv.MobileNo) : ''}${inv.PaymentDate ? '&nbsp;&nbsp;' + esc(inv.PaymentDate) : ''}
                        </div>
                        <div class="dl-card__amounts">
                            <div class="dl-card__amt">
                                <div class="dl-card__amt-label">Total</div>
                                <div class="dl-card__amt-val">${fmt(net)}</div>
                            </div>
                            <div class="dl-card__amt dl-card__amt--paid">
                                <div class="dl-card__amt-label">Paid</div>
                                <div class="dl-card__amt-val dl-cell-money--paid">${fmt(paid)}</div>
                            </div>
                            <div class="dl-card__amt dl-card__amt--due">
                                <div class="dl-card__amt-label">Due</div>
                                <div class="dl-card__amt-val">${fmt(due)}</div>
                            </div>
                        </div>
                        <div class="dl-card__foot">
                            <span style="font-size:10px;color:#aaa;">#${rowNum}</span>
                            <button class="dl-btn-view" data-patient-id="${ptId}" title="Preview Details">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                </div>`);
            });

            $('#dueTableBody').html(rowParts.join(''));
            $('#dueCardList').html(cardParts.join(''));
            $('#footTotal').html(fmt(sumTotal));
            $('#footPaid').html(fmt(sumPaid));
            $('#footDue').html(fmt(sumDue));
            $('#dueTableFoot').show();
            $('#sumTotalPaid').html(fmt(sumPaid));
            $('#sumTotalBill').html(fmt(sumTotal));
            $('#activeTabDueSummary').html('Page Due: ' + fmt(sumDue) + ' (' + rows.length + ' records)');

            renderPagination(meta);
        },

        error: function (xhr, status) {
            if (status === 'abort') return;
            toastr.error('ডেটা লোড হয়নি (Error ' + (xhr.status || '') + ')');
            $('#dueTableBody').html(`<tr><td colspan="9" class="dl-td--empty">Failed to load data.</td></tr>`);
        }
    });
}

// -------------------------------------------------------
// 2. Summary Cards
// -------------------------------------------------------
function loadSummaryCards() {
    $.get(DL_URLS.summary, function (res) {
        if(res) {
            $('#sumTotalDue').html(fmt(res.total_due      || 0));
            $('#sumTotalCount').text((res.total_patients  || 0) + ' patients with due');
        }
    }).fail(function () {
        $('#sumTotalDue').html(fmt(0));
        $('#sumTotalCount').text('লোড হয়নি');
    });
}

// -------------------------------------------------------
// 3. Pagination
// -------------------------------------------------------
function renderPagination(meta) {
    const $wrap = $('#paginationWrap');
    $wrap.empty();
    if (! meta || ! meta.last_page || meta.last_page <= 1) return;

    let html = '<div class="dl-pagination-inner">';

    html += meta.current_page > 1
        ? `<button class="dl-page-btn" data-page="${meta.current_page - 1}">&laquo; Prev</button>`
        : `<button class="dl-page-btn" disabled style="opacity:.5;cursor:not-allowed;">&laquo; Prev</button>`;

    const start = Math.max(1, meta.current_page - 2);
    const end   = Math.min(meta.last_page, meta.current_page + 2);

    if (start > 1) {
        html += `<button class="dl-page-btn" data-page="1">1</button>`;
        if (start > 2) html += `<span style="padding:0 5px;color:#888;">…</span>`;
    }

    for (let i = start; i <= end; i++) {
        html += i === meta.current_page
            ? `<button class="dl-page-btn active" data-page="${i}">${i}</button>`
            : `<button class="dl-page-btn" data-page="${i}">${i}</button>`;
    }

    if (end < meta.last_page) {
        if (end < meta.last_page - 1) html += `<span style="padding:0 5px;color:#888;">…</span>`;
        html += `<button class="dl-page-btn" data-page="${meta.last_page}">${meta.last_page}</button>`;
    }

    html += meta.current_page < meta.last_page
        ? `<button class="dl-page-btn" data-page="${meta.current_page + 1}">Next &raquo;</button>`
        : `<button class="dl-page-btn" disabled style="opacity:.5;cursor:not-allowed;">Next &raquo;</button>`;

    html += `<span class="dl-page-info">Showing ${meta.from || 0}–${meta.to || 0} of ${meta.total || 0}</span></div>`;
    $wrap.html(html);
}

$(document).on('click', '.dl-page-btn:not(:disabled):not(.active)', function () {
    const p = parseInt($(this).data('page'));
    if (p) loadDueList(p);
});

$('#listSearchInput').on('input', function () {
    const val = $(this).val();
    $('#btnClearSearch').toggle(val.length > 0);
    clearTimeout(searchTimer);
    if (val.length === 0 || val.length >= 3) {
        searchTimer = setTimeout(() => loadDueList(1), 400);
    }
});

$('#btnClearSearch').on('click', function () {
    $('#listSearchInput').val('').trigger('input');
});

$('#showAllChk').on('change', () => loadDueList(1));

function buildExportRows() {
    return allLoadedRows.map(function (inv, idx) {
        const net = parseFloat(inv.NetBill ?? (inv.TotalBill - (inv.Discount || 0))) || 0;
        return {
            '#'            : idx + 1,
            'Bill No'      : inv.BillNo      || '',
            'Patient Name' : inv.PatientName || '',
            'Patient Code' : inv.PatientCode || '',
            'Mobile'       : inv.MobileNo    || '',
            'Date'         : inv.PaymentDate || '',
            'Total (BDT)'  : Math.round(net),
            'Paid (BDT)'   : Math.round(parseFloat(inv.PaidAmount) || 0),
            'Due (BDT)'    : Math.round(parseFloat(inv.DueAmount)  || 0),
            'Status'       : inv.Status      || '',
        };
    });
}

const getFileName = ext => 'DueList_Final_' + new Date().toISOString().slice(0, 10) + '.' + ext;

$('#btnExportExcel').on('click', function () {
    if (! allLoadedRows.length) { toastr.warning('No data to export.'); return; }
    const data = buildExportRows();
    const ws   = XLSX.utils.json_to_sheet(data);
    const wb   = XLSX.utils.book_new();
    ws['!cols'] = [{wch:4},{wch:14},{wch:26},{wch:14},{wch:14},{wch:12},{wch:16},{wch:12},{wch:12},{wch:12}];
    XLSX.utils.book_append_sheet(wb, ws, 'Due List');
    XLSX.writeFile(wb, getFileName('xlsx'));
    toastr.success('Excel exported successfully!');
});

$('#btnExportCsv').on('click', function () {
    if (! allLoadedRows.length) { toastr.warning('No data to export.'); return; }
    const data  = buildExportRows();
    const heads = Object.keys(data[0]);
    const csv   = [heads.join(','), ...data.map(row =>
        heads.map(h => '"' + String(row[h]).replace(/"/g, '""') + '"').join(',')
    )].join('\r\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = getFileName('csv'); a.click();
    URL.revokeObjectURL(url);
    toastr.success('CSV exported successfully!');
});

// -------------------------------------------------------
// 6. Patient Details Modal
// -------------------------------------------------------
$(document).on('click', '.dl-btn-view', function () {
    const patientId = $(this).data('patient-id');
    if (! patientId) { toastr.error('Patient ID not found.'); return; }

    $('#modalPatientName').text('Patient Ledger & Details');
    $('#patientDetailsModal').modal('show');
    $('#modalBillBody').html('<tr><td colspan="5" class="dl-modal-loading"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</td></tr>');
    $('#modalPaymentBody').html('<tr><td colspan="4" class="dl-modal-loading"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</td></tr>');
    $('#modalTotalDue').text('৳ 0');
    $('#modalTotalPayment').text('৳ 0');

    $.get(DL_URLS.patientDetails + '/' + parseInt(patientId, 10), function (res) {
        
        if (res.patient && res.patient.name) {
            $('#modalPatientName').text(res.patient.name + ' — Ledger');
        } else {
            $('#modalPatientName').text('Patient Ledger & Details');
        }

        let billRows = '';
        let computedTotalDue = 0;

        if (res.bills && res.bills.length > 0) {
            res.bills.forEach(function (b) {
                const net  = parseFloat(b.NetBill ?? (b.TotalBill - (b.Discount || 0))) || 0;
                const due  = parseFloat(b.DueAmount)  || 0;
                const paid = parseFloat(b.PaidAmount) || 0;
                computedTotalDue += due;

                billRows += `
                <tr>
                    <td style="font-weight:600;color:#1e4a8a;">${esc(b.BillNo)}</td>
                    <td class="text-right">&#2547; ${net.toLocaleString('en-IN')}</td>
                    <td class="text-right" style="color:#1a7a4a;">&#2547; ${paid.toLocaleString('en-IN')}</td>
                    <td class="text-right text-danger" style="font-weight:bold;">&#2547; ${due.toLocaleString('en-IN')}</td>
                    <td class="text-center">
                        <button class="dl-btn-print dl-btn-open-print"
                            data-bill-id="${esc(String(b.ID))}"
                            title="Print Invoice">
                            <i class="fas fa-print"></i>
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            billRows = '<tr><td colspan="5" class="dl-modal-loading text-muted">No bills found</td></tr>';
        }

        $('#modalBillBody').html(billRows);
        $('#modalTotalDue').html('&#2547; ' + computedTotalDue.toLocaleString('en-IN'));

        let paymentRows = '';
        let computedTotalPayment = 0;

        if (res.payments && res.payments.length > 0) {
            res.payments.forEach(function (p) {
                const amt = parseFloat(p.amount) || 0;
                computedTotalPayment += amt;

                paymentRows += `
                <tr>
                    <td>${esc(p.PaymentDate) || '-'}</td>
                    <td class="text-right" style="font-weight:bold;color:#1a7a4a;">&#2547; ${amt.toLocaleString('en-IN')}</td>
                    <td>${esc(p.CollectedBy) || '-'}</td>
                    <td class="text-center">
                        <button class="dl-btn-print dl-btn-open-print"
                            data-bill-id="${esc(String(p.ID))}"
                            title="Print Receipt">
                            <i class="fas fa-print"></i>
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            paymentRows = '<tr><td colspan="4" class="dl-modal-loading text-muted">No payments found</td></tr>';
        }

        $('#modalPaymentBody').html(paymentRows);
        $('#modalTotalPayment').html('&#2547; ' + computedTotalPayment.toLocaleString('en-IN'));

    }).fail(function (xhr) {
        const msg = xhr.status === 404 ? 'Patient not found.' : 'Failed to fetch patient details.';
        toastr.error(msg);
        $('#modalBillBody').html('<tr><td colspan="5" class="dl-modal-loading text-danger">Error loading data</td></tr>');
        $('#modalPaymentBody').html('<tr><td colspan="4" class="dl-modal-loading text-danger">Error loading data</td></tr>');
    });
});

$(document).on('click', '.dl-btn-open-print', function () {
    const billId = $(this).data('bill-id');
    if (billId) {
        window.open(DL_URLS.paymentPrint + '/' + parseInt(billId, 10) + '/print', '_blank');
    }
});

$(document).ready(function () {
    loadDueList();
    loadSummaryCards();
});

})();
</script>
@stop