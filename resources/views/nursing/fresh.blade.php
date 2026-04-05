@extends('adminlte::page')

@section('title', 'Fresh | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-leaf"></i></span>
                Fresh
            </h1>
            <ol class="breadcrumb mt-1 p-0" style="background:transparent; font-size:12px;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('nursing.index') }}">Nursing</a></li>
                <li class="breadcrumb-item active" id="breadcrumb-current">Select Patient</li>
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

{{-- ══ STEP INDICATOR ══ --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="step-track-card">
            <div class="step-track-inner">
                <div class="step-item">
                    <div class="step-circle step-active" id="step1-circle">1</div>
                    <div class="step-text ml-2">
                        <div class="step-label-main step-label-active">Step 1</div>
                        <div class="step-label-sub">Select Patient</div>
                    </div>
                </div>
                <div class="step-connector-line" id="step-connector"></div>
                <div class="step-item">
                    <div class="step-circle step-inactive" id="step2-circle">2</div>
                    <div class="step-text ml-2">
                        <div class="step-label-main step-label-inactive" id="step2-label">Step 2</div>
                        <div class="step-label-sub step-label-inactive" id="step2-sublabel">Prescription</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SAVE ALERT ══ --}}
<div id="save-alert" class="alert d-none mb-3 modern-alert" role="alert"></div>

{{-- ══════════════════════════════════════════
     STEP 1 — SELECT PATIENT
══════════════════════════════════════════ --}}
<div id="panel-step1">

    <div class="modern-card patient-list-card" id="patient-list-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-success-soft"><i class="fas fa-users text-success"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Select Patient for Fresh Prescription</h5>
                    <small class="text-muted">Search and choose a patient to proceed</small>
                </div>
            </div>
            <span class="patient-total-pill">
                <i class="fas fa-database mr-1"></i>
                {{ $patients->total() ?? $patients->count() }} patients
            </span>
        </div>

        <div class="inline-search-bar" id="inline-search-bar">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="patientSearch" class="search-input"
                           placeholder="Search by name, code, or mobile number...">
                    <button class="search-btn search-btn-success" type="button" onclick="filterTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
                <a href="https://profclinic.erpbd.org/patients/newpatient"
                   class="btn-add-new-patient" target="_blank">
                    <i class="fas fa-user-plus mr-1"></i> New Patient
                </a>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="patient-table-scroll" id="patient-table-scroll">
                <table class="table modern-table" id="patientTable">
                    <thead>
                        <tr>
                            <th style="width:46px;">#</th>
                            <th style="width:82px;">Code</th>
                            <th>Name</th>
                            <th style="width:58px;">Age</th>
                            <th style="width:52px;">Sex</th>
                            <th style="width:128px;">Mobile</th>
                            <th>Address</th>
                            <th style="width:70px;">Blood</th>
                            <th style="width:80px; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="patientTableBody">
                        @forelse($patients as $patient)
                        @php
                            $pid      = $patient->id           ?? '';
                            $pcode    = $patient->patientcode  ?? '—';
                            $pname    = $patient->patientname  ?? '—';
                            $page     = $patient->age          ?? '—';
                            $pgender  = strtolower($patient->gender ?? '');
                            $pmobile  = $patient->mobile_no    ?? '—';
                            $paddress = $patient->address      ?? '';
                            $pupozila = $patient->upozila      ?? null;
                            $pblood   = $patient->blood_group  ?? null;
                        @endphp
                        <tr class="patient-row">
                            <td class="text-muted small">{{ $pid }}</td>
                            <td><span class="patient-code-badge">{{ $pcode }}</span></td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-success">{{ strtoupper(substr($pname, 0, 1)) }}</div>
                                    <div>
                                        <strong>{{ $pname }}</strong>
                                        @if($patient->patientfather ?? null)
                                            <br><small class="text-muted" style="font-size:11px;">
                                                <i class="fas fa-user-tie fa-xs mr-1"></i>{{ $patient->patientfather }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $page }}</td>
                            <td>
                                @if($pgender === 'male')
                                    <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($pgender === 'female')
                                    <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $pmobile }}</td>
                            <td class="text-muted small">{{ $paddress }}{{ $pupozila ? ', '.$pupozila : '' }}</td>
                            <td>
                                @if($pblood)
                                    <span class="blood-badge">{{ $pblood }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn-select-patient"
                                    onclick="selectPatient(this)"
                                    data-id="{{ $pid }}"
                                    data-name="{{ $pname }}"
                                    data-age="{{ $page }}"
                                    data-code="{{ $pcode }}"
                                    data-mobile="{{ $pmobile }}"
                                    data-address="{{ $paddress }}"
                                    data-upozila="{{ $pupozila }}"
                                    data-blood="{{ $pblood }}"
                                    data-gender="{{ $patient->gender ?? '' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No patients found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($patients, 'links'))
            <div class="pagination-bar">
                <small class="text-muted">
                    <i class="fas fa-list-ul mr-1"></i>
                    Showing {{ $patients->firstItem() ?? 0 }}–{{ $patients->lastItem() ?? 0 }}
                    of <strong>{{ $patients->total() ?? 0 }}</strong> patients
                </small>
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>

        <div class="modern-card-footer">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1 text-success"></i>
                Click <i class="fas fa-arrow-right" style="font-size:10px;"></i> on a patient row to proceed.
            </small>
            <a href="https://profclinic.erpbd.org/patients/newpatient"
               class="btn-add-new-patient-sm" target="_blank">
                <i class="fas fa-user-plus mr-1"></i> Add New Patient
            </a>
        </div>
    </div>

    {{-- ══ PAST FRESH PRESCRIPTIONS LIST ══ --}}
    <div class="modern-card past-rx-card mt-2" id="past-rx-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-success-soft">
                    <i class="fas fa-leaf" style="color:#28a745;"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Past Fresh Prescriptions</h5>
                    <small class="text-muted">All previously saved fresh prescriptions</small>
                </div>
            </div>
            <span class="patient-total-pill" style="background:#d4edda;color:#155724;">
                <i class="fas fa-file-medical mr-1"></i>
                {{ $FreshPatients->total() ?? $FreshPatients->count() }} records
            </span>
        </div>

        <div class="inline-search-bar" style="border-bottom-color:#c3e6cb;">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline" style="flex:1;">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="freshRxSearch" class="search-input"
                           placeholder="Search by name, code or mobile..."
                           onkeyup="filterFreshRxTable()">
                    <button class="search-btn" type="button"
                            onclick="filterFreshRxTable()"
                            style="background:#28a745;">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="patient-table-scroll">
                <table class="table modern-table" id="freshRxTable">
                    <thead>
                        <tr>
                            <th style="width:46px;">#</th>
                            <th style="width:82px;">Rx ID</th>
                            <th>Patient Name</th>
                            <th style="width:58px;">Age</th>
                            <th style="width:52px;">Sex</th>
                            <th style="width:128px;">Mobile</th>
                            <th style="width:115px;">Prescription Date</th>
                            <th style="width:70px;">Blood</th>
                            <th style="width:110px; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="freshRxTableBody">
                        @forelse($FreshPatients as $fp)
                        @php
                            $fpRxId   = $fp->id ?? '';
                            $fpCode   = $fp->patient_code ?? $fp->patientcode ?? '—';
                            $fpName   = $fp->patient_name ?? $fp->patientname ?? '—';
                            $fpAge    = $fp->patient_age  ?? $fp->age         ?? '—';
                            $fpGender = strtolower($fp->gender ?? '');
                            $fpMobile = $fp->mobile_no    ?? '—';
                            $fpBlood  = $fp->blood_group  ?? null;
                            $fpRxDate = $fp->prescription_date ?? $fp->created_at ?? '';
                        @endphp
                        <tr class="fresh-rx-row">
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <span class="patient-code-badge" style="background:#d4edda;color:#155724;">
                                    #{{ $fpRxId }}
                                </span>
                            </td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-success">
                                        {{ strtoupper(substr($fpName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $fpName }}</strong>
                                        <br>
                                        <small class="text-muted" style="font-size:11px;">{{ $fpCode }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $fpAge }}</td>
                            <td>
                                @if($fpGender === 'male')
                                    <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($fpGender === 'female')
                                    <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $fpMobile }}</td>
                            <td class="small text-muted">
                                @if($fpRxDate)
                                    {{ \Carbon\Carbon::parse($fpRxDate)->format('d/m/Y') }}
                                    <br>
                                    <span style="font-size:10px;">{{ \Carbon\Carbon::parse($fpRxDate)->diffForHumans() }}</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($fpBlood)
                                    <span class="blood-badge">{{ $fpBlood }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-view-rx"
                                    onclick="viewFreshPrescription({{ $fpRxId }})">
                                    <i class="fas fa-eye mr-1"></i> View Rx
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-file-medical-alt"></i>
                                    <p>No past fresh prescriptions found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($FreshPatients, 'links'))
            <div class="pagination-bar">
                <small class="text-muted">
                    <i class="fas fa-list-ul mr-1"></i>
                    Showing {{ $FreshPatients->firstItem() ?? 0 }}–{{ $FreshPatients->lastItem() ?? 0 }}
                    of <strong>{{ $FreshPatients->total() ?? 0 }}</strong> records
                </small>
                {{ $FreshPatients->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

</div>{{-- /#panel-step1 --}}

{{-- ══════════════════════════════════════════
     STEP 2 — PRESCRIPTION FORM
══════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none;">

    {{-- ══ SELECTED PATIENT BAR ══ --}}
    <div class="patient-selected-bar patient-selected-bar-success mb-4">
        <div class="psb-left">
            <div class="psb-avatar psb-avatar-success" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot psb-status-dot-green"></span>
            <span class="psb-status-label">Fresh Prescription</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change
            </button>
        </div>
    </div>

    {{-- Loading bar --}}
    <div id="history-loading" class="admission-status-bar admission-loading-bar" style="display:none;">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        রোগীর ইতিহাস লোড হচ্ছে...
    </div>

    {{-- ══ ON ADMISSION INFO BOX ══ --}}
    <div id="admission-info-box" class="modern-card mb-4" style="display:none; border-left:4px solid #1a237e;">
        <div style="background:linear-gradient(135deg,#1a237e 0%,#283593 100%);padding:12px 20px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-hospital-alt text-white" style="font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;">
                            On Admission Record
                            <span class="badge badge-warning ml-2" id="adm-record-id" style="font-size:11px;"></span>
                        </div>
                        <div style="color:rgba(255,255,255,0.7);font-size:11px;">রোগীর ভর্তির সময়ের তথ্য ও ওষুধ</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm"
                    style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:11px;"
                    onclick="toggleAdmDetails()">
                    <i class="fas fa-chevron-down mr-1" id="adm-toggle-icon"></i>
                    <span id="adm-toggle-text">Details Hide</span>
                </button>
            </div>
        </div>
        <div style="background:#f8f9ff;padding:12px 20px;border-bottom:1px solid #e8eaf6;">
            <div class="row">
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-calendar-check mr-1 text-primary"></i>Admission</div><div class="hist-stat-value text-primary" id="adm-date">—</div><div class="hist-stat-sub" id="adm-time">—</div></div></div>
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-heartbeat mr-1 text-danger"></i>Pulse</div><div class="hist-stat-value" id="adm-pulse">—</div></div></div>
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-tachometer-alt mr-1 text-warning"></i>BP</div><div class="hist-stat-value" id="adm-bp">—</div></div></div>
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-clock mr-1 text-danger"></i>OT Time</div><div class="hist-stat-value text-danger" id="adm-ot-time">—</div></div></div>
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-baby mr-1"></i>Baby</div><div class="hist-stat-value" id="adm-baby-sex">—</div><div class="hist-stat-sub"><span id="adm-baby-weight">—</span> kg · <span id="adm-baby-time">—</span></div></div></div>
                <div class="col-6 col-md-2 mb-2 mb-md-0"><div class="hist-stat-box"><div class="hist-stat-label"><i class="fas fa-calendar-week mr-1 text-success"></i>Preg. Wks</div><div class="hist-stat-value text-success" id="adm-preg-weeks">—</div></div></div>
            </div>
        </div>
        <div id="adm-details-body" style="background:#fff;padding:16px 20px;">
            <div id="adm-medicines-wrap" style="display:none;margin-bottom:14px;">
                <div class="hist-section-title"><i class="fas fa-pills mr-2 text-primary"></i>On Admission ওষুধ <span class="badge badge-primary ml-1" id="adm-med-count">0</span></div>
                <table class="table table-sm table-bordered mt-2 mb-0" style="font-size:12px;background:#f0f4ff;">
                    <thead style="background:#1a237e;color:#fff;">
                        <tr><th style="width:30px;">#</th><th>Medicine</th><th style="width:80px;">Dose</th><th style="width:80px;">Route</th><th style="width:110px;">Frequency</th><th style="width:80px;">Duration</th><th style="width:80px;">Timing</th></tr>
                    </thead>
                    <tbody id="adm-medicines-tbody"></tbody>
                </table>
            </div>
            <div id="adm-no-med-msg" style="display:none;" class="text-center py-2">
                <span class="text-muted" style="font-size:12px;"><i class="fas fa-info-circle mr-1"></i> On Admission এ কোনো ওষুধ নেই।</span>
            </div>
            <div id="ps-medicines-wrap" style="display:none;margin-bottom:14px;">
                <div class="hist-section-title" style="border-left-color:#c62828;"><i class="fas fa-pills mr-2 text-danger"></i>Post-Surgery ওষুধ <span class="badge badge-danger ml-1" id="ps-med-count">0</span></div>
                <table class="table table-sm table-bordered mt-2 mb-0" style="font-size:12px;background:#fff0f0;">
                    <thead style="background:#c62828;color:#fff;">
                        <tr><th style="width:30px;">#</th><th>Medicine</th><th style="width:80px;">Strength</th><th style="width:80px;">Dose</th><th style="width:80px;">Route</th><th style="width:110px;">Frequency</th><th style="width:80px;">Duration</th></tr>
                    </thead>
                    <tbody id="ps-medicines-tbody"></tbody>
                </table>
            </div>
            <div id="ps-no-med-msg" style="display:none;" class="text-center py-2">
                <span class="text-muted" style="font-size:12px;"><i class="fas fa-info-circle mr-1"></i> Post-Surgery এ কোনো ওষুধ নেই।</span>
            </div>
            <div id="adm-history-wrap" style="display:none;margin-bottom:10px;">
                <div class="hist-section-title mt-2"><i class="fas fa-notes-medical mr-2 text-primary"></i>On Admission Prescription History</div>
                <div id="adm-history-list" class="mt-2"></div>
            </div>
        </div>
    </div>

    {{-- ══ POST-SURGERY HISTORY BOX ══ --}}
    <div id="postsurgery-info-box" class="modern-card mb-4" style="display:none; border-left:4px solid #b71c1c;">
        <div style="background:linear-gradient(135deg,#b71c1c 0%,#c62828 100%);padding:12px 20px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-procedures text-white" style="font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;">Post-Surgery Records <span class="badge badge-warning ml-2" id="ps-count-badge" style="font-size:11px;"></span></div>
                        <div style="color:rgba(255,255,255,0.7);font-size:11px;">রোগীর Post-Operative Prescription ইতিহাস</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:11px;" onclick="togglePsDetails()">
                    <i class="fas fa-chevron-down mr-1" id="ps-toggle-icon"></i><span id="ps-toggle-text">Details Hide</span>
                </button>
            </div>
        </div>
        <div id="ps-details-body" style="background:#fff;padding:16px 20px;">
            <div id="ps-history-list"></div>
        </div>
    </div>

    {{-- ══ FRESH HISTORY BOX ══ --}}
    <div id="fresh-info-box" class="modern-card mb-4" style="display:none; border-left:4px solid #2e7d32;">
        <div style="background:linear-gradient(135deg,#2e7d32 0%,#43a047 100%);padding:12px 20px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-leaf text-white" style="font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;">Fresh Records <span class="badge badge-warning ml-2" id="fresh-count-badge" style="font-size:11px;"></span></div>
                        <div style="color:rgba(255,255,255,0.7);font-size:11px;">রোগীর Fresh Prescription ইতিহাস</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:11px;" onclick="toggleFreshDetails()">
                    <i class="fas fa-chevron-down mr-1" id="fresh-toggle-icon"></i><span id="fresh-toggle-text">Details Hide</span>
                </button>
            </div>
        </div>
        <div id="fresh-details-body" style="background:#fff;padding:16px 20px;">
            <div id="fresh-history-list"></div>
        </div>
    </div>

    {{-- ══ PRESCRIPTION FORM CARD ══ --}}
    <div class="modern-card" id="rx-form-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-success-soft"><i class="fas fa-notes-medical text-success"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Fresh Prescription</h5>
                    <small class="text-muted">Fill in patient & medicine details</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">

            <input type="hidden" id="f-patient-id">
            <input type="hidden" id="f-patient-code">

            <hr class="section-divider mt-0 mb-4">

            <div class="section-heading mb-3">
                <i class="fas fa-user-injured mr-2 text-success"></i>
                <span>Patient &amp; Clinical Information</span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="modern-field-group">
                        <label class="modern-label">Doctor</label>
                        <select class="modern-input" id="f-doctor" onchange="updateDoctorHeader()">
                            @forelse($doctors as $doc)
                                @php $displayName = $doc->doctor_name ?? $doc->name ?? null; @endphp
                                <option value="{{ $doc->id }}"
                                    data-docname="{{ e($displayName ?? '') }}"
                                    data-doctorno="{{ e($doc->doctor_no ?? '') }}"
                                    data-speciality="{{ e($doc->speciality ?? '') }}"
                                    data-contact="{{ e($doc->contact ?? '') }}"
                                    data-posting="{{ e($doc->Posting ?? '') }}">
                                    {{ $displayName ?: 'Doctor ID: '.$doc->id.' (No Name)' }}
                                </option>
                            @empty
                                <option value="">No doctors found</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">Patient Name</label>
                        <input type="text" class="modern-input" id="f-patient-name" placeholder="Full name">
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">Age</label>
                        <input type="text" class="modern-input" id="f-patient-age" placeholder="e.g. 25 yrs">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-field-group">
                        <label class="modern-label">Prescription Date</label>
                        <input type="date" class="modern-input" id="f-date">
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">C/C (Chief Complaint)</label>
                        <input type="text" class="modern-input" id="f-cc" placeholder="Chief complaint...">
                    </div>
                    <div class="field-row-2">
                        <div class="modern-field-group">
                            <label class="modern-label">Pulse</label>
                            <div class="input-with-icon">
                                <i class="fas fa-heart input-icon text-danger"></i>
                                <input type="text" class="modern-input with-icon" id="f-pulse" placeholder="82 bpm">
                            </div>
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">BP</label>
                            <div class="input-with-icon">
                                <i class="fas fa-tachometer-alt input-icon text-warning"></i>
                                <input type="text" class="modern-input with-icon" id="f-bp" placeholder="120/80">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ MEDICINES SECTION ══ --}}
            <div class="section-divider-full mt-4 mb-4">
                <div class="section-heading mb-0">
                    <i class="fas fa-pills mr-2 text-success"></i>
                    <span>Medicines</span>
                    <span class="badge badge-pill ml-2" id="med-count-badge"
                          style="background:#e8f5e9;color:#2e7d32;font-size:12px;padding:4px 10px;">0</span>
                    <small id="auto-loaded-note" class="text-success ml-3 d-none" style="font-size:11px;font-weight:600;">
                        <i class="fas fa-check-circle mr-1"></i>Auto-loaded from records
                    </small>
                </div>
                <div class="med-section-actions">
                    <button type="button" class="btn-med-action btn-med-add-success" onclick="addMedRow()">
                        <i class="fas fa-plus mr-1"></i> Add Row
                    </button>
                    <button type="button" class="btn-med-action btn-med-clear-success" onclick="clearAllMeds()">
                        <i class="fas fa-trash-alt mr-1"></i> Clear All
                    </button>
                </div>
            </div>

            {{-- Selected medicines --}}
            <div class="med-table-card selected-med-card-success mb-4">
                <div class="med-table-card-header" style="background:#f9fff9;border-bottom-color:#c8e6c9;">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#43a047;"></span>
                        <span class="med-table-title">Selected Medicines</span>
                        <span class="med-count-pill med-count-pill-success" id="sel-med-badge">0</span>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table med-table mb-0" style="min-width:750px;">
                        <thead>
                            <tr>
                                <th style="width:35px;">#</th>
                                <th>Medicine Name</th>
                                <th style="width:90px;">Dose</th>
                                <th style="width:80px;">Route</th>
                                <th style="width:110px;">Frequency</th>
                                <th style="width:85px;">Duration</th>
                                <th style="width:85px;">Timing</th>
                                <th style="width:110px;">Remarks</th>
                                <th style="width:42px;"></th>
                            </tr>
                        </thead>
                        <tbody id="sel-med-tbody">
                            <tr id="empty-sel-row">
                                <td colspan="9">
                                    <div class="med-empty-state">
                                        <i class="fas fa-pills" style="color:#43a047;"></i>
                                        <span>Patient select করলে records থেকে medicines auto-load হবে।</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Available medicines --}}
            <div class="med-table-card available-med-card mb-0">
                <div class="med-table-card-header">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#1976d2;"></span>
                        <span class="med-table-title">আরো Medicine যোগ করুন</span>
                        <span class="med-count-pill" style="background:#e3f2fd;color:#1565c0;">{{ $medicines->count() }}</span>
                    </div>
                    <div class="avail-filter-wrap">
                        <i class="fas fa-filter avail-filter-icon"></i>
                        <input type="text" class="avail-filter-input" id="med-filter" placeholder="Filter medicines...">
                    </div>
                </div>
                <div style="max-height:220px; overflow-y:auto;">
                    <table class="table med-table mb-0">
                        <thead>
                            <tr>
                                <th width="35"><input type="checkbox" id="select-all-med" style="cursor:pointer;"></th>
                                <th>Medicine</th>
                                <th>Strength</th>
                                <th>Dose</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="avail-med-tbody">
                            @forelse($medicines as $med)
                            <tr class="avail-med-row" data-name="{{ strtolower($med->name ?? '') }}">
                                <td>
                                    <input type="checkbox" class="avail-med-cb modern-checkbox"
                                        data-id="{{ $med->id }}"
                                        data-name="{{ e($med->name ?? '') }}"
                                        data-strength="{{ e($med->strength ?? '') }}"
                                        data-dose="{{ e($med->dose ?? '') }}"
                                        data-route="{{ e($med->route ?? '') }}"
                                        data-frequency="{{ e($med->dose ?? $med->frequency ?? '') }}"
                                        data-duration="{{ e($med->duration ?? '') }}"
                                        data-timing="{{ e($med->instruction ?? $med->timing ?? '') }}"
                                        data-note="{{ e($med->note ?? '') }}"
                                        onchange="onAvailMedChange(this)">
                                </td>
                                <td><span class="avail-med-name">{{ $med->name ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->strength ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->dose ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->frequency ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->duration ?? '—' }}</span></td>
                                <td>
                                    <button type="button" class="btn-quick-add btn-quick-add-green"
                                            onclick="quickAdd(this)" title="Quick Add">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">No medicines found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 pt-2">
                <div class="modern-field-group">
                    <label class="modern-label">
                        <i class="fas fa-comment-medical mr-1 text-secondary"></i>
                        Additional Notes / Rx Text
                    </label>
                    <textarea class="modern-input modern-textarea" id="f-notes" rows="3"
                              placeholder="Additional notes or Rx text..."></textarea>
                </div>
            </div>

            <div class="form-footer mt-4">
                <button type="button" class="btn btn-footer-back" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" class="btn btn-footer-save-success" id="btn-save-rx" onclick="saveAndGenerateRx()">
                    <i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription
                </button>
            </div>
        </div>
    </div>

    {{-- ══ PRESCRIPTION PRINT VIEW ══ --}}
    <div id="rx-view" style="display:none;">

        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-green">
                    <div class="rx-summary-icon"><i class="fas fa-user"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Patient</div>
                        <div class="rx-summary-value" id="ib-name">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-teal">
                    <div class="rx-summary-icon"><i class="fas fa-birthday-cake"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Age</div>
                        <div class="rx-summary-value" id="ib-age">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-orange">
                    <div class="rx-summary-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Date</div>
                        <div class="rx-summary-value" id="ib-date">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="rx-summary-card rx-card-blue">
                    <div class="rx-summary-icon"><i class="fas fa-user-md"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Doctor</div>
                        <div class="rx-summary-value" id="ib-doctor" style="font-size:12px;">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <div class="modern-card-title">
                    <span class="card-title-icon bg-success-soft"><i class="fas fa-notes-medical text-success"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Fresh Prescription</h5>
                        <small class="text-muted">Ready to print</small>
                    </div>
                </div>
                <span class="rx-saved-badge">
                    <i class="fas fa-check-circle mr-1"></i> Saved
                    <span class="ml-1" id="rx-badge-name">—</span>
                </span>
            </div>
            <div class="modern-card-body p-0">
                {{-- PRINT AREA --}}
                <div id="prescription-print-area">
                    <div class="fresh-wrapper">
                        <div class="fresh-header">
                            <div class="fresh-header-left">
                                <div class="fresh-logo-row">
                                    <div class="fresh-cp-logo">
                                        <span class="fresh-cp-c">C</span><span class="fresh-cp-p">P</span>
                                    </div>
                                    <div>
                                        <div class="fresh-clinic-bn">প্রফেসর ক্লিনিক</div>
                                        <div class="fresh-clinic-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                        <div class="fresh-clinic-phones">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</div>
                                        <div class="fresh-clinic-phones">০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                                    </div>
                                </div>
                            </div>
                            <div class="fresh-header-right">
                                <div class="fresh-doctor-title"  id="rx-doctor-name">—</div>
                                <div class="fresh-doctor-deg"    id="rx-doctor-speciality"></div>
                                <div class="fresh-doctor-deg"    id="rx-doctor-regno"></div>
                                <div class="fresh-doctor-college" id="rx-doctor-posting"></div>
                                <div class="fresh-doctor-deg"    id="rx-doctor-contact"></div>
                            </div>
                        </div>
                        <div class="fresh-nad-row">
                            <div class="fresh-nad-field"><span class="fresh-nad-label">Code :</span><span class="fresh-nad-value" id="rx-code">—</span></div>
                            <div class="fresh-nad-field"><span class="fresh-nad-label">Name :</span><span class="fresh-nad-value" id="rx-name">—</span></div>
                            <div class="fresh-nad-field"><span class="fresh-nad-label">Age :</span><span class="fresh-nad-value" id="rx-age">—</span></div>
                            <div class="fresh-nad-field"><span class="fresh-nad-label">Date :</span><span class="fresh-nad-value" id="rx-date">—</span></div>
                        </div>
                        <div class="fresh-body">
                            <div class="fresh-left">
                                <div class="fresh-cc-section">
                                    <div class="fresh-section-title">C/C</div>
                                    <div class="fresh-cc-text" id="rx-cc">—</div>
                                </div>
                                <div class="fresh-oe-section">
                                    <div class="fresh-section-title">O/E</div>
                                    <ul class="fresh-list">
                                        <li>Pulse    <span class="fresh-val" id="rx-pulse">—</span></li>
                                        <li>BP       <span class="fresh-val" id="rx-bp">—</span></li>
                                        <li>Anaemia  <span class="fresh-val" id="rx-anaemia">—</span></li>
                                        <li>Jaundice <span class="fresh-val" id="rx-jaundice">—</span></li>
                                        <li>Tem      <span class="fresh-val" id="rx-tem">—</span></li>
                                        <li>Oedema   <span class="fresh-val" id="rx-oedema">—</span></li>
                                        <li>Weight   <span class="fresh-val" id="rx-weight">—</span></li>
                                        <li>Heart    <span class="fresh-val" id="rx-heart">—</span></li>
                                        <li>Lungs    <span class="fresh-val" id="rx-lungs">—</span></li>
                                        <li>FM       <span class="fresh-val" id="rx-fm">—</span></li>
                                    </ul>
                                </div>
                                <div class="fresh-inv-section">
                                    <div class="fresh-section-title">Inv</div>
                                    <ul class="fresh-list">
                                        @forelse($investigations as $inv)
                                            <li>{{ $inv->name ?? $inv->investigation_name ?? '' }}</li>
                                        @empty
                                            <li>CBC/Hb%</li><li>Urine R/M/E</li><li>RBS/FBS</li><li>HBs Ag</li>
                                            <li>VDRL</li><li>Blood grouping</li><li>S. bilirubin</li><li>Widal test</li>
                                            <li>Blood urea</li><li>S. creatinine</li><li>ASo titre</li><li>RA test</li>
                                            <li>U.R/E</li><li>USG of</li><li>X–ray of</li><li>ECG</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="fresh-right">
                                <div class="fresh-rx-symbol">Rx</div>
                                <div class="fresh-med-table-wrap">
                                    <table class="fresh-med-table" id="rx-medicine-print-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="fresh-th-name">ঔষধের নাম</th>
                                                <th colspan="3" class="fresh-th-group">কখন খাবেন?</th>
                                                <th colspan="2" class="fresh-th-group">আহারের</th>
                                                <th colspan="3" class="fresh-th-group">কতদিন?</th>
                                            </tr>
                                            <tr>
                                                <th class="fresh-th-sub">সকাল</th><th class="fresh-th-sub">দুপুর</th><th class="fresh-th-sub">রাত</th>
                                                <th class="fresh-th-sub">আগে</th><th class="fresh-th-sub">পরে</th>
                                                <th class="fresh-th-sub">দিন</th><th class="fresh-th-sub">মাস</th><th class="fresh-th-sub">চলবে</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rx-med-print-tbody"></tbody>
                                    </table>
                                </div>
                                <div id="rx-rx-text"  style="margin-top:10px;font-size:12px;color:#222;white-space:pre-wrap;"></div>
                                <div id="rx-notes-print" style="margin-top:8px;font-size:11px;color:#444;white-space:pre-wrap;"></div>
                            </div>
                        </div>
                        <div class="fresh-footer">
                            <span>বিঃ দ্রঃ ............................................</span>
                            <span>............... দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।</span>
                        </div>
                    </div>
                </div>
                {{-- END PRINT AREA --}}
            </div>
            <div class="modern-card-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Generated: <span id="gen-time">—</span>
                </small>
                <div style="display:flex; gap:8px;">
                    <button onclick="printRx()" class="btn-rx-action btn-rx-print">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn-rx-action btn-rx-edit" onclick="editRx()">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button type="button" class="btn-rx-action btn-rx-new" onclick="backToStep1()">
                        <i class="fas fa-plus mr-1"></i> New
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /#panel-step2 --}}

{{-- ══════════════════════════════════════════
     PAST FRESH RX VIEW MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="rxViewModal" tabindex="-1" role="dialog" aria-labelledby="rxViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">

            <div class="modal-header rx-modal-header">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white" id="rxViewModalLabel">
                            Fresh Prescription
                        </h5>
                        <small class="modal-subtitle-text" id="modal-subtitle">Loading...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <button type="button" class="btn-rx-modal-print" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn-rx-modal-close" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">

                <div id="modal-loading" class="modal-state-wrap">
                    <div class="modal-spinner-icon">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p class="modal-state-text">Loading prescription...</p>
                </div>

                <div id="modal-error" class="modal-state-wrap d-none">
                    <div class="modal-error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="modal-state-text" id="modal-error-msg">Failed to load prescription.</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" data-dismiss="modal">Close</button>
                </div>

                <div id="modal-rx-area" class="d-none">
                    <div class="modal-summary-bar">
                        <div class="modal-summary-item msi-green">
                            <i class="fas fa-user"></i>
                            <div>
                                <div class="msi-label">Patient</div>
                                <div class="msi-val" id="m-ib-name">—</div>
                            </div>
                        </div>
                        <div class="modal-summary-item msi-teal">
                            <i class="fas fa-birthday-cake"></i>
                            <div>
                                <div class="msi-label">Age</div>
                                <div class="msi-val" id="m-ib-age">—</div>
                            </div>
                        </div>
                        <div class="modal-summary-item msi-orange">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <div class="msi-label">Date</div>
                                <div class="msi-val" id="m-ib-date">—</div>
                            </div>
                        </div>
                        <div class="modal-summary-item msi-blue">
                            <i class="fas fa-hashtag"></i>
                            <div>
                                <div class="msi-label">Rx ID</div>
                                <div class="msi-val" id="m-ib-id">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL PRINT AREA --}}
                    <div id="modal-prescription-print-area" style="padding:20px 24px;">
                        <div class="fresh-wrapper">
                            <div class="fresh-header">
                                <div class="fresh-header-left">
                                    <div class="fresh-logo-row">
                                        <div class="fresh-cp-logo">
                                            <span class="fresh-cp-c">C</span><span class="fresh-cp-p">P</span>
                                        </div>
                                        <div>
                                            <div class="fresh-clinic-bn">প্রফেসর ক্লিনিক</div>
                                            <div class="fresh-clinic-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                            <div class="fresh-clinic-phones">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</div>
                                            <div class="fresh-clinic-phones">০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fresh-header-right">
                                    <div class="fresh-doctor-title"  id="m-rx-doctor-name">—</div>
                                    <div class="fresh-doctor-deg"    id="m-rx-doctor-speciality"></div>
                                    <div class="fresh-doctor-deg"    id="m-rx-doctor-regno"></div>
                                    <div class="fresh-doctor-college" id="m-rx-doctor-posting"></div>
                                    <div class="fresh-doctor-deg"    id="m-rx-doctor-contact"></div>
                                </div>
                            </div>
                            <div class="fresh-nad-row">
                                <div class="fresh-nad-field"><span class="fresh-nad-label">Code :</span><span class="fresh-nad-value" id="m-rx-code">—</span></div>
                                <div class="fresh-nad-field"><span class="fresh-nad-label">Name :</span><span class="fresh-nad-value" id="m-rx-name">—</span></div>
                                <div class="fresh-nad-field"><span class="fresh-nad-label">Age :</span><span class="fresh-nad-value" id="m-rx-age">—</span></div>
                                <div class="fresh-nad-field"><span class="fresh-nad-label">Date :</span><span class="fresh-nad-value" id="m-rx-date">—</span></div>
                            </div>
                            <div class="fresh-body">
                                <div class="fresh-left">
                                    <div class="fresh-cc-section">
                                        <div class="fresh-section-title">C/C</div>
                                        <div class="fresh-cc-text" id="m-rx-cc">—</div>
                                    </div>
                                    <div class="fresh-oe-section">
                                        <div class="fresh-section-title">O/E</div>
                                        <ul class="fresh-list">
                                            <li>Pulse    <span class="fresh-val" id="m-rx-pulse">—</span></li>
                                            <li>BP       <span class="fresh-val" id="m-rx-bp">—</span></li>
                                            <li>Anaemia  <span class="fresh-val" id="m-rx-anaemia">—</span></li>
                                            <li>Jaundice <span class="fresh-val" id="m-rx-jaundice">—</span></li>
                                            <li>Tem      <span class="fresh-val" id="m-rx-tem">—</span></li>
                                            <li>Oedema   <span class="fresh-val" id="m-rx-oedema">—</span></li>
                                            <li>Weight   <span class="fresh-val" id="m-rx-weight">—</span></li>
                                            <li>Heart    <span class="fresh-val" id="m-rx-heart">—</span></li>
                                            <li>Lungs    <span class="fresh-val" id="m-rx-lungs">—</span></li>
                                            <li>FM       <span class="fresh-val" id="m-rx-fm">—</span></li>
                                        </ul>
                                    </div>
                                    <div class="fresh-inv-section">
                                        <div class="fresh-section-title">Inv</div>
                                        <ul class="fresh-list">
                                            @forelse($investigations as $inv)
                                                <li>{{ $inv->name ?? $inv->investigation_name ?? '' }}</li>
                                            @empty
                                                <li>CBC/Hb%</li><li>Urine R/M/E</li><li>RBS/FBS</li><li>HBs Ag</li>
                                                <li>VDRL</li><li>Blood grouping</li><li>S. bilirubin</li><li>Widal test</li>
                                                <li>Blood urea</li><li>S. creatinine</li><li>ASo titre</li><li>RA test</li>
                                                <li>U.R/E</li><li>USG of</li><li>X–ray of</li><li>ECG</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div class="fresh-right">
                                    <div class="fresh-rx-symbol">Rx</div>
                                    <div class="fresh-med-table-wrap">
                                        <table class="fresh-med-table" id="m-rx-medicine-print-table">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="fresh-th-name">ঔষধের নাম</th>
                                                    <th colspan="3" class="fresh-th-group">কখন খাবেন?</th>
                                                    <th colspan="2" class="fresh-th-group">আহারের</th>
                                                    <th colspan="3" class="fresh-th-group">কতদিন?</th>
                                                </tr>
                                                <tr>
                                                    <th class="fresh-th-sub">সকাল</th><th class="fresh-th-sub">দুপুর</th><th class="fresh-th-sub">রাত</th>
                                                    <th class="fresh-th-sub">আগে</th><th class="fresh-th-sub">পরে</th>
                                                    <th class="fresh-th-sub">দিন</th><th class="fresh-th-sub">মাস</th><th class="fresh-th-sub">চলবে</th>
                                                </tr>
                                            </thead>
                                            <tbody id="m-rx-med-print-tbody"></tbody>
                                        </table>
                                    </div>
                                    <div id="m-rx-rx-text"  style="margin-top:10px;font-size:12px;color:#222;white-space:pre-wrap;"></div>
                                    <div id="m-rx-notes"    style="margin-top:8px;font-size:11px;color:#444;white-space:pre-wrap;"></div>
                                </div>
                            </div>
                            <div class="fresh-footer">
                                <span>বিঃ দ্রঃ ............................................</span>
                                <span>............... দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।</span>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL PRINT AREA --}}

                </div>{{-- /#modal-rx-area --}}

            </div>{{-- /modal-body --}}

            <div class="modal-footer rx-modal-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i>
                    Saved: <span id="m-saved-time">—</span>
                </small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ══ SINGLE PRINT OVERLAY ══ --}}
<div id="print-overlay"></div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════ ROOT ═══════════════════════ */
:root {
    --green-deep:   #2E7D32; --green-mid: #43A047; --green-light: #E8F5E9; --green-soft: #C8E6C9;
    --blue-deep:    #1565C0; --blue-mid: #1976D2; --blue-light: #E3F2FD; --blue-soft: #BBDEFB;
    --teal-deep:    #00695C; --teal-mid: #00796B; --teal-light: #E0F2F1;
    --orange:       #E65100; --red-deep: #B71C1C; --red-mid: #E53935;
    --text-primary: #1a2332; --text-muted: #6b7a90; --border: #e4e9f0;
    --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px;
    --shadow-sm: 0 1px 4px rgba(0,0,0,.06); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --font-base: 'DM Sans','Hind Siliguri',Arial,sans-serif;
}
body,.content-wrapper { background:#f0f4f0 !important; font-family:var(--font-base); }

/* ═══════════════════════ PAGE HEADER ═══════════════════════ */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--green-light);display:inline-flex;align-items:center;justify-content:center;color:var(--green-mid);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--green-light);border-color:var(--green-mid);color:var(--green-deep); }

/* ═══════════════════════ STEP TRACK ═══════════════════════ */
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:16px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s;border:2.5px solid transparent; }
.step-active   { background:var(--green-mid);color:#fff;border-color:var(--green-mid);box-shadow:0 0 0 4px rgba(67,160,71,.15); }
.step-done     { background:var(--green-deep);color:#fff;border-color:var(--green-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub  { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--green-mid); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--green-deep); }

/* ═══════════════════════ MODERN CARD ═══════════════════════ */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-success-soft { background:var(--green-light); }
.modern-card-body { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }
.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }
.patient-total-pill { background:var(--green-light);color:var(--green-deep);border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600; }

/* Loading bar */
.admission-status-bar { border-radius:var(--radius-md);padding:12px 18px;margin-bottom:16px;font-size:13.5px;font-weight:500;display:flex;align-items:center;border:1.5px solid transparent; }
.admission-loading-bar { background:var(--blue-light);color:var(--blue-deep);border-color:#90caf9; }

/* ═══════════════════════ SEARCH ═══════════════════════ */
.inline-search-bar { padding:14px 24px;background:#fafbff;border-bottom:2px solid var(--green-soft); }
.inline-search-inner { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.search-input-group { display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s;box-shadow:var(--shadow-sm); }
.search-input-group:focus-within { border-color:var(--green-mid);box-shadow:0 0 0 3px rgba(67,160,71,.1); }
.search-input-group-inline { flex:1;min-width:260px; }
.search-icon { padding:0 12px;color:#aab;font-size:15px; }
.search-input { flex:1;border:none;outline:none;padding:10px 6px;font-size:14px;background:transparent;color:var(--text-primary); }
.search-btn { border:none;padding:10px 22px;font-size:13.5px;font-weight:600;cursor:pointer;transition:background .2s; }
.search-btn-success { background:var(--green-mid);color:#fff; }
.search-btn-success:hover { background:var(--green-deep); }
.btn-add-new-patient { background:var(--green-light);color:var(--green-deep);border:1.5px solid #a5d6a7;border-radius:var(--radius-sm);padding:9px 18px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;white-space:nowrap;transition:all .2s; }
.btn-add-new-patient:hover { background:var(--green-deep);color:#fff;border-color:var(--green-deep); }
.btn-add-new-patient-sm { font-size:12px;background:var(--green-light);color:var(--green-deep);border:1.5px solid #a5d6a7;border-radius:var(--radius-sm);padding:5px 12px;text-decoration:none;display:inline-flex;align-items:center;transition:all .2s; }
.btn-add-new-patient-sm:hover { background:var(--green-deep);color:#fff; }

/* ═══════════════════════ PATIENT TABLE ═══════════════════════ */
.patient-table-scroll { overflow-x:auto;overflow-y:auto;max-height:calc(100vh - 340px); }
.modern-table { border-collapse:separate;border-spacing:0;width:100%; }
.modern-table thead tr th { background:#f5fdf5;color:var(--text-primary);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding:11px 14px;border-bottom:2px solid var(--green-soft);white-space:nowrap;position:sticky;top:0;z-index:10; }
.modern-table tbody tr { transition:background .15s; }
.modern-table tbody tr:hover { background:#f5fdf5; }
.modern-table tbody td { padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text-primary);vertical-align:middle; }
.patient-code-badge { background:#e8f5e9;color:var(--green-deep);border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700;font-family:monospace; }
.patient-name-cell { display:flex;align-items:center;gap:8px; }
.patient-mini-avatar { width:30px;height:30px;border-radius:50%;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.patient-mini-avatar-success { background:linear-gradient(135deg,var(--green-deep),#66bb6a); }
.gender-badge { display:inline-flex;align-items:center;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.gender-male   { background:#e3f2fd;color:var(--blue-deep); }
.gender-female { background:#fce4ec;color:#880e4f; }
.blood-badge { background:#ffebee;color:#c62828;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.btn-select-patient { background:var(--green-mid);color:#fff;border:none;border-radius:var(--radius-sm);width:34px;height:34px;font-size:13px;cursor:pointer;transition:all .2s;box-shadow:0 2px 6px rgba(67,160,71,.22);display:inline-flex;align-items:center;justify-content:center; }
.btn-select-patient:hover { background:var(--green-deep);transform:translateY(-1px);box-shadow:0 4px 12px rgba(67,160,71,.32); }
.empty-state { text-align:center;padding:40px;color:#b0bec5; }
.empty-state i { font-size:36px;margin-bottom:10px;display:block; }
.empty-state p { font-size:14px;margin:0; }
.pagination-bar { display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-top:1.5px solid var(--border);flex-wrap:wrap;gap:8px; }
.pagination { margin-bottom:0; }
.page-link { border-radius:var(--radius-sm) !important;border-color:var(--border);color:var(--green-mid);font-size:13px; }
.page-item.active .page-link { background:var(--green-mid);border-color:var(--green-mid); }

/* ═══════════════════════ PAST RX LIST ═══════════════════════ */
.past-rx-card { border-top:3px solid var(--green-mid); }
.fresh-rx-row:hover { background:#f5fdf5 !important; }
.btn-view-rx { background:linear-gradient(135deg,var(--green-mid),#66bb6a);color:#fff;border:none;border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;box-shadow:0 2px 6px rgba(67,160,71,.22); }
.btn-view-rx:hover { background:linear-gradient(135deg,var(--green-deep),var(--green-mid));transform:translateY(-1px);box-shadow:0 4px 12px rgba(67,160,71,.32); }

/* ═══════════════════════ STEP 2 ═══════════════════════ */
.patient-selected-bar { border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px; }
.patient-selected-bar-success { background:linear-gradient(135deg,#2E7D32 0%,#43A047 100%);box-shadow:0 4px 18px rgba(46,125,50,.22); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.psb-status-dot-green { background:#a5d6a7;box-shadow:0 0 0 3px rgba(165,214,167,.3); }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28);color:#fff; }

/* History stat boxes */
.hist-stat-box { padding:6px 10px;background:#fff;border-radius:6px;border:1px solid #e8eaf6;height:100%; }
.hist-stat-label { font-size:10px;color:#9e9e9e;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px; }
.hist-stat-value { font-size:14px;font-weight:700;color:#263238;line-height:1.2; }
.hist-stat-sub { font-size:10px;color:#78909c;margin-top:1px; }
.hist-section-title { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#37474f;padding:6px 10px;background:#eceff1;border-radius:4px;margin-bottom:4px;border-left:3px solid #1a237e; }
.rx-history-card { border:1px solid #e0e0e0;border-radius:6px;padding:10px 12px;margin-bottom:8px;background:#fafafa;font-size:12px; }
.rx-history-card .rx-history-title { font-weight:700;color:#1a237e;margin-bottom:5px;font-size:12px; }
.rx-history-card ul { margin:0;padding-left:16px; }
.rx-history-card ul li { line-height:1.7; }

/* Form fields */
.section-heading { display:flex;align-items:center;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px; }
.section-divider { border:none;border-top:1.5px solid var(--border); }
.section-divider-full { border-top:1.5px solid var(--border);padding-top:18px;display:flex;align-items:center;justify-content:space-between; }
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--green-mid);box-shadow:0 0 0 3px rgba(67,160,71,.1); }
.modern-textarea { resize:vertical;min-height:80px; }
.field-row-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.input-with-icon { position:relative; }
.input-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none; }
.modern-input.with-icon { padding-left:30px; }

/* Medicine tables */
.med-section-actions { display:flex;gap:8px; }
.btn-med-action { border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center; }
.btn-med-add-success { background:var(--green-light);color:var(--green-deep);border-color:var(--green-soft); }
.btn-med-add-success:hover { background:var(--green-mid);color:#fff; }
.btn-med-clear-success { background:#ffebee;color:#c62828;border:1.5px solid #ffcdd2;border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center;gap:4px; }
.btn-med-clear-success:hover { background:#c62828;color:#fff; }
.med-table-card { border-radius:var(--radius-md);border:1.5px solid var(--border);overflow:hidden;box-shadow:var(--shadow-sm); }
.selected-med-card-success { border-color:var(--green-soft); }
.med-table-card-header { padding:11px 16px;background:#f9fafb;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.med-table-dot { width:8px;height:8px;border-radius:50%;margin-right:8px;flex-shrink:0; }
.med-table-title { font-size:13px;font-weight:700;color:var(--text-primary); }
.med-count-pill { border-radius:20px;padding:2px 10px;font-size:11.5px;font-weight:700;margin-left:8px; }
.med-count-pill-success { background:var(--green-light);color:var(--green-deep); }
.med-table { border-collapse:collapse;width:100%; }
.med-table thead tr th { background:#f5f7fa;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);padding:9px 12px;border-bottom:1.5px solid var(--border);white-space:nowrap; }
.med-table tbody td { padding:8px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.med-table tbody tr:last-child td { border-bottom:none; }
.med-table tbody tr:hover { background:#f5fdf5; }
.med-table .form-control { padding:4px 8px !important;font-size:12.5px !important;min-height:unset !important; }
.med-empty-state { text-align:center;color:#b0bec5;padding:20px;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px; }
.avail-med-name { font-weight:600;color:var(--text-primary);font-size:13px; }
.avail-filter-wrap { display:flex;align-items:center;background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .2s; }
.avail-filter-wrap:focus-within { border-color:var(--green-mid); }
.avail-filter-icon { padding:0 9px;color:#aab;font-size:12px; }
.avail-filter-input { border:none;outline:none;padding:6px 4px;font-size:13px;background:transparent;width:170px; }
.modern-checkbox { width:15px;height:15px;accent-color:var(--green-mid);cursor:pointer; }
.btn-quick-add { width:26px;height:26px;border-radius:6px;border:1.5px solid transparent;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .18s; }
.btn-quick-add-green { background:var(--green-light);color:var(--green-deep);border-color:#a5d6a7; }
.btn-quick-add-green:hover { background:var(--green-deep);color:#fff; }

/* Form footer */
.form-footer { display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1.5px solid var(--border); }
.btn-footer-back { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:600;transition:all .2s; }
.btn-footer-back:hover { background:#f0f4f8;color:var(--text-primary); }
.btn-footer-save-success { background:linear-gradient(135deg,#2E7D32,#43A047);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(46,125,50,.28);transition:all .2s;display:inline-flex;align-items:center;gap:7px; }
.btn-footer-save-success:hover { background:linear-gradient(135deg,#1b5e20,#2E7D32);transform:translateY(-1px);color:#fff; }

/* RX summary cards */
.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-green  { background:linear-gradient(135deg,#2E7D32,#43A047); }
.rx-card-teal   { background:linear-gradient(135deg,#00695C,#00897B); }
.rx-card-orange { background:linear-gradient(135deg,#E65100,#F57C00); }
.rx-card-blue   { background:linear-gradient(135deg,#1565C0,#1976D2); }
.rx-summary-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.22);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.rx-summary-label { color:rgba(255,255,255,.75);font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;font-weight:600; }
.rx-summary-value { color:#fff;font-size:14px;font-weight:700;margin-top:2px; }
.rx-saved-badge { background:var(--green-light);color:var(--green-deep);border:1.5px solid #a5d6a7;border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center; }
.btn-rx-action { border-radius:var(--radius-sm);padding:8px 18px;font-size:13px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-print { background:var(--blue-deep);color:#fff;border-color:var(--blue-deep); }
.btn-rx-print:hover { background:var(--blue-mid); }
.btn-rx-edit  { background:#fff7e0;color:#e65100;border-color:#ffe082; }
.btn-rx-edit:hover { background:#e65100;color:#fff; }
.btn-rx-new   { background:#f0f4f8;color:var(--text-primary);border-color:var(--border); }
.btn-rx-new:hover { background:#e8ecf2; }

/* ═══════════════════════ FRESH RX PRINT LAYOUT ═══════════════════════ */
#prescription-print-area { padding:0;background:#fff; }
.fresh-wrapper { width:100%;max-width:780px;margin:0 auto;background:#fff;border:1px solid #ccc;font-family:'Hind Siliguri',Arial,sans-serif;font-size:12px; }
.fresh-header { display:flex;justify-content:space-between;align-items:flex-start;background:linear-gradient(135deg,#e8f4fd 0%,#d0eaf8 100%);border-bottom:2px solid #4a90d9;padding:12px 16px 10px;gap:10px; }
.fresh-header-left { flex:1; }
.fresh-header-right { text-align:right;border-left:2px solid #4a90d9;padding-left:12px;flex:1; }
.fresh-logo-row { display:flex;align-items:center;gap:10px; }
.fresh-cp-logo { width:46px;height:46px;border-radius:50%;border:2px solid #c0392b;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;flex-shrink:0;background:#fff; }
.fresh-cp-c { color:#c0392b; } .fresh-cp-p { color:#2980b9; }
.fresh-clinic-bn { font-size:22px;font-weight:700;color:#2c3e50;line-height:1.1; }
.fresh-clinic-address { font-size:11px;color:#444;margin-top:2px; }
.fresh-clinic-phones { font-size:10px;color:#555; }
.fresh-doctor-title { font-size:14px;font-weight:700;color:#c0392b; }
.fresh-doctor-deg { font-size:10.5px;color:#2c3e50; }
.fresh-doctor-college { font-size:10px;color:#c0392b;margin-top:2px; }
.fresh-nad-row { display:flex;flex-wrap:wrap;background:#f8e8ee;padding:6px 16px;border-bottom:1px solid #e0aab8;gap:6px 10px; }
.fresh-nad-field { display:flex;align-items:center;gap:6px;flex:1;min-width:130px; }
.fresh-nad-label { font-weight:700;font-size:12px;white-space:nowrap; }
.fresh-nad-value { border-bottom:1px dotted #999;flex:1;padding:0 4px;font-size:12px;min-width:60px; }
.fresh-body { display:flex;min-height:400px; }
.fresh-left { width:38%;border-right:1px solid #ccc;padding:10px 12px;background:#f0f8ff; }
.fresh-section-title { font-weight:700;font-size:12px;text-decoration:underline;margin-bottom:4px;margin-top:8px; }
.fresh-section-title:first-child { margin-top:0; }
.fresh-cc-text { font-size:11.5px;color:#222;min-height:30px;padding:2px 0; }
.fresh-list { list-style:none;padding:0;margin:0; }
.fresh-list li { font-size:11.5px;line-height:1.7;display:flex;justify-content:space-between;align-items:center;padding:0 2px; }
.fresh-list li::before { content:"· ";color:#333; }
.fresh-val { font-size:11px;color:#2c3e50;font-weight:600;margin-left:4px; }
.fresh-right { flex:1;padding:10px 14px;position:relative;overflow:hidden; }
.fresh-rx-symbol { font-size:28px;font-weight:900;font-style:italic;color:#2c3e50;margin-bottom:4px; }
.fresh-footer { display:flex;justify-content:space-between;border-top:1px solid #ccc;padding:6px 16px;font-size:11px;background:#f8e8ee;color:#555; }
.fresh-med-table-wrap { overflow-x:auto;margin-top:4px; }
.fresh-med-table { width:100%;border-collapse:collapse;font-family:'Hind Siliguri',Arial,sans-serif;font-size:11px; }
.fresh-med-table th,.fresh-med-table td { border:1px solid #999;padding:3px 4px;text-align:center;vertical-align:middle; }
.fresh-th-name { text-align:left !important;width:38%;background:#f0f0f0;font-size:11px;font-weight:700; }
.fresh-th-group { background:#e8e8e8;font-weight:700;font-size:10.5px; }
.fresh-th-sub { background:#f5f5f5;font-size:10px;font-weight:600;min-width:26px; }
.fresh-med-table tbody tr td { font-size:11px; }
.fresh-med-table tbody tr td:first-child { text-align:left;padding-left:6px; }
.fresh-med-table tbody tr.empty-row td { height:22px; }

/* ═══════════════════════ MODAL ═══════════════════════ */
.rx-modal-content { border:none;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.rx-modal-header { background:linear-gradient(135deg,#2E7D32 0%,#43A047 100%);border:none;padding:18px 22px;display:flex;align-items:center;justify-content:space-between; }
.rx-modal-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.modal-subtitle-text { color:rgba(255,255,255,.75);font-size:12px;display:block;margin-top:2px; }
.btn-rx-modal-print { background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-modal-print:hover { background:rgba(255,255,255,.32); }
.btn-rx-modal-close { background:rgba(255,255,255,.15);border:none;color:rgba(255,255,255,.85);width:32px;height:32px;border-radius:50%;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s; }
.btn-rx-modal-close:hover { background:rgba(255,255,255,.28);color:#fff; }
.rx-modal-footer { background:#fafbfd;border-top:1px solid var(--border);padding:12px 22px;display:flex;align-items:center;justify-content:space-between; }
.modal-summary-bar { display:flex;gap:0;border-bottom:1px solid var(--border); }
.modal-summary-item { flex:1;padding:14px 18px;display:flex;align-items:center;gap:10px;border-right:1px solid var(--border); }
.modal-summary-item:last-child { border-right:none; }
.msi-green  { background:linear-gradient(135deg,#E8F5E9,#fff); }
.msi-teal   { background:linear-gradient(135deg,#E0F2F1,#fff); }
.msi-orange { background:linear-gradient(135deg,#FFF3E0,#fff); }
.msi-blue   { background:linear-gradient(135deg,#E3F2FD,#fff); }
.modal-summary-item > i { font-size:18px;flex-shrink:0; }
.msi-green  > i { color:var(--green-deep); }
.msi-teal   > i { color:var(--teal-mid); }
.msi-orange > i { color:var(--orange); }
.msi-blue   > i { color:var(--blue-deep); }
.msi-label { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted); }
.msi-val   { font-size:13px;font-weight:700;color:var(--text-primary);margin-top:1px; }
.modal-state-wrap { text-align:center;padding:50px 20px;color:#90A4AE; }
.modal-spinner-icon { font-size:34px;margin-bottom:12px;color:var(--green-mid); }
.modal-error-icon { font-size:36px;margin-bottom:10px;color:#ef5350; }
.modal-state-text { font-size:14px;margin:0; }

/* ═══════════════════════════════════════════════════════════════
   PRINT — FIXED (same as on_admission)
   Uses visibility toggling so flex/inline/list layouts preserved
═══════════════════════════════════════════════════════════════ */
#print-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; min-height: 100%;
    background: #fff;
    z-index: 9999999;
    padding: 10mm 12mm;
    box-sizing: border-box;
}

@media print {
    *{-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}

    body * {
        visibility: hidden;
    }

    #print-overlay,
    #print-overlay * {
        visibility: visible !important;
    }

    #print-overlay {
        display: block !important;
        position: fixed !important;
        top: 0 !important; left: 0 !important;
        width: 100% !important;
        background: #fff !important;
        padding: 10mm 12mm !important;
        box-sizing: border-box !important;
    }

    #print-overlay .fresh-wrapper {
        border: none !important;
        max-width: 100% !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    #print-overlay .modern-card-header,
    #print-overlay .modern-card-footer,
    #print-overlay .rx-summary-card,
    #print-overlay .modal-summary-bar,
    #print-overlay .modal-summary-item {
        display: none !important;
        visibility: hidden !important;
    }

    @page {
        size: A4 portrait;
        margin: 0;
    }
}
</style>
@stop

@section('js')
<script>
var CSRF_TOKEN        = '{{ csrf_token() }}';
var FRESH_HISTORY_URL = '{{ url("nursing/fresh/patient-admission") }}';
var FRESH_STORE_URLS  = [
    '{{ url("/nursing/Fresh/store") }}',
    '{{ url("/nursing/fresh/store") }}',
];
var FRESH_DETAIL_URL  = '/nursing/fresh/detail';

/* ═══════════════════════════════════════════════════
   GLOBAL STATE
═══════════════════════════════════════════════════ */
var selectedMeds    = [];
var admDetailsOpen  = true;
var psDetailsOpen   = true;
var freshDetailsOpen= true;

/* ═══════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════ */
function todayISO(){ return new Date().toISOString().split('T')[0]; }
function fmtDateBD(iso){ if(!iso) return '—'; var p=String(iso).slice(0,10).split('-'); return p[2]+'/'+p[1]+'/'+p[0].slice(2); }
function fmtTime(t){ if(!t) return '—'; var p=String(t).split(':'); var hr=parseInt(p[0]); if(isNaN(hr)) return t; return (hr%12||12)+':'+p[1]+(hr>=12?' pm':' am'); }
function gVal(id){ var el=document.getElementById(id); return el?el.value.trim():''; }
function setText(id,txt){ var el=document.getElementById(id); if(el) el.textContent=(txt!==null&&txt!==undefined&&txt!=='')?txt:'—'; }
function showEl(id){ var el=document.getElementById(id); if(el) el.style.display=''; }
function hideEl(id){ var el=document.getElementById(id); if(el) el.style.display='none'; }
function esc(str){ return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
function showAlert(type,msg){
    var el=document.getElementById('save-alert');
    el.className='alert alert-'+type+' modern-alert';
    el.innerHTML=msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){el.classList.add('d-none');},6000);
}
function showToast(msg,type){
    var bg=type==='success'?'#2e7d32':(type==='info'?'#0288d1':'#e65100');
    var t=document.createElement('div');
    t.style.cssText='position:fixed;bottom:20px;right:20px;z-index:9999;background:'+bg+';color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;box-shadow:0 4px 12px rgba(0,0,0,.2);transition:opacity .3s;max-width:320px;';
    t.innerHTML='<i class="fas fa-check-circle mr-2"></i>'+msg;
    document.body.appendChild(t);
    setTimeout(function(){t.style.opacity='0';setTimeout(function(){t.remove();},300);},2500);
}

/* ═══════════════════════════════════════════════════
   PRINT FUNCTIONS — FIXED (same pattern as on_admission)
═══════════════════════════════════════════════════ */
function _doPrint(sourceId){
    var source  = document.getElementById(sourceId);
    var overlay = document.getElementById('print-overlay');
    if(!source||!overlay){ window.print(); return; }

    var wrapper = source.querySelector('.fresh-wrapper');
    var toClone = wrapper || source;

    overlay.innerHTML = '';
    overlay.appendChild(toClone.cloneNode(true));
    overlay.style.display = 'block';

    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            window.print();
            var cleanup = function(){
                overlay.style.display = 'none';
                overlay.innerHTML = '';
                window.removeEventListener('focus', cleanup);
            };
            window.addEventListener('focus', cleanup);
            setTimeout(function(){
                overlay.style.display = 'none';
                overlay.innerHTML = '';
                window.removeEventListener('focus', cleanup);
            }, 60000);
        });
    });
}

function printRx()    { _doPrint('prescription-print-area'); }
function printModal() { _doPrint('modal-prescription-print-area'); }

/* ═══════════════════════════════════════════════════
   DOCTOR HEADER
═══════════════════════════════════════════════════ */
function updateDoctorHeader(){
    var sel = document.getElementById('f-doctor');
    if(!sel||!sel.options.length) return;
    var d = sel.options[sel.selectedIndex].dataset;
    setText('rx-doctor-name',       d.docname    || '—');
    setText('rx-doctor-speciality', d.speciality || '');
    setText('rx-doctor-regno',      d.doctorno   ? 'Reg No: '+d.doctorno : '');
    setText('rx-doctor-posting',    d.posting    || '');
    setText('rx-doctor-contact',    d.contact    ? 'Mobile: '+d.contact : '');
    setText('ib-doctor',            d.docname    || '—');
}

/* ═══════════════════════════════════════════════════
   MEDICINE TABLE
═══════════════════════════════════════════════════ */
function refreshSelTable(){
    var tbody  = document.getElementById('sel-med-tbody');
    var badge1 = document.getElementById('sel-med-badge');
    var badge2 = document.getElementById('med-count-badge');
    badge1.textContent = selectedMeds.length;
    badge2.textContent = selectedMeds.length;

    if(!selectedMeds.length){
        tbody.innerHTML = '<tr id="empty-sel-row"><td colspan="9">'+
            '<div class="med-empty-state"><i class="fas fa-pills" style="color:#43a047;"></i>'+
            '<span>No medicines selected yet.</span></div></td></tr>';
        return;
    }
    tbody.innerHTML = selectedMeds.map(function(m,i){
        return '<tr>'+
            '<td>'+(i+1)+'</td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.medicine_name||'')+'" onchange="selectedMeds['+i+'].medicine_name=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.dose||'')+'" onchange="selectedMeds['+i+'].dose=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.route||'')+'" onchange="selectedMeds['+i+'].route=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.frequency||'')+'" onchange="selectedMeds['+i+'].frequency=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.duration||'')+'" onchange="selectedMeds['+i+'].duration=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.timing||'')+'" onchange="selectedMeds['+i+'].timing=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.remarks||'')+'" onchange="selectedMeds['+i+'].remarks=this.value" placeholder="Optional"></td>'+
            '<td class="text-center"><button type="button" class="btn-quick-add" style="background:#ffebee;color:#c62828;border-color:#ffcdd2;" onclick="removeMed('+i+')">'+
            '<i class="fas fa-times"></i></button></td></tr>';
    }).join('');
}

function addMedToList(name,dose,route,frequency,duration,timing,remarks){
    if(!name||!name.trim()) return;
    if(selectedMeds.find(function(m){return m.medicine_name.toLowerCase()===name.toLowerCase();})) return;
    selectedMeds.push({medicine_name:name,dose:dose||'',route:route||'',frequency:frequency||'',duration:duration||'',timing:timing||'',remarks:remarks||''});
    refreshSelTable();
}
function addMedRow(){
    selectedMeds.push({medicine_name:'',dose:'',route:'',frequency:'',duration:'',timing:'',remarks:''});
    refreshSelTable();
}
function removeMed(idx){
    var name = selectedMeds[idx]?selectedMeds[idx].medicine_name:'';
    selectedMeds.splice(idx,1);
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){ if((cb.dataset.name||'')===name) cb.checked=false; });
    refreshSelTable();
}
function clearAllMeds(){
    if(!selectedMeds.length) return;
    if(!confirm('সব medicine মুছে ফেলবেন?')) return;
    selectedMeds=[];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;});
    var sa=document.getElementById('select-all-med'); if(sa) sa.checked=false;
    document.getElementById('auto-loaded-note').classList.add('d-none');
    refreshSelTable();
}
function onAvailMedChange(cb){
    if(cb.checked){
        addMedToList(cb.dataset.name,cb.dataset.dose,cb.dataset.route,
            cb.dataset.frequency,cb.dataset.duration,cb.dataset.timing,cb.dataset.note);
    } else {
        selectedMeds=selectedMeds.filter(function(m){return m.medicine_name!==cb.dataset.name;});
        refreshSelTable();
    }
}
function quickAdd(btn){
    var cb=btn.closest('tr').querySelector('.avail-med-cb');
    cb.checked=true;
    onAvailMedChange(cb);
}

/* ═══════════════════════════════════════════════════
   DOM READY
═══════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',function(){
    var mf=document.getElementById('med-filter');
    if(mf) mf.addEventListener('input',function(){
        var q=this.value.toLowerCase();
        document.querySelectorAll('.avail-med-row').forEach(function(r){
            r.style.display=(r.dataset.name||'').includes(q)?'':'none';
        });
    });
    var sa=document.getElementById('select-all-med');
    if(sa) sa.addEventListener('change',function(){
        document.querySelectorAll('.avail-med-cb').forEach(function(cb){
            cb.checked=sa.checked; onAvailMedChange(cb);
        });
    });
    var ps=document.getElementById('patientSearch');
    if(ps) ps.addEventListener('keyup',filterTable);
    updateDoctorHeader();
});

/* ═══════════════════════════════════════════════════
   SELECT PATIENT
═══════════════════════════════════════════════════ */
function selectPatient(btn){
    var d = btn.dataset;
    document.getElementById('f-patient-id').value    = d.id    || '';
    document.getElementById('f-patient-code').value  = d.code  || '';
    document.getElementById('f-patient-name').value  = d.name  || '';
    document.getElementById('f-patient-age').value   = d.age   || '';
    document.getElementById('f-date').value           = todayISO();

    document.getElementById('spb-avatar').textContent = (d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent   = d.name  || '—';
    document.getElementById('spb-meta').textContent   = [d.code,d.age,d.mobile,d.blood,d.upozila].filter(Boolean).join(' · ');

    document.getElementById('step1-circle').className = 'step-circle step-done';
    document.getElementById('step1-circle').innerHTML = '<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className = 'step-circle step-active';
    document.getElementById('step2-label').className  = 'step-label-main step-label-active';
    document.getElementById('breadcrumb-current').textContent = 'Prescription';

    document.getElementById('panel-step1').style.display  = 'none';
    document.getElementById('panel-step2').style.display  = 'block';
    document.getElementById('rx-view').style.display      = 'none';
    document.getElementById('rx-form-card').style.display = 'block';

    selectedMeds = [];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;});
    document.getElementById('auto-loaded-note').classList.add('d-none');
    refreshSelTable();
    hideEl('admission-info-box'); hideEl('postsurgery-info-box'); hideEl('fresh-info-box');
    updateDoctorHeader();
    fetchPatientHistory(d.id);
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ═══════════════════════════════════════════════════
   FETCH PATIENT HISTORY
═══════════════════════════════════════════════════ */
function fetchPatientHistory(patientId){
    showEl('history-loading');
    fetch(FRESH_HISTORY_URL+'/'+patientId,{
        headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}
    })
    .then(function(r){return r.json();})
    .then(function(data){
        hideEl('history-loading');
        if(!data.success) return;
        renderAdmissionBox(data);
        renderPostSurgeryBox(data);
        renderFreshBox(data);
        autoLoadMedicines(data);
    })
    .catch(function(){ hideEl('history-loading'); });
}

/* ═══════════════════════════════════════════════════
   AUTO-LOAD MEDICINES
═══════════════════════════════════════════════════ */
function autoLoadMedicines(data){
    var admMeds = data.medicines              || [];
    var psMeds  = data.post_surgery_medicines || [];

    if(admMeds.length===0 && psMeds.length===0){
        showAlert('warning','<i class="fas fa-exclamation-circle mr-1"></i>এই রোগীর কোনো On Admission বা Post-Surgery medicine পাওয়া যায়নি।');
        return;
    }

    selectedMeds = [];
    var totalAdded = 0;

    admMeds.forEach(function(m){
        var mName=(m.medicine_name||m.name||'').trim();
        if(!mName) return;
        selectedMeds.push({medicine_name:mName,dose:m.dose||'',route:m.route||'',frequency:m.frequency||'',duration:m.duration||'',timing:m.timing||'',remarks:m.remarks||m.note||''});
        totalAdded++;
    });
    psMeds.forEach(function(m){
        var mName=(m.medicine_name||m.name||'').trim();
        if(!mName) return;
        if(selectedMeds.find(function(s){return s.medicine_name.toLowerCase()===mName.toLowerCase();})) return;
        selectedMeds.push({medicine_name:mName,dose:m.dose||'',route:m.route||'',frequency:m.frequency||'',duration:m.duration||'',timing:m.timing||'',remarks:m.remarks||m.note||''});
        totalAdded++;
    });

    refreshSelTable();
    if(totalAdded>0){
        document.getElementById('auto-loaded-note').classList.remove('d-none');
        var parts=[];
        if(admMeds.length>0) parts.push('On Admission: <strong>'+admMeds.length+'টি</strong>');
        if(psMeds.length >0) parts.push('Post-Surgery: <strong>'+psMeds.length+'টি</strong>');
        showAlert('info','<i class="fas fa-pills mr-1"></i> '+parts.join(', ')+' medicine auto-load হয়েছে।');
    }
}

/* ═══════════════════════════════════════════════════
   RENDER HISTORY BOXES
═══════════════════════════════════════════════════ */
function renderAdmissionBox(data){
    var adm = data.admission;
    if(!adm) return;
    setText('adm-record-id',   '#'+adm.id);
    setText('adm-date',        adm.admission_date ? fmtDateBD(adm.admission_date):'—');
    setText('adm-time',        adm.admission_time ? fmtTime(adm.admission_time)  :'—');
    setText('adm-pulse',       adm.pulse          || '—');
    setText('adm-bp',          adm.bp             || '—');
    setText('adm-ot-time',     adm.ot_time        ? fmtTime(adm.ot_time):'—');
    setText('adm-preg-weeks',  adm.pregnancy_weeks|| '—');
    setText('adm-baby-sex',    adm.baby_sex       || '—');
    setText('adm-baby-weight', adm.baby_weight    || '—');
    setText('adm-baby-time',   adm.baby_time      ? fmtTime(adm.baby_time):'—');

    var meds = data.medicines || [];
    if(meds.length>0){
        setText('adm-med-count',meds.length);
        document.getElementById('adm-medicines-tbody').innerHTML = meds.map(function(m,i){
            return '<tr><td>'+(i+1)+'</td><td><strong>'+esc(m.medicine_name||'—')+'</strong></td>'+
                '<td>'+esc(m.dose||'—')+'</td><td>'+esc(m.route||'—')+'</td>'+
                '<td>'+esc(m.frequency||'—')+'</td><td>'+esc(m.duration||'—')+'</td>'+
                '<td>'+esc(m.timing||'—')+'</td></tr>';
        }).join('');
        showEl('adm-medicines-wrap'); hideEl('adm-no-med-msg');
    } else { hideEl('adm-medicines-wrap'); showEl('adm-no-med-msg'); }

    var psMeds = data.post_surgery_medicines || [];
    if(psMeds.length>0){
        setText('ps-med-count',psMeds.length);
        document.getElementById('ps-medicines-tbody').innerHTML = psMeds.map(function(m,i){
            return '<tr><td>'+(i+1)+'</td><td><strong>'+esc(m.medicine_name||'—')+'</strong></td>'+
                '<td>'+esc(m.strength||'—')+'</td><td>'+esc(m.dose||'—')+'</td>'+
                '<td>'+esc(m.route||'—')+'</td><td>'+esc(m.frequency||'—')+'</td>'+
                '<td>'+esc(m.duration||'—')+'</td></tr>';
        }).join('');
        showEl('ps-medicines-wrap'); hideEl('ps-no-med-msg');
    } else { hideEl('ps-medicines-wrap'); showEl('ps-no-med-msg'); }

    var history = data.previous_prescriptions || [];
    if(history.length>0){ document.getElementById('adm-history-list').innerHTML=buildHistoryCards(history); showEl('adm-history-wrap'); }
    else { hideEl('adm-history-wrap'); }

    showEl('admission-info-box');
}

function renderPostSurgeryBox(data){
    var psHistory = data.post_surgery_prescriptions || [];
    if(psHistory.length===0){ hideEl('postsurgery-info-box'); return; }
    setText('ps-count-badge', psHistory.length+' টি');
    document.getElementById('ps-history-list').innerHTML = buildHistoryCards(psHistory);
    showEl('postsurgery-info-box');
}

function renderFreshBox(data){
    var freshHistory = data.fresh_prescriptions || [];
    if(freshHistory.length===0){ hideEl('fresh-info-box'); return; }
    setText('fresh-count-badge', freshHistory.length+' টি');
    document.getElementById('fresh-history-list').innerHTML = buildHistoryCards(freshHistory);
    showEl('fresh-info-box');
}

function buildHistoryCards(list){
    return list.map(function(rx){
        var badge='';
        if(rx.type==='on_admission') badge='<span class="badge badge-primary ml-1">On Admission</span>';
        if(rx.type==='fresh')        badge='<span class="badge badge-success ml-1">Fresh</span>';
        if(rx.type==='post-surgery') badge='<span class="badge badge-danger ml-1">Post-Surgery</span>';
        var dateStr = rx.date ? fmtDateBD(String(rx.date).slice(0,10)):'—';
        var lines = (rx.lines||[]).map(function(l){return '<li>'+esc(l)+'</li>';}).join('');
        return '<div class="rx-history-card">'+
            '<div class="rx-history-title">Prescription #'+esc(rx.id||'—')+badge+
            ' <span class="text-muted font-weight-normal">('+dateStr+')</span></div>'+
            '<ul>'+(lines||'<li class="text-muted">No medicines recorded.</li>')+'</ul>'+
        '</div>';
    }).join('');
}

function toggleAdmDetails(){
    var body=document.getElementById('adm-details-body');
    admDetailsOpen=!admDetailsOpen;
    body.style.display=admDetailsOpen?'':'none';
    document.getElementById('adm-toggle-icon').className=admDetailsOpen?'fas fa-chevron-up mr-1':'fas fa-chevron-down mr-1';
    document.getElementById('adm-toggle-text').textContent=admDetailsOpen?'Details Hide':'Details Show';
}
function togglePsDetails(){
    var body=document.getElementById('ps-details-body');
    psDetailsOpen=!psDetailsOpen;
    body.style.display=psDetailsOpen?'':'none';
    document.getElementById('ps-toggle-icon').className=psDetailsOpen?'fas fa-chevron-up mr-1':'fas fa-chevron-down mr-1';
    document.getElementById('ps-toggle-text').textContent=psDetailsOpen?'Details Hide':'Details Show';
}
function toggleFreshDetails(){
    var body=document.getElementById('fresh-details-body');
    freshDetailsOpen=!freshDetailsOpen;
    body.style.display=freshDetailsOpen?'':'none';
    document.getElementById('fresh-toggle-icon').className=freshDetailsOpen?'fas fa-chevron-up mr-1':'fas fa-chevron-down mr-1';
    document.getElementById('fresh-toggle-text').textContent=freshDetailsOpen?'Details Hide':'Details Show';
}

/* ═══════════════════════════════════════════════════
   SAVE & GENERATE
═══════════════════════════════════════════════════ */
function saveAndGenerateRx(){
    var patientId = gVal('f-patient-id');
    if(!patientId){ showAlert('warning','Please select a patient first.'); return; }

    var medsToSave = selectedMeds.filter(function(m){return m.medicine_name.trim()!=='';});
    var medsPayload = medsToSave.map(function(m){
        return { id:'fresh_'+Date.now(), name:m.medicine_name, strength:'',
            dose:m.dose, route:m.route, frequency:m.frequency,
            duration:m.duration, timing:m.timing, note:m.remarks };
    });

    var doctorSel  = document.getElementById('f-doctor');
    var doctorName = doctorSel&&doctorSel.options.length ? doctorSel.options[doctorSel.selectedIndex].dataset.docname||'' : '';

    var payload = {
        patient_id:patientId, patient_name:gVal('f-patient-name'),
        patient_age:gVal('f-patient-age'), patient_code:gVal('f-patient-code'),
        doctor_name:doctorName, prescription_date:gVal('f-date'),
        rx_text:gVal('f-notes'), notes:gVal('f-notes'),
        medicines:medsPayload,
    };

    var btn = document.getElementById('btn-save-rx');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

    function trySave(idx){
        if(idx>=FRESH_STORE_URLS.length){
            btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription';
            generateRxView(); return;
        }
        fetch(FRESH_STORE_URLS[idx],{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'},
            body:JSON.stringify(payload)
        }).then(function(r){
            if(r.status===404){ trySave(idx+1); return null; }
            return r.json();
        }).then(function(data){
            btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription';
            if(!data) return;
            generateRxView();
            if(data.success) showToast('Saved! ID: #'+data.prescription_id,'success');
        }).catch(function(){ trySave(idx+1); });
    }
    trySave(0);
}

/* ═══════════════════════════════════════════════════
   GENERATE RX VIEW
═══════════════════════════════════════════════════ */
function generateRxView(){
    var pName = gVal('f-patient-name')||'—';
    var pAge  = gVal('f-patient-age') ||'—';
    var pDate = fmtDateBD(gVal('f-date'));
    var pCode = gVal('f-patient-code')||'—';

    setText('ib-name',  pName); setText('ib-age',  pAge);
    setText('ib-date',  pDate); setText('rx-name', pName);
    setText('rx-age',   pAge);  setText('rx-date', pDate);
    setText('rx-code',  pCode); setText('rx-badge-name', pName);
    setText('rx-cc',    gVal('f-cc')      || '');
    setText('rx-pulse', gVal('f-pulse')   || '');
    setText('rx-bp',    gVal('f-bp')      || '');
    setText('rx-anaemia',  gVal('f-anaemia')  || '');
    setText('rx-jaundice', gVal('f-jaundice') || '');
    setText('rx-tem',   gVal('f-tem')     || '');
    setText('rx-oedema',gVal('f-oedema')  || '');
    setText('rx-weight',gVal('f-weight')  || '');
    setText('rx-heart', gVal('f-heart')   || '');
    setText('rx-lungs', gVal('f-lungs')   || '');
    setText('rx-fm',    gVal('f-fm')      || '');

    var rxTextEl = document.getElementById('rx-rx-text');
    if(rxTextEl) rxTextEl.textContent = gVal('f-notes')||'';
    var rxNotesEl = document.getElementById('rx-notes-print');
    if(rxNotesEl) rxNotesEl.textContent = '';

    updateDoctorHeader();
    renderRxMedicines('rx-med-print-tbody');

    setText('gen-time', new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}));
    document.getElementById('step2-circle').className = 'step-circle step-done';
    document.getElementById('step2-circle').innerHTML = '<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('rx-form-card').style.display = 'none';
    document.getElementById('rx-view').style.display      = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ═══════════════════════════════════════════════════
   RENDER MEDICINE PRINT TABLE
═══════════════════════════════════════════════════ */
function renderRxMedicines(tbodyId){
    var tbody = document.getElementById(tbodyId||'rx-med-print-tbody');
    if(!tbody) return;
    tbody.innerHTML = '';

    var printMeds = selectedMeds.filter(function(m){return m.medicine_name.trim();});

    printMeds.forEach(function(m){
        var morning='',noon='',night='';
        var freq=(m.frequency||'').trim();
        var dp=freq.match(/^(\S+)\+(\S+)\+(\S+)/);
        if(dp){morning=dp[1];noon=dp[2];night=dp[3];}else if(freq){morning=freq;}

        var timing=(m.timing||'').toLowerCase();
        var before=(timing.includes('before')||timing.includes('আগে'))?'✓':'';
        var after =(timing.includes('after') ||timing.includes('পরে') )?'✓':'';
        if(!before&&!after&&timing) after='✓';

        var days='',months='',cont='';
        var dur=(m.duration||'').toLowerCase().trim();
        if(dur.includes('cont')||dur.includes('চলবে')){ cont='✓'; }
        else if(dur.includes('month')||dur.includes('মাস')){ months=dur.replace(/[^0-9]/g,'')||'1'; }
        else if(dur){ days=dur.replace(/[^0-9]/g,'')||''; }

        var name = m.medicine_name;
        if(m.route&&m.route!=='') name = m.route+' '+name;

        var tr = document.createElement('tr');
        tr.innerHTML = '<td style="text-align:left;padding-left:6px;">• '+esc(name)+'</td>'+
            '<td>'+morning+'</td><td>'+noon+'</td><td>'+night+'</td>'+
            '<td>'+before+'</td><td>'+after+'</td>'+
            '<td>'+days+'</td><td>'+months+'</td><td>'+cont+'</td>';
        tbody.appendChild(tr);
    });

    var needed = Math.max(0, 8-printMeds.length);
    for(var i=0;i<needed;i++){
        var tr=document.createElement('tr'); tr.className='empty-row';
        tr.innerHTML='<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
        tbody.appendChild(tr);
    }
}

/* ═══════════════════════════════════════════════════
   NAVIGATION
═══════════════════════════════════════════════════ */
function backToStep1(){
    document.getElementById('step1-circle').className   = 'step-circle step-active';
    document.getElementById('step1-circle').textContent = '1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className = 'step-circle step-inactive';
    document.getElementById('step2-label').className  = 'step-label-main step-label-inactive';
    document.getElementById('breadcrumb-current').textContent = 'Select Patient';
    document.getElementById('panel-step1').style.display = 'block';
    document.getElementById('panel-step2').style.display = 'none';
    hideEl('admission-info-box'); hideEl('postsurgery-info-box'); hideEl('fresh-info-box');
    window.scrollTo({top:0,behavior:'smooth'});
}
function editRx(){
    document.getElementById('rx-view').style.display      = 'none';
    document.getElementById('rx-form-card').style.display = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ═══════════════════════════════════════════════════
   TABLE FILTERS
═══════════════════════════════════════════════════ */
function filterTable(){
    var q = document.getElementById('patientSearch').value.toLowerCase();
    document.querySelectorAll('#patientTable tbody tr').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q)?'':'none';
    });
}
function filterFreshRxTable(){
    var q = (document.getElementById('freshRxSearch').value||'').toLowerCase();
    document.querySelectorAll('#freshRxTable tbody tr.fresh-rx-row').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q)?'':'none';
    });
}

/* ═══════════════════════════════════════════════════
   VIEW PAST FRESH PRESCRIPTION MODAL
═══════════════════════════════════════════════════ */
function viewFreshPrescription(prescriptionId){
    document.getElementById('modal-loading').classList.remove('d-none');
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');
    document.getElementById('modal-subtitle').textContent = 'Loading...';

    $('#rxViewModal').modal('show');

    $.ajax({
        url: FRESH_DETAIL_URL + '/' + prescriptionId,
        method: 'GET',
        dataType: 'json',
    }).done(function(res){
        if(!res.success || !res.data){ showModalError(res.message || 'Record not found.'); return; }
        populateFreshModal(res.data);
    }).fail(function(xhr){
        showModalError('Failed to load prescription (HTTP '+xhr.status+')');
    });
}

function showModalError(msg){
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-error').classList.remove('d-none');
    document.getElementById('modal-error-msg').textContent = msg;
}

function populateFreshModal(d){
    document.getElementById('modal-subtitle').textContent =
        (d.patient_name||'—') + '  ·  ' + (d.patient_code || d.p_code || '—');

    setText('m-ib-name',   d.patient_name);
    setText('m-ib-age',    d.patient_age);
    setText('m-ib-date',   fmtDateBD(d.prescription_date || d.created_at));
    setText('m-ib-id',     '#'+d.id);

    setText('m-rx-code',   d.patient_code || d.p_code || '—');
    setText('m-rx-name',   d.patient_name);
    setText('m-rx-age',    d.patient_age);
    setText('m-rx-date',   fmtDateBD(d.prescription_date || d.created_at));
    setText('m-rx-doctor-name',       d.doctor_name || '—');
    setText('m-rx-doctor-speciality', d.doctor_speciality || '');
    setText('m-rx-doctor-regno',      d.doctor_regno ? 'Reg No: '+d.doctor_regno : '');
    setText('m-rx-doctor-posting',    d.doctor_posting || '');
    setText('m-rx-doctor-contact',    d.doctor_contact ? 'Mobile: '+d.doctor_contact : '');
    setText('m-rx-cc',     d.cc     || '—');
    setText('m-rx-pulse',  d.pulse  || '—');
    setText('m-rx-bp',     d.bp     || '—');
    setText('m-rx-notes',  d.notes  || d.rx_text || '—');

    var rxTextEl = document.getElementById('m-rx-rx-text');
    if(rxTextEl) rxTextEl.textContent = d.rx_text || d.notes || '';

    var notesEl = document.getElementById('m-rx-notes');
    if(notesEl) notesEl.textContent = d.notes || '';

    // Build modal medicine print table
    var meds = [];
    if(d.medicines_decoded && Array.isArray(d.medicines_decoded)) meds = d.medicines_decoded;
    else if(typeof d.medicines === 'string'){ try{ meds=JSON.parse(d.medicines); }catch(e){ meds=[]; } }
    else if(Array.isArray(d.medicines)) meds = d.medicines;

    // normalise field names from DB (name vs medicine_name)
    var normMeds = meds.filter(function(m){ return m&&((m.medicine_name||m.name||'').trim()); })
        .map(function(m){
            return {
                medicine_name: m.medicine_name || m.name || '',
                dose:      m.dose      || '',
                route:     m.route     || '',
                frequency: m.frequency || '',
                duration:  m.duration  || '',
                timing:    m.timing    || m.instruction || '',
                remarks:   m.note      || m.remarks || '',
            };
        });

    var savedMeds = selectedMeds;
    selectedMeds  = normMeds;
    renderRxMedicines('m-rx-med-print-tbody');
    selectedMeds  = savedMeds;

    setText('m-saved-time', d.created_at
        ? new Date(d.created_at).toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—'
    );

    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.remove('d-none');
}
</script>
@stop