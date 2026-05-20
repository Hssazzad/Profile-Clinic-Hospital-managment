@extends('adminlte::page')

@section('title', 'On Admission | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-file-medical"></i></span>
                On Admission
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
    $templates       = $templates       ?? collect();
    $investigations  = $investigations  ?? collect();
    $medicines       = $medicines       ?? collect();
    $patients        = $patients        ?? collect();
    $NursingPatients = $NursingPatients ?? collect();
@endphp

{{-- ══ STEP INDICATOR ══ --}}
<div class="row mb-3">
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

{{-- ══ FIXED SEARCH BAR ══ --}}
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

    {{-- ══ GOV-STYLE PANEL SHELL ══ --}}
    <div class="gov-panel" id="patient-list-card">

        {{-- Panel Title Bar --}}
        <div class="gov-panel-titlebar">
            <div class="gov-panel-titlebar-left">
                <div class="gov-panel-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="gov-panel-title">Patient Selection — Admission</div>
                    <div class="gov-panel-subtitle">Search and select a patient to proceed with admission</div>
                </div>
            </div>
            <div class="gov-panel-titlebar-right">
                <span class="gov-counter-badge">
                    <i class="fas fa-database mr-1"></i>
                    Total Records: <strong>{{ $patients->total() ?? $patients->count() }}</strong>
                </span>
                <a href="https://profclinic.erpbd.org/patients/newpatient"
                   class="gov-new-btn" target="_blank">
                    <i class="fas fa-user-plus mr-1"></i> Register New Patient
                </a>
            </div>
        </div>

        {{-- Search Toolbar --}}
        <div class="gov-toolbar" id="inline-search-bar">
            <div class="gov-toolbar-inner">
                <div class="gov-toolbar-label">
                    <i class="fas fa-search mr-1"></i> SEARCH FILTER
                </div>
                <div class="gov-search-group">
                    <input type="text" id="patientSearch" class="gov-search-input"
                           placeholder="Search by Name / Patient Code / Mobile Number…"
                           onkeyup="filterTable()">
                    <button class="gov-search-btn" type="button" onclick="filterTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                    <button class="gov-clear-btn" type="button" onclick="document.getElementById('patientSearch').value='';filterTable();">
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
                        <th class="gov-th" style="width:90px;">Pt. Code</th>
                        <th class="gov-th">Patient Name</th>
                        <th class="gov-th" style="width:52px;">Age</th>
                        <th class="gov-th" style="width:50px;">Sex</th>
                        <th class="gov-th" style="width:128px;">Mobile</th>
                        <th class="gov-th">Address / Upazila</th>
                        <th class="gov-th" style="width:62px;">Blood</th>
                        <th class="gov-th gov-th-action" style="width:76px;">Action</th>
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
                    <tr class="gov-tr patient-row">
                        <td class="gov-td gov-td-sl">{{ $pid }}</td>
                        <td class="gov-td">
                            <span class="gov-code-badge">{{ $pcode }}</span>
                        </td>
                        <td class="gov-td">
                            <div class="gov-name-cell">
                                <div class="gov-avatar">{{ strtoupper(substr($pname, 0, 1)) }}</div>
                                <div class="gov-name-info">
                                    <span class="gov-name-text">{{ $pname }}</span>
                                    @if($patient->patientfather ?? null)
                                        <span class="gov-father-text">
                                            <i class="fas fa-user-tie fa-xs mr-1"></i>{{ $patient->patientfather }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="gov-td gov-td-center">{{ $page }}</td>
                        <td class="gov-td gov-td-center">
                            @if($pgender === 'male')
                                <span class="gov-gender gov-gender-m">M</span>
                            @elseif($pgender === 'female')
                                <span class="gov-gender gov-gender-f">F</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-mono">{{ $pmobile }}</td>
                        <td class="gov-td gov-td-muted">
                            {{ $paddress }}{{ $pupozila ? ', '.$pupozila : '' }}
                        </td>
                        <td class="gov-td gov-td-center">
                            @if($pblood)
                                <span class="gov-blood-badge">{{ $pblood }}</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-action">
                            <button type="button"
                                class="gov-select-btn"
                                onclick="selectPatient(this)"
                                data-id="{{ $pid }}"
                                data-name="{{ $pname }}"
                                data-age="{{ $page }}"
                                data-code="{{ $pcode }}"
                                data-mobile="{{ $pmobile }}"
                                data-address="{{ $paddress }}"
                                data-upozila="{{ $pupozila }}"
                                data-blood="{{ $pblood }}"
                                data-gender="{{ $patient->gender ?? '' }}"
                                title="Select this patient">
                                <i class="fas fa-arrow-right mr-1"></i> Select
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="gov-empty-state">
                                <i class="fas fa-user-slash"></i>
                                <p>No patient records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if(method_exists($patients, 'links'))
        <div class="gov-panel-footer">
            <div class="gov-footer-info">
                <i class="fas fa-list-ul mr-1"></i>
                Showing <strong>{{ $patients->firstItem() ?? 0 }}</strong>
                to <strong>{{ $patients->lastItem() ?? 0 }}</strong>
                of <strong>{{ $patients->total() ?? 0 }}</strong> records
            </div>
            <div class="gov-pagination-wrap">
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>
            <div class="gov-footer-hint">
                <i class="fas fa-hand-pointer mr-1"></i>
                Click <strong>Select</strong> to proceed with admission
            </div>
        </div>
        @endif
    </div>

    {{-- ══ PAST NURSING PRESCRIPTIONS ══ --}}
    <div class="gov-panel gov-panel-teal mt-3" id="past-rx-card">

        <div class="gov-panel-titlebar gov-panel-titlebar-teal">
            <div class="gov-panel-titlebar-left">
                <div class="gov-panel-icon gov-panel-icon-teal"><i class="fas fa-history"></i></div>
                <div>
                    <div class="gov-panel-title">Past On-Admission Prescriptions</div>
                    <div class="gov-panel-subtitle">Previously saved admission prescription records</div>
                </div>
            </div>
            <div class="gov-panel-titlebar-right">
                <span class="gov-counter-badge gov-counter-badge-teal">
                    <i class="fas fa-file-medical mr-1"></i>
                    Total Records: <strong>{{ $NursingPatients->total() ?? $NursingPatients->count() }}</strong>
                </span>
            </div>
        </div>

        {{-- Search Toolbar --}}
        <div class="gov-toolbar gov-toolbar-teal">
            <div class="gov-toolbar-inner">
                <div class="gov-toolbar-label gov-toolbar-label-teal">
                    <i class="fas fa-search mr-1"></i> SEARCH FILTER
                </div>
                <div class="gov-search-group">
                    <input type="text" id="nursingRxSearch" class="gov-search-input gov-search-input-teal"
                           placeholder="Search by Name / Code / Mobile…"
                           onkeyup="filterNursingRxTable()">
                    <button class="gov-search-btn gov-search-btn-teal" type="button" onclick="filterNursingRxTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                    <button class="gov-clear-btn" type="button" onclick="document.getElementById('nursingRxSearch').value='';filterNursingRxTable();">
                        <i class="fas fa-times mr-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="gov-table-wrap">
            <table class="gov-table" id="nursingRxTable">
                <thead>
                    <tr>
                        <th class="gov-th" style="width:46px;">SL#</th>
                        <th class="gov-th" style="width:80px;">Rx ID</th>
                        <th class="gov-th">Patient Name</th>
                        <th class="gov-th" style="width:52px;">Age</th>
                        <th class="gov-th" style="width:50px;">Sex</th>
                        <th class="gov-th" style="width:128px;">Mobile</th>
                        <th class="gov-th" style="width:120px;">Admission Date</th>
                        <th class="gov-th" style="width:62px;">Blood</th>
                        <th class="gov-th gov-th-action" style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody id="nursingRxTableBody">
                    @forelse($NursingPatients as $np)
                    @php
                        $npAdmId   = $np->admission_id   ?? $np->id ?? '';
                        $npCode    = $np->p_code          ?? $np->patient_code ?? '—';
                        $npName    = $np->patient_name    ?? '—';
                        $npAge     = $np->patient_age     ?? '—';
                        $npGender  = strtolower($np->gender ?? '');
                        $npMobile  = $np->mobile_no       ?? '—';
                        $npBlood   = $np->blood_group     ?? null;
                        $npAdmDate = $np->admission_date  ?? $np->created_at ?? '';
                    @endphp
                    <tr class="gov-tr nursing-rx-row">
                        <td class="gov-td gov-td-sl">{{ $loop->iteration }}</td>
                        <td class="gov-td">
                            <span class="gov-code-badge gov-code-badge-teal">#{{ $npAdmId }}</span>
                        </td>
                        <td class="gov-td">
                            <div class="gov-name-cell">
                                <div class="gov-avatar gov-avatar-teal">{{ strtoupper(substr($npName, 0, 1)) }}</div>
                                <div class="gov-name-info">
                                    <span class="gov-name-text">{{ $npName }}</span>
                                    <span class="gov-father-text">{{ $npCode }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="gov-td gov-td-center">{{ $npAge }}</td>
                        <td class="gov-td gov-td-center">
                            @if($npGender === 'male')
                                <span class="gov-gender gov-gender-m">M</span>
                            @elseif($npGender === 'female')
                                <span class="gov-gender gov-gender-f">F</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-mono">{{ $npMobile }}</td>
                        <td class="gov-td">
                            @if($npAdmDate)
                                <span class="gov-date-text">{{ \Carbon\Carbon::parse($npAdmDate)->format('d/m/Y') }}</span>
                                <span class="gov-date-ago">{{ \Carbon\Carbon::parse($npAdmDate)->diffForHumans() }}</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-center">
                            @if($npBlood)
                                <span class="gov-blood-badge">{{ $npBlood }}</span>
                            @else
                                <span class="gov-muted">—</span>
                            @endif
                        </td>
                        <td class="gov-td gov-td-action">
                            <button type="button" class="gov-view-btn"
                                onclick="viewPrescription({{ $npAdmId }})">
                                <i class="fas fa-eye mr-1"></i> View Rx
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="gov-empty-state">
                                <i class="fas fa-file-medical-alt"></i>
                                <p>No past prescription records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($NursingPatients, 'links'))
        <div class="gov-panel-footer">
            <div class="gov-footer-info">
                <i class="fas fa-list-ul mr-1"></i>
                Showing <strong>{{ $NursingPatients->firstItem() ?? 0 }}</strong>
                to <strong>{{ $NursingPatients->lastItem() ?? 0 }}</strong>
                of <strong>{{ $NursingPatients->total() ?? 0 }}</strong> records
            </div>
            <div class="gov-pagination-wrap">
                {{ $NursingPatients->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>{{-- /#panel-step1 --}}

{{-- ══════════════════════════════════════════
     STEP 2 — PRESCRIPTION FORM
══════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none;">

    {{-- ══ SELECTED PATIENT BAR ══ --}}
    <div class="patient-selected-bar mb-4">
        <div class="psb-left">
            <div class="psb-avatar" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot"></span>
            <span class="psb-status-label">Patient Selected</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change
            </button>
        </div>
    </div>

    {{-- ══ TEMPLATE BAR ══ --}}
    <div class="template-bar mb-4">
        <div class="template-bar-header">
            <div class="d-flex align-items-center">
                <div class="tpl-icon-wrap"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="tpl-title">Prescription Template</div>
                    <div class="tpl-subtitle">Load a saved template to auto-fill medicines</div>
                </div>
            </div>
            <span class="tpl-status-badge" id="tpl-status-badge">
                <i class="fas fa-circle mr-1" style="font-size:7px;"></i> No template loaded
            </span>
        </div>
        <div class="template-bar-body">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <label class="tpl-field-label">Select Template</label>
                    <select class="tpl-select" id="f-template-select">
                        <option value="">— Choose a template —</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->ID ?? $tpl->id ?? '' }}">{{ $tpl->title ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7 mt-3 mt-md-0">
                    <label class="tpl-field-label d-none d-md-block">&nbsp;</label>
                    <div class="tpl-actions">
                        <button type="button" class="btn-tpl btn-tpl-load" id="btn-load-tpl" onclick="loadTemplate()">
                            <i class="fas fa-download mr-1"></i> Load Template
                        </button>
                        <button type="button" class="btn-tpl btn-tpl-preview">
                            <i class="fas fa-eye mr-1"></i> Preview
                        </button>
                        <button type="button" class="btn-tpl btn-tpl-save">
                            <i class="fas fa-bookmark mr-1"></i> Save as Mine
                        </button>
                        <span id="tpl-loading" class="tpl-loading-spin d-none">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Loading…
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ PRESCRIPTION FORM CARD ══ --}}
    <div class="modern-card" id="rx-form-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-info-soft"><i class="fas fa-notes-medical text-info"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Prescription Details</h5>
                    <small class="text-muted">Fill in the clinical information below</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">

            <input type="hidden" id="f-patient-id">
            <input type="hidden" id="f-patient-code">

            <hr class="section-divider mt-0 mb-4">

            <div class="section-heading mb-3">
                <i class="fas fa-user-injured mr-2 text-primary"></i>
                <span>Patient &amp; Clinical Information</span>
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
                    <div class="modern-field-group">
                        <label class="modern-label">Prescription Date</label>
                        <input type="date" class="modern-input" id="f-date">
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">
                            <i class="fas fa-baby mr-1" style="color:#e91e8c;"></i>
                            Pregnancy (Weeks)
                        </label>
                        <input type="number" class="modern-input" id="f-preg-weeks" placeholder="e.g. 36" min="1" max="42">
                    </div>
                    <div class="field-row-2">
                        <div class="modern-field-group">
                            <label class="modern-label">Admission Date</label>
                            <input type="date" class="modern-input" id="f-admission-date">
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">Admission Time</label>
                            <input type="time" class="modern-input" id="f-admission-time">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="vitals-group">
                        <div class="vitals-group-label">
                            <i class="fas fa-heartbeat mr-1"></i> Vitals
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
                                <label class="modern-label">Blood Pressure</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-tachometer-alt input-icon text-warning"></i>
                                    <input type="text" class="modern-input with-icon" id="f-bp" placeholder="120/80">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modern-field-group mt-3">
                        <label class="modern-label">OT Time</label>
                        <input type="time" class="modern-input" id="f-ot-time">
                    </div>

                    <div class="baby-section mt-3" id="baby-section-wrapper">
                        <div class="baby-section-header" onclick="toggleBabySection()">
                            <div class="d-flex align-items-center">
                                <span class="baby-icon">👶</span>
                                <span class="baby-section-title">Baby Information</span>
                                <span class="baby-optional-tag">optional</span>
                            </div>
                            <i class="fas fa-chevron-down baby-chevron" id="baby-chevron"></i>
                        </div>
                        <div class="baby-section-body" id="baby-section-body">
                            <div class="field-row-3">
                                <div class="modern-field-group">
                                    <label class="modern-label text-pink">Baby Sex</label>
                                    <select class="modern-input modern-select" id="f-baby-sex">
                                        <option value="">— Select —</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label text-pink">Weight (kg)</label>
                                    <input type="number" class="modern-input" id="f-baby-weight" placeholder="2.5" step="0.1">
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label text-pink">Baby Time</label>
                                    <input type="time" class="modern-input" id="f-baby-time">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ MEDICINES ══ --}}
            <div class="section-divider-full mt-4 mb-4">
                <div class="section-heading mb-0">
                    <i class="fas fa-pills mr-2 text-success"></i>
                    <span>Medicines</span>
                    <span class="badge badge-pill ml-2" id="med-count-badge"
                          style="background:#e8f5e9;color:#2e7d32;font-size:12px;padding:4px 10px;">0</span>
                </div>
                <div class="med-section-actions">
                    <button type="button" class="btn-med-action btn-med-add" onclick="addMedRow()">
                        <i class="fas fa-plus mr-1"></i> Add Row
                    </button>
                    <button type="button" class="btn-med-action btn-med-clear" onclick="clearAllMeds()">
                        <i class="fas fa-trash-alt mr-1"></i> Clear All
                    </button>
                </div>
            </div>

            <div class="med-table-card selected-med-card mb-4">
                <div class="med-table-card-header">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#f9a825;"></span>
                        <span class="med-table-title">Selected Medicines</span>
                        <span class="med-count-pill" id="sel-med-badge">0</span>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="table med-table mb-0" style="min-width:700px;">
                        <thead>
                            <tr>
                                <th style="width:35px;">#</th>
                                <th>Medicine Name</th>
                                <th style="width:95px;">Dose</th>
                                <th style="width:85px;">Route</th>
                                <th style="width:115px;">Frequency</th>
                                <th style="width:85px;">Duration</th>
                                <th style="width:85px;">Timing</th>
                                <th style="width:100px;">Remarks</th>
                                <th style="width:42px;"></th>
                            </tr>
                        </thead>
                        <tbody id="sel-med-tbody">
                            <tr class="empty-row">
                                <td colspan="9">
                                    <div class="med-empty-state">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>No medicines selected yet. Add from the list below.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="med-table-card available-med-card mb-0">
                <div class="med-table-card-header">
                    <div class="d-flex align-items-center">
                        <span class="med-table-dot" style="background:#78909c;"></span>
                        <span class="med-table-title">Available Medicines</span>
                        <span class="med-count-pill" style="background:#eceff1;color:#546e7a;">{{ $medicines->count() }}</span>
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
                                <th>Dose</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="avail-med-tbody">
                            @forelse($medicines as $med)
                            <tr class="avail-med-row" data-name="{{ strtolower($med->medicine_name ?? $med->name ?? '') }}">
                                <td>
                                    <input type="checkbox" class="avail-med-cb modern-checkbox"
                                        data-name="{{ $med->medicine_name ?? $med->name ?? '' }}"
                                        data-dose="{{ $med->dose ?? '' }}"
                                        data-route="{{ $med->route ?? '' }}"
                                        data-frequency="{{ $med->frequency ?? '' }}"
                                        data-duration="{{ $med->duration ?? '' }}"
                                        data-timing="{{ $med->timing ?? '' }}"
                                        onchange="onAvailMedChange(this)">
                                </td>
                                <td><span class="avail-med-name">{{ $med->medicine_name ?? $med->name ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->dose ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->frequency ?? '—' }}</span></td>
                                <td><span class="text-muted small">{{ $med->duration ?? '—' }}</span></td>
                                <td>
                                    <button type="button" class="btn-quick-add" onclick="quickAdd(this)" title="Quick Add">
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

            <div class="mt-4 pt-2">
                <div class="modern-field-group">
                    <label class="modern-label">
                        <i class="fas fa-comment-medical mr-1 text-secondary"></i>
                        Additional Notes
                    </label>
                    <textarea class="modern-input modern-textarea" id="f-notes" rows="3"
                              placeholder="Any additional instructions or notes for this admission..."></textarea>
                </div>
            </div>

            <div class="form-footer mt-4">
                <button type="button" class="btn btn-footer-back" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" class="btn btn-footer-save" id="btn-save-rx" onclick="saveAndGenerate()">
                    <i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription
                </button>
            </div>
        </div>
    </div>

    {{-- ══ PRESCRIPTION PRINT VIEW ══ --}}
    <div id="rx-view" style="display:none;">
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-blue">
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
                    <div class="rx-summary-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="rx-summary-content">
                        <div class="rx-summary-label">Admission</div>
                        <div class="rx-summary-value" id="ib-admission">—</div>
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
                    <span class="card-title-icon bg-success-soft"><i class="fas fa-notes-medical text-success"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Generated Prescription</h5>
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
                        <div class="rx-admission-note">
                            <span class="rx-symbol">Rx</span>
                            <span id="rx-preg-weeks"></span>
                        </div>
                        <div class="rx-section-center">Rx On admission On</div>
                        <div class="rx-time-right"><span id="rx-admission-time"></span></div>
                        <div class="rx-two-col">
                            <div class="rx-col-left">
                                <div class="rx-section-title">O/E</div>
                                <ul class="rx-list">
                                    <li>Pulse – <span id="rx-pulse">—</span></li>
                                    <li>BP – <span id="rx-bp">—</span></li>
                                </ul>
                            </div>
                            <div class="rx-col-right">
                                <ul class="rx-list" id="rx-medicine-list">
                                    <li>NPO-TFO</li>
                                </ul>
                            </div>
                        </div>
                        <hr class="rx-divider">
                        <div class="rx-two-col">
                            <div class="rx-col-left">
                                <div class="rx-section-title">Adv</div>
                                <ul class="rx-list" id="rx-adv-list">
                                    @foreach($investigations as $inv)
                                    <li>{{ $inv->name ?? $inv->investigation_name ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="rx-col-right">
                                <div class="rx-section-title">Pre-Operative Order</div>
                                <ul class="rx-list">
                                    <li>Plz take written &amp; informed consent</li>
                                    <li>Plz clean &amp; shave the operative area.</li>
                                    <li>Send patient to O.T at <span id="rx-ot-time">—</span></li>
                                </ul>
                            </div>
                        </div>
                        <hr class="rx-divider">
                        <div class="rx-baby-note">
                            <div class="rx-section-title">Baby Note</div>
                            <ul class="rx-list">
                                <li>Sex – <span id="rx-baby-sex">—</span></li>
                                <li>Weight <u id="rx-baby-weight">—</u> Kg</li>
                                <li>Time <u id="rx-baby-time">—</u> am/pm</li>
                            </ul>
                        </div>
                        <div class="rx-notes"><p><span id="rx-notes">—</span></p></div>
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
    </div>

</div>{{-- /#panel-step2 --}}

{{-- ══ PRESCRIPTION VIEW MODAL (Past Rx) ══ --}}
<div class="modal fade" id="rxViewModal" tabindex="-1" role="dialog" aria-labelledby="rxViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">
            <div class="modal-header rx-modal-header">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3"><i class="fas fa-file-medical"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white" id="rxViewModalLabel">
                            On Admission Prescription
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
                        <div class="modal-summary-item msi-blue">
                            <i class="fas fa-user"></i>
                            <div><div class="msi-label">Patient</div><div class="msi-val" id="m-ib-name">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-green">
                            <i class="fas fa-birthday-cake"></i>
                            <div><div class="msi-label">Age</div><div class="msi-val" id="m-ib-age">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-orange">
                            <i class="fas fa-calendar-alt"></i>
                            <div><div class="msi-label">Admission</div><div class="msi-val" id="m-ib-admission">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-teal">
                            <i class="fas fa-hashtag"></i>
                            <div><div class="msi-label">Rx ID</div><div class="msi-val" id="m-ib-id">—</div></div>
                        </div>
                    </div>
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
                            <div class="rx-admission-note">
                                <span class="rx-symbol">Rx</span>
                                <span id="m-rx-preg-weeks"></span>
                            </div>
                            <div class="rx-section-center">Rx On admission On</div>
                            <div class="rx-time-right"><span id="m-rx-admission-time"></span></div>
                            <div class="rx-two-col">
                                <div class="rx-col-left">
                                    <div class="rx-section-title">O/E</div>
                                    <ul class="rx-list">
                                        <li>Pulse – <span id="m-rx-pulse">—</span></li>
                                        <li>BP – <span id="m-rx-bp">—</span></li>
                                    </ul>
                                </div>
                                <div class="rx-col-right">
                                    <ul class="rx-list" id="m-rx-medicine-list">
                                        <li data-static="1">NPO-TFO</li>
                                    </ul>
                                </div>
                            </div>
                            <hr class="rx-divider">
                            <div class="rx-two-col">
                                <div class="rx-col-left">
                                    <div class="rx-section-title">Adv</div>
                                    <ul class="rx-list" id="m-rx-adv-list">
                                        @foreach($investigations as $inv)
                                        <li>{{ $inv->name ?? $inv->investigation_name ?? '' }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="rx-col-right">
                                    <div class="rx-section-title">Pre-Operative Order</div>
                                    <ul class="rx-list">
                                        <li>Plz take written &amp; informed consent</li>
                                        <li>Plz clean &amp; shave the operative area.</li>
                                        <li>Send patient to O.T at <span id="m-rx-ot-time">—</span></li>
                                    </ul>
                                </div>
                            </div>
                            <hr class="rx-divider">
                            <div class="rx-baby-note">
                                <div class="rx-section-title">Baby Note</div>
                                <ul class="rx-list">
                                    <li>Sex – <span id="m-rx-baby-sex">—</span></li>
                                    <li>Weight <u id="m-rx-baby-weight">—</u> Kg</li>
                                    <li>Time <u id="m-rx-baby-time">—</u> am/pm</li>
                                </ul>
                            </div>
                            <div class="rx-notes"><p><span id="m-rx-notes">—</span></p></div>
                        </div>
                    </div>
                </div>
            </div>
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

<div id="print-overlay"></div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════
   ROOT VARIABLES
═══════════════════════════════════════════ */
:root {
    --blue-deep:  #1565C0; --blue-mid: #1976D2; --blue-light: #E3F2FD; --blue-soft: #BBDEFB;
    --green-deep: #2E7D32; --green-mid: #43A047; --green-light: #E8F5E9;
    --teal-deep:  #00695C; --teal-mid: #00796B; --teal-light: #E0F2F1; --teal-soft: #B2DFDB;
    --orange:     #E65100; --pink: #C2185B;
    --text-primary: #1a2332; --text-muted: #6b7a90; --border: #e4e9f0;
    --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px;
    --shadow-sm: 0 1px 4px rgba(0,0,0,.06); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --font-base: 'DM Sans','Hind Siliguri',Arial,sans-serif;

    /* Gov palette */
    --gov-bg:       #f2f4f7;
    --gov-header:   #1a3a5c;
    --gov-header2:  #1e4976;
    --gov-accent:   #c9972a;
    --gov-border:   #c8cdd6;
    --gov-row-odd:  #ffffff;
    --gov-row-even: #f6f8fb;
    --gov-row-hover:#e8f0fb;
    --gov-text:     #1c2b3a;
    --gov-muted:    #6b7890;
    --gov-teal-hdr: #0d4a42;
    --gov-teal-hdr2:#105c54;
}
body,.content-wrapper { background:var(--gov-bg) !important; font-family:var(--font-base); }

/* ═══════════════════════════════════════════
   PAGE HEADER & STEP TRACK (unchanged)
═══════════════════════════════════════════ */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--blue-light);display:inline-flex;align-items:center;justify-content:center;color:var(--blue-deep);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--blue-light);border-color:var(--blue-mid);color:var(--blue-deep); }
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:14px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s;border:2.5px solid transparent; }
.step-active   { background:var(--blue-deep);color:#fff;border-color:var(--blue-deep);box-shadow:0 0 0 4px rgba(21,101,192,.15); }
.step-done     { background:var(--green-deep);color:#fff;border-color:var(--green-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub  { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--blue-deep); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--green-deep); }

/* ═══════════════════════════════════════════
   FIXED SEARCH BAR (unchanged)
═══════════════════════════════════════════ */
.fixed-search-bar { position:fixed;top:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#1565C0 0%,#1a78d8 100%);box-shadow:0 4px 24px rgba(21,101,192,.35);transform:translateY(-100%);transition:transform .3s cubic-bezier(.4,0,.2,1),opacity .3s;opacity:0;pointer-events:none; }
.fixed-search-bar.visible { transform:translateY(0);opacity:1;pointer-events:all; }
.fixed-search-inner { display:flex;align-items:center;gap:16px;padding:10px 20px;flex-wrap:wrap; }
.fixed-search-brand { display:flex;align-items:center;gap:8px;flex-shrink:0; }
.fsc-dot { width:8px;height:8px;border-radius:50%;background:#69f0ae;box-shadow:0 0 0 3px rgba(105,240,174,.3); }
.fsc-label { color:rgba(255,255,255,.9);font-size:13px;font-weight:700;white-space:nowrap; }
.fixed-search-field { flex:1;min-width:240px; }
.fixed-search-meta { flex-shrink:0; }
.fsc-count-pill { background:rgba(255,255,255,.18);color:#fff;border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600;white-space:nowrap; }
.search-input-group { display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s;box-shadow:var(--shadow-sm); }
.search-input-group:focus-within { border-color:var(--blue-mid);box-shadow:0 0 0 3px rgba(25,118,210,.1); }
.search-input-group-fixed { border:2px solid rgba(255,255,255,.35);background:rgba(255,255,255,.12); }
.search-input-group-fixed:focus-within { border-color:rgba(255,255,255,.7); }
.search-input-group-fixed .search-icon { color:rgba(255,255,255,.7); }
.search-input-group-fixed .search-input { background:transparent;color:#fff; }
.search-input-group-fixed .search-input::placeholder { color:rgba(255,255,255,.55); }
.search-input-group-fixed .search-btn { background:rgba(255,255,255,.22);color:#fff; }
.search-input-group-fixed .search-btn:hover { background:rgba(255,255,255,.35); }
.search-icon { padding:0 12px;color:#aab;font-size:15px; }
.search-input { flex:1;border:none;outline:none;padding:10px 6px;font-size:14px;background:transparent;color:var(--text-primary); }
.search-btn { background:var(--blue-deep);color:#fff;border:none;padding:10px 22px;font-size:13.5px;font-weight:600;cursor:pointer;transition:background .2s; }
.search-btn:hover { background:var(--blue-mid); }

/* ═══════════════════════════════════════════
   GOV PANEL — MAIN SHELL
═══════════════════════════════════════════ */
.gov-panel {
    background:#fff;
    border:1px solid var(--gov-border);
    border-top:3px solid var(--gov-header);
    border-radius:0 0 4px 4px;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
    margin-bottom:16px;
    overflow:hidden;
}
.gov-panel-teal {
    border-top-color: var(--gov-teal-hdr);
}

/* Title bar */
.gov-panel-titlebar {
    background:linear-gradient(90deg, var(--gov-header) 0%, var(--gov-header2) 100%);
    padding:10px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
    border-bottom:2px solid var(--gov-accent);
}
.gov-panel-titlebar-teal {
    background:linear-gradient(90deg, var(--gov-teal-hdr) 0%, var(--gov-teal-hdr2) 100%);
    border-bottom-color:#4db6ac;
}
.gov-panel-titlebar-left { display:flex;align-items:center;gap:10px; }
.gov-panel-titlebar-right { display:flex;align-items:center;gap:10px; }
.gov-panel-icon {
    width:34px;height:34px;border-radius:4px;
    background:rgba(255,255,255,.15);
    color:#fff;font-size:15px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
    border:1px solid rgba(255,255,255,.2);
}
.gov-panel-icon-teal { background:rgba(255,255,255,.12); }
.gov-panel-title { font-size:14px;font-weight:700;color:#fff;line-height:1.2;letter-spacing:.2px; }
.gov-panel-subtitle { font-size:11px;color:rgba(255,255,255,.7);margin-top:1px; }

.gov-counter-badge {
    background:rgba(255,255,255,.15);
    color:#fff;
    border:1px solid rgba(255,255,255,.25);
    border-radius:3px;
    padding:4px 12px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
}
.gov-counter-badge-teal { background:rgba(255,255,255,.12); }
.gov-new-btn {
    background:var(--gov-accent);
    color:#fff;
    border:none;
    border-radius:3px;
    padding:6px 14px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    transition:all .2s;
    letter-spacing:.2px;
    white-space:nowrap;
}
.gov-new-btn:hover { background:#b8841f;color:#fff;text-decoration:none; }

/* Toolbar */
.gov-toolbar {
    background:#f0f3f8;
    border-bottom:1.5px solid var(--gov-border);
    padding:8px 16px;
}
.gov-toolbar-teal { background:#f0f6f5; }
.gov-toolbar-inner { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.gov-toolbar-label {
    font-size:11px;font-weight:800;
    color:var(--gov-header);
    text-transform:uppercase;
    letter-spacing:.8px;
    white-space:nowrap;
    flex-shrink:0;
}
.gov-toolbar-label-teal { color:var(--gov-teal-hdr); }
.gov-toolbar-hint { font-size:11px;color:var(--gov-muted);white-space:nowrap; }
.gov-toolbar-hint kbd {
    background:#fff;
    border:1px solid var(--gov-border);
    border-radius:3px;
    padding:1px 5px;
    font-size:10px;
    color:var(--gov-header);
}
.gov-search-group { display:flex;align-items:center;gap:4px;flex:1;min-width:260px; }
.gov-search-input {
    flex:1;
    border:1.5px solid var(--gov-border);
    border-radius:3px;
    padding:6px 10px;
    font-size:13px;
    color:var(--gov-text);
    background:#fff;
    outline:none;
    transition:border-color .2s;
    font-family:var(--font-base);
    height:32px;
}
.gov-search-input:focus { border-color:var(--blue-mid);box-shadow:0 0 0 2px rgba(25,118,210,.12); }
.gov-search-input-teal:focus { border-color:var(--teal-mid); }
.gov-search-btn {
    border:none;border-radius:3px;padding:0 14px;
    height:32px;font-size:12px;font-weight:700;
    cursor:pointer;transition:background .2s;
    background:var(--gov-header);color:#fff;
    display:inline-flex;align-items:center;
    white-space:nowrap;letter-spacing:.2px;
}
.gov-search-btn:hover { background:var(--blue-mid); }
.gov-search-btn-teal { background:var(--gov-teal-hdr); }
.gov-search-btn-teal:hover { background:var(--teal-mid); }
.gov-clear-btn {
    border:1.5px solid var(--gov-border);border-radius:3px;
    padding:0 10px;height:32px;font-size:12px;font-weight:600;
    cursor:pointer;transition:all .2s;
    background:#fff;color:var(--gov-muted);
    display:inline-flex;align-items:center;
    white-space:nowrap;
}
.gov-clear-btn:hover { background:#ffebee;color:#c62828;border-color:#ffcdd2; }

/* Data Table */
.gov-table-wrap { overflow-x:auto; }
.gov-table {
    border-collapse:collapse;
    width:100%;
    font-size:12.5px;
}
.gov-th {
    background:#e8ecf4;
    color:var(--gov-header);
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.6px;
    padding:8px 10px;
    border-bottom:2px solid var(--gov-border);
    border-right:1px solid #d0d5df;
    white-space:nowrap;
    position:sticky;
    top:0;
    z-index:5;
}
.gov-th:last-child { border-right:none; }
.gov-th-action { text-align:center; }
.gov-tr { transition:background .12s; }
.gov-tr:nth-child(odd)  { background:var(--gov-row-odd); }
.gov-tr:nth-child(even) { background:var(--gov-row-even); }
.gov-tr:hover { background:var(--gov-row-hover) !important; }
.gov-td {
    padding:7px 10px;
    border-bottom:1px solid #eaedf2;
    border-right:1px solid #f0f2f6;
    vertical-align:middle;
    color:var(--gov-text);
}
.gov-td:last-child { border-right:none; }
.gov-td-sl     { color:var(--gov-muted);font-size:11.5px;text-align:center; }
.gov-td-center { text-align:center; }
.gov-td-mono   { font-family:'Courier New',monospace;font-size:12px;letter-spacing:.3px; }
.gov-td-muted  { color:var(--gov-muted);font-size:12px; }
.gov-td-action { text-align:center; }

/* Name cell */
.gov-name-cell  { display:flex;align-items:center;gap:7px; }
.gov-avatar {
    width:26px;height:26px;border-radius:3px;
    background:var(--gov-header);color:#fff;
    font-size:11px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
    letter-spacing:0;
}
.gov-avatar-teal { background:var(--gov-teal-hdr); }
.gov-name-info  { display:flex;flex-direction:column;gap:1px; }
.gov-name-text  { font-weight:600;font-size:13px;color:var(--gov-text);line-height:1.2; }
.gov-father-text{ font-size:10.5px;color:var(--gov-muted); }

/* Badges */
.gov-code-badge {
    background:#e8ecf4;color:var(--gov-header);
    border:1px solid #c8cdd6;border-radius:2px;
    padding:1px 7px;font-size:11.5px;font-weight:700;
    font-family:'Courier New',monospace;letter-spacing:.3px;
}
.gov-code-badge-teal { background:#e0f2f1;color:var(--teal-deep);border-color:var(--teal-soft); }
.gov-gender {
    display:inline-flex;align-items:center;justify-content:center;
    width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:800;
}
.gov-gender-m { background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd; }
.gov-gender-f { background:#fce7f3;color:#be185d;border:1px solid #f9a8d4; }
.gov-blood-badge {
    background:#fee2e2;color:#991b1b;
    border:1px solid #fca5a5;border-radius:2px;
    padding:1px 7px;font-size:11.5px;font-weight:700;
}
.gov-muted { color:var(--gov-muted);font-size:12px; }
.gov-date-text { display:block;font-size:12.5px;font-weight:600;color:var(--gov-text); }
.gov-date-ago  { display:block;font-size:10.5px;color:var(--gov-muted); }

/* Action buttons */
.gov-select-btn {
    background:var(--gov-header);
    color:#fff;
    border:none;
    border-radius:3px;
    padding:5px 12px;
    font-size:11.5px;
    font-weight:700;
    cursor:pointer;
    transition:all .18s;
    display:inline-flex;
    align-items:center;
    letter-spacing:.2px;
    white-space:nowrap;
    box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.gov-select-btn:hover { background:var(--blue-mid);transform:translateY(-1px);box-shadow:0 3px 8px rgba(21,101,192,.3); }
.gov-view-btn {
    background:var(--gov-teal-hdr);
    color:#fff;
    border:none;
    border-radius:3px;
    padding:5px 11px;
    font-size:11.5px;
    font-weight:700;
    cursor:pointer;
    transition:all .18s;
    display:inline-flex;
    align-items:center;
    white-space:nowrap;
    box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.gov-view-btn:hover { background:var(--teal-mid);transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,105,92,.3); }

/* Panel footer */
.gov-panel-footer {
    background:#f0f3f8;
    border-top:1.5px solid var(--gov-border);
    padding:8px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
}
.gov-footer-info { font-size:12px;color:var(--gov-muted); }
.gov-footer-hint { font-size:11.5px;color:var(--gov-muted); }
.gov-pagination-wrap .pagination { margin:0; }
.gov-pagination-wrap .page-link { border-radius:2px !important;border-color:var(--gov-border);color:var(--gov-header);font-size:12px;padding:3px 9px; }
.gov-pagination-wrap .page-item.active .page-link { background:var(--gov-header);border-color:var(--gov-header); }

/* Empty state */
.gov-empty-state { text-align:center;padding:32px;color:#90a4b7; }
.gov-empty-state i { font-size:28px;margin-bottom:8px;display:block; }
.gov-empty-state p { font-size:13px;margin:0; }

/* ═══════════════════════════════════════════
   STEP 2 & FORM ELEMENTS (fully preserved)
═══════════════════════════════════════════ */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-info-soft    { background:#E1F5FE; }
.bg-success-soft { background:var(--green-light); }
.bg-teal-soft    { background:var(--teal-light); }
.modern-card-body   { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }
.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }

.patient-selected-bar { background:linear-gradient(135deg,#1565C0 0%,#1E88E5 100%);border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;box-shadow:0 4px 18px rgba(21,101,192,.22); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;background:#69f0ae;box-shadow:0 0 0 3px rgba(105,240,174,.3);display:inline-block; }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28);color:#fff; }

.template-bar { background:#fff;border-radius:var(--radius-md);border:1.5px solid #bbdefb;box-shadow:var(--shadow-md);overflow:hidden; }
.template-bar-header { background:linear-gradient(90deg,#e3f2fd 0%,#f8fbff 100%);padding:14px 20px;border-bottom:1px solid #bbdefb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.tpl-icon-wrap { width:38px;height:38px;border-radius:9px;background:var(--blue-light);color:var(--blue-deep);font-size:16px;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0; }
.tpl-title { font-size:14px;font-weight:700;color:var(--blue-deep);line-height:1.2; }
.tpl-subtitle { font-size:12px;color:var(--text-muted); }
.tpl-status-badge { background:#fff;border:1.5px solid #bbdefb;color:var(--blue-deep);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600; }
.tpl-status-badge.loaded { background:var(--green-light);border-color:#a5d6a7;color:var(--green-deep); }
.template-bar-body { padding:16px 20px; }
.tpl-field-label { font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block; }
.tpl-select { width:100%;border:2px solid var(--border);border-radius:var(--radius-sm);padding:9px 14px;font-size:14px;color:var(--text-primary);background:#fff;transition:border-color .2s;outline:none; }
.tpl-select:focus { border-color:var(--blue-mid);box-shadow:0 0 0 3px rgba(25,118,210,.1); }
.tpl-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }
.btn-tpl { border-radius:var(--radius-sm);padding:9px 18px;font-size:13px;font-weight:600;border:2px solid transparent;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-tpl-load    { background:var(--blue-deep);color:#fff;border-color:var(--blue-deep); }
.btn-tpl-load:hover { background:var(--blue-mid);transform:translateY(-1px); }
.btn-tpl-preview { background:#fff;color:var(--blue-deep);border-color:#bbdefb; }
.btn-tpl-preview:hover { background:var(--blue-light); }
.btn-tpl-save    { background:#fff;color:var(--green-deep);border-color:#a5d6a7; }
.btn-tpl-save:hover { background:var(--green-light); }
.tpl-loading-spin { font-size:13px;color:var(--blue-mid);font-weight:500;display:inline-flex;align-items:center;gap:5px; }

.section-heading { display:flex;align-items:center;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px; }
.section-divider { border:none;border-top:1.5px solid var(--border); }
.section-divider-full { border-top:1.5px solid var(--border);padding-top:18px;display:flex;align-items:center;justify-content:space-between; }
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--blue-mid);box-shadow:0 0 0 3px rgba(25,118,210,.1); }
.modern-select { appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7a90' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px; }
.modern-textarea { resize:vertical;min-height:80px; }
.text-pink { color:var(--pink) !important; }
.field-row-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.field-row-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px; }
.input-with-icon { position:relative; }
.input-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none; }
.modern-input.with-icon { padding-left:30px; }
.vitals-group { background:#fafbff;border:1.5px solid var(--border);border-radius:var(--radius-md);padding:14px 16px 6px;position:relative; }
.vitals-group-label { position:absolute;top:-11px;left:12px;background:#fafbff;padding:0 8px;font-size:11.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px; }
.baby-section { border:1.5px solid #fce4ec;border-radius:var(--radius-md);overflow:hidden;background:#fff; }
.baby-section-header { background:#fce4ec;padding:11px 14px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;user-select:none;transition:background .2s; }
.baby-section-header:hover { background:#f8bbd0; }
.baby-icon { font-size:16px;margin-right:8px; }
.baby-section-title { font-size:13px;font-weight:700;color:var(--pink); }
.baby-optional-tag { background:rgba(194,24,91,.12);color:var(--pink);border-radius:20px;padding:2px 8px;font-size:10.5px;font-weight:600;margin-left:8px;text-transform:uppercase;letter-spacing:.4px; }
.baby-chevron { color:var(--pink);font-size:12px;transition:transform .25s; }
.baby-chevron.open { transform:rotate(180deg); }
.baby-section-body { padding:14px 14px 6px;display:none; }
.baby-section-body.open { display:block; }
.med-section-actions { display:flex;gap:8px; }
.btn-med-action { border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center; }
.btn-med-add   { background:var(--blue-light);color:var(--blue-deep);border-color:#bbdefb; }
.btn-med-add:hover { background:var(--blue-deep);color:#fff; }
.btn-med-clear { background:#ffebee;color:#c62828;border-color:#ffcdd2; }
.btn-med-clear:hover { background:#c62828;color:#fff; }
.med-table-card { border-radius:var(--radius-md);border:1.5px solid var(--border);overflow:hidden;box-shadow:var(--shadow-sm); }
.selected-med-card { border-color:#ffe082; }
.med-table-card-header { padding:11px 16px;background:#f9fafb;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.selected-med-card .med-table-card-header { background:#fffde7;border-bottom-color:#ffe082; }
.med-table-dot { width:8px;height:8px;border-radius:50%;margin-right:8px;flex-shrink:0; }
.med-table-title { font-size:13px;font-weight:700;color:var(--text-primary); }
.med-count-pill { background:#fff3e0;color:#e65100;border-radius:20px;padding:2px 10px;font-size:11.5px;font-weight:700;margin-left:8px; }
.med-table { border-collapse:collapse;width:100%; }
.med-table thead tr th { background:#f5f7fa;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);padding:9px 12px;border-bottom:1.5px solid var(--border);white-space:nowrap; }
.med-table tbody td { padding:8px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.med-table tbody tr:last-child td { border-bottom:none; }
.med-table tbody tr:hover { background:#f8f9ff; }
.med-table .form-control { padding:4px 8px !important;font-size:12.5px !important;min-height:unset !important; }
.med-empty-state { text-align:center;color:#b0bec5;padding:20px;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px; }
.avail-med-name { font-weight:600;color:var(--text-primary);font-size:13px; }
.avail-filter-wrap { display:flex;align-items:center;background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .2s; }
.avail-filter-wrap:focus-within { border-color:var(--blue-mid); }
.avail-filter-icon { padding:0 9px;color:#aab;font-size:12px; }
.avail-filter-input { border:none;outline:none;padding:6px 4px;font-size:13px;background:transparent;width:170px; }
.modern-checkbox { width:15px;height:15px;accent-color:var(--blue-deep);cursor:pointer; }
.btn-quick-add { width:26px;height:26px;border-radius:6px;background:var(--green-light);color:var(--green-deep);border:1.5px solid #a5d6a7;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .18s; }
.btn-quick-add:hover { background:var(--green-deep);color:#fff; }
.form-footer { display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1.5px solid var(--border); }
.btn-footer-back { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:600;transition:all .2s; }
.btn-footer-back:hover { background:#f0f4f8;color:var(--text-primary); }
.btn-footer-save { background:linear-gradient(135deg,#2E7D32,#43A047);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(46,125,50,.3);transition:all .2s;display:inline-flex;align-items:center;gap:7px; }
.btn-footer-save:hover { background:linear-gradient(135deg,#1B5E20,#2E7D32);transform:translateY(-1px);color:#fff; }

.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-blue   { background:linear-gradient(135deg,#1565C0,#1E88E5); }
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

/* ═══════════════════════════════════════════
   RX PRINT LAYOUT (unchanged)
═══════════════════════════════════════════ */
#prescription-print-area { padding:24px;background:#fff; }
.rx-wrapper { width:100%;max-width:800px;margin:0 auto;background:#fff;border:1px solid #d4d4d4;padding:26px 32px;font-family:'Hind Siliguri',Arial,sans-serif; }
.rx-header { display:flex;align-items:center;justify-content:center;gap:18px;border-bottom:2.5px solid #1a237e;padding-bottom:12px;margin-bottom:6px; }
.rx-logo { width:62px;height:62px;border:3px solid #1a237e;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:#1a237e;font-style:italic;font-family:Georgia,serif; }
.rx-clinic-sub { font-size:8.5px;color:#1a237e;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-top:3px;text-align:center; }
.rx-clinic-info { text-align:center; }
.rx-clinic-name { font-size:36px;font-weight:700;color:#1a237e;letter-spacing:1px; }
.rx-address { font-size:13px;font-weight:600;color:#1a237e;margin-top:3px; }
.rx-phones { font-size:11px;color:#1a237e;margin-top:2px; }
.rx-patient-row { display:flex;justify-content:space-between;align-items:flex-end;border-bottom:1px solid #444;padding:8px 0 5px; }
.rx-field { display:flex;align-items:center;gap:5px;font-size:13px; }
.rx-field label { font-weight:700;white-space:nowrap;font-size:12px;margin-bottom:0; }
.rx-value { border-bottom:1px dotted #555;min-width:120px;padding:0 6px 1px;font-size:13px; }
.rx-admission-note { font-size:13px;margin:10px 0 5px;line-height:1.7; }
.rx-symbol { font-size:22px;font-weight:700;margin-right:5px; }
.rx-section-center { text-align:center;font-weight:700;font-size:13px;text-decoration:underline;margin:5px 0 3px; }
.rx-time-right { text-align:right;font-size:13px;font-style:italic;margin-bottom:8px; }
.rx-two-col { display:flex;gap:22px;margin:8px 0; }
.rx-col-left,.rx-col-right { flex:1; }
.rx-section-title { font-weight:700;font-size:13px;text-decoration:underline;margin-bottom:5px; }
.rx-list { list-style:none;padding:0;margin:0; }
.rx-list li { font-size:12.5px;line-height:1.8; }
.rx-list li::before { content:"• "; }
.rx-divider { border:none;border-top:1px solid #c0c0c0;margin:11px 0; }
.rx-baby-note { margin-top:10px;border-top:1px solid #444;padding-top:7px; }
.rx-notes { margin-top:10px;font-size:12px;line-height:1.9;color:#222; }

/* ═══════════════════════════════════════════
   RX MODAL (unchanged)
═══════════════════════════════════════════ */
.rx-modal-content { border:none;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.rx-modal-header { background:linear-gradient(135deg,#1565C0 0%,#1E88E5 100%);border:none;padding:18px 22px;display:flex;align-items:center;justify-content:space-between; }
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
.msi-blue  { background:linear-gradient(135deg,#E3F2FD,#fff); }
.msi-green { background:linear-gradient(135deg,#E8F5E9,#fff); }
.msi-orange{ background:linear-gradient(135deg,#FFF3E0,#fff); }
.msi-teal  { background:linear-gradient(135deg,#E0F2F1,#fff); }
.modal-summary-item > i { font-size:18px;flex-shrink:0; }
.msi-blue  > i { color:var(--blue-deep); }
.msi-green > i { color:var(--green-deep); }
.msi-orange> i { color:var(--orange); }
.msi-teal  > i { color:var(--teal-mid); }
.msi-label { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted); }
.msi-val   { font-size:13px;font-weight:700;color:var(--text-primary);margin-top:1px; }
.modal-state-wrap { text-align:center;padding:50px 20px;color:#90A4AE; }
.modal-spinner-icon { font-size:34px;margin-bottom:12px;color:var(--blue-mid); }
.modal-error-icon { font-size:36px;margin-bottom:10px;color:#ef5350; }
.modal-state-text { font-size:14px;margin:0; }

/* ═══════════════════════════════════════════
   PRINT (unchanged)
═══════════════════════════════════════════ */
#print-overlay { display:none;position:fixed;top:0;left:0;width:100%;min-height:100%;background:#fff;z-index:9999999;padding:10mm 12mm;box-sizing:border-box; }
@media print {
    body * { visibility:hidden; }
    #print-overlay, #print-overlay * { visibility:visible !important; }
    #print-overlay { display:block !important;position:fixed !important;top:0 !important;left:0 !important;width:100% !important;background:#fff !important;padding:10mm 12mm !important;box-sizing:border-box !important; }
    #print-overlay .rx-wrapper { border:1px solid #d4d4d4 !important;max-width:100% !important;padding:20px 26px !important;margin:0 !important;box-shadow:none !important;page-break-inside:avoid; }
    #print-overlay .modern-card-header,#print-overlay .modern-card-footer,#print-overlay .rx-summary-card,#print-overlay .modal-summary-bar,#print-overlay .modal-summary-item { display:none !important;visibility:hidden !important; }
    @page { size:A4 portrait;margin:0; }
}
</style>
@stop

@section('js')
<script>
var CSRF       = '{{ csrf_token() }}';
var STORE_URL  = '{{ url("/nursing/admission/store") }}';
var TPL_URL    = '{{ url("/nursing/admission/apply-template") }}';
var DETAIL_URL = '{{ url("/nursing/admission/detail") }}';

/* ═══ FIXED SEARCH BAR ═══ */
(function initFixedBar() {
    var bar        = document.getElementById('fixed-search-bar');
    var inlineBar  = document.getElementById('inline-search-bar');
    var fixedInput = document.getElementById('patientSearchFixed');
    var inlineInput= document.getElementById('patientSearch');
    if (!bar || !inlineBar) return;
    bar.style.display = '';
    function getSidebarWidth() {
        var sb = document.querySelector('.main-sidebar');
        if (!sb) return 0;
        var r = sb.getBoundingClientRect();
        return r.width > 10 ? r.right : 0;
    }
    function updateBarPosition() {
        bar.style.left  = getSidebarWidth() + 'px';
        bar.style.right = '0';
        bar.style.width = 'auto';
    }
    function onScroll() {
        if (document.getElementById('panel-step1').style.display === 'none') {
            bar.classList.remove('visible'); return;
        }
        var rect = inlineBar.getBoundingClientRect();
        if (rect.bottom <= 0) { updateBarPosition(); bar.classList.add('visible'); }
        else { bar.classList.remove('visible'); }
    }
    if (fixedInput && inlineInput) {
        fixedInput.addEventListener('input', function() { inlineInput.value = this.value; filterTable(); });
        inlineInput.addEventListener('input', function() { fixedInput.value = this.value; });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function() { updateBarPosition(); onScroll(); });
    document.addEventListener('DOMContentLoaded', function() { updateBarPosition(); onScroll(); });
    document.querySelectorAll('[data-widget="pushmenu"]').forEach(function(btn) {
        btn.addEventListener('click', function() { setTimeout(updateBarPosition, 320); });
    });
})();

/* ═══ HELPERS ═══ */
function todayISO() { return new Date().toISOString().split('T')[0]; }
function fmtDateBD(iso) {
    if (!iso) return '—';
    var p = String(iso).split('T')[0].split('-');
    if (p.length < 3) return iso;
    return p[2]+'/'+p[1]+'/'+p[0].slice(2);
}
function fmtTime(t) {
    if (!t) return '—';
    var p = String(t).split(':');
    var hr = parseInt(p[0]);
    if (isNaN(hr)) return t;
    return (hr%12||12)+':'+p[1]+(hr>=12?' pm':' am');
}
function gVal(id) { var e=document.getElementById(id); return e?e.value.trim():''; }
function setText(id,txt) { var e=document.getElementById(id); if(e) e.textContent=txt||'—'; }
function showAlert(type,msg) {
    var el=document.getElementById('save-alert');
    el.className='alert alert-'+type+' modern-alert';
    el.innerHTML=msg; el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){el.classList.add('d-none');},6000);
}
function esc(v){
    return String(v||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ═══ PRINT ═══ */
function _doPrint(sourceId) {
    var source  = document.getElementById(sourceId);
    var overlay = document.getElementById('print-overlay');
    if (!source || !overlay) { window.print(); return; }
    var rxWrapper = source.querySelector('.rx-wrapper');
    var toClone   = rxWrapper || source;
    overlay.innerHTML = '';
    overlay.appendChild(toClone.cloneNode(true));
    overlay.style.display = 'block';
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            window.print();
            var cleanup = function() {
                overlay.style.display = 'none'; overlay.innerHTML = '';
                window.removeEventListener('focus', cleanup);
            };
            window.addEventListener('focus', cleanup);
            setTimeout(function() { overlay.style.display='none'; overlay.innerHTML=''; window.removeEventListener('focus',cleanup); }, 60000);
        });
    });
}
function printRx()    { _doPrint('prescription-print-area'); }
function printModal() { _doPrint('modal-prescription-print-area'); }

/* ═══ BABY SECTION ═══ */
function toggleBabySection(){
    document.getElementById('baby-section-body').classList.toggle('open');
    document.getElementById('baby-chevron').classList.toggle('open');
}

/* ═══ MEDICINE STATE ═══ */
var selectedMeds = [];
function refreshSelTable(){
    var tbody  = document.getElementById('sel-med-tbody');
    var badge1 = document.getElementById('sel-med-badge');
    var badge2 = document.getElementById('med-count-badge');
    badge1.textContent = selectedMeds.length;
    badge2.textContent = selectedMeds.length;
    if (!selectedMeds.length) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="9"><div class="med-empty-state">'+
            '<i class="fas fa-plus-circle"></i><span>No medicines selected yet. Add from the list below.</span></div></td></tr>';
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
            '<td class="text-center"><button type="button" class="btn-quick-add" onclick="removeMed('+i+')" title="Remove">'+
            '<i class="fas fa-times" style="color:#c62828;"></i></button></td></tr>';
    }).join('');
}
function addMedToList(name,dose,route,frequency,duration,timing){
    if (!name||!name.trim()) return;
    if (selectedMeds.find(function(m){return m.medicine_name.toLowerCase()===name.toLowerCase();})) return;
    selectedMeds.push({medicine_name:name,dose:dose||'',route:route||'',frequency:frequency||'',duration:duration||'',timing:timing||'',remarks:''});
    refreshSelTable();
}
function addMedRow(){
    selectedMeds.push({medicine_name:'',dose:'',route:'',frequency:'',duration:'',timing:'',remarks:''});
    refreshSelTable();
}
function removeMed(idx){
    var name = selectedMeds[idx] ? selectedMeds[idx].medicine_name : '';
    selectedMeds.splice(idx,1);
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){ if((cb.dataset.name||'')===name) cb.checked=false; });
    refreshSelTable();
}
function clearAllMeds(){
    if (!selectedMeds.length) return;
    if (!confirm('সব medicine মুছে ফেলবেন?')) return;
    selectedMeds=[];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;});
    var sa=document.getElementById('select-all-med');
    if(sa) sa.checked=false;
    refreshSelTable();
}
function onAvailMedChange(cb){
    if (cb.checked) {
        addMedToList(cb.dataset.name,cb.dataset.dose,cb.dataset.route,cb.dataset.frequency,cb.dataset.duration,cb.dataset.timing);
    } else {
        selectedMeds = selectedMeds.filter(function(m){return m.medicine_name!==cb.dataset.name;});
        refreshSelTable();
    }
}
function quickAdd(btn){
    var cb = btn.closest('tr').querySelector('.avail-med-cb');
    cb.checked = true;
    onAvailMedChange(cb);
}

/* ═══ DOM READY ═══ */
document.addEventListener('DOMContentLoaded', function(){
    var mf = document.getElementById('med-filter');
    if(mf) mf.addEventListener('input', function(){
        var q = this.value.toLowerCase();
        document.querySelectorAll('.avail-med-row').forEach(function(r){
            r.style.display = (r.dataset.name||'').includes(q) ? '' : 'none';
        });
    });
    var sa = document.getElementById('select-all-med');
    if(sa) sa.addEventListener('change', function(){
        document.querySelectorAll('.avail-med-cb').forEach(function(cb){
            cb.checked = sa.checked;
            onAvailMedChange(cb);
        });
    });
    var ps = document.getElementById('patientSearch');
    if(ps) ps.addEventListener('keyup', filterTable);
});

/* ═══ PAST RX TABLE FILTER ═══ */
function filterNursingRxTable(){
    var q = (document.getElementById('nursingRxSearch').value||'').toLowerCase();
    document.querySelectorAll('#nursingRxTable tbody tr.nursing-rx-row').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* ═══ LOAD TEMPLATE ═══ */
function loadTemplate(){
    var id = document.getElementById('f-template-select').value;
    if (!id) { showAlert('warning','Please select a template first.'); return; }
    document.getElementById('tpl-loading').classList.remove('d-none');
    document.getElementById('btn-load-tpl').disabled = true;
    $.ajax({
        url: TPL_URL, method: 'POST',
        data: {_token: CSRF, template_id: id},
        dataType: 'json',
    }).done(function(res){
        if (!res.success) { showAlert('danger', res.message || 'Failed to load template'); return; }
        selectedMeds = [];
        document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;});
        var meds = res.medicines || [];
        if (!meds.length) { refreshSelTable(); showAlert('warning', 'Template loaded but no medicines found.'); return; }
        meds.forEach(function(m){
            var mName = (m.medicine_name || '').trim();
            if (!mName) return;
            selectedMeds.push({ medicine_name:mName, dose:m.dose||'', route:m.route||'', frequency:m.frequency||'', duration:m.duration||'', timing:m.timing||'', remarks:m.remarks||'' });
            document.querySelectorAll('.avail-med-cb').forEach(function(cb){
                if ((cb.dataset.name || '').toLowerCase() === mName.toLowerCase()) cb.checked = true;
            });
        });
        var sel = document.getElementById('f-template-select');
        var tplName = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : 'Template';
        var badge = document.getElementById('tpl-status-badge');
        if (badge) { badge.classList.add('loaded'); badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + tplName; }
        refreshSelTable();
        showAlert('success', '<i class="fas fa-check-circle mr-1"></i> Template loaded: <strong>' + meds.length + ' medicine(s)</strong> added.');
    }).fail(function(xhr){
        showAlert('danger', 'Error: HTTP ' + xhr.status + ' — ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText.substring(0, 200)));
    }).always(function(){
        document.getElementById('tpl-loading').classList.add('d-none');
        document.getElementById('btn-load-tpl').disabled = false;
    });
}

/* ═══ STEP NAVIGATION ═══ */
function selectPatient(btn){
    var d = btn.dataset;
    document.getElementById('f-patient-id').value     = d.id    ||'';
    document.getElementById('f-patient-code').value   = d.code  ||'';
    document.getElementById('f-patient-name').value   = d.name  ||'';
    document.getElementById('f-patient-age').value    = d.age   ||'';
    document.getElementById('f-date').value            = todayISO();
    document.getElementById('f-admission-date').value  = todayISO();
    document.getElementById('spb-avatar').textContent  = (d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent    = d.name||'—';
    document.getElementById('spb-meta').textContent    = [d.code,d.age,d.mobile,d.blood,d.upozila].filter(Boolean).join(' · ');
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
    document.getElementById('fixed-search-bar').classList.remove('visible');
    selectedMeds = [];
    refreshSelTable();
    window.scrollTo({top:0,behavior:'smooth'});
}
function backToStep1(){
    document.getElementById('step1-circle').className   = 'step-circle step-active';
    document.getElementById('step1-circle').textContent = '1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className = 'step-circle step-inactive';
    document.getElementById('step2-label').className  = 'step-label-main step-label-inactive';
    document.getElementById('breadcrumb-current').textContent = 'Select Patient';
    document.getElementById('panel-step1').style.display = 'block';
    document.getElementById('panel-step2').style.display = 'none';
    window.scrollTo({top:0,behavior:'smooth'});
}
function editRx(){
    document.getElementById('rx-view').style.display      = 'none';
    document.getElementById('rx-form-card').style.display = 'block';
}

/* ═══ TABLE FILTER ═══ */
function filterTable(){
    var q = document.getElementById('patientSearch').value.toLowerCase();
    document.getElementById('patientSearchFixed').value = q;
    _doFilter(q);
}
function filterTableFixed(){
    var q = document.getElementById('patientSearchFixed').value.toLowerCase();
    document.getElementById('patientSearch').value = q;
    _doFilter(q);
}
function _doFilter(q){
    document.querySelectorAll('#patientTable tbody tr').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* ═══ SAVE & GENERATE ═══ */
function saveAndGenerate(){
    var patientId = gVal('f-patient-id');
    if (!patientId) { showAlert('warning','No patient selected!'); return; }
    var medsToSave = selectedMeds.filter(function(m){return m.medicine_name.trim()!=='';});
    var btn = document.getElementById('btn-save-rx');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving…';
    var payload = {
        patient_id:patientId, patient_name:gVal('f-patient-name'), patient_age:gVal('f-patient-age'),
        patient_code:gVal('f-patient-code'), pulse:gVal('f-pulse'), bp:gVal('f-bp'),
        rx_date:gVal('f-date'), admission_date:gVal('f-admission-date'),
        admission_time:gVal('f-admission-time'), ot_time:gVal('f-ot-time'),
        pregnancy_weeks:gVal('f-preg-weeks'), baby_sex:gVal('f-baby-sex'),
        baby_weight:gVal('f-baby-weight'), baby_time:gVal('f-baby-time'),
        notes:gVal('f-notes'), admission_type:'on_admission', medicines:medsToSave,
    };
    $.ajax({
        url:STORE_URL, method:'POST', data:JSON.stringify(payload),
        contentType:'application/json', dataType:'json', headers:{'X-CSRF-TOKEN':CSRF},
    }).done(function(res){
        if (!res.success) { showAlert('danger','<i class="fas fa-times-circle mr-1"></i>'+res.message); return; }
        generateRxView(res.admission_id);
        showAlert('success','<i class="fas fa-check-circle mr-1"></i> Prescription saved! ID: <strong>#'+res.admission_id+'</strong>');
    }).fail(function(xhr){
        var msg = xhr.responseJSON&&xhr.responseJSON.message ? xhr.responseJSON.message : 'Save failed (HTTP '+xhr.status+')';
        showAlert('danger','<i class="fas fa-times-circle mr-1"></i>'+msg);
    }).always(function(){
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription';
    });
}
function generateRxView(admissionId){
    setText('ib-name',     gVal('f-patient-name'));
    setText('ib-age',      gVal('f-patient-age'));
    setText('ib-admission',fmtDateBD(gVal('f-admission-date')));
    setText('ib-saved-id', '#'+admissionId);
    setText('rx-name',           gVal('f-patient-name'));
    setText('rx-age',            gVal('f-patient-age'));
    setText('rx-date',           fmtDateBD(gVal('f-date')));
    setText('rx-preg-weeks',     gVal('f-preg-weeks') ? gVal('f-preg-weeks')+' wks' : '');
    setText('rx-admission-time', fmtTime(gVal('f-admission-time')));
    setText('rx-pulse',          gVal('f-pulse'));
    setText('rx-bp',             gVal('f-bp'));
    setText('rx-ot-time',        fmtTime(gVal('f-ot-time')));
    setText('rx-baby-sex',       gVal('f-baby-sex'));
    setText('rx-baby-weight',    gVal('f-baby-weight'));
    setText('rx-baby-time',      fmtTime(gVal('f-baby-time')));
    setText('rx-notes',          gVal('f-notes'));
    var ul = document.getElementById('rx-medicine-list');
    ul.querySelectorAll('li[data-med]').forEach(function(li){li.remove();});
    selectedMeds.filter(function(m){return m.medicine_name.trim();}).forEach(function(m){
        var li = document.createElement('li');
        li.setAttribute('data-med','1');
        li.textContent = [m.medicine_name,m.dose,m.route,m.frequency,
            m.duration?'× '+m.duration:'',m.timing?'('+m.timing+')':''].filter(Boolean).join('  ');
        ul.appendChild(li);
    });
    setText('gen-time', new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}));
    document.getElementById('rx-form-card').style.display = 'none';
    document.getElementById('rx-view').style.display      = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ═══ VIEW PAST PRESCRIPTION MODAL ═══ */
function viewPrescription(admissionId){
    document.getElementById('modal-loading').classList.remove('d-none');
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');
    document.getElementById('modal-subtitle').textContent = 'Loading...';
    $('#rxViewModal').modal('show');
    $.ajax({
        url: DETAIL_URL + '/' + admissionId,
        method: 'GET', dataType: 'json',
    }).done(function(res){
        if (!res.success || !res.data) { showModalError(res.message || 'Record not found.'); return; }
        populateModal(res.data);
    }).fail(function(xhr){
        showModalError('Failed to load prescription (HTTP ' + xhr.status + ')');
    });
}
function showModalError(msg){
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-error').classList.remove('d-none');
    document.getElementById('modal-error-msg').textContent = msg;
}
function populateModal(d){
    document.getElementById('modal-subtitle').textContent =
        (d.patient_name||'—') + '  ·  ' + (d.p_code || d.patient_code || '—');
    setText('m-ib-name',      d.patient_name);
    setText('m-ib-age',       d.patient_age);
    setText('m-ib-admission', fmtDateBD(d.admission_date || d.rx_date));
    setText('m-ib-id',        '#' + d.id);
    setText('m-rx-name', d.patient_name);
    setText('m-rx-age',  d.patient_age);
    setText('m-rx-date', fmtDateBD(d.rx_date || d.admission_date));
    var pw = document.getElementById('m-rx-preg-weeks');
    if (pw) pw.textContent = d.pregnancy_weeks ? d.pregnancy_weeks + ' wks' : '';
    setText('m-rx-admission-time', fmtTime(d.admission_time));
    setText('m-rx-pulse',          d.pulse);
    setText('m-rx-bp',             d.bp);
    setText('m-rx-ot-time',        fmtTime(d.ot_time));
    setText('m-rx-baby-sex',       d.baby_sex);
    setText('m-rx-baby-weight',    d.baby_weight);
    setText('m-rx-baby-time',      fmtTime(d.baby_time));
    setText('m-rx-notes',          d.notes);
    var ul = document.getElementById('m-rx-medicine-list');
    ul.querySelectorAll('li:not([data-static])').forEach(function(li){li.remove();});
    var meds = [];
    if (d.medicines_decoded && Array.isArray(d.medicines_decoded)) { meds = d.medicines_decoded; }
    else if (typeof d.medicines === 'string') { try { meds = JSON.parse(d.medicines); } catch(e) { meds = []; } }
    else if (Array.isArray(d.medicines)) { meds = d.medicines; }
    meds.filter(function(m){ return m && (m.medicine_name||'').trim(); }).forEach(function(m){
        var li = document.createElement('li');
        li.textContent = [m.medicine_name,m.dose,m.route,m.frequency,
            m.duration?'× '+m.duration:'',m.timing?'('+m.timing+')':''].filter(Boolean).join('  ');
        ul.appendChild(li);
    });
    setText('m-saved-time', d.created_at
        ? new Date(d.created_at).toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—');
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.remove('d-none');
}
</script>
@stop