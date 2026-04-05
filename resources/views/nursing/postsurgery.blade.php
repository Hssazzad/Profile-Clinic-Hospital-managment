@extends('adminlte::page')

@section('title', 'Post-Surgery | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-procedures"></i></span>
                Post-Surgery
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
@php
    $medicines           = $medicines           ?? collect();
    $patients            = $patients            ?? collect();
    $PostSurgeryPatients = $PostSurgeryPatients ?? collect();
@endphp

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
                        <div class="step-label-sub step-label-inactive" id="step2-sublabel">Post-Op Prescription</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SAVE ALERT ══ --}}
<div id="save-alert" class="alert d-none mb-3 modern-alert" role="alert"></div>

{{-- ══ FIXED SEARCH BAR (same as On Admission) ══ --}}
<div id="fixed-search-bar" class="fixed-search-bar" style="display:none;">
    <div class="fixed-search-inner">
        <div class="fixed-search-brand">
            <span class="fsc-dot"></span>
            <span class="fsc-label">Patient Search</span>
        </div>
        <div class="fixed-search-field">
            <div class="search-input-group search-input-group-fixed">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="patientSearchFixed" class="search-input"
                       placeholder="Search by name, code, or mobile...">
                <button class="search-btn" type="button" onclick="filterTableFixed()">Search</button>
            </div>
        </div>
        <div class="fixed-search-meta">
            <span class="fsc-count-pill">
                <i class="fas fa-users mr-1"></i>
                <strong id="fsc-count">{{ $patients->total() ?? $patients->count() }}</strong> patients
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     STEP 1 — SELECT PATIENT
══════════════════════════════════════════ --}}
<div id="panel-step1">

    <div class="modern-card patient-list-card" id="patient-list-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-danger-soft"><i class="fas fa-users text-danger"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Select Patient for Post-Surgery</h5>
                    <small class="text-muted">Search and choose a patient to proceed</small>
                </div>
            </div>
            <span class="patient-total-pill patient-total-pill-danger">
                <i class="fas fa-database mr-1"></i>
                {{ $patients->total() ?? $patients->count() }} patients
            </span>
        </div>

        {{-- Inline Search Bar --}}
        <div class="inline-search-bar inline-search-bar-danger" id="inline-search-bar">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline" style="flex:1;">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="patientSearch" class="search-input"
                           placeholder="Search by name, code, or mobile number...">
                    <button class="search-btn search-btn-danger" type="button" onclick="filterTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
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
                            $pid     = $patient->id          ?? '';
                            $pcode   = $patient->patientcode ?? '—';
                            $pname   = $patient->patientname ?? '—';
                            $page    = $patient->age         ?? '—';
                            $pgender = strtolower($patient->gender ?? '');
                            $pmobile = $patient->mobile_no   ?? '—';
                            $paddr   = $patient->address     ?? '';
                            $pupo    = $patient->upozila     ?? null;
                            $pblood  = $patient->blood_group ?? null;
                        @endphp
                        <tr class="patient-row">
                            <td class="text-muted small">{{ $pid }}</td>
                            <td><span class="patient-code-badge patient-code-badge-danger">{{ $pcode }}</span></td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-danger">{{ strtoupper(substr($pname,0,1)) }}</div>
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
                                @if($pgender==='male')   <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($pgender==='female') <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $pmobile }}</td>
                            <td class="text-muted small">{{ $paddr }}{{ $pupo ? ', '.$pupo : '' }}</td>
                            <td>
                                @if($pblood) <span class="blood-badge">{{ $pblood }}</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-select-patient btn-select-danger"
                                    onclick="selectPatient(this)"
                                    data-id="{{ $pid }}"
                                    data-name="{{ $pname }}"
                                    data-age="{{ $page }}"
                                    data-code="{{ $pcode }}"
                                    data-mobile="{{ $pmobile }}"
                                    data-upozila="{{ $pupo }}"
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

            @if(method_exists($patients,'links'))
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
                <i class="fas fa-info-circle mr-1 text-danger"></i>
                Click <i class="fas fa-arrow-right" style="font-size:10px;"></i> on a patient row to proceed.
            </small>
        </div>
    </div>

    {{-- ══ PAST POST-SURGERY PRESCRIPTIONS LIST ══ --}}
    <div class="modern-card past-rx-card mt-2" id="past-rx-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-danger-soft">
                    <i class="fas fa-history text-danger"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Past Post-Surgery Prescriptions</h5>
                    <small class="text-muted">All previously saved post-surgery prescriptions</small>
                </div>
            </div>
            <span class="patient-total-pill patient-total-pill-danger">
                <i class="fas fa-file-medical mr-1"></i>
                {{ $PostSurgeryPatients->total() ?? $PostSurgeryPatients->count() }} records
            </span>
        </div>

        <div class="inline-search-bar inline-search-bar-danger">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline" style="flex:1;">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="nursingRxSearch" class="search-input"
                           placeholder="Search by name, code or mobile..."
                           onkeyup="filterNursingRxTable()">
                    <button class="search-btn search-btn-danger" type="button" onclick="filterNursingRxTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="patient-table-scroll">
                <table class="table modern-table" id="nursingRxTable">
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
                    <tbody id="nursingRxTableBody">
                        @forelse($PostSurgeryPatients as $np)
                        @php
                            $npRxId   = $np->id                ?? '';
                            $npCode   = $np->patient_code      ?? $np->patientcode ?? '—';
                            $npName   = $np->patient_name      ?? '—';
                            $npAge    = $np->patient_age       ?? '—';
                            $npGender = strtolower($np->gender ?? '');
                            $npMobile = $np->mobile_no         ?? '—';
                            $npBlood  = $np->blood_group       ?? null;
                            $npDate   = $np->prescription_date ?? $np->created_at ?? '';
                        @endphp
                        <tr class="nursing-rx-row">
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <span class="patient-code-badge patient-code-badge-danger">#{{ $npRxId }}</span>
                            </td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-danger">
                                        {{ strtoupper(substr($npName,0,1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $npName }}</strong>
                                        <br><small class="text-muted" style="font-size:11px;">{{ $npCode }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $npAge }}</td>
                            <td>
                                @if($npGender==='male')   <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($npGender==='female') <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $npMobile }}</td>
                            <td class="small text-muted">
                                @if($npDate)
                                    {{ \Carbon\Carbon::parse($npDate)->format('d/m/Y') }}
                                    <br><span style="font-size:10px;">{{ \Carbon\Carbon::parse($npDate)->diffForHumans() }}</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($npBlood) <span class="blood-badge">{{ $npBlood }}</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-view-rx"
                                    onclick="viewPrescription({{ $npRxId }})">
                                    <i class="fas fa-eye mr-1"></i> View Rx
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-file-medical-alt"></i>
                                    <p>No past post-surgery prescriptions found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($PostSurgeryPatients,'links'))
            <div class="pagination-bar">
                <small class="text-muted">
                    <i class="fas fa-list-ul mr-1"></i>
                    Showing {{ $PostSurgeryPatients->firstItem() ?? 0 }}–{{ $PostSurgeryPatients->lastItem() ?? 0 }}
                    of <strong>{{ $PostSurgeryPatients->total() ?? 0 }}</strong> records
                </small>
                {{ $PostSurgeryPatients->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

</div>{{-- /#panel-step1 --}}

{{-- ══════════════════════════════════════════
     STEP 2 — FORM + VIEW
══════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none;">

    {{-- Selected Patient Bar --}}
    <div class="patient-selected-bar patient-selected-bar-danger mb-4">
        <div class="psb-left">
            <div class="psb-avatar" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot psb-status-dot-red"></span>
            <span class="psb-status-label">Post-Surgery Patient</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    {{-- Status bars --}}
    <div id="admission-loading" class="admission-status-bar admission-loading-bar" style="display:none;">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Pre-Operation medicines লোড হচ্ছে...
    </div>
    <div id="no-admission-alert" class="admission-status-bar admission-warning-bar" style="display:none;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        কোনো <strong>On Admission</strong> রেকর্ড পাওয়া যায়নি। তবুও Post-Surgery Prescription দেওয়া যাবে।
    </div>

    {{-- Prescription Form --}}
    <div class="modern-card" id="rx-form-section">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-danger-soft"><i class="fas fa-notes-medical text-danger"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Post-Operative Prescription</h5>
                    <small class="text-muted">Fill in the post-surgery clinical information</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">
            <input type="hidden" id="f-patient-id">
            <hr class="section-divider mt-0 mb-4">

            <div class="section-heading mb-3">
                <i class="fas fa-user-injured mr-2 text-danger"></i>
                <span>Patient & Post-Op Information</span>
            </div>

            <div class="row">
                <div class="col-md-6">
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
                        <label class="modern-label">
                            <i class="fas fa-clock mr-1 text-danger" style="font-size:11px;"></i>
                            Post-Op Time
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-procedures input-icon text-danger"></i>
                            <input type="time" class="modern-input with-icon" id="f-postop-time">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Medicines --}}
            <div class="section-divider-full mt-4 mb-4">
                <div class="section-heading mb-0">
                    <i class="fas fa-pills mr-2 text-danger"></i>
                    <span>Medicines / Orders</span>
                    <span class="badge badge-pill ml-2" id="sel-med-count-badge"
                          style="background:#ffebee;color:#c62828;font-size:12px;padding:4px 10px;">0</span>
                </div>
                <div>
                    <button type="button" class="btn-med-action btn-med-add-danger" onclick="addBlankMedRow()">
                        <i class="fas fa-plus mr-1"></i> Add Blank Row
                    </button>
                </div>
            </div>

            <div class="med-table-card selected-med-card-danger mb-4">
                <div class="med-table-card-header" style="background:#fff5f5;border-bottom-color:#ffcdd2;">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#e53935;"></span>
                        <span class="med-table-title">Selected Medicines</span>
                        <span class="med-count-pill med-count-pill-danger" id="sel-med-badge">0</span>
                        <small id="adm-loaded-note" class="text-success ml-3 d-none" style="font-size:11px;font-weight:600;">
                            <i class="fas fa-check-circle mr-1"></i>Template থেকে লোড হয়েছে
                        </small>
                    </div>
                    <button type="button" class="btn-med-clear-danger" onclick="clearAllMeds()">
                        <i class="fas fa-trash-alt mr-1"></i> Clear All
                    </button>
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
                            <tr class="empty-row" id="empty-med-row">
                                <td colspan="9">
                                    <div class="med-empty-state">
                                        <i class="fas fa-syringe" style="color:#e53935;"></i>
                                        <span>Patient select করলে Pre-Operation medicines auto-load হবে।</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="med-table-card mb-0">
                <div class="med-table-card-header">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#43a047;"></span>
                        <span class="med-table-title">আরো Medicine যোগ করুন</span>
                        <span class="med-count-pill" style="background:#e8f5e9;color:#2e7d32;">{{ $medicines->count() }}</span>
                    </div>
                    <div class="avail-filter-wrap">
                        <i class="fas fa-filter avail-filter-icon"></i>
                        <input type="text" id="med-filter-input" class="avail-filter-input"
                            placeholder="Filter medicines..." oninput="filterCheckboxList(this.value)">
                    </div>
                </div>
                <div style="max-height:220px;overflow-y:auto;">
                    <table class="table med-table mb-0">
                        <thead>
                            <tr>
                                <th width="35"></th>
                                <th>Medicine</th>
                                <th>Dose</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="avail-med-tbody">
                            @forelse($medicines as $med)
                            <tr class="avail-med-row" data-name="{{ strtolower($med->name ?? $med->medicine_name ?? '') }}">
                                <td>
                                    <input type="checkbox" class="avail-med-cb modern-checkbox"
                                        data-name="{{ $med->name ?? $med->medicine_name ?? '' }}"
                                        data-dose="{{ $med->dose ?? '' }}"
                                        data-route="{{ $med->route ?? '' }}"
                                        data-frequency="{{ $med->frequency ?? '' }}"
                                        data-duration="{{ $med->duration ?? '' }}"
                                        data-timing="{{ $med->timing ?? '' }}"
                                        data-note="{{ $med->note ?? '' }}"
                                        onchange="onAvailMedChange(this)">
                                </td>
                                <td><span class="avail-med-name">{{ $med->name ?? $med->medicine_name ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->dose ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->frequency ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->duration ?? '—' }}</span></td>
                                <td>
                                    <button type="button" class="btn-quick-add btn-quick-add-green"
                                            onclick="quickAddMed(this)" title="Quick Add">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No medicines found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-footer mt-4">
                <button type="button" class="btn btn-footer-back" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" class="btn btn-footer-save-danger" id="btn-save" onclick="saveAndGenerateRx()">
                    <i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription
                </button>
            </div>
        </div>
    </div>{{-- /#rx-form-section --}}

    {{-- ══ PRESCRIPTION PRINT VIEW ══ --}}
    <div id="rx-view" style="display:none;">
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-red">
                    <div class="rx-summary-icon"><i class="fas fa-user"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Patient</div>
                        <div class="rx-summary-value" id="ib-name">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-green">
                    <div class="rx-summary-icon"><i class="fas fa-birthday-cake"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Age</div>
                        <div class="rx-summary-value" id="ib-age">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-orange">
                    <div class="rx-summary-icon"><i class="fas fa-clock"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Post-Op Time</div>
                        <div class="rx-summary-value" id="ib-postop">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="rx-summary-card rx-card-teal">
                    <div class="rx-summary-icon"><i class="fas fa-database"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Saved ID</div>
                        <div class="rx-summary-value" id="ib-saved-id">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <div class="modern-card-title">
                    <span class="card-title-icon bg-danger-soft"><i class="fas fa-notes-medical text-danger"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Post-Operative Prescription</h5>
                        <small class="text-muted">Ready to print</small>
                    </div>
                </div>
                <span class="rx-saved-badge">
                    <i class="fas fa-check-circle mr-1"></i> Saved to Database
                </span>
            </div>
            <div class="modern-card-body p-0">
                <div id="prescription-print-area">
                    <div class="rx-wrapper">
                        <div class="rx-header">
                            <div class="rx-logo-wrap">
                                <div class="rx-logo">CP</div>
                                <div class="rx-clinic-sub">Professor Clinic</div>
                            </div>
                            <div class="rx-clinic-info">
                                <div class="rx-clinic-name">প্রফেসর ক্লিনিক</div>
                                <div class="rx-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                <div class="rx-phones">☎ 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
                            </div>
                        </div>
                        <div class="rx-patient-row">
                            <div class="rx-field"><label>নাম ঃ</label><div class="rx-value" id="rx-name">—</div></div>
                            <div class="rx-field"><label>বয়স ঃ</label><div class="rx-value" id="rx-age">—</div></div>
                            <div class="rx-field"><label>তারিখ ঃ</label><div class="rx-value" id="rx-date">—</div></div>
                        </div>
                        <div style="margin:10px 0 3px;font-size:13px;">
                            <span class="rx-symbol">Rx</span>
                        </div>
                        <div class="rx-section-center">Post-Operative Order On</div>
                        <div class="rx-time-right" id="rx-postop-time-display"></div>
                        <ul class="rx-list" id="rx-medicine-list" style="margin-top:6px;">
                            <li data-npo="1">NPO-TFO</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modern-card-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Generated: <span id="gen-time">—</span>
                </small>
                <div style="display:flex;gap:8px;">
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
    </div>{{-- /#rx-view --}}

</div>{{-- /#panel-step2 --}}

{{-- ══ PRESCRIPTION VIEW MODAL (Same as On Admission) ══ --}}
<div class="modal fade" id="rxViewModal" tabindex="-1" role="dialog" aria-labelledby="rxViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">

            <div class="modal-header rx-modal-header">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3">
                        <i class="fas fa-procedures"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white" id="rxViewModalLabel">
                            Post-Surgery Prescription
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
                    <div class="modal-spinner-icon"><i class="fas fa-spinner fa-spin"></i></div>
                    <p class="modal-state-text">Loading prescription...</p>
                </div>

                <div id="modal-error" class="modal-state-wrap d-none">
                    <div class="modal-error-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="modal-state-text" id="modal-error-msg">Failed to load prescription.</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" data-dismiss="modal">Close</button>
                </div>

                <div id="modal-rx-area" class="d-none">
                    <div class="modal-summary-bar">
                        <div class="modal-summary-item msi-red">
                            <i class="fas fa-user"></i>
                            <div>
                                <div class="msi-label">Patient</div>
                                <div class="msi-val" id="m-ib-name">—</div>
                            </div>
                        </div>
                        <div class="modal-summary-item msi-green">
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
                                <div class="msi-val" id="m-ib-admission">—</div>
                            </div>
                        </div>
                        <div class="modal-summary-item msi-teal">
                            <i class="fas fa-hashtag"></i>
                            <div>
                                <div class="msi-label">Rx ID</div>
                                <div class="msi-val" id="m-ib-id">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- ★ MODAL PRINT AREA — same rx-wrapper layout as main print ★ --}}
                    <div id="modal-prescription-print-area" style="padding:20px 24px;">
                        <div class="rx-wrapper">
                            <div class="rx-header">
                                <div class="rx-logo-wrap">
                                    <div class="rx-logo">CP</div>
                                    <div class="rx-clinic-sub">Professor Clinic</div>
                                </div>
                                <div class="rx-clinic-info">
                                    <div class="rx-clinic-name">প্রফেসর ক্লিনিক</div>
                                    <div class="rx-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                    <div class="rx-phones">☎ 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
                                </div>
                            </div>
                            <div class="rx-patient-row">
                                <div class="rx-field"><label>নাম ঃ</label><div class="rx-value" id="m-rx-name">—</div></div>
                                <div class="rx-field"><label>বয়স ঃ</label><div class="rx-value" id="m-rx-age">—</div></div>
                                <div class="rx-field"><label>তারিখ ঃ</label><div class="rx-value" id="m-rx-date">—</div></div>
                            </div>
                            <div style="margin:10px 0 3px;font-size:13px;">
                                <span class="rx-symbol">Rx</span>
                            </div>
                            <div class="rx-section-center">Post-Operative Order On</div>
                            <div class="rx-time-right" id="m-rx-postop-time-display"></div>
                            <ul class="rx-list" id="m-rx-medicine-list" style="margin-top:6px;">
                                <li data-static="1">NPO-TFO</li>
                            </ul>
                            <div class="rx-notes" id="m-rx-notes-wrap" style="display:none;">
                                <p><span id="m-rx-notes">—</span></p>
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
    --red-deep:   #B71C1C; --red-mid: #E53935; --red-light: #FFEBEE; --red-soft: #FFCDD2;
    --green-deep: #2E7D32; --green-mid: #43A047; --green-light: #E8F5E9;
    --teal-mid:   #00797B; --teal-light: #E0F2F1;
    --orange:     #E65100; --blue-deep: #1565C0; --blue-mid: #1976D2;
    --text-primary: #1a2332; --text-muted: #6b7a90; --border: #e4e9f0;
    --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px;
    --shadow-sm: 0 1px 4px rgba(0,0,0,.06); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --font-base: 'DM Sans','Hind Siliguri',Arial,sans-serif;
}
body,.content-wrapper { background:#f4f0f0 !important; font-family:var(--font-base); }

/* ═══════════════════════ PAGE HEADER ═══════════════════════ */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--red-light);display:inline-flex;align-items:center;justify-content:center;color:var(--red-mid);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--red-light);border-color:var(--red-mid);color:var(--red-deep); }

/* ═══════════════════════ STEP TRACK ═══════════════════════ */
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:16px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s;border:2.5px solid transparent; }
.step-active   { background:var(--red-mid);color:#fff;border-color:var(--red-mid);box-shadow:0 0 0 4px rgba(229,57,53,.15); }
.step-done     { background:var(--green-deep);color:#fff;border-color:var(--green-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub  { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--red-mid); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--green-deep); }

/* ═══════════════════════ FIXED SEARCH BAR ═══════════════════════ */
.fixed-search-bar { position:fixed;top:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#B71C1C 0%,#E53935 100%);box-shadow:0 4px 24px rgba(183,28,28,.35);transform:translateY(-100%);transition:transform .3s cubic-bezier(.4,0,.2,1),opacity .3s;opacity:0;pointer-events:none; }
.fixed-search-bar.visible { transform:translateY(0);opacity:1;pointer-events:all; }
.fixed-search-inner { display:flex;align-items:center;gap:16px;padding:10px 20px;flex-wrap:wrap; }
.fixed-search-brand { display:flex;align-items:center;gap:8px;flex-shrink:0; }
.fsc-dot { width:8px;height:8px;border-radius:50%;background:#ffcc80;box-shadow:0 0 0 3px rgba(255,204,128,.3); }
.fsc-label { color:rgba(255,255,255,.9);font-size:13px;font-weight:700;white-space:nowrap; }
.fixed-search-field { flex:1;min-width:240px; }
.fixed-search-meta { flex-shrink:0; }
.fsc-count-pill { background:rgba(255,255,255,.18);color:#fff;border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600;white-space:nowrap; }

/* ═══════════════════════ ALERT / STATUS ═══════════════════════ */
.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }
.admission-status-bar { border-radius:var(--radius-md);padding:12px 18px;margin-bottom:16px;font-size:13.5px;font-weight:500;display:flex;align-items:center;border:1.5px solid transparent; }
.admission-loading-bar { background:#E3F2FD;color:var(--blue-deep);border-color:#90caf9; }
.admission-warning-bar { background:#fff8e1;color:#e65100;border-color:#ffe082; }

/* ═══════════════════════ MODERN CARD ═══════════════════════ */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-danger-soft { background:var(--red-light); }
.text-danger    { color:var(--red-mid) !important; }
.modern-card-body   { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }
.past-rx-card { border-top:3px solid var(--red-mid); }

/* ═══════════════════════ INLINE SEARCH BAR ═══════════════════════ */
.inline-search-bar { padding:14px 24px;background:#fafbff;border-bottom:2px solid var(--border); }
.inline-search-bar-danger { background:#fff8f8;border-bottom-color:var(--red-soft); }
.inline-search-inner { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.search-input-group { display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s;box-shadow:var(--shadow-sm); }
.search-input-group:focus-within { border-color:var(--red-mid);box-shadow:0 0 0 3px rgba(229,57,53,.1); }
.search-input-group-fixed { border:2px solid rgba(255,255,255,.35);background:rgba(255,255,255,.12); }
.search-input-group-fixed:focus-within { border-color:rgba(255,255,255,.7); }
.search-input-group-fixed .search-icon { color:rgba(255,255,255,.7); }
.search-input-group-fixed .search-input { background:transparent;color:#fff; }
.search-input-group-fixed .search-input::placeholder { color:rgba(255,255,255,.55); }
.search-input-group-fixed .search-btn { background:rgba(255,255,255,.22);color:#fff; }
.search-input-group-fixed .search-btn:hover { background:rgba(255,255,255,.35); }
.search-input-group-inline { flex:1;min-width:260px; }
.search-icon { padding:0 12px;color:#aab;font-size:15px; }
.search-input { flex:1;border:none;outline:none;padding:10px 6px;font-size:14px;background:transparent;color:var(--text-primary); }
.search-btn { border:none;padding:10px 22px;font-size:13.5px;font-weight:600;cursor:pointer;transition:background .2s; }
.search-btn-danger { background:var(--red-mid);color:#fff; }
.search-btn-danger:hover { background:var(--red-deep); }

/* ═══════════════════════ PATIENT TABLE ═══════════════════════ */
.patient-table-scroll { overflow-x:auto;overflow-y:auto;max-height:calc(100vh - 340px); }
.modern-table { border-collapse:separate;border-spacing:0;width:100%; }
.modern-table thead tr th { background:#fdf5f5;color:var(--text-primary);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding:11px 14px;border-bottom:2px solid var(--red-soft);white-space:nowrap;position:sticky;top:0;z-index:10; }
.modern-table tbody tr { transition:background .15s; }
.modern-table tbody tr:hover { background:#fff5f5; }
.modern-table tbody td { padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text-primary);vertical-align:middle; }
.patient-total-pill { border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600; }
.patient-total-pill-danger { background:var(--red-light);color:var(--red-deep); }
.patient-code-badge { border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700;font-family:monospace; }
.patient-code-badge-danger { background:#fdecea;color:var(--red-deep); }
.patient-name-cell { display:flex;align-items:center;gap:8px; }
.patient-mini-avatar { width:30px;height:30px;border-radius:50%;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.patient-mini-avatar-danger { background:linear-gradient(135deg,var(--red-deep),#ef5350); }
.gender-badge { display:inline-flex;align-items:center;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.gender-male   { background:#e3f2fd;color:var(--blue-deep); }
.gender-female { background:#fce4ec;color:#880e4f; }
.blood-badge { background:#ffebee;color:#c62828;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.btn-select-patient { border:none;border-radius:var(--radius-sm);width:34px;height:34px;font-size:13px;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;justify-content:center; }
.btn-select-danger { background:var(--red-mid);color:#fff;box-shadow:0 2px 6px rgba(229,57,53,.22); }
.btn-select-danger:hover { background:var(--red-deep);transform:translateY(-1px);box-shadow:0 4px 12px rgba(229,57,53,.28); }
.btn-view-rx { background:linear-gradient(135deg,var(--red-mid),#ef5350);color:#fff;border:none;border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;box-shadow:0 2px 6px rgba(229,57,53,.22); }
.btn-view-rx:hover { background:linear-gradient(135deg,var(--red-deep),var(--red-mid));transform:translateY(-1px);box-shadow:0 4px 12px rgba(229,57,53,.32); }
.empty-state { text-align:center;padding:40px;color:#b0bec5; }
.empty-state i { font-size:36px;margin-bottom:10px;display:block; }
.empty-state p { font-size:14px;margin:0; }
.pagination-bar { display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-top:1.5px solid var(--border);flex-wrap:wrap;gap:8px; }
.pagination { margin-bottom:0; }
.page-link { border-radius:var(--radius-sm) !important;border-color:var(--border);color:var(--red-mid);font-size:13px; }
.page-item.active .page-link { background:var(--red-mid);border-color:var(--red-mid); }

/* ═══════════════════════ PATIENT SELECTED BAR ═══════════════════════ */
.patient-selected-bar { border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px; }
.patient-selected-bar-danger { background:linear-gradient(135deg,#B71C1C 0%,#E53935 100%);box-shadow:0 4px 18px rgba(183,28,28,.18); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.psb-status-dot-red { background:#ffcc80;box-shadow:0 0 0 3px rgba(255,204,128,.3); }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28);color:#fff; }

/* ═══════════════════════ FORM FIELDS ═══════════════════════ */
.section-heading { display:flex;align-items:center;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px; }
.section-divider { border:none;border-top:1.5px solid var(--border); }
.section-divider-full { border-top:1.5px solid var(--border);padding-top:18px;display:flex;align-items:center;justify-content:space-between; }
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--red-mid);box-shadow:0 0 0 3px rgba(229,57,53,.1); }
.input-with-icon { position:relative; }
.input-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none; }
.modern-input.with-icon { padding-left:30px; }

/* ═══════════════════════ MEDICINE TABLES ═══════════════════════ */
.btn-med-action { border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center; }
.btn-med-add-danger { background:var(--red-light);color:var(--red-deep);border-color:var(--red-soft); }
.btn-med-add-danger:hover { background:var(--red-mid);color:#fff; }
.btn-med-clear-danger { background:transparent;border:none;color:#c62828;font-size:12px;font-weight:600;cursor:pointer;padding:4px 10px;border-radius:var(--radius-sm);transition:all .18s;display:inline-flex;align-items:center;gap:4px; }
.btn-med-clear-danger:hover { background:var(--red-light); }
.med-table-card { border-radius:var(--radius-md);border:1.5px solid var(--border);overflow:hidden;box-shadow:var(--shadow-sm); }
.selected-med-card-danger { border-color:var(--red-soft); }
.med-table-card-header { padding:11px 16px;background:#f9fafb;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.med-table-dot { width:8px;height:8px;border-radius:50%;margin-right:8px;flex-shrink:0; }
.med-table-title { font-size:13px;font-weight:700;color:var(--text-primary); }
.med-count-pill { border-radius:20px;padding:2px 10px;font-size:11.5px;font-weight:700;margin-left:8px; }
.med-count-pill-danger { background:var(--red-light);color:var(--red-deep); }
.med-table { border-collapse:collapse;width:100%; }
.med-table thead tr th { background:#f5f7fa;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);padding:9px 12px;border-bottom:1.5px solid var(--border);white-space:nowrap; }
.med-table tbody td { padding:8px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.med-table tbody tr:last-child td { border-bottom:none; }
.med-table tbody tr:hover { background:#fdf5f5; }
.med-table .form-control { padding:4px 8px !important;font-size:12.5px !important; }
.med-empty-state { text-align:center;color:#b0bec5;padding:22px;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px; }
.avail-med-name { font-weight:600;color:var(--text-primary);font-size:13px; }
.avail-filter-wrap { display:flex;align-items:center;background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .2s; }
.avail-filter-wrap:focus-within { border-color:var(--red-mid); }
.avail-filter-icon { padding:0 9px;color:#aab;font-size:12px; }
.avail-filter-input { border:none;outline:none;padding:6px 4px;font-size:13px;background:transparent;width:170px; }
.modern-checkbox { width:15px;height:15px;accent-color:var(--red-mid);cursor:pointer; }
.btn-quick-add { width:26px;height:26px;border-radius:6px;border:1.5px solid transparent;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .18s; }
.btn-quick-add-green { background:var(--green-light);color:var(--green-deep);border-color:#a5d6a7; }
.btn-quick-add-green:hover { background:var(--green-deep);color:#fff; }

/* ═══════════════════════ FORM FOOTER ═══════════════════════ */
.form-footer { display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1.5px solid var(--border); }
.btn-footer-back { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:600;transition:all .2s; }
.btn-footer-back:hover { background:#f0f4f8;color:var(--text-primary); }
.btn-footer-save-danger { background:linear-gradient(135deg,#B71C1C,#E53935);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(183,28,28,.28);transition:all .2s;display:inline-flex;align-items:center;gap:7px; }
.btn-footer-save-danger:hover { background:linear-gradient(135deg,#7f0000,#B71C1C);transform:translateY(-1px);color:#fff; }

/* ═══════════════════════ RX SUMMARY CARDS ═══════════════════════ */
.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-red    { background:linear-gradient(135deg,#B71C1C,#E53935); }
.rx-card-green  { background:linear-gradient(135deg,#2E7D32,#43A047); }
.rx-card-orange { background:linear-gradient(135deg,#E65100,#F57C00); }
.rx-card-teal   { background:linear-gradient(135deg,#00695C,#00897B); }
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

/* ═══════════════════════ RX MODAL (same as On Admission) ═══════════════════════ */
.rx-modal-content { border:none;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.rx-modal-header { background:linear-gradient(135deg,#B71C1C 0%,#E53935 100%);border:none;padding:18px 22px;display:flex;align-items:center;justify-content:space-between; }
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
.msi-red    { background:linear-gradient(135deg,#ffebee,#fff); }
.msi-green  { background:linear-gradient(135deg,#e8f5e9,#fff); }
.msi-orange { background:linear-gradient(135deg,#fff3e0,#fff); }
.msi-teal   { background:linear-gradient(135deg,#e0f2f1,#fff); }
.modal-summary-item > i { font-size:18px;flex-shrink:0; }
.msi-red    > i { color:var(--red-mid); }
.msi-green  > i { color:var(--green-deep); }
.msi-orange > i { color:var(--orange); }
.msi-teal   > i { color:var(--teal-mid); }
.msi-label { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted); }
.msi-val   { font-size:13px;font-weight:700;color:var(--text-primary);margin-top:1px; }
.modal-state-wrap { text-align:center;padding:50px 20px;color:#90A4AE; }
.modal-spinner-icon { font-size:34px;margin-bottom:12px;color:var(--red-mid); }
.modal-error-icon   { font-size:36px;margin-bottom:10px;color:#ef5350; }
.modal-state-text   { font-size:14px;margin:0; }

/* ═══════════════════════ RX PRINT LAYOUT ═══════════════════════ */
#prescription-print-area { padding:24px;background:#fff; }
.rx-wrapper { width:100%;max-width:800px;margin:0 auto;background:#fff;border:1px solid #d4d4d4;padding:26px 32px;font-family:'Hind Siliguri',Arial,sans-serif; }
.rx-header { display:flex;align-items:center;justify-content:center;gap:18px;border-bottom:2.5px solid #1a237e;padding-bottom:12px;margin-bottom:6px; }
.rx-logo { width:62px;height:62px;border:3px solid #1a237e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:#1a237e;font-style:italic;font-family:Georgia,serif; }
.rx-clinic-sub { font-size:8.5px;color:#1a237e;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-top:3px;text-align:center; }
.rx-clinic-info { text-align:center; }
.rx-clinic-name { font-size:36px;font-weight:700;color:#1a237e;letter-spacing:1px; }
.rx-address { font-size:13px;font-weight:600;color:#1a237e;margin-top:3px; }
.rx-phones  { font-size:11px;color:#1a237e;margin-top:2px; }
.rx-patient-row { display:flex;justify-content:space-between;align-items:flex-end;border-bottom:1px solid #444;padding:8px 0 5px;margin-top:8px; }
.rx-field { display:flex;align-items:center;gap:5px;font-size:13px; }
.rx-field label { font-weight:700;white-space:nowrap;font-size:12px;margin-bottom:0; }
.rx-value { border-bottom:1px dotted #555;min-width:120px;padding:0 6px 1px;font-size:13px; }
.rx-symbol { font-size:22px;font-weight:700;margin-right:5px; }
.rx-section-center { text-align:center;font-weight:700;font-size:13px;text-decoration:underline;margin:5px 0 3px; }
.rx-time-right { text-align:right;font-size:13px;font-style:italic;margin-bottom:8px; }
.rx-list { list-style:none;padding:0;margin:0; }
.rx-list li { font-size:12.5px;line-height:1.8; }
.rx-list li::before { content:"• "; }
.rx-notes { margin-top:10px;font-size:12px;color:#222; }

/* ═══════════════════════ PRINT OVERLAY ═══════════════════════ */
#print-overlay { display:none;position:fixed;top:0;left:0;width:100%;min-height:100%;background:#fff;z-index:9999999;padding:10mm 12mm;box-sizing:border-box; }
@media print {
    body * { visibility:hidden; }
    #print-overlay, #print-overlay * { visibility:visible !important; }
    #print-overlay { display:block !important;position:fixed !important;top:0 !important;left:0 !important;width:100% !important;background:#fff !important;padding:10mm 12mm !important;box-sizing:border-box !important; }
    #print-overlay .rx-wrapper { border:1px solid #d4d4d4 !important;max-width:100% !important;padding:20px 26px !important;margin:0 !important;box-shadow:none !important; }
    #print-overlay .modern-card-header, #print-overlay .modern-card-footer,
    #print-overlay .modal-summary-bar, #print-overlay .modal-summary-item { display:none !important;visibility:hidden !important; }
    @page { size:A4 portrait;margin:0; }
}
</style>
@stop

@section('js')
<script>
var CSRF_TOKEN = '{{ csrf_token() }}';

var POST_STORE_URLS = [
    '{{ url("/nursing/PostSurgery/store") }}',
    '{{ url("/nursing/postsurgery/store") }}',
];

/* ══════════════════════════════════════════
   FIXED SEARCH BAR (same as On Admission)
══════════════════════════════════════════ */
(function initFixedBar(){
    var bar        = document.getElementById('fixed-search-bar');
    var inlineBar  = document.getElementById('inline-search-bar');
    var fixedInput = document.getElementById('patientSearchFixed');
    var inlineInput= document.getElementById('patientSearch');
    if(!bar||!inlineBar) return;
    bar.style.display = '';

    function getSidebarWidth(){
        var sb=document.querySelector('.main-sidebar');
        if(!sb) return 0;
        var r=sb.getBoundingClientRect();
        return r.width>10?r.right:0;
    }
    function updateBarPosition(){
        bar.style.left  = getSidebarWidth()+'px';
        bar.style.right = '0';
        bar.style.width = 'auto';
    }
    function onScroll(){
        if(document.getElementById('panel-step1').style.display==='none'){ bar.classList.remove('visible'); return; }
        var rect=inlineBar.getBoundingClientRect();
        if(rect.bottom<=0){ updateBarPosition(); bar.classList.add('visible'); }
        else { bar.classList.remove('visible'); }
    }
    if(fixedInput&&inlineInput){
        fixedInput.addEventListener('input',function(){ inlineInput.value=this.value; filterTable(); });
        inlineInput.addEventListener('input',function(){ fixedInput.value=this.value; });
    }
    window.addEventListener('scroll', onScroll, {passive:true});
    window.addEventListener('resize', function(){ updateBarPosition(); onScroll(); });
    document.addEventListener('DOMContentLoaded', function(){ updateBarPosition(); onScroll(); });
    document.querySelectorAll('[data-widget="pushmenu"]').forEach(function(btn){
        btn.addEventListener('click',function(){ setTimeout(updateBarPosition,320); });
    });
})();

/* ══════════════════════════════════════════
   HELPERS
══════════════════════════════════════════ */
function todayISO(){ return new Date().toISOString().split('T')[0]; }
function fmtDateBD(iso){
    if(!iso) return '—';
    var p=String(iso).split('T')[0].split('-');
    if(p.length<3) return iso;
    return p[2]+'/'+p[1]+'/'+p[0].slice(2);
}
function fmtTime(t){
    if(!t) return '—';
    var p=String(t).split(':');
    var hr=parseInt(p[0]);
    if(isNaN(hr)) return t;
    return (hr%12||12)+':'+p[1]+(hr>=12?' pm':' am');
}
function gVal(id){ var e=document.getElementById(id); return e?e.value.trim():''; }
function setText(id,v){ var e=document.getElementById(id); if(e) e.textContent=(v||'—'); }
function showEl(id){ var e=document.getElementById(id); if(e) e.style.display=''; }
function hideEl(id){ var e=document.getElementById(id); if(e) e.style.display='none'; }
function esc(v){ return String(v||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

function showAlert(type,msg){
    var el=document.getElementById('save-alert');
    el.className='alert alert-'+type+' modern-alert';
    el.innerHTML=msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){ el.classList.add('d-none'); },6000);
}

/* ══════════════════════════════════════════
   PRINT (same overlay technique as On Admission)
══════════════════════════════════════════ */
function _doPrint(sourceId){
    var source  = document.getElementById(sourceId);
    var overlay = document.getElementById('print-overlay');
    if(!source||!overlay){ window.print(); return; }
    var rxWrapper = source.querySelector('.rx-wrapper');
    var toClone   = rxWrapper || source;
    overlay.innerHTML = '';
    overlay.appendChild(toClone.cloneNode(true));
    overlay.style.display = 'block';
    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            window.print();
            var cleanup=function(){
                overlay.style.display='none';
                overlay.innerHTML='';
                window.removeEventListener('focus',cleanup);
            };
            window.addEventListener('focus',cleanup);
            setTimeout(function(){
                overlay.style.display='none';
                overlay.innerHTML='';
                window.removeEventListener('focus',cleanup);
            },60000);
        });
    });
}
function printRx()    { _doPrint('prescription-print-area'); }
function printModal() { _doPrint('modal-prescription-print-area'); }

/* ══════════════════════════════════════════
   MEDICINE STATE
══════════════════════════════════════════ */
var selectedMeds = [];

function refreshSelTable(){
    var tbody  = document.getElementById('sel-med-tbody');
    var badge1 = document.getElementById('sel-med-badge');
    var badge2 = document.getElementById('sel-med-count-badge');
    badge1.textContent = selectedMeds.length;
    badge2.textContent = selectedMeds.length;

    if(!selectedMeds.length){
        tbody.innerHTML='<tr id="empty-med-row"><td colspan="9">' +
            '<div class="med-empty-state"><i class="fas fa-syringe" style="color:#e53935;"></i>' +
            '<span>No medicines selected yet.</span></div></td></tr>';
        return;
    }
    tbody.innerHTML = selectedMeds.map(function(m,i){
        return '<tr>'+
            '<td>'+(i+1)+'</td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.medicine_name)+'" onchange="selectedMeds['+i+'].medicine_name=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.dose)+'" onchange="selectedMeds['+i+'].dose=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.route)+'" onchange="selectedMeds['+i+'].route=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.frequency)+'" onchange="selectedMeds['+i+'].frequency=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.duration)+'" onchange="selectedMeds['+i+'].duration=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.timing)+'" onchange="selectedMeds['+i+'].timing=this.value"></td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.remarks)+'" onchange="selectedMeds['+i+'].remarks=this.value" placeholder="Optional"></td>'+
            '<td class="text-center"><button type="button" class="btn-quick-add" style="background:#ffebee;color:#c62828;border-color:#ffcdd2;" onclick="removeMed('+i+')"><i class="fas fa-times"></i></button></td>'+
        '</tr>';
    }).join('');
}

function addMedToList(name,dose,route,frequency,duration,timing,note){
    if(!name||!name.trim()) return;
    if(selectedMeds.find(function(m){ return m.medicine_name.toLowerCase()===name.toLowerCase(); })) return;
    selectedMeds.push({ medicine_name:name, dose:dose||'', route:route||'', frequency:frequency||'', duration:duration||'', timing:timing||'', remarks:note||'' });
    refreshSelTable();
}
function addBlankMedRow(){
    selectedMeds.push({ medicine_name:'', dose:'', route:'', frequency:'', duration:'', timing:'', remarks:'' });
    refreshSelTable();
}
function removeMed(idx){
    var name=selectedMeds[idx]?selectedMeds[idx].medicine_name:'';
    selectedMeds.splice(idx,1);
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){ if((cb.dataset.name||'')===name) cb.checked=false; });
    refreshSelTable();
}
function clearAllMeds(){
    if(!selectedMeds.length) return;
    if(!confirm('সব medicine মুছে ফেলবেন?')) return;
    selectedMeds=[];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){ cb.checked=false; });
    document.getElementById('adm-loaded-note').classList.add('d-none');
    refreshSelTable();
}
function onAvailMedChange(cb){
    if(cb.checked){ addMedToList(cb.dataset.name,cb.dataset.dose,cb.dataset.route,cb.dataset.frequency,cb.dataset.duration,cb.dataset.timing,cb.dataset.note); }
    else { selectedMeds=selectedMeds.filter(function(m){ return m.medicine_name!==cb.dataset.name; }); refreshSelTable(); }
}
function quickAddMed(btn){
    var cb=btn.closest('tr').querySelector('.avail-med-cb');
    cb.checked=true; onAvailMedChange(cb);
}
function filterCheckboxList(q){
    var lower=q.toLowerCase().trim();
    document.querySelectorAll('.avail-med-row').forEach(function(r){
        r.style.display=(!lower||(r.dataset.name||'').includes(lower))?'':'none';
    });
}

/* ══════════════════════════════════════════
   AUTO-LOAD PRE-OP MEDICINES
══════════════════════════════════════════ */
function fetchAndLoadPreOpMeds(){
    showEl('admission-loading');
    hideEl('no-admission-alert');
    document.getElementById('adm-loaded-note').classList.add('d-none');

    fetch('/nursing/postsurgery/pre-operation-medicines', {
        method:'GET',
        headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN }
    })
    .then(function(res){
        hideEl('admission-loading');
        if(!res.ok){ showAlert('warning','<i class="fas fa-exclamation-circle mr-1"></i> Pre-Operation medicines লোড হয়নি। নিচ থেকে যোগ করুন।'); return null; }
        return res.json();
    })
    .then(function(data){
        if(!data) return;
        if(!data.success||!data.rows||!data.rows.length){
            showAlert('warning','<i class="fas fa-exclamation-circle mr-1"></i> কোনো Pre-Operation medicine template পাওয়া যায়নি। নিচ থেকে যোগ করুন।');
            return;
        }
        selectedMeds=[];
        data.rows.forEach(function(m){
            var mName=(m.name||m.medicine_name||'').trim();
            if(!mName) return;
            selectedMeds.push({ medicine_name:mName, dose:m.dose||m.dosage||'', route:m.route||'', frequency:m.frequency||'', duration:m.duration||'', timing:m.timing||'', remarks:m.remarks||m.note||'' });
        });
        refreshSelTable();
        document.getElementById('adm-loaded-note').classList.remove('d-none');
        showAlert('info','<i class="fas fa-info-circle mr-1"></i> Template থেকে <strong>'+data.rows.length+'টি Pre-Operation medicine</strong> auto-load হয়েছে।');
    })
    .catch(function(err){
        hideEl('admission-loading');
        console.warn('Pre-Op fetch error:', err);
    });
}

/* ══════════════════════════════════════════
   STEP NAVIGATION
══════════════════════════════════════════ */
function selectPatient(btn){
    var d=btn.dataset;
    document.getElementById('f-patient-id').value   = d.id   ||'';
    document.getElementById('f-patient-name').value = d.name ||'';
    document.getElementById('f-patient-age').value  = d.age  ||'';
    document.getElementById('f-date').value          = todayISO();
    document.getElementById('f-postop-time').value  = '';

    document.getElementById('spb-avatar').textContent = (d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent   = d.name||'—';
    document.getElementById('spb-meta').textContent   = [d.code,d.age,d.mobile,d.blood,d.upozila].filter(Boolean).join(' · ');

    document.getElementById('step1-circle').className   = 'step-circle step-done';
    document.getElementById('step1-circle').innerHTML   = '<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className   = 'step-circle step-active';
    document.getElementById('step2-label').className    = 'step-label-main step-label-active';
    document.getElementById('step2-sublabel').className = 'step-label-sub';
    document.getElementById('breadcrumb-current').textContent = 'Post-Op Prescription';

    document.getElementById('panel-step1').style.display    = 'none';
    document.getElementById('panel-step2').style.display    = 'block';
    document.getElementById('rx-view').style.display        = 'none';
    document.getElementById('rx-form-section').style.display= 'block';
    document.getElementById('fixed-search-bar').classList.remove('visible');

    selectedMeds=[];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){ cb.checked=false; });
    refreshSelTable();
    fetchAndLoadPreOpMeds();
    window.scrollTo({top:0,behavior:'smooth'});
}

function backToStep1(){
    document.getElementById('step1-circle').className   = 'step-circle step-active';
    document.getElementById('step1-circle').textContent = '1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className   = 'step-circle step-inactive';
    document.getElementById('step2-label').className    = 'step-label-main step-label-inactive';
    document.getElementById('step2-sublabel').className = 'step-label-sub step-label-inactive';
    document.getElementById('breadcrumb-current').textContent = 'Select Patient';
    document.getElementById('panel-step1').style.display = 'block';
    document.getElementById('panel-step2').style.display = 'none';
    window.scrollTo({top:0,behavior:'smooth'});
}
function editRx(){
    document.getElementById('rx-view').style.display         = 'none';
    document.getElementById('rx-form-section').style.display = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ══════════════════════════════════════════
   TABLE FILTERS
══════════════════════════════════════════ */
function filterTable(){
    var q=document.getElementById('patientSearch').value.toLowerCase();
    document.getElementById('patientSearchFixed').value=q;
    _doFilter(q);
}
function filterTableFixed(){
    var q=document.getElementById('patientSearchFixed').value.toLowerCase();
    document.getElementById('patientSearch').value=q;
    _doFilter(q);
}
function _doFilter(q){
    document.querySelectorAll('#patientTable tbody tr').forEach(function(row){
        row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';
    });
}
function filterNursingRxTable(){
    var q=(document.getElementById('nursingRxSearch').value||'').toLowerCase();
    document.querySelectorAll('#nursingRxTable tbody tr.nursing-rx-row').forEach(function(row){
        row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';
    });
}

/* ══════════════════════════════════════════
   SAVE & GENERATE
══════════════════════════════════════════ */
function saveAndGenerateRx(){
    var patientId=gVal('f-patient-id');
    if(!patientId){ showAlert('warning','No patient selected!'); return; }
    var medsToSave=selectedMeds.filter(function(m){ return m.medicine_name.trim()!==''; });
    var btn=document.getElementById('btn-save');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Saving…';
    var payload={
        patient_id       :patientId,
        patient_name     :gVal('f-patient-name'),
        patient_age      :gVal('f-patient-age'),
        prescription_date:gVal('f-date'),
        post_op_time     :gVal('f-postop-time'),
        notes            :'',
        medicines        :medsToSave,
    };
    function trySave(idx){
        if(idx>=POST_STORE_URLS.length){ btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription'; showAlert('danger','Could not save. Route not found.'); return; }
        fetch(POST_STORE_URLS[idx],{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'},
            body:JSON.stringify(payload),
        })
        .then(function(res){ if(res.status===404){ trySave(idx+1); return null; } return res.json(); })
        .then(function(data){
            if(!data) return;
            btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription';
            if(!data.success){ showAlert('danger',data.message||'Save failed.'); return; }
            generateRxView(data.prescription_id||data.admission_id||'');
            showAlert('success','<i class="fas fa-check-circle mr-1"></i> Prescription saved! ID: <strong>#'+(data.prescription_id||data.admission_id)+'</strong>');
        })
        .catch(function(){ trySave(idx+1); });
    }
    trySave(0);
}

/* ══════════════════════════════════════════
   GENERATE RX VIEW
══════════════════════════════════════════ */
function generateRxView(savedId){
    setText('ib-name',   gVal('f-patient-name'));
    setText('ib-age',    gVal('f-patient-age'));
    setText('ib-postop', fmtTime(gVal('f-postop-time')));
    setText('ib-saved-id','#'+savedId);
    setText('rx-name', gVal('f-patient-name'));
    setText('rx-age',  gVal('f-patient-age'));
    setText('rx-date', fmtDateBD(gVal('f-date')));

    var ptEl=document.getElementById('rx-postop-time-display');
    if(ptEl){ var t=fmtTime(gVal('f-postop-time')); ptEl.textContent=(t!=='—')?t:''; }

    var ul=document.getElementById('rx-medicine-list');
    ul.querySelectorAll('li[data-med]').forEach(function(li){ li.remove(); });
    selectedMeds.filter(function(m){ return m.medicine_name.trim(); }).forEach(function(m){
        var parts=[m.medicine_name];
        if(m.dose)  parts.push(m.dose);
        if(m.route) parts.push(m.route);
        if(m.frequency) parts.push(m.frequency);
        if(m.duration)  parts.push('× '+m.duration);
        if(m.timing)    parts.push('('+m.timing+')');
        var li=document.createElement('li'); li.setAttribute('data-med','1'); li.textContent=parts.join('  '); ul.appendChild(li);
    });

    setText('gen-time',new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}));
    document.getElementById('step2-circle').className='step-circle step-done';
    document.getElementById('step2-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('rx-form-section').style.display='none';
    document.getElementById('rx-view').style.display='block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ══════════════════════════════════════════
   VIEW PAST PRESCRIPTION MODAL
   ★ Same pattern as On Admission modal
══════════════════════════════════════════ */
function viewPrescription(prescriptionId){
    document.getElementById('modal-loading').classList.remove('d-none');
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');
    document.getElementById('modal-subtitle').textContent='Loading...';
    $('#rxViewModal').modal('show');

    $.ajax({
        url:'/nursing/postsurgery/detail/'+prescriptionId,
        method:'GET',
        dataType:'json',
    }).done(function(res){
        if(!res.success||!res.data){ showModalError(res.message||'Record not found.'); return; }
        populateModal(res.data);
    }).fail(function(xhr){
        showModalError('Failed to load prescription (HTTP '+xhr.status+')');
    });
}

function showModalError(msg){
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-error').classList.remove('d-none');
    document.getElementById('modal-error-msg').textContent=msg;
}

function populateModal(d){
    document.getElementById('modal-subtitle').textContent=(d.patient_name||'—')+'  ·  '+(d.patient_code||d.p_code||'—');

    setText('m-ib-name',      d.patient_name);
    setText('m-ib-age',       d.patient_age);
    setText('m-ib-admission', fmtDateBD(d.prescription_date||d.created_at));
    setText('m-ib-id',        '#'+d.id);

    // ★ Fill the rx-wrapper fields (same layout as main print)
    setText('m-rx-name', d.patient_name);
    setText('m-rx-age',  d.patient_age);
    setText('m-rx-date', fmtDateBD(d.prescription_date||d.created_at));

    var ptEl=document.getElementById('m-rx-postop-time-display');
    if(ptEl){ ptEl.textContent=d.post_op_time?fmtTime(d.post_op_time):''; }

    // Notes
    var notesWrap=document.getElementById('m-rx-notes-wrap');
    var notesEl  =document.getElementById('m-rx-notes');
    if(d.notes&&d.notes.trim()){ if(notesEl) notesEl.textContent=d.notes; if(notesWrap) notesWrap.style.display=''; }
    else { if(notesWrap) notesWrap.style.display='none'; }

    // Medicines
    var ul=document.getElementById('m-rx-medicine-list');
    ul.querySelectorAll('li:not([data-static])').forEach(function(li){ li.remove(); });

    var meds=[];
    if(d.medicines&&Array.isArray(d.medicines)) meds=d.medicines;
    else if(typeof d.medicines==='string'){ try{ meds=JSON.parse(d.medicines); }catch(e){ meds=[]; } }

    meds.filter(function(m){ return m&&(m.medicine_name||'').trim(); })
        .forEach(function(m){
            var parts=[m.medicine_name];
            if(m.strength||m.dose) parts.push(m.strength||m.dose);
            if(m.route)     parts.push(m.route);
            if(m.frequency) parts.push(m.frequency);
            if(m.duration)  parts.push('× '+m.duration);
            if(m.timing)    parts.push('('+m.timing+')');
            if(m.note||m.remarks) parts.push(m.note||m.remarks);
            var li=document.createElement('li'); li.setAttribute('data-med','1'); li.textContent=parts.filter(Boolean).join('  '); ul.appendChild(li);
        });

    setText('m-saved-time', d.created_at
        ? new Date(d.created_at).toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—'
    );

    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.remove('d-none');
}

/* ══ DOM READY ══ */
document.addEventListener('DOMContentLoaded',function(){
    var ps=document.getElementById('patientSearch');
    if(ps) ps.addEventListener('keyup',filterTable);
});
</script>
@stop