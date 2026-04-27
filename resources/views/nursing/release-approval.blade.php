@extends('adminlte::page')

@section('title', 'Release Approval | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-clipboard-check"></i></span>
                Release Approval
            </h1>
            <ol class="breadcrumb mt-1 p-0" style="background:transparent; font-size:12px;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('nursing.index') }}">Nursing</a></li>
                <li class="breadcrumb-item active">Release Approval</li>
            </ol>
        </div>
        <div>
            <a href="{{ route('nursing.index') }}" class="btn btn-back-modern btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')

<div id="save-alert" class="alert d-none mb-3 modern-alert" role="alert"></div>

{{-- ══ GOV PANEL ══ --}}
<div class="gov-panel gov-panel-orange">

    {{-- Panel Title Bar --}}
    <div class="gov-panel-titlebar gov-panel-titlebar-orange">
        <div class="gov-panel-titlebar-left">
            <div class="gov-panel-icon gov-panel-icon-orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="gov-panel-title">Pending Release Approvals</div>
                <div class="gov-panel-subtitle">Nurse submitted — waiting for manager approval</div>
            </div>
        </div>
        <div class="gov-panel-titlebar-right">
            <span class="gov-counter-badge gov-counter-badge-orange" id="pending-count-badge">
                <i class="fas fa-clock mr-1"></i>
                Pending: <strong>{{ $patients->total() }}</strong>
            </span>
        </div>
    </div>

    {{-- Search Toolbar --}}
    <div class="gov-toolbar gov-toolbar-orange">
        <div class="gov-toolbar-inner">
            <div class="gov-toolbar-label gov-toolbar-label-orange">
                <i class="fas fa-search mr-1"></i> SEARCH FILTER
            </div>
            <div class="gov-search-group">
                <input type="text" id="patientSearch" class="gov-search-input gov-search-input-orange"
                       placeholder="Search by Name / Patient Code / Mobile Number…"
                       onkeyup="filterTable()">
                <button class="gov-search-btn gov-search-btn-orange" type="button" onclick="filterTable()">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                <button class="gov-clear-btn" type="button"
                        onclick="document.getElementById('patientSearch').value=''; filterTable();">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
            </div>
            <div class="gov-toolbar-hint">
                <i class="fas fa-info-circle mr-1"></i>
                Press <kbd>Enter</kbd> or click Search to filter
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="gov-table-wrap">
        <table class="gov-table" id="patientTable">
            <thead>
                <tr>
                    <th class="gov-th" style="width:46px;">SL#</th>
                    <th class="gov-th" style="width:88px;">Pt. Code</th>
                    <th class="gov-th">Patient Name</th>
                    <th class="gov-th" style="width:52px;">Age</th>
                    <th class="gov-th" style="width:50px;">Sex</th>
                    <th class="gov-th" style="width:128px;">Mobile</th>
                    <th class="gov-th">Address / Upazila</th>
                    <th class="gov-th" style="width:112px;">Admission Date</th>
                    <th class="gov-th" style="width:128px;">Submitted At</th>
                    <th class="gov-th" style="width:80px;">Status</th>
                    <th class="gov-th gov-th-action" style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    @php
                        $g = strtolower($patient->gender ?? '');
                    @endphp
                    <tr class="gov-tr" id="row-{{ $patient->admission_id }}">
                        <td class="gov-td gov-td-sl">{{ $patient->id }}</td>
                        <td class="gov-td">
                            <span class="gov-code-badge gov-code-badge-orange">{{ $patient->patientcode ?? '—' }}</span>
                        </td>
                        <td class="gov-td">
                            <div class="gov-name-cell">
                                <div class="gov-avatar gov-avatar-orange">
                                    {{ strtoupper(substr($patient->patientname ?? 'P', 0, 1)) }}
                                </div>
                                <div class="gov-name-info">
                                    <span class="gov-name-text">{{ $patient->patientname ?? '—' }}</span>
                                    @if($patient->patientfather ?? null)
                                        <span class="gov-father-text">
                                            <i class="fas fa-user-tie fa-xs mr-1"></i>{{ $patient->patientfather }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="gov-td gov-td-center">{{ $patient->age ?? '—' }}</td>
                        <td class="gov-td gov-td-center">
                            @if($g === 'male')
                                <span class="gov-gender gov-gender-m">M</span>
                            @elseif($g === 'female')
                                <span class="gov-gender gov-gender-f">F</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-mono">{{ $patient->mobile_no ?? '—' }}</td>
                        <td class="gov-td gov-td-muted">
                            {{ $patient->address ?? '' }}{{ ($patient->upozila ?? null) ? ', '.$patient->upozila : '' }}
                        </td>
                        <td class="gov-td">
                            @if($patient->admission_date ?? null)
                                <span class="gov-date-text">
                                    <i class="fas fa-calendar-alt mr-1" style="color:var(--gov-orange-hdr);font-size:10px;"></i>
                                    {{ \Carbon\Carbon::parse($patient->admission_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td">
                            @if($patient->submitted_at ?? null)
                                <span class="gov-date-text">
                                    <i class="fas fa-clock mr-1" style="color:var(--gov-orange-hdr);font-size:10px;"></i>
                                    {{ \Carbon\Carbon::parse($patient->submitted_at)->format('d M, h:i A') }}
                                </span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-center">
                            <span class="gov-status-badge gov-status-pending">
                                <i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i>
                                Pending
                            </span>
                        </td>
                        <td class="gov-td gov-td-action">
                            <button type="button"
                                class="gov-approve-btn"
                                onclick="confirmAction('approve', {{ $patient->admission_id }}, {{ $patient->id }}, '{{ addslashes($patient->patientname ?? '') }}')"
                                title="Approve Release">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                            <button type="button"
                                class="gov-reject-btn"
                                onclick="confirmAction('reject', {{ $patient->admission_id }}, {{ $patient->id }}, '{{ addslashes($patient->patientname ?? '') }}')"
                                title="Reject Release">
                                <i class="fas fa-times mr-1"></i> Reject
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11">
                            <div class="gov-empty-state">
                                <i class="fas fa-check-double" style="color:#00897b;"></i>
                                <p>No pending release approvals. All caught up!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Panel Footer / Pagination --}}
    @if(method_exists($patients, 'links'))
    <div class="gov-panel-footer">
        <div class="gov-footer-info">
            <i class="fas fa-list-ul mr-1"></i>
            Showing <strong>{{ $patients->firstItem() ?? 0 }}</strong>
            to <strong>{{ $patients->lastItem() ?? 0 }}</strong>
            of <strong>{{ $patients->total() }}</strong> pending
        </div>
        <div class="gov-pagination-wrap">
            {{ $patients->links('pagination::bootstrap-4') }}
        </div>
        <div class="gov-footer-hint">
            <i class="fas fa-info-circle mr-1" style="color:var(--gov-orange-hdr);"></i>
            <strong>Approve</strong> করলে patient released হবে।
            <strong>Reject</strong> করলে nurse-এর discharge list এ ফিরে যাবে।
        </div>
    </div>
    @endif

</div>

{{-- ══ CONFIRM MODAL ══ --}}
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:4px; border:none; box-shadow:0 8px 32px rgba(0,0,0,.22); overflow:hidden; border-top:3px solid var(--gov-orange-hdr);">

            <div class="modal-header border-0 pb-0" style="padding:18px 22px 10px; background:#fafbfd; border-bottom:1px solid #e8ecf4;">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div class="modal-icon-wrap" id="modal-icon-wrap">
                        <i class="fas fa-check" id="modal-header-icon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold" id="modal-title" style="font-size:15px;">Confirm Action</h5>
                        <small class="text-muted" id="modal-subtitle">Please review before proceeding</small>
                    </div>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" style="font-size:20px;">&times;</button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="modal-patient-bar">
                    <div class="modal-patient-avatar" id="modal-patient-avatar">P</div>
                    <div>
                        <div class="modal-patient-name" id="modal-patient-name">—</div>
                        <div class="modal-patient-sub" id="modal-action-desc">—</div>
                    </div>
                </div>

                <div id="reject-reason-wrap" style="display:none; margin-top:14px;">
                    <label class="gov-field-label">
                        Rejection Reason <span class="text-muted font-weight-normal">(optional)</span>
                    </label>
                    <textarea class="gov-textarea" id="reject-reason" rows="2"
                        placeholder="কেন reject করছেন লিখুন (optional)..."></textarea>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4" style="gap:8px;">
                <button type="button" class="btn-modal-action btn-modal-cancel" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn-modal-action" id="modal-confirm-btn" onclick="submitAction()">
                    <i class="fas fa-check mr-2"></i> Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   ROOT VARIABLES
═══════════════════════════════════════════════════════ */
:root {
    --blue-deep:  #1565C0; --blue-mid: #1976D2; --blue-light: #E3F2FD;
    --teal-deep:  #00695C; --teal-mid: #00796B; --teal-light: #E0F2F1; --teal-soft: #B2DFDB;
    --text-primary: #1a2332; --text-muted: #6b7a90; --border: #e4e9f0;
    --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px;
    --shadow-sm: 0 1px 4px rgba(0,0,0,.06); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --font-base: 'DM Sans','Hind Siliguri',Arial,sans-serif;

    /* Gov palette */
    --gov-bg:           #f2f4f7;
    --gov-header:       #1a3a5c;
    --gov-header2:      #1e4976;
    --gov-accent:       #c9972a;
    --gov-border:       #c8cdd6;
    --gov-row-odd:      #ffffff;
    --gov-row-even:     #f6f8fb;
    --gov-row-hover:    #fff4ec;
    --gov-text:         #1c2b3a;
    --gov-muted:        #6b7890;

    /* Orange gov palette */
    --gov-orange-hdr:   #b84b00;
    --gov-orange-hdr2:  #d05500;
    --gov-orange-accent:#e65100;
    --gov-orange-light: #fff4ec;
    --gov-orange-soft:  #ffe0cc;
    --gov-orange-border:#f0c090;
}
body, .content-wrapper { background: var(--gov-bg) !important; font-family: var(--font-base); }

/* ═══════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════ */
.page-main-title {
    font-size: 22px; font-weight: 700;
    color: var(--text-primary);
    display: flex; align-items: center; gap: 10px;
}
.page-title-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--gov-orange-light);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--gov-orange-accent); font-size: 17px;
}
.btn-back-modern {
    background: #fff; border: 1.5px solid var(--border);
    color: var(--text-primary); border-radius: var(--radius-sm);
    font-weight: 500; padding: 6px 14px; font-size: 13px;
    transition: all .2s; text-decoration: none;
}
.btn-back-modern:hover { background: var(--gov-orange-light); border-color: var(--gov-orange-accent); color: var(--gov-orange-accent); }

/* ═══════════════════════════════════════════════════════
   ALERT
═══════════════════════════════════════════════════════ */
.modern-alert { border-radius: var(--radius-md); border: none; font-size: 13.5px; font-weight: 500; box-shadow: var(--shadow-sm); }

/* ═══════════════════════════════════════════════════════
   GOV PANEL — ORANGE VARIANT
═══════════════════════════════════════════════════════ */
.gov-panel {
    background: #fff;
    border: 1px solid var(--gov-border);
    border-top: 3px solid var(--gov-header);
    border-radius: 0 0 4px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    margin-bottom: 16px;
    overflow: hidden;
}
.gov-panel-orange {
    border-top-color: var(--gov-orange-hdr);
}

/* Title bar */
.gov-panel-titlebar {
    background: linear-gradient(90deg, var(--gov-header) 0%, var(--gov-header2) 100%);
    padding: 10px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    border-bottom: 2px solid var(--gov-accent);
}
.gov-panel-titlebar-orange {
    background: linear-gradient(90deg, var(--gov-orange-hdr) 0%, var(--gov-orange-hdr2) 100%);
    border-bottom-color: var(--gov-accent);
}
.gov-panel-titlebar-left  { display: flex; align-items: center; gap: 10px; }
.gov-panel-titlebar-right { display: flex; align-items: center; gap: 10px; }

.gov-panel-icon {
    width: 34px; height: 34px; border-radius: 4px;
    background: rgba(255,255,255,.15);
    color: #fff; font-size: 15px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; border: 1px solid rgba(255,255,255,.2);
}
.gov-panel-icon-orange { background: rgba(255,255,255,.12); }
.gov-panel-title    { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; letter-spacing: .2px; }
.gov-panel-subtitle { font-size: 11px; color: rgba(255,255,255,.7); margin-top: 1px; }

.gov-counter-badge {
    background: rgba(255,255,255,.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 3px; padding: 4px 12px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.gov-counter-badge-orange { background: rgba(255,255,255,.12); }

/* Toolbar */
.gov-toolbar {
    background: #f0f3f8;
    border-bottom: 1.5px solid var(--gov-border);
    padding: 8px 16px;
}
.gov-toolbar-orange { background: #fef6f0; border-bottom-color: var(--gov-orange-border); }
.gov-toolbar-inner  { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.gov-toolbar-label {
    font-size: 11px; font-weight: 800; color: var(--gov-header);
    text-transform: uppercase; letter-spacing: .8px;
    white-space: nowrap; flex-shrink: 0;
}
.gov-toolbar-label-orange { color: var(--gov-orange-hdr); }
.gov-toolbar-hint { font-size: 11px; color: var(--gov-muted); white-space: nowrap; }
.gov-toolbar-hint kbd {
    background: #fff; border: 1px solid var(--gov-border);
    border-radius: 3px; padding: 1px 5px;
    font-size: 10px; color: var(--gov-header);
}
.gov-search-group { display: flex; align-items: center; gap: 4px; flex: 1; min-width: 260px; }
.gov-search-input {
    flex: 1; border: 1.5px solid var(--gov-border);
    border-radius: 3px; padding: 6px 10px;
    font-size: 13px; color: var(--gov-text); background: #fff;
    outline: none; transition: border-color .2s;
    font-family: var(--font-base); height: 32px;
}
.gov-search-input-orange:focus { border-color: var(--gov-orange-hdr); box-shadow: 0 0 0 2px rgba(184,75,0,.12); }
.gov-search-btn {
    border: none; border-radius: 3px; padding: 0 14px;
    height: 32px; font-size: 12px; font-weight: 700;
    cursor: pointer; transition: background .2s;
    background: var(--gov-header); color: #fff;
    display: inline-flex; align-items: center;
    white-space: nowrap; letter-spacing: .2px;
}
.gov-search-btn-orange { background: var(--gov-orange-hdr); }
.gov-search-btn-orange:hover { background: var(--gov-orange-hdr2); }
.gov-clear-btn {
    border: 1.5px solid var(--gov-border); border-radius: 3px;
    padding: 0 10px; height: 32px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all .2s;
    background: #fff; color: var(--gov-muted);
    display: inline-flex; align-items: center; white-space: nowrap;
}
.gov-clear-btn:hover { background: #ffebee; color: #c62828; border-color: #ffcdd2; }

/* ═══════════════════════════════════════════════════════
   GOV TABLE
═══════════════════════════════════════════════════════ */
.gov-table-wrap { overflow-x: auto; }
.gov-table { border-collapse: collapse; width: 100%; font-size: 12.5px; }
.gov-th {
    background: #fdf0e8;
    color: var(--gov-orange-hdr);
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    padding: 8px 10px;
    border-bottom: 2px solid var(--gov-orange-border);
    border-right: 1px solid #f0d0b8;
    white-space: nowrap; position: sticky; top: 0; z-index: 5;
}
.gov-th:last-child { border-right: none; }
.gov-th-action { text-align: center; }

.gov-tr { transition: background .12s; }
.gov-tr:nth-child(odd)  { background: var(--gov-row-odd); }
.gov-tr:nth-child(even) { background: #fdf9f6; }
.gov-tr:hover { background: #fff4ec !important; }

.gov-td {
    padding: 7px 10px;
    border-bottom: 1px solid #eaedf2;
    border-right: 1px solid #f0f2f6;
    vertical-align: middle; color: var(--gov-text);
}
.gov-td:last-child { border-right: none; }
.gov-td-sl     { color: var(--gov-muted); font-size: 11.5px; text-align: center; }
.gov-td-center { text-align: center; }
.gov-td-mono   { font-family: 'Courier New', monospace; font-size: 12px; letter-spacing: .3px; }
.gov-td-muted  { color: var(--gov-muted); font-size: 12px; }
.gov-td-action { text-align: center; }

/* Name cell */
.gov-name-cell { display: flex; align-items: center; gap: 7px; }
.gov-avatar {
    width: 26px; height: 26px; border-radius: 3px;
    background: var(--gov-header); color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.gov-avatar-orange { background: linear-gradient(135deg, var(--gov-orange-hdr), #e65100); }
.gov-name-info  { display: flex; flex-direction: column; gap: 1px; }
.gov-name-text  { font-weight: 600; font-size: 13px; color: var(--gov-text); line-height: 1.2; }
.gov-father-text{ font-size: 10.5px; color: var(--gov-muted); }

/* Badges */
.gov-code-badge {
    background: #e8ecf4; color: var(--gov-header);
    border: 1px solid #c8cdd6; border-radius: 2px;
    padding: 1px 7px; font-size: 11.5px; font-weight: 700;
    font-family: 'Courier New', monospace; letter-spacing: .3px;
}
.gov-code-badge-orange { background: #fdf0e8; color: var(--gov-orange-hdr); border-color: var(--gov-orange-border); }

.gov-gender {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px; border-radius: 50%;
    font-size: 11px; font-weight: 800;
}
.gov-gender-m { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.gov-gender-f { background: #fce7f3; color: #be185d; border: 1px solid #f9a8d4; }

.gov-date-text { display: block; font-size: 12.5px; font-weight: 600; color: var(--gov-text); }
.gov-muted     { color: var(--gov-muted); font-size: 12px; }

/* Status badge */
.gov-status-badge {
    display: inline-flex; align-items: center;
    border-radius: 3px; padding: 2px 9px;
    font-size: 11px; font-weight: 700;
}
.gov-status-pending {
    background: var(--gov-orange-light);
    color: var(--gov-orange-hdr);
    border: 1px solid var(--gov-orange-border);
}

/* ═══════════════════════════════════════════════════════
   ACTION BUTTONS
═══════════════════════════════════════════════════════ */
.gov-approve-btn {
    background: var(--gov-orange-hdr);
    color: #fff; border: none; border-radius: 3px;
    padding: 5px 11px; font-size: 11.5px; font-weight: 700;
    cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center;
    white-space: nowrap; letter-spacing: .2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.18);
    margin-right: 4px;
}
.gov-approve-btn:hover {
    background: #2e7d32; transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(46,125,50,.3);
}
.gov-reject-btn {
    background: #fff; color: #c62828;
    border: 1.5px solid #ffcdd2; border-radius: 3px;
    padding: 4px 11px; font-size: 11.5px; font-weight: 700;
    cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center;
    white-space: nowrap; letter-spacing: .2px;
}
.gov-reject-btn:hover {
    background: #c62828; color: #fff; border-color: #c62828;
    transform: translateY(-1px); box-shadow: 0 3px 8px rgba(198,40,40,.3);
}

/* Empty state */
.gov-empty-state { text-align: center; padding: 44px; color: #b0bec5; }
.gov-empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
.gov-empty-state p { font-size: 14px; margin: 0; }

/* ═══════════════════════════════════════════════════════
   PANEL FOOTER / PAGINATION
═══════════════════════════════════════════════════════ */
.gov-panel-footer {
    background: #fef6f0;
    border-top: 1.5px solid var(--gov-orange-border);
    padding: 8px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
}
.gov-footer-info  { font-size: 12px; color: var(--gov-muted); white-space: nowrap; }
.gov-footer-hint  { font-size: 11.5px; color: var(--gov-muted); }
.gov-footer-hint strong { color: var(--gov-orange-hdr); }
.gov-pagination-wrap .pagination { margin-bottom: 0; }
.gov-pagination-wrap .page-link  {
    border-radius: 3px !important; border-color: var(--gov-border);
    color: var(--gov-orange-hdr); font-size: 12.5px; padding: 5px 10px;
}
.gov-pagination-wrap .page-item.active .page-link {
    background: var(--gov-orange-hdr); border-color: var(--gov-orange-hdr);
}

/* ═══════════════════════════════════════════════════════
   MODAL
═══════════════════════════════════════════════════════ */
.modal-icon-wrap {
    width: 38px; height: 38px; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}
.modal-icon-approve { background: #e8f5e9; color: #2e7d32; }
.modal-icon-reject  { background: #ffebee; color: #c62828; }

.modal-patient-bar {
    display: flex; align-items: center; gap: 12px;
    background: var(--gov-orange-light);
    border-radius: 4px;
    border: 1.5px solid var(--gov-orange-border);
    padding: 10px 14px;
}
.modal-patient-avatar {
    width: 36px; height: 36px; border-radius: 3px;
    background: linear-gradient(135deg, var(--gov-orange-hdr), #e65100);
    color: #fff; font-size: 15px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.modal-patient-name { font-weight: 700; font-size: 14px; color: var(--gov-text); }
.modal-patient-sub  { font-size: 12px; color: var(--gov-muted); margin-top: 2px; }

.gov-field-label {
    display: block; font-size: 11px; font-weight: 800;
    color: var(--gov-muted); text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: 6px;
}
.gov-textarea {
    width: 100%; border: 1.5px solid var(--gov-border);
    border-radius: 3px; padding: 8px 10px;
    font-size: 13px; color: var(--gov-text);
    background: #fff; outline: none;
    transition: border-color .2s; font-family: var(--font-base);
    resize: vertical;
}
.gov-textarea:focus { border-color: var(--gov-orange-hdr); box-shadow: 0 0 0 2px rgba(184,75,0,.1); }

.btn-modal-action {
    border-radius: 3px; padding: 9px 22px;
    font-size: 13px; font-weight: 700; border: none;
    cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; justify-content: center;
    letter-spacing: .2px;
}
.btn-modal-cancel { background: #fff; color: var(--gov-muted); border: 1.5px solid var(--gov-border); }
.btn-modal-cancel:hover { background: #f0f4f8; }
.btn-confirm-approve { background: linear-gradient(135deg, #2e7d32, #388e3c); color: #fff; box-shadow: 0 3px 10px rgba(46,125,50,.28); }
.btn-confirm-approve:hover { background: linear-gradient(135deg, #1b5e20, #2e7d32); }
.btn-confirm-reject  { background: linear-gradient(135deg, #c62828, #e53935); color: #fff; box-shadow: 0 3px 10px rgba(198,40,40,.28); }
.btn-confirm-reject:hover  { background: linear-gradient(135deg, #b71c1c, #c62828); }

@media print { .gov-toolbar, .gov-panel-titlebar { display: none !important; } }
</style>
@stop

@section('js')
<script>
var CSRF_TOKEN  = '{{ csrf_token() }}';
var APPROVE_URL = '{{ url("/nursing/release-approval/approve") }}';
var REJECT_URL  = '{{ url("/nursing/release-approval/reject") }}';

var _currentAction      = '';
var _currentAdmissionId = null;
var _currentPatientId   = null;
var _currentPatientName = '';

/* ══════════════════════════════════════════
   HELPERS
══════════════════════════════════════════ */
function showAlert(type, msg){
    var el = document.getElementById('save-alert');
    el.className = 'alert alert-' + type + ' modern-alert';
    el.innerHTML = msg;
    el.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(function(){ el.classList.add('d-none'); }, 6000);
}

function showToast(msg, type){
    var bg = type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#b84b00');
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;background:'+bg+';color:#fff;padding:12px 20px;border-radius:4px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.2);max-width:320px;';
    t.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + msg;
    document.body.appendChild(t);
    setTimeout(function(){
        t.style.opacity = '0';
        t.style.transition = 'opacity .3s';
        setTimeout(function(){ t.remove(); }, 300);
    }, 3000);
}

/* ══════════════════════════════════════════
   CONFIRM MODAL
══════════════════════════════════════════ */
function confirmAction(action, admissionId, patientId, patientName){
    _currentAction      = action;
    _currentAdmissionId = admissionId;
    _currentPatientId   = patientId;
    _currentPatientName = patientName;

    var isApprove = action === 'approve';

    document.getElementById('modal-icon-wrap').className   = 'modal-icon-wrap ' + (isApprove ? 'modal-icon-approve' : 'modal-icon-reject');
    document.getElementById('modal-header-icon').className = 'fas ' + (isApprove ? 'fa-check' : 'fa-times');
    document.getElementById('modal-title').textContent     = isApprove ? 'Approve Release' : 'Reject Release';
    document.getElementById('modal-subtitle').textContent  = isApprove
        ? 'Patient will be marked as Released'
        : 'Patient will be returned to Discharge list';

    document.getElementById('modal-patient-avatar').textContent = (patientName || 'P').charAt(0).toUpperCase();
    document.getElementById('modal-patient-name').textContent   = patientName || '—';
    document.getElementById('modal-action-desc').textContent    = isApprove
        ? 'Approve করলে patient কে released করা হবে।'
        : 'Reject করলে nurse-এর discharge list এ ফেরত যাবে।';

    document.getElementById('reject-reason-wrap').style.display = isApprove ? 'none' : 'block';
    document.getElementById('reject-reason').value = '';

    var btn = document.getElementById('modal-confirm-btn');
    btn.className = 'btn-modal-action ' + (isApprove ? 'btn-confirm-approve' : 'btn-confirm-reject');
    btn.innerHTML = isApprove
        ? '<i class="fas fa-check mr-2"></i> Yes, Approve'
        : '<i class="fas fa-times mr-2"></i> Yes, Reject';

    $('#confirmModal').modal('show');
}

/* ══════════════════════════════════════════
   SUBMIT APPROVE / REJECT
══════════════════════════════════════════ */
function submitAction(){
    var url     = _currentAction === 'approve' ? APPROVE_URL : REJECT_URL;
    var payload = {
        admission_id : _currentAdmissionId,
        patient_id   : _currentPatientId,
        reason       : document.getElementById('reject-reason').value.trim(),
    };

    var btn = document.getElementById('modal-confirm-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

    fetch(url, {
        method  : 'POST',
        headers : {
            'X-CSRF-TOKEN' : CSRF_TOKEN,
            'Accept'       : 'application/json',
            'Content-Type' : 'application/json'
        },
        body : JSON.stringify(payload),
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        $('#confirmModal').modal('hide');
        btn.disabled = false;

        if(data.success){
            var row = document.getElementById('row-' + _currentAdmissionId);
            if(row){
                row.style.transition = 'opacity .4s';
                row.style.opacity    = '0';
                setTimeout(function(){ row.remove(); updatePendingCount(); }, 400);
            }
            var isApprove = _currentAction === 'approve';
            showToast(data.message || (isApprove ? 'Patient released!' : 'Rejected & returned.'), 'success');
            showAlert('success', '<i class="fas fa-check-circle mr-2"></i>' + (data.message || 'Done.'));
        } else {
            btn.innerHTML = _currentAction === 'approve'
                ? '<i class="fas fa-check mr-2"></i> Yes, Approve'
                : '<i class="fas fa-times mr-2"></i> Yes, Reject';
            showAlert('danger', '<i class="fas fa-exclamation-circle mr-2"></i>' + (data.message || 'Action failed.'));
        }
    })
    .catch(function(e){
        $('#confirmModal').modal('hide');
        btn.disabled = false;
        showAlert('danger', '<i class="fas fa-exclamation-circle mr-2"></i> Network error: ' + e.message);
    });
}

/* ══════════════════════════════════════════
   UPDATE PENDING COUNT
══════════════════════════════════════════ */
function updatePendingCount(){
    var rows  = document.querySelectorAll('#patientTable tbody tr[id]');
    var badge = document.getElementById('pending-count-badge');
    if(badge) badge.innerHTML = '<i class="fas fa-clock mr-1"></i> Pending: <strong>' + rows.length + '</strong>';
    if(rows.length === 0){
        document.querySelector('#patientTable tbody').innerHTML =
            '<tr><td colspan="11"><div class="gov-empty-state">' +
            '<i class="fas fa-check-double" style="color:#00897b;"></i>' +
            '<p>No pending release approvals. All caught up!</p>' +
            '</div></td></tr>';
    }
}

/* ══════════════════════════════════════════
   TABLE SEARCH
══════════════════════════════════════════ */
function filterTable(){
    var q = document.getElementById('patientSearch').value.toLowerCase();
    document.querySelectorAll('#patientTable tbody tr').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
document.getElementById('patientSearch').addEventListener('keyup', function(e){
    if(e.key === 'Enter') filterTable();
});
</script>
@stop