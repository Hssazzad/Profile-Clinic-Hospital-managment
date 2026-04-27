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

{{-- ----------------------------------------------
     SUMMARY STRIP
---------------------------------------------- --}}
<div class="dl-summary-strip">
    <div class="dl-summary-card dl-summary-card--due">
        <div class="dl-summary-card__icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Total Due</div>
            <div class="dl-summary-card__value" id="sumTotalDue">&#2547; —</div>
            <div class="dl-summary-card__count" id="sumTotalCount">— invoices</div>
        </div>
    </div>
    <div class="dl-summary-card dl-summary-card--paid">
        <div class="dl-summary-card__icon"><i class="fas fa-check-circle"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Paid (this page)</div>
            <div class="dl-summary-card__value" id="sumTotalPaid">&#2547; —</div>
            <div class="dl-summary-card__count">current page</div>
        </div>
    </div>
    <div class="dl-summary-card dl-summary-card--total">
        <div class="dl-summary-card__icon"><i class="fas fa-file-alt"></i></div>
        <div class="dl-summary-card__body">
            <div class="dl-summary-card__label">Total Bill (this page)</div>
            <div class="dl-summary-card__value" id="sumTotalBill">&#2547; —</div>
            <div class="dl-summary-card__count">current page</div>
        </div>
    </div>
</div>

{{-- ----------------------------------------------
     FILTER + TOOLBAR
---------------------------------------------- --}}
<div class="dl-toolbar">
    <div class="dl-toolbar__search">
        <span class="dl-toolbar__search-icon"><i class="fas fa-search"></i></span>
        <input type="text" id="listSearchInput"
               placeholder="Name / Bill No / Patient Code…"
               class="dl-search-input">
        <button class="dl-search-clear" id="btnClearSearch" style="display:none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="dl-toolbar__right">
        <label class="dl-toggle-label">
            <input type="checkbox" id="showAllChk" class="dl-toggle-chk">
            <span class="dl-toggle-track"></span>
            <span class="dl-toggle-text">Show All</span>
        </label>
        <div class="dl-export-group">
            <button id="btnExportExcel" class="dl-btn dl-btn--excel">
                <i class="fas fa-file-excel"></i><span>Excel</span>
            </button>
            <button id="btnExportCsv" class="dl-btn dl-btn--csv">
                <i class="fas fa-file-csv"></i><span>CSV</span>
            </button>
            <button id="btnPrintList" class="dl-btn dl-btn--print">
                <i class="fas fa-print"></i><span>Print</span>
            </button>
        </div>
    </div>
</div>

{{-- ----------------------------------------------
     TABLE PANEL
---------------------------------------------- --}}
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
                    <th class="dl-th dl-th--center">Actions</th>
                </tr>
            </thead>
            <tbody id="dueTableBody">
                <tr>
                    <td colspan="9" class="dl-td--loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading…
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

@stop


@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<style>
/* ------------------------------------------
   ROOT TOKENS
------------------------------------------ */
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

/* -- PAGE HEADER -- */
.dl-page-header {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 2px 0 6px;
}
.dl-page-header__left { display: flex; align-items: center; gap: 12px; }
.dl-page-header__icon {
    width: 42px; height: 42px; background: var(--gov-navy); border-radius: var(--radius);
    display: flex; align-items: center; justify-content: center;
    color: var(--gov-amber-lt); font-size: 18px; flex-shrink: 0; box-shadow: var(--gov-shadow);
}
.dl-page-header__title {
    font-size: 17px; font-weight: 700;
    color: var(--gov-navy); margin: 0; line-height: 1.3;
}
.dl-page-header__sub { font-size: 11px; color: var(--gov-muted); margin: 0; letter-spacing: .3px; }
.dl-breadcrumb { font-size: 11px; background: transparent !important; padding: 0 !important; }
.dl-breadcrumb .breadcrumb-item a { color: var(--gov-navy-lt); }
.dl-breadcrumb .breadcrumb-item.active { color: var(--gov-amber); }
.dl-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: var(--gov-border); }

/* -- SUMMARY STRIP -- */
.dl-summary-strip {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 10px; margin-bottom: 14px;
}
.dl-summary-card {
    background: var(--gov-white); border-radius: var(--radius-lg);
    padding: 14px 16px; display: flex; align-items: center; gap: 14px;
    box-shadow: var(--gov-shadow); border-top: 3px solid transparent; transition: box-shadow .2s;
}
.dl-summary-card:hover { box-shadow: var(--gov-shadow-lg); }
.dl-summary-card--due   { border-top-color: var(--gov-red); }
.dl-summary-card--paid  { border-top-color: var(--gov-green); }
.dl-summary-card--total { border-top-color: var(--gov-navy-lt); }
.dl-summary-card__icon {
    width: 44px; height: 44px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.dl-summary-card--due   .dl-summary-card__icon { background: var(--gov-red-lt);   color: var(--gov-red); }
.dl-summary-card--paid  .dl-summary-card__icon { background: var(--gov-green-lt); color: var(--gov-green); }
.dl-summary-card--total .dl-summary-card__icon { background: #e8edf5; color: var(--gov-navy-lt); }
.dl-summary-card__label { font-size: 11px; color: var(--gov-muted); margin-bottom: 2px; }
.dl-summary-card__value { font-family: var(--font-mono); font-size: 18px; font-weight: 700; color: var(--gov-text); line-height: 1.2; }
.dl-summary-card--due  .dl-summary-card__value { color: var(--gov-red); }
.dl-summary-card--paid .dl-summary-card__value { color: var(--gov-green); }
.dl-summary-card__count { font-size: 10px; color: var(--gov-muted); margin-top: 2px; }

/* -- TOOLBAR -- */
.dl-toolbar {
    background: var(--gov-white); border: 1px solid var(--gov-border);
    border-radius: var(--radius-lg); padding: 10px 14px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    margin-bottom: 12px; box-shadow: var(--gov-shadow);
}
.dl-toolbar__search { flex: 1; min-width: 180px; position: relative; display: flex; align-items: center; }
.dl-toolbar__search-icon { position: absolute; left: 10px; color: var(--gov-muted); font-size: 12px; pointer-events: none; }
.dl-search-input {
    width: 100%; height: 34px; border: 1px solid var(--gov-border);
    border-radius: var(--radius); padding: 0 32px 0 30px; font-size: 12px;
    color: var(--gov-text); background: #f8fafc; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.dl-search-input:focus { border-color: var(--gov-navy-lt); box-shadow: 0 0 0 3px rgba(30,74,138,.12); background: var(--gov-white); }
.dl-search-clear { position: absolute; right: 8px; background: none; border: none; color: var(--gov-muted); cursor: pointer; font-size: 11px; padding: 0; }
.dl-toolbar__right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Toggle */
.dl-toggle-label { display: flex; align-items: center; gap: 7px; cursor: pointer; margin: 0; }
.dl-toggle-chk { display: none; }
.dl-toggle-track {
    width: 34px; height: 18px; background: var(--gov-border);
    border-radius: 9px; position: relative; transition: background .2s; flex-shrink: 0;
}
.dl-toggle-track::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 12px; height: 12px; border-radius: 50%;
    background: var(--gov-white); transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.dl-toggle-chk:checked + .dl-toggle-track { background: var(--gov-navy-lt); }
.dl-toggle-chk:checked + .dl-toggle-track::after { transform: translateX(16px); }
.dl-toggle-text { font-size: 11px; color: var(--gov-muted); white-space: nowrap; }

.dl-export-group { display: flex; gap: 6px; }
.dl-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border: none; border-radius: var(--radius);
    font-size: 11px; font-weight: 600; cursor: pointer;
    transition: opacity .15s, transform .1s; white-space: nowrap;
}
.dl-btn:active { transform: scale(.97); }
.dl-btn--excel { background: #1e6b3f; color: #fff; }
.dl-btn--csv   { background: var(--gov-amber); color: #fff; }
.dl-btn--print { background: var(--gov-navy);  color: #fff; }

/* -- PANEL -- */
.dl-panel { background: var(--gov-white); border: 1px solid var(--gov-border); border-radius: var(--radius-lg); box-shadow: var(--gov-shadow); overflow: hidden; }
.dl-panel__header {
    background: var(--gov-navy); padding: 9px 14px;
    display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;
}
.dl-panel__title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--gov-white); }
.dl-panel__dot { width: 8px; height: 8px; background: var(--gov-amber-lt); border-radius: 50%; flex-shrink: 0; }
.dl-panel__meta { font-size: 11px; color: rgba(255,255,255,.7); font-family: var(--font-mono); }

/* -- TABLE -- */
.dl-table-wrap { overflow-x: auto; }
.dl-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.dl-th {
    background: #f0f4f9; border: 1px solid var(--gov-border);
    padding: 8px 10px; font-size: 11px; font-weight: 700;
    color: var(--gov-navy); white-space: nowrap; letter-spacing: .2px;
    text-transform: uppercase;
}
.dl-th--center { text-align: center; }
.dl-th--right  { text-align: right; }
.dl-th--num    { width: 36px; }
.dl-th--due    { color: var(--gov-red) !important; }

#dueTableBody tr { transition: background .1s; }
#dueTableBody tr:hover { background: #f5f8fd; }
#dueTableBody tr:nth-child(even) { background: #fafbfd; }
#dueTableBody tr:nth-child(even):hover { background: #f0f4fa; }
#dueTableBody td { border: 1px solid var(--gov-line); padding: 7px 10px; font-size: 12px; color: var(--gov-text); vertical-align: middle; }

.dl-td--loading { text-align: center; color: var(--gov-muted); padding: 40px !important; border: 1px solid var(--gov-line) !important; font-size: 13px !important; }
.dl-td--empty   { text-align: center; color: var(--gov-muted); padding: 50px !important; font-size: 13px !important; }

.dl-tfoot-row { background: #f0f4fa; }
.dl-tfoot-label { border: 1px solid var(--gov-border); padding: 7px 10px; text-align: right; font-weight: 700; font-size: 11px; color: var(--gov-navy); }
.dl-tfoot-val { border: 1px solid var(--gov-border); padding: 7px 10px; text-align: right; font-family: var(--font-mono); font-size: 12px; font-weight: 700; }
.dl-tfoot-val--total { color: var(--gov-text); }
.dl-tfoot-val--paid  { color: var(--gov-green); }
.dl-tfoot-val--due   { color: var(--gov-red); background: var(--gov-red-lt); }

.dl-cell-billno { font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--gov-navy-lt); background: #e8edf7; padding: 2px 7px; border-radius: 3px; display: inline-block; }
.dl-cell-name   { font-weight: 600; color: var(--gov-text); line-height: 1.3; }
.dl-cell-sub    { font-size: 10px; color: var(--gov-muted); margin-top: 1px; }
.dl-cell-date   { font-size: 11px; color: var(--gov-muted); white-space: nowrap; font-family: var(--font-mono); }
.dl-cell-money  { font-family: var(--font-mono); font-size: 12px; text-align: right; display: block; }
.dl-cell-money--due  { color: var(--gov-red); font-weight: 700; }
.dl-cell-money--paid { color: var(--gov-green); font-weight: 600; }

.dl-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 9px; border-radius: 3px; white-space: nowrap; letter-spacing: .2px; }
.dl-badge--paid      { background: var(--gov-green-lt); color: var(--gov-green); border: 1px solid #a8d5bc; }
.dl-badge--partial   { background: #fff8e1; color: #b45309; border: 1px solid #f5d98c; }
.dl-badge--due       { background: var(--gov-red-lt); color: var(--gov-red); border: 1px solid #f0b8b3; }
.dl-badge--confirmed { background: #e8edf7; color: var(--gov-navy-lt); border: 1px solid #b8c8e0; }

.dl-btn-print { background: var(--gov-navy); color: var(--gov-white); border: none; border-radius: 3px; padding: 4px 10px; font-size: 11px; cursor: pointer; transition: background .15s; }
.dl-btn-print:hover { background: var(--gov-navy-lt); }

/* -- MOBILE CARDS -- */
.dl-card-list { padding: 10px 12px; display: flex; flex-direction: column; gap: 10px; }
.dl-card { border: 1px solid var(--gov-border); border-radius: var(--radius-lg); overflow: hidden; background: var(--gov-white); box-shadow: 0 1px 4px rgba(10,35,66,.07); }
.dl-card__head { background: var(--gov-navy); padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.dl-card__billno { font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--gov-amber-lt); }
.dl-card__body { padding: 10px 12px; }
.dl-card__name { font-size: 13px; font-weight: 700; color: var(--gov-navy); margin-bottom: 2px; }
.dl-card__info { font-size: 11px; color: var(--gov-muted); margin-bottom: 8px; }
.dl-card__amounts { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 10px; }
.dl-card__amt { background: #f5f7fa; border: 1px solid var(--gov-line); border-radius: var(--radius); padding: 6px 8px; text-align: center; }
.dl-card__amt-label { font-size: 10px; color: var(--gov-muted); }
.dl-card__amt-val   { font-family: var(--font-mono); font-size: 13px; font-weight: 700; color: var(--gov-text); margin-top: 1px; }
.dl-card__amt--due  { background: var(--gov-red-lt); border-color: #f0b8b3; }
.dl-card__amt--due  .dl-card__amt-val { color: var(--gov-red); }
.dl-card__amt--paid .dl-card__amt-val { color: var(--gov-green); }
.dl-card__foot { display: flex; align-items: center; justify-content: space-between; }

/* -- PAGINATION -- */
.dl-pagination { padding: 10px 14px; border-top: 1px solid var(--gov-line); background: #fafbfc; }
.dl-pagination-inner { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
.dl-page-btn { border: 1px solid var(--gov-border); background: var(--gov-white); padding: 4px 10px; border-radius: var(--radius); font-size: 12px; cursor: pointer; color: var(--gov-navy); transition: background .1s, color .1s; }
.dl-page-btn:hover:not(.active) { background: #eef2f8; }
.dl-page-btn.active { background: var(--gov-navy); color: var(--gov-white); border-color: var(--gov-navy); font-weight: 700; }
.dl-page-info { font-size: 11px; color: var(--gov-muted); margin-left: 8px; }

/* -- RESPONSIVE -- */
@media (max-width: 767px) {
    .dl-summary-strip    { grid-template-columns: 1fr 1fr; }
    .dl-summary-card--total { display: none; }
    .dl-toolbar          { flex-direction: column; align-items: stretch; gap: 8px; }
    .dl-toolbar__search  { min-width: unset; }
    .dl-toolbar__right   { justify-content: space-between; }
    .dl-export-group .dl-btn span { display: none; }
    .dl-export-group .dl-btn { padding: 6px 10px; }
    .dl-table-wrap       { display: none; }
    .dl-card-list        { display: flex !important; }
    .dl-page-header      { flex-direction: column; align-items: flex-start; }
    .dl-breadcrumb       { display: none; }
}
@media (max-width: 420px) {
    .dl-summary-strip    { grid-template-columns: 1fr; }
    .dl-summary-card--paid { display: none; }
    .dl-summary-card__value { font-size: 16px; }
    .dl-card__amounts    { grid-template-columns: 1fr 1fr; }
}

/* -- PRINT -- */
@media print {
    .content-header, .main-sidebar, .main-header,
    .dl-toolbar, .dl-pagination, .dl-btn-print { display: none !important; }
    body, .content-wrapper { background: #fff !important; }
    .dl-panel { box-shadow: none; border: 1px solid #ccc; }
    .dl-panel__header { background: #0a2342 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #dueTableFoot { display: table-footer-group !important; }
    .dl-card-list  { display: none !important; }
    .dl-table-wrap { display: block !important; }
}
</style>
@stop


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
toastr.options = { positionClass:'toast-top-right', timeOut:3000, progressBar:true, closeButton:true };
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
</script>

<script>
(function(){
'use strict';

let currentPage   = 1;
let searchTimer   = null;
let allLoadedRows = [];

const fmt = n => '&#2547; ' + parseInt(parseFloat(n)||0).toLocaleString('en-IN');

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

// --- 1. Load Due List --------------------------------------
function loadDueList(page){
    page = page || 1;
    currentPage = page;
    const q       = $('#listSearchInput').val().trim();
    const showAll = $('#showAllChk').is(':checked') ? 1 : 0;

    $('#dueTableBody').html(`<tr><td colspan="9" class="dl-td--loading"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>`);
    $('#dueTableFoot').hide();
    $('#dueCardList').html('<div style="text-align:center;padding:30px;color:#888;"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');

    $.get('{{ route("billing.invoice.list") }}', { q, show_all: showAll, page, per_page: 20 }, function(res){
        const rows = res.data || [];
        const meta = res.meta || {};
        allLoadedRows = rows;

        if (!rows.length) {
            const empty = `<tr><td colspan="9" class="dl-td--empty"><i class="fas fa-inbox" style="font-size:28px;display:block;margin:0 auto 8px;opacity:.3;"></i>No due invoices found.</td></tr>`;
            $('#dueTableBody').html(empty);
            $('#dueCardList').html('<div style="text-align:center;padding:40px;color:#888;"><i class="fas fa-inbox" style="font-size:28px;display:block;margin:0 auto 8px;opacity:.3;"></i>No records found.</div>');
            $('#paginationWrap').empty();
            $('#activeTabDueSummary').text('');
            return;
        }

        let sumTotal = 0, sumPaid = 0, sumDue = 0;
        let tableRows = '', cards = '';

        rows.forEach(function(inv, idx){
            const net    = parseFloat(inv.TotalBill) - parseFloat(inv.Discount || 0);
            const due    = parseFloat(inv.DueAmount)  || 0;
            const paid   = parseFloat(inv.PaidAmount) || 0;
            const status = (inv.Status || 'due').toLowerCase();
            const rowNum = ((meta.current_page || 1) - 1) * (meta.per_page || 20) + idx + 1;
            sumTotal += net; sumPaid += paid; sumDue += due;
            const printUrl = `{{ url('Billing/payment') }}/${inv.ID}/print`;

            tableRows += `
            <tr>
                <td style="text-align:center;color:#aaa;font-size:11px;">${rowNum}</td>
                <td><span class="dl-cell-billno">${inv.BillNo || '—'}</span></td>
                <td>
                    <div class="dl-cell-name">${inv.PatientName || '—'}</div>
                    <div class="dl-cell-sub">${inv.PatientCode || ''}${inv.MobileNo ? ' · ' + inv.MobileNo : ''}</div>
                </td>
                <td><span class="dl-cell-date">${inv.PaymentDate || '—'}</span></td>
                <td><span class="dl-cell-money">${fmt(net)}</span></td>
                <td><span class="dl-cell-money dl-cell-money--paid">${fmt(paid)}</span></td>
                <td><span class="dl-cell-money dl-cell-money--due">${fmt(due)}</span></td>
                <td style="text-align:center;">${statusBadge(status)}</td>
                <td style="text-align:center;">
                    <button class="dl-btn-print" onclick="window.open('${printUrl}','_blank')">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
            </tr>`;

            cards += `
            <div class="dl-card">
                <div class="dl-card__head">
                    <span class="dl-card__billno">${inv.BillNo || '—'}</span>
                    ${statusBadge(status)}
                </div>
                <div class="dl-card__body">
                    <div class="dl-card__name">${inv.PatientName || '—'}</div>
                    <div class="dl-card__info">
                        ${inv.PatientCode || ''}${inv.MobileNo ? ' · ' + inv.MobileNo : ''}${inv.PaymentDate ? ' · ' + inv.PaymentDate : ''}
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
                        <button class="dl-btn-print" onclick="window.open('${printUrl}','_blank')">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>`;
        });

        $('#dueTableBody').html(tableRows);
        $('#dueCardList').html(cards);
        $('#footTotal').html(fmt(sumTotal));
        $('#footPaid').html(fmt(sumPaid));
        $('#footDue').html(fmt(sumDue));
        $('#dueTableFoot').show();
        $('#sumTotalPaid').html(fmt(sumPaid));
        $('#sumTotalBill').html(fmt(sumTotal));
        $('#activeTabDueSummary').html('Page Due: ' + fmt(sumDue) + ' (' + rows.length + ' records)');
        renderPagination(meta);

    }).fail(function(){
        toastr.error('Failed to load invoice data.');
    });
}

// --- 2. Summary Card --------------------------------------
function loadSummaryCards(){
    $.get('{{ route("billing.invoice.list") }}', { show_all: 0, per_page: 9999, page: 1 }, function(res){
        const rows     = res.data || [];
        const meta     = res.meta || {};
        const totalDue = rows.reduce((s,i) => s + (parseFloat(i.DueAmount)||0), 0);
        $('#sumTotalDue').html(fmt(totalDue));
        $('#sumTotalCount').text((meta.total || rows.length) + ' invoices');
    });
}

// --- 3. Pagination ----------------------------------------
function renderPagination(meta){
    const $wrap = $('#paginationWrap');
    $wrap.empty();
    if (!meta || !meta.last_page || meta.last_page <= 1) return;
    let html = '<div class="dl-pagination-inner">';
    if (meta.current_page > 1)
        html += `<button class="dl-page-btn" data-page="${meta.current_page - 1}">&laquo; Prev</button>`;
    for (let i = 1; i <= meta.last_page; i++) {
        if (i === meta.current_page)
            html += `<button class="dl-page-btn active" data-page="${i}">${i}</button>`;
        else if (i === 1 || i === meta.last_page || Math.abs(i - meta.current_page) <= 1)
            html += `<button class="dl-page-btn" data-page="${i}">${i}</button>`;
        else if (Math.abs(i - meta.current_page) === 2)
            html += `<span style="color:#aaa;padding:0 2px;">…</span>`;
    }
    if (meta.current_page < meta.last_page)
        html += `<button class="dl-page-btn" data-page="${meta.current_page + 1}">Next &raquo;</button>`;
    html += `<span class="dl-page-info">${meta.from}–${meta.to} / ${meta.total}</span></div>`;
    $wrap.html(html);
}
$(document).on('click', '.dl-page-btn', function(){
    loadDueList(parseInt($(this).data('page')));
});

// --- 4. Search --------------------------------------------
$('#listSearchInput').on('input', function(){
    $('#btnClearSearch').toggle($(this).val().length > 0);
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadDueList(1), 300);
});
$('#btnClearSearch').on('click', function(){ $('#listSearchInput').val('').trigger('input'); });
$('#showAllChk').on('change', () => loadDueList(1));

// --- 5. Export --------------------------------------------
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
const getFileName = ext => 'DueList_Final_' + new Date().toISOString().slice(0,10) + '.' + ext;

$('#btnExportExcel').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data to export.'); return; }
    const data = buildExportRows();
    const ws   = XLSX.utils.json_to_sheet(data);
    const wb   = XLSX.utils.book_new();
    ws['!cols'] = [{wch:4},{wch:14},{wch:26},{wch:14},{wch:14},{wch:12},{wch:16},{wch:12},{wch:12},{wch:12},{wch:10}];
    XLSX.utils.book_append_sheet(wb, ws, 'Due List');
    XLSX.writeFile(wb, getFileName('xlsx'));
    toastr.success('Excel exported successfully!');
});

$('#btnExportCsv').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data to export.'); return; }
    const data  = buildExportRows();
    const heads = Object.keys(data[0]);
    const csv   = [heads.join(','), ...data.map(row => heads.map(h => '"' + String(row[h]).replace(/"/g,'""') + '"').join(','))].join('\r\n');
    const blob  = new Blob(['\uFEFF' + csv], { type:'text/csv;charset=utf-8;' });
    const url   = URL.createObjectURL(blob);
    const a     = document.createElement('a');
    a.href = url; a.download = getFileName('csv'); a.click();
    URL.revokeObjectURL(url);
    toastr.success('CSV exported successfully!');
});

$('#btnPrintList').on('click', function(){
    if (!allLoadedRows.length){ toastr.warning('No data to print.'); return; }
    const data      = buildExportRows();
    const today     = new Date().toLocaleDateString('en-GB');
    const totalDue  = data.reduce((s,r) => s + r['Due (BDT)'],   0);
    const totalPaid = data.reduce((s,r) => s + r['Paid (BDT)'],  0);
    const totalBill = data.reduce((s,r) => s + r['Total (BDT)'], 0);
    const rows = data.map(r => `
        <tr>
            <td>${r['#']}</td>
            <td><strong>${r['Bill No']}</strong></td>
            <td>${r['Patient Name']}<br><small>${r['Patient Code']}${r['Mobile'] ? ' · ' + r['Mobile'] : ''}</small></td>
            <td>${r['Date']}</td>
            <td style="text-align:right;">&#2547; ${r['Total (BDT)'].toLocaleString()}</td>
            <td style="text-align:right;color:#1a7a4a;">&#2547; ${r['Paid (BDT)'].toLocaleString()}</td>
            <td style="text-align:right;color:#c0392b;font-weight:700;">&#2547; ${r['Due (BDT)'].toLocaleString()}</td>
            <td>${r['Status']}</td>
        </tr>`).join('');
    const win = window.open('','_blank','width=1000,height=740');
    win.document.write(`<!DOCTYPE html><html><head>
        <meta charset="UTF-8"><title>Due Invoice List</title>
        <style>
            *{box-sizing:border-box;margin:0;padding:0;}
            body{font-family:Arial,sans-serif;font-size:12px;padding:20px;color:#1a2433;}
            .hdr{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;padding-bottom:10px;border-bottom:3px double #0a2342;}
            .hdr h2{font-size:15px;color:#0a2342;font-weight:700;}
            .hdr p{font-size:11px;color:#666;margin-top:3px;}
            .badge{font-size:11px;font-weight:700;padding:3px 12px;border-radius:3px;background:#fff3cd;color:#856404;border:1px solid #ffc107;display:inline-block;}
            .print-date{font-size:10px;color:#888;margin-top:4px;}
            table{width:100%;border-collapse:collapse;margin-top:4px;}
            th{background:#0a2342;color:#fff;border:1px solid #163566;padding:7px 8px;font-size:11px;text-align:left;}
            td{border:1px solid #ddd;padding:5px 8px;font-size:11px;}
            tr:nth-child(even) td{background:#f5f7fa;}
            tfoot td{background:#fdf0ef;font-weight:700;border:1px solid #ccc;}
            small{font-size:10px;color:#888;}
            @media print{body{padding:10px;}}
        </style>
    </head><body>
        <div class="hdr">
            <div><h2>Due Invoice List — Professor Clinic, Bogura</h2>
            <p>Due Invoice List &nbsp;|&nbsp; Total: ${data.length} records</p></div>
            <div style="text-align:right;"><span class="badge">Due List Final</span><div class="print-date">Printed: ${today}</div></div>
        </div>
        <table>
            <thead><tr>
                <th>#</th><th>Bill No</th><th>Patient</th><th>Date</th>
                <th style="text-align:right;">Total (&#2547;)</th>
                <th style="text-align:right;">Paid (&#2547;)</th>
                <th style="text-align:right;">Due (&#2547;)</th>
                <th>Status</th>
            </tr></thead>
            <tbody>${rows}</tbody>
            <tfoot><tr>
                <td colspan="4" style="text-align:right;font-weight:700;">Total :</td>
                <td style="text-align:right;">&#2547; ${totalBill.toLocaleString()}</td>
                <td style="text-align:right;color:#1a7a4a;">&#2547; ${totalPaid.toLocaleString()}</td>
                <td style="text-align:right;color:#c0392b;">&#2547; ${totalDue.toLocaleString()}</td>
                <td></td>
            </tr></tfoot>
        </table>
        <script>window.onload=function(){window.print();window.close();};<\/script>
    </body></html>`);
    win.document.close();
});

// --- INIT --------------------------------------------------
loadDueList();
loadSummaryCards();

})();
</script>
@stop