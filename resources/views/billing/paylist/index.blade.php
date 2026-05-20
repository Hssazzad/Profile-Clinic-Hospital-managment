@extends('adminlte::page')

@section('title', 'Payment List')

@section('content_header')
<div class="wg-topbar">
    <div class="wg-topbar-left">
        <div class="wg-topbar-icon">
            <i class="fas fa-receipt"></i>
        </div>
        <div>
            <h1 class="wg-page-title">Payment List</h1>
            <ol class="wg-breadcrumb">
                <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#">Billing</a></li>
                <li class="active">Payment List</li>
            </ol>
        </div>
    </div>
    <div class="wg-topbar-actions">
        <button class="wg-btn wg-btn-ghost" id="btnBulkPrint" style="display:none;" onclick="doBulkPrint()">
            <i class="fas fa-print"></i> Print Selected
        </button>
        <button class="wg-btn wg-btn-ghost" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>
@stop

@section('content')

<div id="save-alert" class="wg-alert d-none" role="alert"></div>

{{-- =============================================
     SUMMARY STRIP
============================================= --}}
<div class="wg-stat-grid mb-4">

    <div class="wg-stat-card">
        <div class="wg-stat-icon wg-stat-icon-slate">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="wg-stat-body">
            <div class="wg-stat-label">Total Invoices</div>
            <div class="wg-stat-value" id="sumInvoices">—</div>
            <div class="wg-stat-sub" id="sumPatients">— patients</div>
        </div>
    </div>

    <div class="wg-stat-card">
        <div class="wg-stat-icon wg-stat-icon-navy">
            <i class="fas fa-coins"></i>
        </div>
        <div class="wg-stat-body">
            <div class="wg-stat-label">Total Bill</div>
            <div class="wg-stat-value" id="sumBill">—</div>
        </div>
    </div>

    <div class="wg-stat-card">
        <div class="wg-stat-icon wg-stat-icon-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="wg-stat-body">
            <div class="wg-stat-label">Total Paid</div>
            <div class="wg-stat-value wg-stat-value-green" id="sumPaid">—</div>
        </div>
    </div>

    <div class="wg-stat-card">
        <div class="wg-stat-icon wg-stat-icon-red">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="wg-stat-body">
            <div class="wg-stat-label">Total Due</div>
            <div class="wg-stat-value wg-stat-value-red" id="sumDue">—</div>
        </div>
    </div>

</div>

{{-- =============================================
     FILTER PANEL
============================================= --}}
<div class="wg-panel mb-4">

    <div class="wg-panel-header">
        <div class="wg-panel-header-left">
            <span class="wg-panel-header-icon"><i class="fas fa-filter"></i></span>
            <div>
                <div class="wg-panel-title">Filter & Search</div>
                <div class="wg-panel-subtitle">Date, patient or keyword payment search</div>
            </div>
        </div>
        <div class="wg-panel-header-right">
            <span class="wg-chip" id="filterBadge">
                <i class="fas fa-calendar-day"></i> Today
            </span>
        </div>
    </div>

    {{-- Row 1 --}}
    <div class="wg-filter-row">

        <div class="wg-field wg-field-grow">
            <label class="wg-label"><i class="fas fa-user"></i> Patient</label>
            <select id="patientSelect" style="width:100%"></select>
        </div>

        <div class="wg-field wg-field-grow">
            <label class="wg-label"><i class="fas fa-search"></i> Search</label>
            <div class="wg-search-group">
                <input type="text" id="searchQ" class="wg-input"
                       placeholder="Name / Code / Bill No / Mobile..."
                       onkeydown="if(event.key==='Enter') loadData(1)">
                <button class="wg-btn wg-btn-primary" onclick="loadData(1)">
                    <i class="fas fa-search"></i> Load
                </button>
                <button class="wg-btn wg-btn-ghost" onclick="resetFilter()">
                    <i class="fas fa-times"></i> Reset
                </button>
            </div>
        </div>

        <div class="wg-field" style="width:120px;">
            <label class="wg-label"><i class="fas fa-list-ol"></i> Per Page</label>
            <select id="perPage" class="wg-input" style="height:34px;">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

    </div>

    {{-- Row 2 --}}
    <div class="wg-filter-row wg-filter-row-divider">

        <div class="wg-field">
            <label class="wg-label"><i class="fas fa-calendar-alt"></i> Date Mode</label>
            <div class="wg-seg">
                <input type="radio" name="dateMode" id="modeAll" value="all" checked>
                <label for="modeAll">All Time</label>
                <input type="radio" name="dateMode" id="modeSingle" value="single">
                <label for="modeSingle">Single</label>
                <input type="radio" name="dateMode" id="modeRange" value="range">
                <label for="modeRange">Range</label>
            </div>
        </div>

        <div class="wg-field">
            <label class="wg-label"><i class="fas fa-columns"></i> Date Column</label>
            <div class="wg-seg">
                <input type="radio" name="dateCol" id="colPayDate" value="PaymentDate" checked>
                <label for="colPayDate">Payment Date</label>
                <input type="radio" name="dateCol" id="colCreated" value="created_at">
                <label for="colCreated">Created At</label>
            </div>
        </div>

        <div id="singleWrap" class="wg-field" style="display:none;">
            <label class="wg-label"><i class="fas fa-calendar-day"></i> Date</label>
            <input type="date" id="singleDate" class="wg-input" style="width:160px;height:34px;">
        </div>

        <div id="rangeWrap" style="display:none;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div class="wg-field">
                <label class="wg-label"><i class="fas fa-calendar"></i> From</label>
                <input type="date" id="dateFrom" class="wg-input" style="width:155px;height:34px;">
            </div>
            <div class="wg-field">
                <label class="wg-label"><i class="fas fa-calendar"></i> To</label>
                <input type="date" id="dateTo" class="wg-input" style="width:155px;height:34px;">
            </div>
        </div>

        <div class="wg-hint ml-auto">
            <i class="fas fa-info-circle"></i>
            Press <kbd>Enter</kbd> or click Load to apply
        </div>

    </div>

</div>

{{-- =============================================
     DATA TABLE PANEL
============================================= --}}
<div class="wg-panel">

    <div class="wg-panel-header">
        <div class="wg-panel-header-left">
            <span class="wg-panel-header-icon"><i class="fas fa-table"></i></span>
            <div>
                <div class="wg-panel-title">Payment Records</div>
                <div class="wg-panel-subtitle">Date-grouped payment history</div>
            </div>
        </div>
        <div class="wg-panel-header-right">
            <span class="wg-chip" id="tableInfo">—</span>
        </div>
    </div>

    <div class="wg-table-toolbar">
        <button class="wg-btn wg-btn-sm wg-btn-ghost" onclick="toggleSelectAll()">
            <i class="fas fa-check-square"></i> Select All
        </button>
        <span class="wg-toolbar-count">
            Selected: <strong id="selectedCount">0</strong>
        </span>
    </div>

    <div id="tableBody">
        <div class="wg-loader">
            <i class="fas fa-circle-notch fa-spin"></i>
            <div>Loading payment records…</div>
        </div>
    </div>

    <div class="wg-panel-footer" id="paginationWrap" style="display:none;">
        <div class="wg-footer-info" id="pageInfo">—</div>
        <div class="wg-pagination" id="pageBtns"></div>
        <div class="wg-footer-hint">
            <i class="fas fa-hand-pointer"></i> Click a row to view details
        </div>
    </div>

</div>

<iframe id="printFrame" style="display:none;"></iframe>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
/* ===========================================
   DESIGN SYSTEM — WHITE GOVERNMENT
=========================================== */
:root {
    /* Core palette */
    --ink:         #0f1923;
    --ink-2:       #253344;
    --ink-muted:   #6b7a8d;
    --ink-faint:   #98a5b5;

    /* Accent: institutional navy */
    --navy:        #1a3660;
    --navy-mid:    #1e4480;
    --navy-light:  #e8edf6;
    --navy-soft:   #c8d3e8;

    /* Status */
    --green:       #1a6b3c;
    --green-light: #e6f4ec;
    --red:         #b91c1c;
    --red-light:   #fef2f2;

    /* Surfaces */
    --bg:          #f5f6f8;
    --white:       #ffffff;
    --border:      #d4d9e2;
    --border-soft: #e8eaef;

    /* Divider accent */
    --accent-bar:  #1a3660;

    /* Typography */
    --font:        'IBM Plex Sans', Arial, sans-serif;
    --font-mono:   'IBM Plex Mono', 'Courier New', monospace;

    /* Radii */
    --r-sm:  3px;
    --r-md:  5px;

    /* Shadow */
    --sh-sm: 0 1px 3px rgba(0,0,0,.07);
    --sh-md: 0 2px 10px rgba(0,0,0,.08);
}

*  { box-sizing: border-box; }
body, .content-wrapper { background: var(--bg) !important; font-family: var(--font); color: var(--ink); }

/* ===========================================
   PAGE TOP BAR
=========================================== */
.wg-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding-bottom: 4px;
}
.wg-topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.wg-topbar-icon {
    width: 42px; height: 42px;
    border-radius: var(--r-md);
    background: var(--navy);
    color: #fff;
    font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wg-page-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 2px;
    letter-spacing: -.2px;
}
.wg-breadcrumb {
    list-style: none;
    padding: 0; margin: 0;
    display: flex; gap: 4px; align-items: center;
    font-size: 11.5px; color: var(--ink-muted);
}
.wg-breadcrumb li + li::before { content: '/'; margin-right: 4px; color: var(--border); }
.wg-breadcrumb a { color: var(--ink-muted); text-decoration: none; }
.wg-breadcrumb a:hover { color: var(--navy); }
.wg-breadcrumb .active { color: var(--navy); font-weight: 600; }
.wg-topbar-actions { display: flex; gap: 8px; align-items: center; }

/* ===========================================
   BUTTONS
=========================================== */
.wg-btn {
    display: inline-flex; align-items: center; gap: 6px;
    border: 1.5px solid transparent;
    border-radius: var(--r-sm);
    padding: 7px 16px;
    font-size: 12.5px; font-weight: 600;
    font-family: var(--font);
    cursor: pointer;
    transition: all .15s;
    line-height: 1;
    white-space: nowrap;
}
.wg-btn-primary {
    background: var(--navy);
    border-color: var(--navy);
    color: #fff;
}
.wg-btn-primary:hover { background: var(--navy-mid); border-color: var(--navy-mid); }
.wg-btn-ghost {
    background: var(--white);
    border-color: var(--border);
    color: var(--ink-2);
}
.wg-btn-ghost:hover { background: var(--navy-light); border-color: var(--navy-soft); color: var(--navy); }
.wg-btn-sm { padding: 5px 11px !important; font-size: 11.5px !important; }

/* ===========================================
   STAT CARDS
=========================================== */
.wg-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
@media(max-width: 900px) { .wg-stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 480px) { .wg-stat-grid { grid-template-columns: 1fr 1fr; gap: 8px; } }

.wg-stat-card {
    background: var(--white);
    border: 1px solid var(--border-soft);
    border-top: 3px solid var(--navy);
    border-radius: 0 0 var(--r-md) var(--r-md);
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: var(--sh-sm);
    transition: box-shadow .15s;
}
.wg-stat-card:hover { box-shadow: var(--sh-md); }

.wg-stat-icon {
    width: 38px; height: 38px;
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.wg-stat-icon-slate { background: #eef0f4; color: var(--ink-2); }
.wg-stat-icon-navy  { background: var(--navy-light); color: var(--navy); }
.wg-stat-icon-green { background: var(--green-light); color: var(--green); }
.wg-stat-icon-red   { background: var(--red-light); color: var(--red); }

.wg-stat-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--ink-muted);
    margin-bottom: 4px;
}
.wg-stat-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1;
    font-family: var(--font-mono);
}
.wg-stat-value-green { color: var(--green); }
.wg-stat-value-red   { color: var(--red); }
.wg-stat-sub {
    font-size: 11px;
    color: var(--ink-faint);
    margin-top: 4px;
}

/* ===========================================
   PANELS
=========================================== */
.wg-panel {
    background: var(--white);
    border: 1px solid var(--border);
    border-top: 3px solid var(--navy);
    border-radius: 0 0 var(--r-md) var(--r-md);
    box-shadow: var(--sh-sm);
    overflow: hidden;
}

/* Panel header */
.wg-panel-header {
    background: var(--navy);
    padding: 11px 18px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    border-bottom: 2px solid rgba(255,255,255,.12);
}
.wg-panel-header-left  { display: flex; align-items: center; gap: 10px; }
.wg-panel-header-right { display: flex; align-items: center; gap: 8px; }
.wg-panel-header-icon {
    width: 32px; height: 32px;
    border-radius: var(--r-sm);
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wg-panel-title    { font-size: 13.5px; font-weight: 700; color: #fff; letter-spacing: .1px; }
.wg-panel-subtitle { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 1px; }
.wg-chip {
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.22);
    color: rgba(255,255,255,.9);
    border-radius: 3px;
    padding: 4px 12px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    display: inline-flex; align-items: center; gap: 5px;
}

/* Filter rows */
.wg-filter-row {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 18px;
    background: var(--white);
}
.wg-filter-row-divider {
    border-top: 1px solid var(--border-soft);
    background: #fafbfc;
}

.wg-field { display: flex; flex-direction: column; gap: 4px; }
.wg-field-grow { flex: 1; min-width: 200px; }

.wg-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--navy);
    display: flex; align-items: center; gap: 4px;
}

.wg-input {
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    padding: 6px 10px;
    font-size: 13px;
    font-family: var(--font);
    color: var(--ink);
    background: var(--white);
    outline: none;
    transition: border-color .15s;
    height: 34px;
}
.wg-input:focus { border-color: var(--navy); box-shadow: 0 0 0 2px rgba(26,54,96,.1); }

.wg-search-group {
    display: flex;
    align-items: center;
    gap: 5px;
    flex: 1;
    min-width: 260px;
}
.wg-search-group .wg-input { flex: 1; }

/* Segmented control */
.wg-seg {
    display: flex;
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    overflow: hidden;
}
.wg-seg input[type=radio] { display: none; }
.wg-seg label {
    flex: 1;
    text-align: center;
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    background: var(--white);
    color: var(--ink-muted);
    transition: all .12s;
    margin: 0;
    white-space: nowrap;
    user-select: none;
    border-right: 1px solid var(--border-soft);
    line-height: 1.4;
}
.wg-seg label:last-of-type { border-right: none; }
.wg-seg input[type=radio]:checked + label {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
}

.wg-hint {
    font-size: 11px;
    color: var(--ink-faint);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 4px;
}
.wg-hint kbd {
    background: #f0f2f5;
    border: 1px solid var(--border);
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 10px;
    color: var(--ink-2);
}

/* Table toolbar */
.wg-table-toolbar {
    background: #fafbfc;
    border-bottom: 1px solid var(--border-soft);
    padding: 7px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.wg-toolbar-count {
    font-size: 12px;
    color: var(--ink-muted);
}
.wg-toolbar-count strong {
    color: var(--navy);
    font-size: 13px;
}

/* ===========================================
   TABLE
=========================================== */
.wg-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.wg-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 12.5px;
    min-width: 720px;
}

.wg-th {
    background: #f0f3f8;
    color: var(--navy);
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    padding: 8px 12px;
    border-bottom: 2px solid var(--navy-soft);
    border-right: 1px solid var(--border-soft);
    white-space: nowrap;
    position: sticky; top: 0; z-index: 5;
}
.wg-th:last-child { border-right: none; }
.wg-th-check { width: 40px; text-align: center; }
.wg-th-right { text-align: right; }

.wg-tr { transition: background .1s; border-bottom: 1px solid var(--border-soft); }
.wg-tr:nth-child(odd)  { background: #ffffff; }
.wg-tr:nth-child(even) { background: #f9fafc; }
.wg-tr:hover           { background: var(--navy-light) !important; }

.wg-td {
    padding: 9px 12px;
    border-right: 1px solid var(--border-soft);
    vertical-align: middle;
    color: var(--ink);
}
.wg-td:last-child  { border-right: none; }
.wg-td-center      { text-align: center; }
.wg-td-right       { text-align: right; }
.wg-td-muted       { color: var(--ink-muted); font-size: 12px; }

/* Bill No badge */
.wg-billno {
    display: inline-block;
    background: var(--navy-light);
    color: var(--navy);
    border: 1px solid var(--navy-soft);
    border-radius: var(--r-sm);
    padding: 2px 9px;
    font-size: 11.5px;
    font-weight: 700;
    font-family: var(--font-mono);
    letter-spacing: .3px;
}

/* Name cell */
.wg-name-cell { display: flex; align-items: center; gap: 8px; }
.wg-avatar {
    width: 28px; height: 28px;
    border-radius: var(--r-sm);
    background: var(--navy);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wg-name-main { font-weight: 600; font-size: 13px; color: var(--ink); line-height: 1.2; }
.wg-name-sub  { font-size: 10.5px; color: var(--ink-muted); margin-top: 1px; }

/* Amount */
.wg-amount       { font-family: var(--font-mono); font-weight: 600; font-size: 12.5px; color: var(--ink); }
.wg-amount-green { color: var(--green); }
.wg-amount-red   { color: var(--red); }
.wg-amount-muted { color: #c0c8d4; }

/* Checkbox */
.wg-cb { width: 15px; height: 15px; accent-color: var(--navy); cursor: pointer; }

/* Group bar */
.wg-group-bar {
    background: var(--navy);
    color: #fff;
    padding: 8px 16px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
    font-size: 12px; font-weight: 700; letter-spacing: .3px;
}
.wg-group-bar-right { font-size: 11.5px; color: rgba(255,255,255,.6); font-weight: 400; }
.wg-group-bar i { color: rgba(255,255,255,.55); margin-right: 6px; }

/* Empty / loader */
.wg-empty {
    text-align: center;
    padding: 56px 20px;
    color: var(--ink-muted);
    font-size: 13.5px;
}
.wg-empty i { font-size: 2.5rem; color: var(--border); display: block; margin-bottom: 12px; }

.wg-loader {
    text-align: center;
    padding: 52px 20px;
    color: var(--navy);
    font-size: 13px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.wg-loader i { font-size: 1.8rem; }

/* Panel footer */
.wg-panel-footer {
    background: #fafbfc;
    border-top: 1px solid var(--border-soft);
    padding: 9px 18px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
}
.wg-footer-info { font-size: 12px; color: var(--ink-muted); white-space: nowrap; }
.wg-footer-hint { font-size: 11.5px; color: var(--ink-faint); display: flex; align-items: center; gap: 5px; }

/* Pagination */
.wg-pagination { display: flex; gap: 3px; flex-wrap: wrap; }
.wg-page-btn {
    width: 30px; height: 30px;
    border-radius: var(--r-sm);
    border: 1.5px solid var(--border);
    background: var(--white);
    color: var(--ink-muted);
    font-size: 12px; font-weight: 600;
    cursor: pointer;
    transition: all .12s;
    display: inline-flex; align-items: center; justify-content: center;
}
.wg-page-btn:hover   { border-color: var(--navy); color: var(--navy); background: var(--navy-light); }
.wg-page-btn.active  { background: var(--navy); border-color: var(--navy); color: #fff; }
.wg-page-btn:disabled { opacity: .35; cursor: not-allowed; }

/* Alert */
.wg-alert {
    border-radius: var(--r-md);
    border: none;
    font-size: 13.5px;
    font-weight: 500;
    box-shadow: var(--sh-sm);
    padding: 12px 16px;
}

/* Select2 override — white style */
.select2-container--bootstrap4 .select2-selection {
    height: 34px !important;
    border: 1.5px solid var(--border) !important;
    border-radius: var(--r-sm) !important;
    background: var(--white) !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 31px !important;
    font-size: 13px !important;
    color: var(--ink) !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { height: 31px !important; }
.select2-container--bootstrap4 .select2-selection:focus,
.select2-container--bootstrap4 .select2-container--focus .select2-selection {
    border-color: var(--navy) !important;
    box-shadow: 0 0 0 2px rgba(26,54,96,.1) !important;
}

/* Print */
@media print {
    .wg-panel-header, .wg-filter-row, .wg-stat-grid,
    .wg-panel-footer, .content-header, .main-header,
    .main-sidebar, .wg-th-check, .wg-td-center:first-child { display: none !important; }
    .wg-table { min-width: unset; font-size: 11px; }
    .wg-group-bar { background: #1a3660 !important; -webkit-print-color-adjust: exact; }
}
</style>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ===========================================
   PAYLIST JS
=========================================== */
const ROUTES = {
    list:    '{{ route("billing.paylist.list") }}',
    summary: '{{ route("billing.paylist.summary") }}',
    search:  '{{ route("billing.invoice.searchPatient") }}',
    print:   '{{ route("billing.invoice.print", ":id") }}',
};

let currentPage = 1;
let totalPages  = 1;
let selectedIds = new Set();
let allFlatRows = [];
let allSelected = false;

const today = new Date().toISOString().split('T')[0];
document.getElementById('singleDate').value = today;
document.getElementById('dateFrom').value   = today;
document.getElementById('dateTo').value     = today;

/* -- Select2 -- */
$('#patientSelect').select2({
    theme: 'bootstrap4',
    placeholder: '— All Patients —',
    allowClear: true,
    ajax: {
        url: ROUTES.search,
        dataType: 'json',
        delay: 300,
        data: params => ({ q: params.term }),
        processResults: data => ({
            results: data.map(p => ({
                id:   p.id,
                text: p.patientname + ' (' + p.patientcode + ')',
            }))
        }),
        cache: true,
    },
    minimumInputLength: 1,
});

$('#patientSelect').on('change', function () {
    const pid = $(this).val();
    document.querySelectorAll('[name=dateMode]').forEach(r => r.disabled = !!pid);
    const op = pid ? '.4' : '1';
    document.getElementById('singleWrap').style.opacity = op;
    document.getElementById('rangeWrap').style.opacity  = op;
    loadData(1);
});

/* -- Date Mode -- */
document.querySelectorAll('[name=dateMode]').forEach(r => {
    r.addEventListener('change', function () {
        document.getElementById('rangeWrap').style.display  = this.value === 'range'  ? 'flex' : 'none';
        document.getElementById('singleWrap').style.display = this.value === 'single' ? 'flex' : 'none';
    });
});

/* -- Params -- */
function getParams(page) {
    const pid      = $('#patientSelect').val();
    const dateMode = document.querySelector('[name=dateMode]:checked').value;
    const dateCol  = document.querySelector('[name=dateCol]:checked').value;

    const params = new URLSearchParams({
        page:        page,
        per_page:    document.getElementById('perPage').value,
        q:           document.getElementById('searchQ').value.trim(),
        date_column: dateCol,
    });

    if (pid) {
        params.append('patient_id', pid);
        params.append('date_mode', 'all');
        updateFilterBadge('all', true);
    } else if (dateMode === 'range') {
        params.append('date_mode', 'range');
        params.append('date_from', document.getElementById('dateFrom').value);
        params.append('date_to',   document.getElementById('dateTo').value);
        updateFilterBadge('range');
    } else if (dateMode === 'single') {
        params.append('date_mode', 'single');
        params.append('single_date', document.getElementById('singleDate').value);
        updateFilterBadge('single');
    } else {
        params.append('date_mode', 'all');
        updateFilterBadge('all');
    }

    return params;
}

function updateFilterBadge(mode, hasPatient) {
    const badge = document.getElementById('filterBadge');
    if (hasPatient) {
        badge.innerHTML = '<i class="fas fa-user"></i> Patient · All Time';
    } else if (mode === 'range') {
        const f = document.getElementById('dateFrom').value;
        const t = document.getElementById('dateTo').value;
        badge.innerHTML = `<i class="fas fa-calendar-week"></i> ${f} – ${t}`;
    } else if (mode === 'all') {
        badge.innerHTML = '<i class="fas fa-infinity"></i> All Time';
    } else {
        const sd = document.getElementById('singleDate').value;
        badge.innerHTML = `<i class="fas fa-calendar-day"></i> ${sd}`;
    }
}

/* -- Load -- */
function loadData(page) {
    currentPage = page;
    selectedIds.clear();
    allSelected = false;
    updateSelectUI();

    const params = getParams(page);

    document.getElementById('tableBody').innerHTML =
        '<div class="wg-loader"><i class="fas fa-circle-notch fa-spin"></i><div>Loading payment records…</div></div>';
    document.getElementById('paginationWrap').style.display = 'none';

    fetch(ROUTES.summary + '?' + params.toString())
        .then(r => r.json()).then(renderSummary).catch(() => {});

    fetch(ROUTES.list + '?' + params.toString())
        .then(r => r.json())
        .then(res => {
            allFlatRows = res.flat || [];
            renderTable(res.data, res.meta);
        })
        .catch(() => {
            document.getElementById('tableBody').innerHTML =
                '<div class="wg-empty"><i class="fas fa-exclamation-triangle"></i><p>Error loading data. Please try again.</p></div>';
        });
}

/* -- Summary -- */
function renderSummary(s) {
    document.getElementById('sumInvoices').textContent = s.total_invoices.toLocaleString();
    document.getElementById('sumPatients').textContent = s.total_patients + ' patient(s)';
    document.getElementById('sumBill').textContent     = '? ' + fmt(s.total_bill);
    document.getElementById('sumPaid').textContent     = '? ' + fmt(s.total_paid);
    document.getElementById('sumDue').textContent      = '? ' + fmt(s.total_due);
}

/* -- Table -- */
function renderTable(grouped, meta) {
    const keys = Object.keys(grouped || {});

    if (!keys.length) {
        document.getElementById('tableBody').innerHTML =
            '<div class="wg-empty"><i class="fas fa-inbox"></i><p>No payment records found.</p></div>';
        document.getElementById('tableInfo').textContent = 'No records';
        return;
    }

    document.getElementById('tableInfo').textContent =
        'Showing ' + (meta.from || 0) + '–' + (meta.to || 0) + ' of ' + meta.total + ' records';

    let html = '';

    keys.forEach(date => {
        const rows       = grouped[date];
        const groupTotal = rows.reduce((s, r) => s + parseFloat(r.TotalBill || 0), 0);

        html += `
        <div class="wg-group-bar">
            <div><i class="fas fa-calendar-day"></i>${formatDate(date)}</div>
            <div class="wg-group-bar-right">${rows.length} invoice(s) &nbsp;·&nbsp; ? ${fmt(groupTotal)}</div>
        </div>
        <div class="wg-table-wrap">
        <table class="wg-table">
        <thead><tr>
            <th class="wg-th wg-th-check">
                <input type="checkbox" class="wg-cb" onchange="toggleGroup(this,'${date}')">
            </th>
            <th class="wg-th">Bill No</th>
            <th class="wg-th">Patient</th>
            <th class="wg-th wg-th-right">Total Bill</th>
            <th class="wg-th wg-th-right">Paid</th>
            <th class="wg-th wg-th-right">Due</th>
            <th class="wg-th">Payment Date</th>
        </tr></thead>
        <tbody>`;

        rows.forEach(r => {
            const due   = parseFloat(r.DueAmount  || 0);
            const paid  = parseFloat(r.PaidAmount || 0);
            const total = parseFloat(r.TotalBill  || 0);
            const dueClass  = due  > 0 ? 'wg-amount-red'   : 'wg-amount-muted';
            const paidClass = paid >= total && total > 0 ? 'wg-amount-green' : '';
            const initial   = (r.PatientName || 'P').charAt(0).toUpperCase();

            html += `
            <tr class="wg-tr" data-id="${r.ID}" data-date="${date}">
                <td class="wg-td wg-td-center">
                    <input type="checkbox" class="wg-cb row-cb" data-id="${r.ID}" onchange="onRowCheck(this)">
                </td>
                <td class="wg-td">
                    <span class="wg-billno">${r.BillNo || '—'}</span>
                </td>
                <td class="wg-td">
                    <div class="wg-name-cell">
                        <div class="wg-avatar">${initial}</div>
                        <div>
                            <div class="wg-name-main">${r.PatientName || '—'}</div>
                            <div class="wg-name-sub">${r.PatientCode || ''}</div>
                        </div>
                    </div>
                </td>
                <td class="wg-td wg-td-right">
                    <span class="wg-amount">? ${fmt(total)}</span>
                </td>
                <td class="wg-td wg-td-right">
                    <span class="wg-amount ${paidClass}">? ${fmt(paid)}</span>
                </td>
                <td class="wg-td wg-td-right">
                    <span class="wg-amount ${dueClass}">? ${fmt(due)}</span>
                </td>
                <td class="wg-td wg-td-muted">
                    <i class="fas fa-calendar-alt" style="color:#c0c8d4;font-size:10px;margin-right:4px;"></i>${r.PaymentDate || r.created_at || '—'}
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
    });

    document.getElementById('tableBody').innerHTML = html;
    renderPagination(meta);
}

/* -- Pagination -- */
function renderPagination(meta) {
    totalPages = meta.last_page;
    document.getElementById('pageInfo').textContent =
        'Page ' + meta.current_page + ' of ' + meta.last_page + ' (' + meta.total + ' records)';

    let btns = `<button class="wg-page-btn" onclick="loadData(${meta.current_page - 1})"
        ${meta.current_page <= 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;

    pageRange(meta.current_page, meta.last_page).forEach(p => {
        if (p === '...') {
            btns += `<button class="wg-page-btn" disabled>…</button>`;
        } else {
            btns += `<button class="wg-page-btn ${p == meta.current_page ? 'active' : ''}"
                onclick="loadData(${p})">${p}</button>`;
        }
    });

    btns += `<button class="wg-page-btn" onclick="loadData(${meta.current_page + 1})"
        ${meta.current_page >= meta.last_page ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;

    document.getElementById('pageBtns').innerHTML = btns;
    document.getElementById('paginationWrap').style.display = 'flex';
}

function pageRange(cur, last) {
    if (last <= 7) return Array.from({length: last}, (_, i) => i + 1);
    if (cur <= 4)      return [1,2,3,4,5,'...',last];
    if (cur >= last-3) return [1,'...',last-4,last-3,last-2,last-1,last];
    return [1,'...',cur-1,cur,cur+1,'...',last];
}

/* -- Checkbox -- */
function onRowCheck(cb) {
    const id = parseInt(cb.dataset.id);
    cb.checked ? selectedIds.add(id) : selectedIds.delete(id);
    updateSelectUI();
}
function toggleGroup(masterCb, date) {
    document.querySelectorAll('.row-cb').forEach(cb => {
        const row = cb.closest('tr');
        if (row && row.dataset.date === date) {
            cb.checked = masterCb.checked;
            const id = parseInt(cb.dataset.id);
            masterCb.checked ? selectedIds.add(id) : selectedIds.delete(id);
        }
    });
    updateSelectUI();
}
function toggleSelectAll() {
    allSelected = !allSelected;
    document.querySelectorAll('.row-cb').forEach(cb => {
        cb.checked = allSelected;
        const id = parseInt(cb.dataset.id);
        allSelected ? selectedIds.add(id) : selectedIds.delete(id);
    });
    updateSelectUI();
}
function updateSelectUI() {
    document.getElementById('selectedCount').textContent = selectedIds.size;
    document.getElementById('btnBulkPrint').style.display = selectedIds.size > 0 ? 'inline-flex' : 'none';
}

/* -- Bulk Print -- */
function doBulkPrint() {
    if (!selectedIds.size) return;
    const ids  = Array.from(selectedIds);
    const rows = allFlatRows.filter(r => ids.includes(r.ID));

    let html = `<html><head><style>
        body{font-family:'IBM Plex Sans',Arial,sans-serif;font-size:12px;color:#0f1923;}
        .inv-block{border:1px solid #d4d9e2;margin-bottom:22px;padding:14px;border-radius:4px;page-break-inside:avoid;border-top:3px solid #1a3660;}
        .inv-title{font-size:13px;font-weight:700;margin-bottom:10px;color:#1a3660;padding-bottom:6px;border-bottom:1px solid #e8eaef;}
        table{width:100%;border-collapse:collapse;margin-top:8px;}
        th,td{border:1px solid #e8eaef;padding:6px 10px;text-align:left;font-size:11.5px;}
        th{background:#f0f3f8;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#1a3660;font-size:10.5px;}
        .amount{font-weight:600;font-family:'IBM Plex Mono',monospace;}
    </style></head><body>`;

    rows.forEach(r => {
        html += `<div class="inv-block">
            <div class="inv-title">Invoice: ${r.BillNo || '—'}</div>
            <table>
            <tr><th>Patient</th><td>${r.PatientName} (${r.PatientCode})</td><th>Mobile</th><td>${r.MobileNo || '—'}</td></tr>
            <tr><th>Total Bill</th><td class="amount">? ${fmt(r.TotalBill)}</td><th>Paid</th><td class="amount">? ${fmt(r.PaidAmount)}</td></tr>
            <tr><th>Due</th><td class="amount">? ${fmt(r.DueAmount)}</td><th>Date</th><td>${r.PaymentDate || r.created_at || '—'}</td></tr>
            </table>
        </div>`;
    });

    html += '</body></html>';
    const frame = document.getElementById('printFrame');
    frame.style.display = 'block';
    frame.contentDocument.open();
    frame.contentDocument.write(html);
    frame.contentDocument.close();
    setTimeout(() => { frame.contentWindow.print(); frame.style.display = 'none'; }, 400);
}

/* -- Reset -- */
function resetFilter() {
    $('#patientSelect').val(null).trigger('change');
    document.getElementById('searchQ').value       = '';
    document.getElementById('singleDate').value    = today;
    document.getElementById('dateFrom').value      = today;
    document.getElementById('dateTo').value        = today;
    document.querySelector('#modeAll').checked     = true;
    document.querySelector('#colPayDate').checked  = true;
    document.getElementById('rangeWrap').style.display  = 'none';
    document.getElementById('singleWrap').style.display = 'none';
    document.querySelectorAll('[name=dateMode]').forEach(r => r.disabled = false);
    document.getElementById('singleWrap').style.opacity = '1';
    document.getElementById('rangeWrap').style.opacity  = '1';
    updateFilterBadge('all');
    loadData(1);
}

/* -- Helpers -- */
function fmt(v) {
    return parseFloat(v || 0).toLocaleString('en-BD', {minimumFractionDigits:2, maximumFractionDigits:2});
}
function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-BD', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
}

/* -- Init -- */
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('singleWrap').style.display = 'none';
    document.getElementById('rangeWrap').style.display  = 'none';
    updateFilterBadge('all');
    loadData(1);
});
</script>
@endpush