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

<div class="modern-card">
    <div class="modern-card-header">
        <div class="modern-card-title">
            <span class="card-title-icon bg-orange-soft"><i class="fas fa-hourglass-half text-orange"></i></span>
            <div>
                <h5 class="mb-0 font-weight-bold">Pending Release Approvals</h5>
                <small class="text-muted">Nurse submitted — waiting for manager approval</small>
            </div>
        </div>
        <span class="release-count-badge release-count-badge-orange">
            <i class="fas fa-clock mr-1"></i>
            {{ $patients->total() }} Pending
        </span>
    </div>

    {{-- ✅ STICKY BAR — on admission exact pattern --}}
    <div class="patient-sticky-bar" id="patient-sticky-bar">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="search-input-group">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="patientSearch" class="search-input"
                        placeholder="Search by name, code, or mobile number...">
                    <button class="search-btn search-btn-orange" type="button" onclick="filterTable()">
                        Search
                    </button>
                </div>
            </div>
            <div class="col-md-5 mt-2 mt-md-0">
                <div class="d-flex align-items-center justify-content-md-end">
                    <span class="sticky-bar-info sticky-bar-info-orange">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        <strong>{{ $patients->total() }}</strong> pending approvals
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{-- ✅ Spacer — on admission exact pattern --}}
    <div class="sticky-bar-spacer" id="sticky-bar-spacer"></div>

    <div class="modern-card-body pt-0">
        <div class="table-responsive">
            <table class="table modern-table" id="patientTable">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:80px;">Code</th>
                        <th>Name</th>
                        <th style="width:65px;">Age</th>
                        <th style="width:55px;">Gender</th>
                        <th style="width:130px;">Mobile</th>
                        <th>Address / Upazila</th>
                        <th style="width:110px;">Admission Date</th>
                        <th style="width:120px;">Submitted At</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:150px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr id="row-{{ $patient->admission_id }}">
                            <td class="text-muted small">{{ $patient->id }}</td>
                            <td><span class="patient-code-badge">{{ $patient->patientcode ?? '—' }}</span></td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-orange">
                                        {{ strtoupper(substr($patient->patientname ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $patient->patientname ?? '—' }}</strong>
                                        @if($patient->patientfather ?? null)
                                            <br><small class="text-muted">
                                                <i class="fas fa-user-tie fa-xs"></i> {{ $patient->patientfather }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $patient->age ?? '—' }}</td>
                            <td>
                                @php $g = strtolower($patient->gender ?? ''); @endphp
                                @if($g === 'male')
                                    <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($g === 'female')
                                    <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $patient->mobile_no ?? '—' }}</td>
                            <td class="text-muted small">
                                {{ $patient->address ?? '' }}
                                @if($patient->upozila ?? null)
                                    <span class="text-muted">, {{ $patient->upozila }}</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($patient->admission_date ?? null)
                                    <span class="date-badge">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($patient->admission_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($patient->submitted_at ?? null)
                                    <span class="date-badge text-orange">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($patient->submitted_at)->format('d M, h:i A') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-circle mr-1" style="font-size:7px;"></i>
                                    Pending
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn-action-sm btn-approve-btn mr-1"
                                    onclick="confirmAction('approve', {{ $patient->admission_id }}, {{ $patient->id }}, '{{ addslashes($patient->patientname ?? '') }}')"
                                    title="Approve Release">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <button type="button"
                                    class="btn-action-sm btn-reject-btn"
                                    onclick="confirmAction('reject', {{ $patient->admission_id }}, {{ $patient->id }}, '{{ addslashes($patient->patientname ?? '') }}')"
                                    title="Reject Release">
                                    <i class="fas fa-times mr-1"></i> Reject
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <i class="fas fa-check-double" style="color:#00897b;"></i>
                                    <p>No pending release approvals. All caught up!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($patients, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <small class="text-muted">
                <i class="fas fa-list-ul mr-1"></i>
                Showing {{ $patients->firstItem() ?? 0 }}–{{ $patients->lastItem() ?? 0 }}
                of <strong>{{ $patients->total() }}</strong> pending
            </small>
            {{ $patients->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <div class="modern-card-footer">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1 text-orange"></i>
            <strong>Approve</strong> করলে patient released হবে।
            <strong>Reject</strong> করলে nurse-এর discharge list এ ফিরে যাবে।
        </small>
    </div>
</div>

{{-- ══ CONFIRM MODAL ══ --}}
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header border-0 pb-0" style="padding:20px 24px 10px;">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div class="modal-icon-wrap" id="modal-icon-wrap">
                        <i class="fas fa-check" id="modal-header-icon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold" id="modal-title">Confirm Action</h5>
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
                    <label class="modern-label">
                        Rejection Reason <span class="text-muted">(optional)</span>
                    </label>
                    <textarea class="modern-input" id="reject-reason" rows="2"
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
   ROOT
═══════════════════════════════════════════════════════ */
:root {
    --teal-deep:    #00695C;
    --teal-mid:     #00897B;
    --teal-light:   #E0F2F1;
    --teal-soft:    #B2DFDB;
    --orange:       #E65100;
    --orange-light: #FFF3E0;
    --orange-soft:  #FFCCBC;
    --blue-deep:    #1565C0;
    --blue-mid:     #1976D2;
    --blue-light:   #E3F2FD;
    --blue-soft:    #BBDEFB;
    --red-mid:      #c62828;
    --text-primary: #1a2332;
    --text-muted:   #6b7a90;
    --border:       #e4e9f0;
    --radius-sm:    6px;
    --radius-md:    10px;
    --radius-lg:    16px;
    --shadow-sm:    0 1px 4px rgba(0,0,0,.06);
    --shadow-md:    0 4px 16px rgba(0,0,0,.08);
    --font-base:    'DM Sans', 'Hind Siliguri', Arial, sans-serif;
}
body, .content-wrapper { background: #f0f0f6 !important; font-family: var(--font-base); }
.text-orange { color: var(--orange) !important; }
.text-teal   { color: var(--teal-mid) !important; }

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
    background: var(--orange-light);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--orange); font-size: 17px;
}
.btn-back-modern {
    background: #fff; border: 1.5px solid var(--border);
    color: var(--text-primary); border-radius: var(--radius-sm);
    font-weight: 500; padding: 6px 14px; font-size: 13px;
    transition: all .2s; text-decoration: none;
}
.btn-back-modern:hover { background: var(--orange-light); border-color: var(--orange); color: var(--orange); }

/* BADGE */
.release-count-badge { border-radius: 20px; padding: 5px 14px; font-size: 12.5px; font-weight: 700; }
.release-count-badge-orange { background: var(--orange-light); color: var(--orange); border: 1.5px solid var(--orange-soft); }

/* ═══════════════════════════════════════════════════════
   ALERT
═══════════════════════════════════════════════════════ */
.modern-alert { border-radius: var(--radius-md); border: none; font-size: 13.5px; font-weight: 500; box-shadow: var(--shadow-sm); }

/* ═══════════════════════════════════════════════════════
   MODERN CARD
═══════════════════════════════════════════════════════ */
.modern-card {
    background: #fff; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md); border: 1px solid var(--border);
    overflow: hidden; margin-bottom: 24px;
}
.modern-card-header {
    padding: 18px 24px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    background: #fafbfd;
}
.modern-card-title { display: flex; align-items: center; gap: 12px; }
.card-title-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.bg-orange-soft { background: var(--orange-light); }
.modern-card-body { padding: 24px; }
.modern-card-footer {
    padding: 14px 24px; border-top: 1px solid var(--border);
    background: #fafbfd;
    display: flex; align-items: center; justify-content: space-between;
}

/* ═══════════════════════════════════════════════════════
   ✅ STICKY BAR — on admission exact same pattern
═══════════════════════════════════════════════════════ */
.patient-sticky-bar {
    padding: 14px 24px;
    background: #fff;
    border-bottom: 2px solid var(--orange-soft);
    z-index: 999;
    transition: box-shadow .25s, border-color .25s;
}
.patient-sticky-bar.is-sticky {
    position: fixed;
    top: 57px;               /* AdminLTE navbar height */
    left: 0;
    right: 0;
    border-bottom-color: var(--orange);
    box-shadow: 0 4px 18px rgba(230,81,0,.13);
}
.sticky-bar-spacer { display: none; }
.sticky-bar-spacer.active { display: block; }

.sticky-bar-info {
    font-size: 13px; padding: 6px 14px;
    border-radius: 20px; font-weight: 500;
}
.sticky-bar-info-orange { background: var(--orange-light); color: var(--orange); }
.sticky-bar-info-orange strong { color: var(--orange); }

/* ═══════════════════════════════════════════════════════
   SEARCH
═══════════════════════════════════════════════════════ */
.search-input-group {
    display: flex; align-items: center;
    background: #fff; border: 2px solid var(--border);
    border-radius: 10px; overflow: hidden;
    transition: border-color .2s; box-shadow: var(--shadow-sm);
}
.search-input-group:focus-within { border-color: var(--orange); box-shadow: 0 0 0 3px rgba(230,81,0,.1); }
.search-icon { padding: 0 12px; color: #aab; font-size: 15px; }
.search-input {
    flex: 1; border: none; outline: none; padding: 10px 6px;
    font-size: 14px; background: transparent; color: var(--text-primary);
}
.search-btn { border: none; padding: 10px 22px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: background .2s; }
.search-btn-orange { background: var(--orange); color: #fff; }
.search-btn-orange:hover { background: #bf360c; }

/* ═══════════════════════════════════════════════════════
   PATIENT TABLE
═══════════════════════════════════════════════════════ */
.modern-table { border-collapse: separate; border-spacing: 0; width: 100%; }
.modern-table thead tr th {
    background: #fff8f0; color: var(--text-primary);
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; padding: 11px 14px;
    border-bottom: 2px solid var(--orange-soft);
    white-space: nowrap; position: sticky; top: 0; z-index: 10;
}
.modern-table tbody tr { transition: background .15s; }
.modern-table tbody tr:hover { background: #fff8f0; }
.modern-table tbody td {
    padding: 10px 14px; border-bottom: 1px solid var(--border);
    font-size: 13px; color: var(--text-primary); vertical-align: middle;
}
.patient-code-badge {
    background: var(--orange-light); color: var(--orange);
    border-radius: 5px; padding: 2px 8px;
    font-size: 11.5px; font-weight: 700; font-family: monospace;
}
.patient-name-cell { display: flex; align-items: center; gap: 8px; }
.patient-mini-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    color: #fff; font-size: 12px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.patient-mini-avatar-orange { background: linear-gradient(135deg, var(--orange), #ff7043); }
.gender-badge { display: inline-flex; align-items: center; border-radius: 5px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; }
.gender-male   { background: #e3f2fd; color: var(--blue-deep); }
.gender-female { background: #fce4ec; color: #880e4f; }
.date-badge { font-size: 12px; color: var(--text-muted); }
.status-badge { display: inline-flex; align-items: center; border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 700; }
.status-pending { background: var(--orange-light); color: var(--orange); }
.empty-state { text-align: center; padding: 40px; color: #b0bec5; }
.empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
.empty-state p { font-size: 14px; margin: 0; }

/* ═══════════════════════════════════════════════════════
   ACTION BUTTONS
═══════════════════════════════════════════════════════ */
.btn-action-sm {
    border-radius: var(--radius-sm); padding: 5px 11px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s; display: inline-flex; align-items: center;
    border: 1.5px solid transparent;
}
.btn-approve-btn { background: #e8f5e9; color: #2e7d32; border-color: #a5d6a7; }
.btn-approve-btn:hover { background: #2e7d32; color: #fff; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(46,125,50,.3); }
.btn-reject-btn  { background: #ffebee; color: var(--red-mid); border-color: #ef9a9a; }
.btn-reject-btn:hover  { background: var(--red-mid); color: #fff; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(198,40,40,.3); }

/* ═══════════════════════════════════════════════════════
   FORM (modal textarea)
═══════════════════════════════════════════════════════ */
.modern-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
.modern-input {
    width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    padding: 9px 12px; font-size: 13.5px; color: var(--text-primary);
    background: #fff; transition: border-color .2s, box-shadow .2s;
    outline: none; font-family: var(--font-base);
}
.modern-input:focus { border-color: var(--orange); box-shadow: 0 0 0 3px rgba(230,81,0,.1); }

/* ═══════════════════════════════════════════════════════
   MODAL
═══════════════════════════════════════════════════════ */
.modal-icon-wrap { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.modal-icon-approve { background: #e8f5e9; color: #2e7d32; }
.modal-icon-reject  { background: #ffebee; color: var(--red-mid); }
.modal-patient-bar {
    display: flex; align-items: center; gap: 12px;
    background: #fafbfd; border-radius: var(--radius-md);
    border: 1.5px solid var(--border); padding: 12px 16px;
}
.modal-patient-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, var(--orange), #ff7043);
    color: #fff; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.modal-patient-name { font-weight: 700; font-size: 14px; color: var(--text-primary); }
.modal-patient-sub  { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.btn-modal-action {
    border-radius: var(--radius-sm); padding: 10px 22px;
    font-size: 13.5px; font-weight: 700; border: none;
    cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; justify-content: center;
}
.btn-modal-cancel { background: #fff; color: var(--text-muted); border: 1.5px solid var(--border); }
.btn-modal-cancel:hover { background: #f0f4f8; }
.btn-confirm-approve { background: linear-gradient(135deg, #2e7d32, #388e3c); color: #fff; box-shadow: 0 4px 12px rgba(46,125,50,.28); }
.btn-confirm-approve:hover { background: linear-gradient(135deg, #1b5e20, #2e7d32); }
.btn-confirm-reject  { background: linear-gradient(135deg, #c62828, #e53935); color: #fff; box-shadow: 0 4px 12px rgba(198,40,40,.28); }
.btn-confirm-reject:hover  { background: linear-gradient(135deg, #b71c1c, #c62828); }

/* ═══════════════════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════════════════ */
.pagination { margin-bottom: 0; }
.page-link { border-radius: var(--radius-sm) !important; border-color: var(--border); color: var(--orange); font-size: 13px; }
.page-item.active .page-link { background: var(--orange); border-color: var(--orange); }

@media print { .patient-sticky-bar { display: none !important; } }
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
    var bg = type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565C0');
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;background:'+bg+';color:#fff;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.2);max-width:320px;';
    t.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + msg;
    document.body.appendChild(t);
    setTimeout(function(){
        t.style.opacity = '0';
        t.style.transition = 'opacity .3s';
        setTimeout(function(){ t.remove(); }, 300);
    }, 3000);
}

/* ══════════════════════════════════════════
   ✅ STICKY BAR — on admission exact same JS
══════════════════════════════════════════ */
(function initStickyBar(){
    var bar    = document.getElementById('patient-sticky-bar');
    var spacer = document.getElementById('sticky-bar-spacer');
    if(!bar || !spacer) return;

    var NAVBAR_H     = 57;
    var barOffsetTop = 0;
    var barHeight    = 0;

    function measure(){
        bar.classList.remove('is-sticky');
        spacer.classList.remove('active');
        spacer.style.height = '';
        barOffsetTop = bar.getBoundingClientRect().top + window.scrollY;
        barHeight    = bar.offsetHeight;
    }

    function onScroll(){
        if(window.scrollY + NAVBAR_H >= barOffsetTop){
            bar.classList.add('is-sticky');
            spacer.classList.add('active');
            spacer.style.height = barHeight + 'px';
        } else {
            bar.classList.remove('is-sticky');
            spacer.classList.remove('active');
            spacer.style.height = '';
        }
    }

    window.addEventListener('load',   function(){ measure(); onScroll(); });
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function(){ measure(); onScroll(); });
})();

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
    var badge = document.querySelector('.release-count-badge-orange');
    if(badge) badge.innerHTML = '<i class="fas fa-clock mr-1"></i> ' + rows.length + ' Pending';
    if(rows.length === 0){
        document.querySelector('#patientTable tbody').innerHTML =
            '<tr><td colspan="11"><div class="empty-state">' +
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
document.getElementById('patientSearch').addEventListener('keyup', filterTable);
</script>
@stop