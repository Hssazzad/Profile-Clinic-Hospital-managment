@extends('adminlte::page')

@section('title', 'Round Prescription | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-sync-alt"></i></span>
                Round Prescription
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
                        <div class="step-label-sub step-label-inactive" id="step2-sublabel">Round Prescription</div>
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

    <div class="modern-card patient-list-card" id="patient-list-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-indigo-soft"><i class="fas fa-users text-indigo"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Select Patient for Round Prescription</h5>
                    <small class="text-muted">Search and choose a patient to proceed</small>
                </div>
            </div>
            <span class="patient-total-pill">
                <i class="fas fa-database mr-1"></i>
                {{ $patients->total() ?? $patients->count() }} patients
            </span>
        </div>

        {{-- Inline Search Bar --}}
        <div class="inline-search-bar" id="inline-search-bar">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="patientSearch" class="search-input"
                           placeholder="Search by name, code, or mobile number...">
                    <button class="search-btn search-btn-indigo" type="button" onclick="filterTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
                <a href="https://profclinic.erpbd.org/patients/newpatient"
                   class="btn-add-patient" target="_blank">
                    <i class="fas fa-plus mr-1"></i> Add New Patient
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
                    <tbody>
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
                            <td><span class="patient-code-badge">{{ $pcode }}</span></td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-indigo">{{ strtoupper(substr($pname,0,1)) }}</div>
                                    <div>
                                        <strong>{{ $pname }}</strong>
                                        @if($patient->patientfather ?? null)
                                            <br><small class="text-muted" style="font-size:11px;"><i class="fas fa-user-tie fa-xs mr-1"></i>{{ $patient->patientfather }}</small>
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
                                <button type="button" class="btn-select-patient btn-select-indigo"
                                    onclick="selectPatient(this)"
                                    data-id="{{ $pid }}"
                                    data-name="{{ $pname }}"
                                    data-age="{{ $page }}"
                                    data-code="{{ $pcode }}"
                                    data-mobile="{{ $pmobile }}"
                                    data-upozila="{{ $pupo }}"
                                    data-blood="{{ $pblood }}">
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
                <i class="fas fa-info-circle mr-1 text-indigo"></i>
                Click <i class="fas fa-arrow-right" style="font-size:10px;"></i> on a patient row to proceed.
            </small>
            <a href="https://profclinic.erpbd.org/patients/newpatient"
               class="btn-add-new-patient-sm" target="_blank">
                <i class="fas fa-user-plus mr-1"></i> Add New Patient
            </a>
        </div>
    </div>

    {{-- ══ PAST ROUND PRESCRIPTIONS LIST ══ --}}
    <div class="modern-card past-rx-card mt-2" id="past-rx-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-indigo-soft">
                    <i class="fas fa-sync-alt text-indigo"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Past Round Prescriptions</h5>
                    <small class="text-muted">Click <strong>View</strong> to see prescription or <strong>History</strong> to see all visits</small>
                </div>
            </div>
            <span class="patient-total-pill">
                <i class="fas fa-file-medical mr-1"></i>
                {{ $RoundPatients->total() ?? $RoundPatients->count() }} records
            </span>
        </div>

        <div class="inline-search-bar">
            <div class="inline-search-inner">
                <div class="search-input-group search-input-group-inline" style="flex:1;">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" id="roundRxSearch" class="search-input"
                           placeholder="Search by name, code or mobile..."
                           onkeyup="filterRoundRxTable()">
                    <button class="search-btn search-btn-indigo" type="button" onclick="filterRoundRxTable()">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="patient-table-scroll">
                <table class="table modern-table" id="roundRxTable">
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
                            <th style="width:160px; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="roundRxTableBody">
                        @forelse($RoundPatients as $rp)
                        @php
                            $rpRxId  = $rp->id                ?? '';
                            $rpPtId  = $rp->patient_id        ?? '';
                            $rpCode  = $rp->patient_code      ?? $rp->patientcode ?? '—';
                            $rpName  = $rp->patient_name      ?? $rp->patientname ?? '—';
                            $rpAge   = $rp->patient_age       ?? $rp->age ?? '—';
                            $rpGender= strtolower($rp->gender ?? '');
                            $rpMobile= $rp->mobile_no         ?? '—';
                            $rpBlood = $rp->blood_group       ?? null;
                            $rpRxDate= $rp->prescription_date ?? $rp->created_at ?? '';
                        @endphp
                        <tr class="round-rx-row">
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <span class="patient-code-badge" style="background:#e8eaf6;color:#3949AB;">
                                    #{{ $rpRxId }}
                                </span>
                            </td>
                            <td>
                                <div class="patient-name-cell">
                                    <div class="patient-mini-avatar patient-mini-avatar-indigo">
                                        {{ strtoupper(substr($rpName,0,1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $rpName }}</strong>
                                        <br><small class="text-muted" style="font-size:11px;">{{ $rpCode }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $rpAge }}</td>
                            <td>
                                @if($rpGender==='male')   <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                @elseif($rpGender==='female') <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-monospace small">{{ $rpMobile }}</td>
                            <td class="small text-muted">
                                @if($rpRxDate)
                                    {{ \Carbon\Carbon::parse($rpRxDate)->format('d/m/Y') }}
                                    <br><span style="font-size:10px;">{{ \Carbon\Carbon::parse($rpRxDate)->diffForHumans() }}</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($rpBlood) <span class="blood-badge">{{ $rpBlood }}</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap;">
                                    <button type="button" class="btn-view-rx"
                                        onclick="viewRoundPrescription({{ $rpRxId }})"
                                        title="View this prescription">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </button>
                                    <button type="button" class="btn-history-rx"
                                        onclick="viewPatientHistory({{ $rpPtId }}, '{{ addslashes($rpName) }}', '{{ $rpCode }}')"
                                        title="View all visit history">
                                        <i class="fas fa-history mr-1"></i> History
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-file-medical-alt"></i>
                                    <p>No past round prescriptions found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($RoundPatients,'links'))
            <div class="pagination-bar">
                <small class="text-muted">
                    <i class="fas fa-list-ul mr-1"></i>
                    Showing {{ $RoundPatients->firstItem() ?? 0 }}–{{ $RoundPatients->lastItem() ?? 0 }}
                    of <strong>{{ $RoundPatients->total() ?? 0 }}</strong> records
                </small>
                {{ $RoundPatients->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

</div>{{-- /#panel-step1 --}}

{{-- ══════════════════════════════════════════
     STEP 2 — PRESCRIPTION FORM + VIEW
══════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none;">

    {{-- Selected Patient Bar --}}
    <div class="patient-selected-bar patient-selected-bar-indigo mb-4">
        <div class="psb-left">
            <div class="psb-avatar" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot psb-status-dot-indigo"></span>
            <span class="psb-status-label">Round Prescription</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    {{-- PRESCRIPTION FORM --}}
    <div class="modern-card" id="rx-form-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-indigo-soft"><i class="fas fa-notes-medical text-indigo"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Round Prescription</h5>
                    <small class="text-muted">Add medicines for today's round</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">
            <form id="rx-form">
                @csrf
                <input type="hidden" id="f-patient-id">
                <hr class="section-divider mt-0 mb-4">

                <div class="section-heading mb-3">
                    <i class="fas fa-user-injured mr-2 text-indigo"></i>
                    <span>Patient Information</span>
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
                                        {{ $displayName ?: 'Doctor ID: '.$doc->id }}
                                    </option>
                                @empty
                                    <option value="">No doctors found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">Patient Code</label>
                            <input type="text" class="modern-input" id="f-patient-code" readonly>
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">Patient Name</label>
                            <input type="text" class="modern-input" id="f-patient-name" placeholder="Full name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modern-field-group">
                            <label class="modern-label">Age</label>
                            <input type="text" class="modern-input" id="f-patient-age" placeholder="e.g. 25 yrs">
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">Prescription Date</label>
                            <input type="date" class="modern-input" id="f-date">
                        </div>
                        <div class="modern-field-group">
                            <label class="modern-label">Round Time</label>
                            <div class="input-with-icon">
                                <i class="fas fa-clock input-icon text-indigo"></i>
                                <input type="time" class="modern-input with-icon" id="f-round-time">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="section-divider mt-2 mb-4">

                <div class="section-divider-full mt-2 mb-4">
                    <div class="section-heading mb-0">
                        <i class="fas fa-pills mr-2 text-indigo"></i>
                        <span>Medicines</span>
                        <span class="badge badge-pill ml-2" id="sel-med-count-badge"
                              style="background:#ede7f6;color:#4527a0;font-size:12px;padding:4px 10px;">0</span>
                    </div>
                    <button type="button" class="btn-med-action btn-med-add-indigo" onclick="addBlankRow()">
                        <i class="fas fa-plus mr-1"></i> Add Blank Row
                    </button>
                </div>

                <div class="med-table-card selected-med-card-indigo mb-4">
                    <div class="med-table-card-header" style="background:#f9f7ff;border-bottom-color:#d1c4e9;">
                        <div class="d-flex align-items-center">
                            <span class="med-table-dot" style="background:#7e57c2;"></span>
                            <span class="med-table-title">Selected Medicines</span>
                            <span class="med-count-pill med-count-pill-indigo" id="sel-med-badge">0</span>
                        </div>
                        <button type="button" class="btn-med-clear-indigo" onclick="clearAllMeds()">
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
                                <tr id="empty-sel-row">
                                    <td colspan="9">
                                        <div class="med-empty-state">
                                            <i class="fas fa-pills" style="color:#7e57c2;"></i>
                                            <span>No medicines selected. Click Add Blank Row.</span>
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
                            <span class="med-table-dot" style="background:#1976d2;"></span>
                            <span class="med-table-title">Medicine List</span>
                            <span class="med-count-pill" style="background:#e3f2fd;color:#1565c0;">{{ $medicines->count() }}</span>
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
                                            data-name="{{ e($med->name) }}"
                                            data-strength="{{ e($med->strength ?? '') }}"
                                            data-dose="{{ e($med->dose ?? '') }}"
                                            data-route="{{ e($med->route ?? '') }}"
                                            data-frequency="{{ e($med->dose ?? $med->frequency ?? '') }}"
                                            data-duration="{{ e($med->duration ?? '') }}"
                                            data-timing="{{ e($med->instruction ?? $med->timing ?? '') }}"
                                            data-note="{{ e($med->note ?? '') }}"
                                            onchange="onAvailMedChange(this)">
                                    </td>
                                    <td><span class="avail-med-name">{{ $med->name }}</span></td>
                                    <td><span class="text-muted small">{{ $med->strength ?? '—' }}</span></td>
                                    <td><span class="text-muted small">{{ $med->dose ?? '—' }}</span></td>
                                    <td><span class="text-muted small">{{ $med->frequency ?? '—' }}</span></td>
                                    <td><span class="text-muted small">{{ $med->duration ?? '—' }}</span></td>
                                    <td>
                                        <button type="button" class="btn-quick-add btn-quick-add-indigo"
                                                onclick="quickAddMed(this)" title="Quick Add">
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

                <hr class="section-divider mt-4 mb-4">

                <div class="modern-field-group">
                    <label class="modern-label">Additional Notes</label>
                    <textarea class="modern-input" id="f-notes" rows="2" placeholder="Additional notes..."></textarea>
                </div>

                <div class="form-footer mt-4">
                    <button type="button" class="btn btn-footer-back" onclick="backToStep1()">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>
                    <button type="button" class="btn-footer-save-indigo" id="btn-save" onclick="saveAndGenerateRx()">
                        <i class="fas fa-save mr-1"></i> Save &amp; Generate Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ PRESCRIPTION PRINT VIEW ══ --}}
    <div id="rx-view" style="display:none;">
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-summary-card rx-card-indigo">
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
                    <div class="rx-summary-icon"><i class="fas fa-calendar"></i></div>
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
                    <span class="card-title-icon bg-indigo-soft"><i class="fas fa-notes-medical text-indigo"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Round Prescription</h5>
                        <small class="text-muted">Ready to print</small>
                    </div>
                </div>
                <span class="rx-saved-badge">
                    <i class="fas fa-check-circle mr-1"></i> Saved
                    <span class="ml-1" id="rx-badge-name">—</span>
                </span>
            </div>
            <div class="modern-card-body p-0">
                {{-- ★ PRINT AREA ★ --}}
                <div id="prescription-print-area">
                    <div class="round-wrapper">
                        <div class="round-header">
                            <div class="round-header-left">
                                <div class="round-logo-row">
                                    <div class="round-cp-logo">
                                        <span class="round-cp-c">C</span><span class="round-cp-p">P</span>
                                    </div>
                                    <div>
                                        <div class="round-clinic-bn">প্রফেসর ক্লিনিক</div>
                                        <div class="round-clinic-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                        <div class="round-clinic-phones">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</div>
                                        <div class="round-clinic-phones">০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                                    </div>
                                </div>
                            </div>
                            <div class="round-header-right">
                                <div class="round-doctor-title"  id="rx-doctor-name">—</div>
                                <div class="round-doctor-deg"    id="rx-doctor-speciality"></div>
                                <div class="round-doctor-deg"    id="rx-doctor-regno"></div>
                                <div class="round-doctor-college" id="rx-doctor-posting"></div>
                                <div class="round-doctor-deg"    id="rx-doctor-contact"></div>
                            </div>
                        </div>
                        <div class="round-nad-row">
                            <div class="round-nad-field"><span class="round-nad-label">Code :</span><span class="round-nad-value" id="rx-code">—</span></div>
                            <div class="round-nad-field"><span class="round-nad-label">Name :</span><span class="round-nad-value" id="rx-name">—</span></div>
                            <div class="round-nad-field"><span class="round-nad-label">Age :</span><span class="round-nad-value" id="rx-age">—</span></div>
                            <div class="round-nad-field"><span class="round-nad-label">Date :</span><span class="round-nad-value" id="rx-date">—</span></div>
                        </div>
                        <div class="round-body">
                            <div class="round-rx-symbol">Rx</div>
                            <div class="round-section-label">Round Prescription</div>
                            <div class="round-time-right" id="rx-round-time-display"></div>
                            <div class="round-med-table-wrap">
                                <table class="round-med-table" id="rx-medicine-print-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="round-th-name">ঔষধের নাম</th>
                                            <th colspan="3" class="round-th-group">কখন খাবেন?</th>
                                            <th colspan="2" class="round-th-group">আহারের</th>
                                            <th colspan="3" class="round-th-group">কতদিন?</th>
                                        </tr>
                                        <tr>
                                            <th class="round-th-sub">সকাল</th>
                                            <th class="round-th-sub">দুপুর</th>
                                            <th class="round-th-sub">রাত</th>
                                            <th class="round-th-sub">আগে</th>
                                            <th class="round-th-sub">পরে</th>
                                            <th class="round-th-sub">দিন</th>
                                            <th class="round-th-sub">মাস</th>
                                            <th class="round-th-sub">চলবে</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rx-med-print-tbody"></tbody>
                                </table>
                            </div>
                            <div class="round-rx-notes" id="rx-notes"></div>
                        </div>
                        <div class="round-footer">
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

{{-- ══ PRINT OVERLAY — only ONE, inside @section('content') ══ --}}
<div id="print-overlay"></div>
<div id="history-print-overlay"></div>

{{-- ══ MODAL: SINGLE RX VIEW ══ --}}
<div class="modal fade" id="rxViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">
            <div class="modal-header rx-modal-header-indigo">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3"><i class="fas fa-file-medical"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white">Round Prescription</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:12px;" id="modal-subtitle">Loading...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <button type="button" class="btn-rx-modal-print" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn-rx-modal-close" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="modal-loading" class="modal-state-wrap">
                    <div style="font-size:34px;color:#3949AB;margin-bottom:12px;"><i class="fas fa-spinner fa-spin"></i></div>
                    <p class="modal-state-text">Loading prescription...</p>
                </div>
                <div id="modal-error" class="modal-state-wrap d-none">
                    <div style="font-size:36px;color:#ef5350;margin-bottom:10px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="modal-state-text" id="modal-error-msg">Failed to load prescription.</p>
                </div>
                <div id="modal-rx-area" class="d-none">
                    <div class="modal-summary-bar">
                        <div class="modal-summary-item msi-indigo"><i class="fas fa-user"></i><div><div class="msi-label">Patient</div><div class="msi-val" id="m-ib-name">—</div></div></div>
                        <div class="modal-summary-item msi-green"><i class="fas fa-birthday-cake"></i><div><div class="msi-label">Age</div><div class="msi-val" id="m-ib-age">—</div></div></div>
                        <div class="modal-summary-item msi-orange"><i class="fas fa-calendar"></i><div><div class="msi-label">Date</div><div class="msi-val" id="m-ib-admission">—</div></div></div>
                        <div class="modal-summary-item msi-teal"><i class="fas fa-hashtag"></i><div><div class="msi-label">Rx ID</div><div class="msi-val" id="m-ib-id">—</div></div></div>
                    </div>
                    {{-- ★ MODAL PRINT AREA ★ --}}
                    <div id="modal-prescription-print-area" style="padding:20px 24px;">
                        <div class="round-wrapper">
                            <div class="round-header">
                                <div class="round-header-left">
                                    <div class="round-logo-row">
                                        <div class="round-cp-logo"><span class="round-cp-c">C</span><span class="round-cp-p">P</span></div>
                                        <div>
                                            <div class="round-clinic-bn">প্রফেসর ক্লিনিক</div>
                                            <div class="round-clinic-address">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                            <div class="round-clinic-phones">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="round-header-right">
                                    <div class="round-doctor-title" id="m-rx-doctor-name">—</div>
                                    <div class="round-doctor-deg"   id="m-rx-doctor-deg"></div>
                                </div>
                            </div>
                            <div class="round-nad-row">
                                <div class="round-nad-field"><span class="round-nad-label">Name :</span><span class="round-nad-value" id="m-rx-name">—</span></div>
                                <div class="round-nad-field"><span class="round-nad-label">Age :</span><span class="round-nad-value" id="m-rx-age">—</span></div>
                                <div class="round-nad-field"><span class="round-nad-label">Date :</span><span class="round-nad-value" id="m-rx-date">—</span></div>
                            </div>
                            <div class="round-body">
                                <div class="round-rx-symbol">Rx</div>
                                <div class="round-section-label">Round Prescription</div>
                                <div class="round-med-table-wrap">
                                    <table class="round-med-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="round-th-name">ঔষধের নাম</th>
                                                <th colspan="3" class="round-th-group">কখন খাবেন?</th>
                                                <th colspan="2" class="round-th-group">আহারের</th>
                                                <th colspan="3" class="round-th-group">কতদিন?</th>
                                            </tr>
                                            <tr>
                                                <th class="round-th-sub">সকাল</th><th class="round-th-sub">দুপুর</th><th class="round-th-sub">রাত</th>
                                                <th class="round-th-sub">আগে</th><th class="round-th-sub">পরে</th>
                                                <th class="round-th-sub">দিন</th><th class="round-th-sub">মাস</th><th class="round-th-sub">চলবে</th>
                                            </tr>
                                        </thead>
                                        <tbody id="m-rx-med-tbody"></tbody>
                                    </table>
                                </div>
                                <div class="round-rx-notes" id="m-rx-notes"></div>
                            </div>
                            <div class="round-footer">
                                <span>বিঃ দ্রঃ ............................................</span>
                                <span>............... দিন/মাস পর সাক্ষাৎ করিবেন।</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #e4e9f0;padding:12px 22px;display:flex;align-items:center;justify-content:space-between;">
                <small class="text-muted">Saved: <span id="m-saved-time">—</span></small>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="btn-rx-action btn-rx-print" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ MODAL: PATIENT VISIT HISTORY ══ --}}
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">
            <div class="modal-header rx-modal-header-indigo">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3"><i class="fas fa-history"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white" id="history-modal-title">Visit History</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:12px;" id="history-modal-sub">Loading...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <button type="button" class="btn-rx-modal-print" onclick="printHistory()">
                        <i class="fas fa-print mr-1"></i> Print All
                    </button>
                    <button type="button" class="btn-rx-modal-close" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="history-loading" class="modal-state-wrap">
                    <div style="font-size:34px;color:#3949AB;margin-bottom:12px;"><i class="fas fa-spinner fa-spin"></i></div>
                    <p class="modal-state-text">Loading visit history...</p>
                </div>
                <div id="history-error" class="modal-state-wrap d-none">
                    <div style="font-size:36px;color:#ef5350;margin-bottom:10px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="modal-state-text" id="history-error-msg">Failed to load history.</p>
                </div>
                <div id="history-area" class="d-none">
                    <div class="history-patient-bar" id="history-patient-bar"></div>
                    <div class="history-visit-strip" id="history-visit-strip"></div>
                    <div id="history-timeline" style="padding:20px 24px 10px;"></div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #e4e9f0;padding:12px 22px;display:flex;align-items:center;justify-content:space-between;">
                <small class="text-muted" id="history-footer-info">—</small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════ ROOT ═══════════════════════ */
:root {
    --indigo-deep:  #283593; --indigo-mid: #3949AB; --indigo-light: #E8EAF6; --indigo-soft: #C5CAE9;
    --blue-deep:    #1565C0; --blue-mid: #1976D2; --blue-light: #E3F2FD;
    --orange:       #E65100; --teal: #00695C; --green-deep: #2E7D32;
    --text-primary: #1a2332; --text-muted: #6b7a90; --border: #e4e9f0;
    --radius-sm: 6px; --radius-md: 10px; --radius-lg: 16px;
    --shadow-sm: 0 1px 4px rgba(0,0,0,.06); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --font-base: 'DM Sans','Hind Siliguri',Arial,sans-serif;
}
body,.content-wrapper { background:#f0f0f6 !important; font-family:var(--font-base); }
.text-indigo { color:var(--indigo-mid) !important; }

/* PAGE HEADER */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--indigo-light);display:inline-flex;align-items:center;justify-content:center;color:var(--indigo-mid);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--indigo-light);border-color:var(--indigo-mid);color:var(--indigo-deep); }

/* STEP TRACK */
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:16px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s;border:2.5px solid transparent; }
.step-active   { background:var(--indigo-mid);color:#fff;border-color:var(--indigo-mid);box-shadow:0 0 0 4px rgba(57,73,171,.15); }
.step-done     { background:var(--indigo-deep);color:#fff;border-color:var(--indigo-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub  { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--indigo-mid); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--indigo-deep); }

/* FIXED SEARCH BAR */
.fixed-search-bar { position:fixed;top:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#283593 0%,#3949AB 100%);box-shadow:0 4px 24px rgba(40,53,147,.35);transform:translateY(-100%);transition:transform .3s cubic-bezier(.4,0,.2,1),opacity .3s;opacity:0;pointer-events:none; }
.fixed-search-bar.visible { transform:translateY(0);opacity:1;pointer-events:all; }
.fixed-search-inner { display:flex;align-items:center;gap:16px;padding:10px 20px;flex-wrap:wrap; }
.fixed-search-brand { display:flex;align-items:center;gap:8px;flex-shrink:0; }
.fsc-dot { width:8px;height:8px;border-radius:50%;background:#9fa8da;box-shadow:0 0 0 3px rgba(159,168,218,.3); }
.fsc-label { color:rgba(255,255,255,.9);font-size:13px;font-weight:700;white-space:nowrap; }
.fixed-search-field { flex:1;min-width:240px; }
.fixed-search-meta { flex-shrink:0; }
.fsc-count-pill { background:rgba(255,255,255,.18);color:#fff;border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600;white-space:nowrap; }

/* ALERT */
.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }

/* MODERN CARD */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-indigo-soft { background:var(--indigo-light); }
.modern-card-body { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }
.patient-total-pill { background:var(--indigo-light);color:var(--indigo-deep);border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600; }
.past-rx-card { border-top:3px solid var(--indigo-mid); }

/* INLINE SEARCH BAR */
.inline-search-bar { padding:14px 24px;background:#fafbff;border-bottom:2px solid var(--indigo-soft); }
.inline-search-inner { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.search-input-group { display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s;box-shadow:var(--shadow-sm); }
.search-input-group:focus-within { border-color:var(--indigo-mid);box-shadow:0 0 0 3px rgba(57,73,171,.1); }
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
.search-btn-indigo { background:var(--indigo-mid);color:#fff; }
.search-btn-indigo:hover { background:var(--indigo-deep); }
.btn-add-patient { background:var(--indigo-light);color:var(--indigo-deep);border:1.5px solid var(--indigo-soft);border-radius:var(--radius-sm);padding:9px 18px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;transition:all .2s; }
.btn-add-patient:hover { background:var(--indigo-mid);color:#fff; }
.btn-add-new-patient-sm { font-size:12px;background:var(--indigo-light);color:var(--indigo-deep);border:1.5px solid var(--indigo-soft);border-radius:var(--radius-sm);padding:5px 12px;text-decoration:none;display:inline-flex;align-items:center;transition:all .2s; }
.btn-add-new-patient-sm:hover { background:var(--indigo-mid);color:#fff; }

/* PATIENT TABLE */
.patient-table-scroll { overflow-x:auto;overflow-y:auto;max-height:calc(100vh - 340px); }
.modern-table { border-collapse:separate;border-spacing:0;width:100%; }
.modern-table thead tr th { background:#f0f0f8;color:var(--text-primary);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding:11px 14px;border-bottom:2px solid var(--indigo-soft);white-space:nowrap;position:sticky;top:0;z-index:10; }
.modern-table tbody tr { transition:background .15s; }
.modern-table tbody tr:hover { background:#f0f0f8; }
.modern-table tbody td { padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text-primary);vertical-align:middle; }
.patient-code-badge { background:var(--indigo-light);color:var(--indigo-deep);border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700;font-family:monospace; }
.patient-name-cell { display:flex;align-items:center;gap:8px; }
.patient-mini-avatar { width:30px;height:30px;border-radius:50%;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.patient-mini-avatar-indigo { background:linear-gradient(135deg,var(--indigo-deep),#5c6bc0); }
.gender-badge { display:inline-flex;align-items:center;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.gender-male   { background:#e3f2fd;color:var(--blue-deep); }
.gender-female { background:#fce4ec;color:#880e4f; }
.blood-badge   { background:#ffebee;color:#c62828;border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700; }
.btn-select-patient { border:none;border-radius:var(--radius-sm);width:34px;height:34px;font-size:13px;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;justify-content:center; }
.btn-select-indigo { background:var(--indigo-mid);color:#fff;box-shadow:0 2px 6px rgba(57,73,171,.25); }
.btn-select-indigo:hover { background:var(--indigo-deep);transform:translateY(-1px); }
.btn-view-rx { background:linear-gradient(135deg,var(--indigo-mid),#5c6bc0);color:#fff;border:none;border-radius:var(--radius-sm);padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;box-shadow:0 2px 6px rgba(57,73,171,.22); }
.btn-view-rx:hover { background:linear-gradient(135deg,var(--indigo-deep),var(--indigo-mid));transform:translateY(-1px); }
.btn-history-rx { background:linear-gradient(135deg,#00695C,#00897B);color:#fff;border:none;border-radius:var(--radius-sm);padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;box-shadow:0 2px 6px rgba(0,105,92,.22); }
.btn-history-rx:hover { background:linear-gradient(135deg,#004D40,#00695C);transform:translateY(-1px); }
.empty-state { text-align:center;padding:40px;color:#b0bec5; }
.empty-state i { font-size:36px;margin-bottom:10px;display:block; }
.empty-state p { font-size:14px;margin:0; }
.pagination-bar { display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-top:1.5px solid var(--border);flex-wrap:wrap;gap:8px; }
.pagination { margin-bottom:0; }
.page-link { border-radius:var(--radius-sm) !important;border-color:var(--border);color:var(--indigo-mid);font-size:13px; }
.page-item.active .page-link { background:var(--indigo-mid);border-color:var(--indigo-mid); }

/* STEP 2 ELEMENTS */
.patient-selected-bar { border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px; }
.patient-selected-bar-indigo { background:linear-gradient(135deg,#283593 0%,#3949AB 100%);box-shadow:0 4px 18px rgba(40,53,147,.18); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.psb-status-dot-indigo { background:#9fa8da;box-shadow:0 0 0 3px rgba(159,168,218,.3); }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28);color:#fff; }

/* FORM FIELDS */
.section-heading { display:flex;align-items:center;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px; }
.section-divider { border:none;border-top:1.5px solid var(--border); }
.section-divider-full { border-top:1.5px solid var(--border);padding-top:18px;display:flex;align-items:center;justify-content:space-between; }
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--indigo-mid);box-shadow:0 0 0 3px rgba(57,73,171,.1); }
.input-with-icon { position:relative; }
.input-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none; }
.modern-input.with-icon { padding-left:30px; }

/* MEDICINE TABLES */
.btn-med-action { border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center; }
.btn-med-add-indigo { background:var(--indigo-light);color:var(--indigo-deep);border-color:var(--indigo-soft); }
.btn-med-add-indigo:hover { background:var(--indigo-mid);color:#fff; }
.btn-med-clear-indigo { background:transparent;border:none;color:#c62828;font-size:12px;font-weight:600;cursor:pointer;padding:4px 10px;border-radius:var(--radius-sm);transition:all .18s;display:inline-flex;align-items:center;gap:4px; }
.btn-med-clear-indigo:hover { background:#ffebee; }
.med-table-card { border-radius:var(--radius-md);border:1.5px solid var(--border);overflow:hidden;box-shadow:var(--shadow-sm); }
.selected-med-card-indigo { border-color:var(--indigo-soft); }
.med-table-card-header { padding:11px 16px;background:#f9fafb;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between; }
.med-table-dot { width:8px;height:8px;border-radius:50%;margin-right:8px;flex-shrink:0; }
.med-table-title { font-size:13px;font-weight:700;color:var(--text-primary); }
.med-count-pill { border-radius:20px;padding:2px 10px;font-size:11.5px;font-weight:700;margin-left:8px; }
.med-count-pill-indigo { background:var(--indigo-light);color:var(--indigo-deep); }
.med-table { border-collapse:collapse;width:100%; }
.med-table thead tr th { background:#f5f7fa;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);padding:9px 12px;border-bottom:1.5px solid var(--border);white-space:nowrap; }
.med-table tbody td { padding:8px 12px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.med-table tbody tr:last-child td { border-bottom:none; }
.med-table tbody tr:hover { background:#f0f0f8; }
.med-table .form-control { padding:4px 8px !important;font-size:12.5px !important; }
.med-empty-state { text-align:center;color:#b0bec5;padding:22px;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px; }
.avail-med-name { font-weight:600;color:var(--text-primary);font-size:13px; }
.avail-filter-wrap { display:flex;align-items:center;background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .2s; }
.avail-filter-wrap:focus-within { border-color:var(--indigo-mid); }
.avail-filter-icon { padding:0 9px;color:#aab;font-size:12px; }
.avail-filter-input { border:none;outline:none;padding:6px 4px;font-size:13px;background:transparent;width:170px; }
.modern-checkbox { width:15px;height:15px;accent-color:var(--indigo-mid);cursor:pointer; }
.btn-quick-add { width:26px;height:26px;border-radius:6px;border:1.5px solid transparent;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .18s; }
.btn-quick-add-indigo { background:var(--indigo-light);color:var(--indigo-deep);border-color:var(--indigo-soft); }
.btn-quick-add-indigo:hover { background:var(--indigo-deep);color:#fff; }

/* FORM FOOTER */
.form-footer { display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1.5px solid var(--border); }
.btn-footer-back { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:600;transition:all .2s; }
.btn-footer-back:hover { background:#f0f4f8;color:var(--text-primary); }
.btn-footer-save-indigo { background:linear-gradient(135deg,#283593,#3949AB);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(40,53,147,.28);transition:all .2s;display:inline-flex;align-items:center;gap:7px; }
.btn-footer-save-indigo:hover { background:linear-gradient(135deg,#1a237e,#283593);transform:translateY(-1px);color:#fff; }

/* RX SUMMARY CARDS */
.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-indigo { background:linear-gradient(135deg,#283593,#3949AB); }
.rx-card-teal   { background:linear-gradient(135deg,#00695C,#00897B); }
.rx-card-orange { background:linear-gradient(135deg,#E65100,#F57C00); }
.rx-card-blue   { background:linear-gradient(135deg,#1565C0,#1976D2); }
.rx-summary-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.22);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.rx-summary-label { color:rgba(255,255,255,.75);font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;font-weight:600; }
.rx-summary-value { color:#fff;font-size:14px;font-weight:700;margin-top:2px; }
.rx-saved-badge { background:var(--indigo-light);color:var(--indigo-deep);border:1.5px solid var(--indigo-soft);border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center; }
.btn-rx-action { border-radius:var(--radius-sm);padding:8px 18px;font-size:13px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-print { background:var(--blue-deep);color:#fff;border-color:var(--blue-deep); }
.btn-rx-print:hover { background:var(--blue-mid); }
.btn-rx-edit  { background:#fff7e0;color:#e65100;border-color:#ffe082; }
.btn-rx-edit:hover { background:#e65100;color:#fff; }
.btn-rx-new   { background:#f0f4f8;color:var(--text-primary);border-color:var(--border); }
.btn-rx-new:hover { background:#e8ecf2; }

/* MODALS */
.rx-modal-content { border:none;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.rx-modal-header-indigo { background:linear-gradient(135deg,#283593 0%,#3949AB 100%);border:none;padding:18px 22px;display:flex;align-items:center;justify-content:space-between; }
.rx-modal-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.2);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.btn-rx-modal-print { background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-modal-print:hover { background:rgba(255,255,255,.32); }
.btn-rx-modal-close { background:rgba(255,255,255,.15);border:none;color:rgba(255,255,255,.85);width:32px;height:32px;border-radius:50%;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s; }
.btn-rx-modal-close:hover { background:rgba(255,255,255,.28);color:#fff; }
.modal-state-wrap { text-align:center;padding:50px 20px;color:#90A4AE; }
.modal-state-text { font-size:14px;margin:0; }
.modal-summary-bar { display:flex;border-bottom:1px solid var(--border); }
.modal-summary-item { flex:1;padding:14px 18px;display:flex;align-items:center;gap:10px;border-right:1px solid var(--border); }
.modal-summary-item:last-child { border-right:none; }
.msi-indigo { background:linear-gradient(135deg,var(--indigo-light),#fff); }
.msi-green  { background:linear-gradient(135deg,#E8F5E9,#fff); }
.msi-orange { background:linear-gradient(135deg,#FFF3E0,#fff); }
.msi-teal   { background:linear-gradient(135deg,#E0F2F1,#fff); }
.modal-summary-item > i { font-size:18px;flex-shrink:0; }
.msi-indigo > i { color:var(--indigo-mid); }
.msi-green  > i { color:var(--green-deep); }
.msi-orange > i { color:var(--orange); }
.msi-teal   > i { color:#00796B; }
.msi-label { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted); }
.msi-val   { font-size:13px;font-weight:700;color:var(--text-primary);margin-top:1px; }

/* VISIT HISTORY */
.history-patient-bar { background:linear-gradient(90deg,#e8eaf6,#f5f5ff);padding:14px 24px;border-bottom:1px solid var(--indigo-soft);display:flex;align-items:center;gap:14px;flex-wrap:wrap; }
.hpb-avatar { width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--indigo-deep),#5c6bc0);color:#fff;font-size:18px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.hpb-name { font-size:16px;font-weight:700;color:var(--indigo-deep); }
.hpb-meta { font-size:12px;color:var(--text-muted);margin-top:2px; }
.history-visit-strip { background:#fff;border-bottom:1px solid var(--border);padding:10px 24px;display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
.hvs-label { font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-right:4px; }
.hvs-pill { background:var(--indigo-light);color:var(--indigo-deep);border-radius:20px;padding:4px 14px;font-size:12.5px;font-weight:700;border:1.5px solid var(--indigo-soft); }
.visit-timeline { position:relative; }
.visit-timeline::before { content:'';position:absolute;left:22px;top:0;bottom:0;width:2px;background:var(--indigo-soft);z-index:0; }
.visit-card { position:relative;margin-bottom:20px;padding-left:56px; }
.visit-dot { position:absolute;left:14px;top:18px;width:18px;height:18px;border-radius:50%;background:var(--indigo-mid);border:3px solid #fff;box-shadow:0 0 0 2px var(--indigo-soft);z-index:1;display:flex;align-items:center;justify-content:center; }
.visit-dot-latest { background:var(--green-deep);box-shadow:0 0 0 2px #a5d6a7; }
.visit-dot span { color:#fff;font-size:8px;font-weight:700; }
.visit-box { background:#fff;border-radius:var(--radius-md);border:1.5px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden; }
.visit-box-latest { border-color:var(--indigo-soft);box-shadow:0 4px 16px rgba(57,73,171,.12); }
.visit-header { background:linear-gradient(90deg,var(--indigo-light),#f5f5ff);padding:12px 18px;border-bottom:1px solid var(--indigo-soft);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px; }
.visit-number { background:var(--indigo-mid);color:#fff;border-radius:5px;padding:2px 10px;font-size:11.5px;font-weight:700; }
.visit-number-latest { background:var(--green-deep); }
.visit-date { font-size:13px;font-weight:700;color:var(--indigo-deep); }
.visit-doctor { font-size:12px;color:var(--text-muted);margin-top:1px; }
.visit-badge-latest { background:#e8f5e9;color:var(--green-deep);border:1.5px solid #a5d6a7;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:700; }
.visit-body { padding:14px 18px; }
.visit-med-table { width:100%;border-collapse:collapse;font-size:12px;font-family:'Hind Siliguri',Arial,sans-serif; }
.visit-med-table th { background:#f0f0f8;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);padding:6px 10px;border:1px solid #ddd;text-align:center;white-space:nowrap; }
.visit-med-table th:first-child { text-align:left; }
.visit-med-table td { padding:6px 10px;border:1px solid #e8e8e8;vertical-align:middle;text-align:center; }
.visit-med-table td:first-child { text-align:left;font-weight:500; }
.visit-med-table tbody tr:nth-child(even) { background:#fafafa; }
.visit-med-table tbody tr:hover { background:#f0f0f8; }
.visit-notes { font-size:12px;color:var(--text-muted);margin-top:10px;padding-top:8px;border-top:1px dashed #ddd;font-style:italic; }
.visit-print-btn { background:var(--indigo-light);color:var(--indigo-deep);border:1.5px solid var(--indigo-soft);border-radius:var(--radius-sm);padding:4px 12px;font-size:11.5px;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:4px; }
.visit-print-btn:hover { background:var(--indigo-mid);color:#fff; }
.no-meds-note { color:#b0bec5;font-size:12px;font-style:italic;padding:10px 0; }

/* ═══════════════════════ RX PRINT LAYOUT ═══════════════════════ */
#prescription-print-area { padding:0;background:#fff; }
.round-wrapper { width:100%;max-width:780px;margin:0 auto;background:#fff;border:1px solid #ccc;font-family:'Hind Siliguri',Arial,sans-serif;font-size:12px; }
.round-header { display:flex;justify-content:space-between;align-items:flex-start;background:linear-gradient(135deg,#e8eaf6 0%,#c5cae9 100%) !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;border-bottom:2px solid #5c6bc0;padding:12px 16px 10px;gap:10px; }
.round-header-left { flex:1; }
.round-header-right { text-align:right;border-left:2px solid #5c6bc0;padding-left:12px;flex:1; }
.round-logo-row { display:flex;align-items:center;gap:10px; }
.round-cp-logo { width:46px;height:46px;border-radius:50%;border:2px solid #c0392b;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;flex-shrink:0;background:#fff !important;-webkit-print-color-adjust:exact; }
.round-cp-c { color:#c0392b; } .round-cp-p { color:#3949ab; }
.round-clinic-bn { font-size:22px;font-weight:700;color:#2c3e50;line-height:1.1; }
.round-clinic-address { font-size:11px;color:#444;margin-top:2px; }
.round-clinic-phones { font-size:10px;color:#555; }
.round-doctor-title  { font-size:14px;font-weight:700;color:#c0392b; }
.round-doctor-deg    { font-size:10.5px;color:#2c3e50; }
.round-doctor-college { font-size:10px;color:#c0392b;margin-top:2px; }
.round-nad-row { display:flex;flex-wrap:wrap;background:#eef0ff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;padding:6px 16px;border-bottom:1px solid #c5cae9;gap:6px 10px; }
.round-nad-field { display:flex;align-items:center;gap:6px;flex:1;min-width:130px; }
.round-nad-label { font-weight:700;font-size:12px;white-space:nowrap; }
.round-nad-value { border-bottom:1px dotted #999;flex:1;padding:0 4px;font-size:12px;min-width:60px; }
.round-body { padding:10px 16px;min-height:280px; }
.round-rx-symbol { font-size:28px;font-weight:900;font-style:italic;color:#2c3e50;margin-bottom:2px; }
.round-section-label { text-align:center;font-weight:700;font-size:13px;text-decoration:underline;margin:4px 0 3px;color:#283593; }
.round-time-right { text-align:right;font-size:13px;font-style:italic;margin-bottom:6px; }
.round-rx-notes { font-size:11px;color:#444;white-space:pre-wrap;margin-top:8px; }
.round-footer { display:flex;justify-content:space-between;border-top:1px solid #ccc;padding:6px 16px;font-size:11px;background:#eef0ff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;color:#555; }
.round-med-table-wrap { overflow-x:auto;margin-top:4px; }
.round-med-table { width:100%;border-collapse:collapse;font-family:'Hind Siliguri',Arial,sans-serif;font-size:11.5px; }
.round-med-table th,.round-med-table td { border:1px solid #888;padding:4px 5px;text-align:center;vertical-align:middle; }
.round-th-name { text-align:left !important;width:38%;background:#dde0f7 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;font-size:12px;font-weight:700;vertical-align:middle !important; }
.round-th-group { background:#e8e8e8 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;font-weight:700;font-size:11px; }
.round-th-sub { background:#f2f2f2 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;font-size:10.5px;font-weight:700;min-width:32px; }
.round-med-table tbody tr td { font-size:11.5px; }
.round-med-table tbody tr td:first-child { text-align:left;padding-left:6px;font-weight:500; }
.round-med-table tbody tr.empty-row td { height:24px; }
.round-med-table tbody tr:nth-child(even) td { background:#fafafa !important;-webkit-print-color-adjust:exact; }

/* ═══════════════════════════════════════════════════════
   ★ PRINT CSS — FIXED
   visibility toggling — flex/table layout সংরক্ষিত থাকে
   display:block!important দেওয়া হয় না (এটাই ছিল মূল সমস্যা)
═══════════════════════════════════════════════════════ */
/* Print overlay base style */
#print-overlay, #history-print-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; min-height: 100%;
    background: #fff;
    z-index: 9999999;
    padding: 8mm 10mm;
    box-sizing: border-box;
}

@media print {
    /* Hide all page content */
    body * {
        visibility: hidden;
    }
    
    /* Show only print overlays */
    #print-overlay,
    #history-print-overlay {
        visibility: visible !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: #fff !important;
        padding: 10mm !important;
        box-sizing: border-box !important;
        z-index: 9999999 !important;
    }
    
    /* Ensure content inside overlays is visible */
    #print-overlay *,
    #history-print-overlay * {
        visibility: visible !important;
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
var CSRF_TOKEN = '{{ csrf_token() }}';
var ROUND_STORE_URLS = [
    '{{ url("/nursing/roundprescription/store") }}',
    '{{ url("/nursing/RoundPrescription/store") }}',
];
var ROUND_DETAIL_URL  = '/nursing/roundprescription/detail/';
var ROUND_HISTORY_URL = '/nursing/roundprescription/patient-history/';

var selectedMeds = [];

/* ─── HELPERS ─── */
function todayISO(){ return new Date().toISOString().split('T')[0]; }
function fmtDateBD(iso){ if(!iso) return '—'; var p=String(iso).slice(0,10).split('-'); return p[2]+'/'+p[1]+'/'+p[0].slice(2); }
function fmtTime(t){ if(!t) return '—'; var p=String(t).split(':'); var hr=parseInt(p[0]); if(isNaN(hr)) return t; return (hr%12||12)+':'+p[1]+(hr>=12?' pm':' am'); }
function gVal(id){ var el=document.getElementById(id); return el?el.value.trim():''; }
function setText(id,v){ var el=document.getElementById(id); if(el) el.textContent=(v!==null&&v!==undefined&&String(v).trim()!=='')?v:'—'; }
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
function showAlert(type,msg){ var el=document.getElementById('save-alert'); el.className='alert alert-'+type+' modern-alert'; el.innerHTML=msg; el.classList.remove('d-none'); window.scrollTo({top:0,behavior:'smooth'}); setTimeout(function(){el.classList.add('d-none');},6000); }
function showToast(msg,type){ var bg=type==='success'?'#283593':(type==='info'?'#0288d1':'#e65100'); var t=document.createElement('div'); t.style.cssText='position:fixed;bottom:20px;right:20px;z-index:9999;background:'+bg+';color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;box-shadow:0 4px 12px rgba(0,0,0,.2);max-width:320px;'; t.innerHTML='<i class="fas fa-check-circle mr-2"></i>'+msg; document.body.appendChild(t); setTimeout(function(){ t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(function(){ t.remove(); },300); },2500); }

/* ═══════════════════════════════════════════
   ★ PRINT HELPERS — FIXED
   Strategy: clone শুধু .round-wrapper
   visibility toggling — display কে touch করা হয় না
   Double rAF — browser render নিশ্চিত করে
═══════════════════════════════════════════ */
function _doPrint(sourceId, overlayId) {
    var src = document.getElementById(sourceId);
    var ovl = document.getElementById(overlayId || 'print-overlay');
    
    if (!src || !ovl) {
        console.error('Print elements not found:', {sourceId, source: !!src, overlayId, overlay: !!ovl});
        window.print();
        return;
    }
    
    console.log('Starting print process for:', sourceId);
    
    // Clone the content
    ovl.innerHTML = '';
    var cloned = src.cloneNode(true);
    ovl.appendChild(cloned);
    ovl.style.display = 'block';
    
    // Wait for content to render, then print
    setTimeout(function() {
        console.log('Triggering print dialog');
        window.print();
        
        // Clean up after print dialog closes
        setTimeout(function() {
            ovl.style.display = 'none';
            ovl.innerHTML = '';
            console.log('Print overlay cleaned up');
        }, 1000);
    }, 500);
}

function printRx()    { _doPrint('prescription-print-area', 'print-overlay'); }
function printModal() { _doPrint('modal-prescription-print-area', 'print-overlay'); }
function printHistory() {
    var src = document.getElementById('history-timeline');
    var ovl = document.getElementById('history-print-overlay');
    if (!src || !ovl) { window.print(); return; }
    ovl.innerHTML =
        '<div style="font-family:\'Hind Siliguri\',Arial,sans-serif;padding:0;">'+
        '<div style="text-align:center;border-bottom:2px solid #3949AB;padding-bottom:8px;margin-bottom:12px;">'+
            '<strong style="font-size:18px;color:#283593;">প্রফেসর ক্লিনিক — Round Prescription History</strong><br>'+
            '<span style="font-size:13px;color:#555;">'+document.getElementById('history-modal-sub').textContent+'</span>'+
        '</div>'+src.innerHTML+'</div>';
    ovl.style.display = 'block';
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            window.print();
            var cleanup = function () { ovl.style.display='none'; ovl.innerHTML=''; window.removeEventListener('focus',cleanup); };
            window.addEventListener('focus', cleanup);
            setTimeout(function(){ ovl.style.display='none'; ovl.innerHTML=''; window.removeEventListener('focus',cleanup); }, 60000);
        });
    });
}

/* ─── FIXED SEARCH BAR ─── */
(function initFixedBar(){
    var bar       = document.getElementById('fixed-search-bar');
    var inlineBar = document.getElementById('inline-search-bar');
    var fixedInput= document.getElementById('patientSearchFixed');
    var inlineInput=document.getElementById('patientSearch');
    if(!bar||!inlineBar) return;
    bar.style.display='';
    function getSidebarWidth(){ var sb=document.querySelector('.main-sidebar'); if(!sb) return 0; var r=sb.getBoundingClientRect(); return r.width>10?r.right:0; }
    function updatePos(){ bar.style.left=getSidebarWidth()+'px'; bar.style.right='0'; bar.style.width='auto'; }
    function onScroll(){
        if(document.getElementById('panel-step1').style.display==='none'){ bar.classList.remove('visible'); return; }
        var rect=inlineBar.getBoundingClientRect();
        if(rect.bottom<=0){ updatePos(); bar.classList.add('visible'); }
        else { bar.classList.remove('visible'); }
    }
    if(fixedInput&&inlineInput){
        fixedInput.addEventListener('input',function(){ inlineInput.value=this.value; filterTable(); });
        inlineInput.addEventListener('input',function(){ fixedInput.value=this.value; });
    }
    window.addEventListener('scroll', onScroll, {passive:true});
    window.addEventListener('resize', function(){ updatePos(); onScroll(); });
    document.addEventListener('DOMContentLoaded', function(){ updatePos(); onScroll(); });
    document.querySelectorAll('[data-widget="pushmenu"]').forEach(function(btn){
        btn.addEventListener('click', function(){ setTimeout(updatePos,320); });
    });
})();

/* ─── MEDICINE TABLE ─── */
function refreshSelTable(){
    var tbody=document.getElementById('sel-med-tbody');
    document.getElementById('sel-med-badge').textContent=selectedMeds.length;
    document.getElementById('sel-med-count-badge').textContent=selectedMeds.length;
    if(!selectedMeds.length){
        tbody.innerHTML='<tr id="empty-sel-row"><td colspan="9"><div class="med-empty-state"><i class="fas fa-pills" style="color:#7e57c2;"></i><span>No medicines selected. Click Add Blank Row.</span></div></td></tr>';
        return;
    }
    tbody.innerHTML=selectedMeds.map(function(m,i){
        return '<tr>'+
        '<td>'+(i+1)+'</td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.medicine_name)+'" onchange="selectedMeds['+i+'].medicine_name=this.value"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.dose)+'" onchange="selectedMeds['+i+'].dose=this.value"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.route)+'" onchange="selectedMeds['+i+'].route=this.value"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.frequency)+'" onchange="selectedMeds['+i+'].frequency=this.value" placeholder="1+0+1"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.duration)+'" onchange="selectedMeds['+i+'].duration=this.value" placeholder="7 days"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.timing)+'" onchange="selectedMeds['+i+'].timing=this.value" placeholder="before/after"></td>'+
        '<td><input type="text" class="form-control form-control-sm" value="'+esc(m.remarks)+'" onchange="selectedMeds['+i+'].remarks=this.value" placeholder="Optional"></td>'+
        '<td class="text-center"><button type="button" class="btn-quick-add" style="background:#ffebee;color:#c62828;border-color:#ffcdd2;width:26px;height:26px;" onclick="removeMed('+i+')"><i class="fas fa-times"></i></button></td>'+
        '</tr>';
    }).join('');
}
function addMedToList(n,d,r,f,dur,t,note){
    if(!n||!n.trim()) return;
    if(selectedMeds.find(function(m){return m.medicine_name.toLowerCase()===n.toLowerCase();})) return;
    selectedMeds.push({medicine_name:n,dose:d||'',route:r||'',frequency:f||'',duration:dur||'',timing:t||'',remarks:note||''});
    refreshSelTable();
}
function addBlankRow(){ selectedMeds.push({medicine_name:'',dose:'',route:'',frequency:'',duration:'',timing:'',remarks:''}); refreshSelTable(); }
function removeMed(idx){ var name=selectedMeds[idx]?selectedMeds[idx].medicine_name:''; selectedMeds.splice(idx,1); document.querySelectorAll('.avail-med-cb').forEach(function(cb){if((cb.dataset.name||'')===name)cb.checked=false;}); refreshSelTable(); }
function clearAllMeds(){ if(!selectedMeds.length) return; if(!confirm('সব ওষুধ মুছে ফেলতে চান?')) return; selectedMeds=[]; document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;}); refreshSelTable(); }
function onAvailMedChange(cb){ if(cb.checked){addMedToList(cb.dataset.name,cb.dataset.dose,cb.dataset.route,cb.dataset.frequency,cb.dataset.duration,cb.dataset.timing,cb.dataset.note);}else{selectedMeds=selectedMeds.filter(function(m){return m.medicine_name!==cb.dataset.name;});refreshSelTable();} }
function quickAddMed(btn){ var cb=btn.closest('tr').querySelector('.avail-med-cb'); cb.checked=true; onAvailMedChange(cb); }
function filterCheckboxList(q){ var lower=q.toLowerCase().trim(); document.querySelectorAll('.avail-med-row').forEach(function(r){r.style.display=(!lower||(r.dataset.name||'').includes(lower))?'':'none';}); }

/* ─── DOCTOR HEADER ─── */
function updateDoctorHeader(){
    var sel=document.getElementById('f-doctor'); if(!sel||!sel.options.length) return;
    var d=sel.options[sel.selectedIndex].dataset;
    setText('rx-doctor-name',       d.docname   ||'');
    setText('rx-doctor-speciality', d.speciality||'');
    setText('rx-doctor-regno',      d.doctorno  ?'Reg No: '+d.doctorno:'');
    setText('rx-doctor-posting',    d.posting   ||'');
    setText('rx-doctor-contact',    d.contact   ?'Mobile: '+d.contact:'');
    setText('ib-doctor',            d.docname   ||'—');
}

/* ─── SELECT PATIENT ─── */
function selectPatient(btn){
    var d=btn.dataset;
    document.getElementById('f-patient-id').value   = d.id;
    document.getElementById('f-patient-code').value = d.code;
    document.getElementById('f-patient-name').value = d.name;
    document.getElementById('f-patient-age').value  = d.age;
    document.getElementById('f-date').value          = todayISO();
    document.getElementById('f-round-time').value   = '';
    document.getElementById('spb-avatar').textContent=(d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent =d.name;
    document.getElementById('spb-meta').textContent =[d.code,d.age,d.mobile,d.blood,d.upozila].filter(Boolean).join(' · ');

    document.getElementById('step1-circle').className='step-circle step-done';
    document.getElementById('step1-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className='step-circle step-active';
    document.getElementById('step2-label').className='step-label-main step-label-active';
    document.getElementById('step2-sublabel').className='step-label-sub';
    document.getElementById('breadcrumb-current').textContent='Round Prescription';

    document.getElementById('panel-step1').style.display='none';
    document.getElementById('panel-step2').style.display='block';
    document.getElementById('rx-view').style.display='none';
    document.getElementById('rx-form-card').style.display='block';
    document.getElementById('fixed-search-bar').classList.remove('visible');

    selectedMeds=[];
    document.querySelectorAll('.avail-med-cb').forEach(function(cb){cb.checked=false;});
    refreshSelTable();
    updateDoctorHeader();
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ─── SAVE ─── */
function saveAndGenerateRx(){
    var patientId=gVal('f-patient-id');
    if(!patientId){ showAlert('warning','Please select a patient first.'); return; }
    var medsToSave=selectedMeds.filter(function(m){return m.medicine_name.trim()!=='';});
    var doctorSel=document.getElementById('f-doctor');
    var doctorName=doctorSel&&doctorSel.options.length?doctorSel.options[doctorSel.selectedIndex].dataset.docname||'':'';
    var payload={
        patient_id:patientId, patient_name:gVal('f-patient-name'),
        patient_age:gVal('f-patient-age'), patient_code:gVal('f-patient-code'),
        doctor_name:doctorName, prescription_date:gVal('f-date'),
        round_time:gVal('f-round-time'), notes:gVal('f-notes'), medicines:medsToSave
    };
    var btn=document.getElementById('btn-save');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
    function trySave(idx){
        if(idx>=ROUND_STORE_URLS.length){ btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save & Generate Prescription'; generateRx(); return; }
        fetch(ROUND_STORE_URLS[idx],{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify(payload)})
        .then(function(r){ if(r.status===404){trySave(idx+1); return null;} return r.json(); })
        .then(function(data){
            btn.disabled=false; btn.innerHTML='<i class="fas fa-save mr-1"></i> Save & Generate Prescription';
            if(!data) return;
            generateRx();
            if(data.success) showToast('Saved! ID: #'+data.prescription_id,'success');
        })
        .catch(function(){trySave(idx+1);});
    }
    trySave(0);
}

/* ─── GENERATE PRINT VIEW ─── */
function generateRx(){
    var pName=gVal('f-patient-name')||'—'; var pAge=gVal('f-patient-age')||'—';
    var pDate=fmtDateBD(gVal('f-date')); var pCode=gVal('f-patient-code')||'—';
    setText('ib-name',pName); setText('ib-age',pAge); setText('ib-date',pDate);
    setText('rx-name',pName); setText('rx-age',pAge); setText('rx-date',pDate);
    setText('rx-code',pCode); setText('rx-badge-name',pName); setText('rx-notes',gVal('f-notes')||'');
    var rt=fmtTime(gVal('f-round-time'));
    var el=document.getElementById('rx-round-time-display');
    if(el) el.textContent=(rt!=='—')?'Round Time: '+rt:'';
    updateDoctorHeader();
    renderRxMedicines();
    setText('gen-time',new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}));
    document.getElementById('step2-circle').className='step-circle step-done';
    document.getElementById('step2-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('rx-form-card').style.display='none';
    document.getElementById('rx-view').style.display='block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ─── PARSE MEDICINE → TABLE ROW ─── */
function parseMedRow(m){
    var morning='',noon='',night='';
    var freq=(m.frequency||'').trim();
    var dp=freq.match(/^([^+]+)\+([^+]+)\+([^+]+)/);
    if(dp){morning=dp[1].trim();noon=dp[2].trim();night=dp[3].trim();}
    else if(freq){morning=freq;}
    var timing=(m.timing||'').toLowerCase();
    var before='',after='';
    if(timing.includes('before')||timing.includes('আগে')){before='✓';}
    else if(timing.includes('after')||timing.includes('পরে')){after='✓';}
    else if(timing){after='✓';}
    var days='',months='',cont='';
    var dur=(m.duration||'').toLowerCase().trim();
    if(dur.includes('cont')||dur.includes('চলবে')||dur==='ongoing'){cont='✓';}
    else if(dur.includes('month')||dur.includes('মাস')){months=dur.replace(/[^0-9]/g,'')||'1';}
    else if(dur){days=dur.replace(/[^0-9]/g,'')||dur;}
    var name=(m.route?m.route+' ':'')+m.medicine_name;
    return {name:name,morning:morning,noon:noon,night:night,before:before,after:after,days:days,months:months,cont:cont};
}

function renderRxMedicines(){
    var tbody=document.getElementById('rx-med-print-tbody'); if(!tbody) return; tbody.innerHTML='';
    var meds=selectedMeds.filter(function(m){return m.medicine_name.trim();});
    meds.forEach(function(m){
        var r=parseMedRow(m);
        var tr=document.createElement('tr');
        tr.innerHTML='<td style="text-align:left;padding-left:6px;">• '+esc(r.name)+'</td><td>'+esc(r.morning)+'</td><td>'+esc(r.noon)+'</td><td>'+esc(r.night)+'</td><td>'+esc(r.before)+'</td><td>'+esc(r.after)+'</td><td>'+esc(r.days)+'</td><td>'+esc(r.months)+'</td><td>'+esc(r.cont)+'</td>';
        tbody.appendChild(tr);
    });
    var needed=Math.max(0,8-meds.length);
    for(var i=0;i<needed;i++){
        var tr=document.createElement('tr'); tr.className='empty-row';
        tr.innerHTML='<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
        tbody.appendChild(tr);
    }
}

function buildMedTableHTML(meds){
    if(!meds||!meds.length) return '<p class="no-meds-note">No medicines recorded for this visit.</p>';
    var rows=meds.filter(function(m){return m&&(m.medicine_name||'').trim();}).map(function(m){
        var r=parseMedRow(m);
        return '<tr><td style="text-align:left;padding-left:6px;">• '+esc(r.name)+'</td><td>'+esc(r.morning)+'</td><td>'+esc(r.noon)+'</td><td>'+esc(r.night)+'</td><td>'+esc(r.before)+'</td><td>'+esc(r.after)+'</td><td>'+esc(r.days)+'</td><td>'+esc(r.months)+'</td><td>'+esc(r.cont)+'</td></tr>';
    }).join('');
    if(!rows) return '<p class="no-meds-note">No medicines recorded.</p>';
    return '<table class="visit-med-table">'+
        '<thead><tr><th rowspan="2" style="text-align:left;min-width:160px;">ঔষধের নাম</th><th colspan="3">কখন খাবেন?</th><th colspan="2">আহারের</th><th colspan="3">কতদিন?</th></tr>'+
        '<tr><th>সকাল</th><th>দুপুর</th><th>রাত</th><th>আগে</th><th>পরে</th><th>দিন</th><th>মাস</th><th>চলবে</th></tr></thead>'+
        '<tbody>'+rows+'</tbody></table>';
}

/* ─── NAVIGATION ─── */
function backToStep1(){
    document.getElementById('step1-circle').className='step-circle step-active';
    document.getElementById('step1-circle').textContent='1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className='step-circle step-inactive';
    document.getElementById('step2-label').className='step-label-main step-label-inactive';
    document.getElementById('step2-sublabel').className='step-label-sub step-label-inactive';
    document.getElementById('breadcrumb-current').textContent='Select Patient';
    document.getElementById('panel-step1').style.display='block';
    document.getElementById('panel-step2').style.display='none';
    window.scrollTo({top:0,behavior:'smooth'});
}
function editRx(){
    document.getElementById('rx-view').style.display='none';
    document.getElementById('rx-form-card').style.display='block';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ─── TABLE FILTERS ─── */
function filterTable(){ var q=document.getElementById('patientSearch').value.toLowerCase(); document.getElementById('patientSearchFixed').value=q; _doFilter(q); }
function filterTableFixed(){ var q=document.getElementById('patientSearchFixed').value.toLowerCase(); document.getElementById('patientSearch').value=q; _doFilter(q); }
function _doFilter(q){ document.querySelectorAll('#patientTable tbody tr').forEach(function(row){row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';}); }
function filterRoundRxTable(){ var q=(document.getElementById('roundRxSearch').value||'').toLowerCase(); document.querySelectorAll('#roundRxTable tbody tr.round-rx-row').forEach(function(row){row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';}); }

document.getElementById('patientSearch').addEventListener('keyup', filterTable);
document.addEventListener('DOMContentLoaded', function(){ updateDoctorHeader(); });

/* ─── VIEW SINGLE Rx MODAL ─── */
function viewRoundPrescription(id){
    document.getElementById('modal-loading').classList.remove('d-none');
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');
    document.getElementById('modal-subtitle').textContent='Loading...';
    $('#rxViewModal').modal('show');
    $.ajax({url:ROUND_DETAIL_URL+id,method:'GET',dataType:'json'})
    .done(function(res){ if(!res.success||!res.data){showModalError(res.message||'Record not found.');return;} populateSingleModal(res.data); })
    .fail(function(xhr){ showModalError('Failed to load prescription (HTTP '+xhr.status+')'); });
}
function showModalError(msg){ document.getElementById('modal-loading').classList.add('d-none'); document.getElementById('modal-error').classList.remove('d-none'); document.getElementById('modal-error-msg').textContent=msg; }
function populateSingleModal(d){
    document.getElementById('modal-subtitle').textContent=(d.patient_name||'—')+' · '+(d.patient_code||d.p_code||'—');
    setText('m-ib-name',d.patient_name); setText('m-ib-age',d.patient_age); setText('m-ib-admission',fmtDateBD(d.prescription_date||d.created_at)); setText('m-ib-id','#'+d.id);
    setText('m-rx-name',d.patient_name); setText('m-rx-age',d.patient_age); setText('m-rx-date',fmtDateBD(d.prescription_date||d.created_at));
    setText('m-rx-doctor-name',d.doctor_name||''); setText('m-rx-doctor-deg',''); setText('m-rx-notes',d.notes||'');
    var meds=Array.isArray(d.medicines_decoded)?d.medicines_decoded:(typeof d.medicines==='string'?JSON.parse(d.medicines||'[]'):(Array.isArray(d.medicines)?d.medicines:[]));
    var tbody=document.getElementById('m-rx-med-tbody'); tbody.innerHTML='';
    meds.filter(function(m){return m&&(m.medicine_name||'').trim();}).forEach(function(m){
        var r=parseMedRow(m); var tr=document.createElement('tr');
        tr.innerHTML='<td style="text-align:left;padding-left:6px;">• '+esc(r.name)+'</td><td>'+esc(r.morning)+'</td><td>'+esc(r.noon)+'</td><td>'+esc(r.night)+'</td><td>'+esc(r.before)+'</td><td>'+esc(r.after)+'</td><td>'+esc(r.days)+'</td><td>'+esc(r.months)+'</td><td>'+esc(r.cont)+'</td>';
        tbody.appendChild(tr);
    });
    var needed=Math.max(0,8-meds.length);
    for(var i=0;i<needed;i++){var tr=document.createElement('tr'); tr.className='empty-row'; tr.innerHTML='<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>'; tbody.appendChild(tr);}
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.remove('d-none');
}

/* ─── PATIENT HISTORY MODAL ─── */
function viewPatientHistory(patientId, patientName, patientCode){
    document.getElementById('history-loading').classList.remove('d-none');
    document.getElementById('history-error').classList.add('d-none');
    document.getElementById('history-area').classList.add('d-none');
    document.getElementById('history-modal-title').textContent='Visit History';
    document.getElementById('history-modal-sub').textContent=(patientName||'Patient')+' · '+(patientCode||'');
    document.getElementById('history-timeline').innerHTML='';
    document.getElementById('history-patient-bar').innerHTML='';
    document.getElementById('history-visit-strip').innerHTML='';
    document.getElementById('history-footer-info').textContent='—';
    $('#historyModal').modal('show');
    $.ajax({url:ROUND_HISTORY_URL+patientId,method:'GET',dataType:'json'})
    .done(function(res){ if(!res.success){showHistoryError(res.message||'No history found.');return;} renderHistory(res,patientName,patientCode); })
    .fail(function(xhr){ showHistoryError(xhr.status===404?'History route নেই। Route add করুন: GET /nursing/roundprescription/patient-history/{patientId}':'Failed to load history (HTTP '+xhr.status+')'); });
}
function showHistoryError(msg){ document.getElementById('history-loading').classList.add('d-none'); document.getElementById('history-error').classList.remove('d-none'); document.getElementById('history-error-msg').textContent=msg; }

function renderHistory(res, fallbackName, fallbackCode){
    var patient=res.patient||{};
    var prescriptions=res.prescriptions||[];
    var pName=patient.name||fallbackName||'—'; var pCode=patient.code||fallbackCode||'—';
    var pAge=patient.age||'—'; var pMobile=patient.mobile_no||'—'; var pBlood=patient.blood_group||'';

    document.getElementById('history-patient-bar').innerHTML=
        '<div class="hpb-avatar">'+esc(pName.charAt(0).toUpperCase())+'</div>'+
        '<div><div class="hpb-name">'+esc(pName)+'</div><div class="hpb-meta">'+[pCode,pAge,pMobile,pBlood].filter(Boolean).map(esc).join(' · ')+'</div></div>'+
        '<div style="margin-left:auto;"><span style="background:#e8eaf6;color:#3949AB;border:1.5px solid #c5cae9;border-radius:8px;padding:6px 16px;font-size:13px;font-weight:700;"><i class="fas fa-history mr-1"></i>'+prescriptions.length+' Visit'+(prescriptions.length!==1?'s':'')+'</span></div>';

    if(prescriptions.length){
        var stripHTML='<span class="hvs-label">Visits:</span>';
        prescriptions.forEach(function(rx,i){
            if(i>0) stripHTML+='<i class="fas fa-chevron-right" style="color:var(--indigo-soft);font-size:12px;"></i>';
            var isLatest=(i===prescriptions.length-1);
            stripHTML+='<span class="hvs-pill" style="'+(isLatest?'background:#e8f5e9;color:#2e7d32;border-color:#a5d6a7;':'')+'"><i class="fas fa-sync-alt mr-1" style="font-size:10px;"></i>'+(i+1)+(isLatest?' (Latest)':'')+'</span>';
        });
        document.getElementById('history-visit-strip').innerHTML=stripHTML;
    }

    var timelineEl=document.getElementById('history-timeline');
    if(!prescriptions.length){
        timelineEl.innerHTML='<div class="empty-state"><i class="fas fa-file-medical-alt"></i><p>No round prescriptions found.</p></div>';
    } else {
        var timelineDiv=document.createElement('div'); timelineDiv.className='visit-timeline';
        prescriptions.forEach(function(rx,i){
            var visitNum=i+1; var isLatest=(i===prescriptions.length-1);
            var rxDate=fmtDateBD(rx.prescription_date||rx.created_at);
            var rxTime=rx.round_time?(' <span style="font-size:11px;color:var(--text-muted);">'+fmtTime(rx.round_time)+'</span>'):'';
            var doctor=rx.doctor_name||'—'; var notes=rx.notes||'';
            var meds=rx.medicines||[]; if(typeof meds==='string'){try{meds=JSON.parse(meds);}catch(e){meds=[];}}
            var medCount=meds.filter(function(m){return m&&(m.medicine_name||'').trim();}).length;
            var card=document.createElement('div'); card.className='visit-card';
            var dotEl=document.createElement('div'); dotEl.className='visit-dot'+(isLatest?' visit-dot-latest':''); dotEl.innerHTML='<span>'+visitNum+'</span>';
            var boxEl=document.createElement('div'); boxEl.className='visit-box'+(isLatest?' visit-box-latest':'');
            boxEl.innerHTML=
                '<div class="visit-header'+(isLatest?' visit-header-latest':'')+'">'+
                    '<div>'+
                        '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">'+
                            '<span class="visit-number'+(isLatest?' visit-number-latest':'')+'">Visit '+visitNum+'</span>'+
                            (isLatest?'<span class="visit-badge-latest"><i class="fas fa-star mr-1" style="font-size:9px;"></i>Latest</span>':'')+
                        '</div>'+
                        '<div class="visit-date mt-1">'+esc(rxDate)+rxTime+'</div>'+
                        '<div class="visit-doctor"><i class="fas fa-user-md mr-1" style="color:var(--indigo-mid);"></i>'+esc(doctor)+'</div>'+
                    '</div>'+
                    '<div style="display:flex;align-items:center;gap:8px;">'+
                        '<span style="background:#ede7f6;color:#4527a0;border-radius:12px;padding:3px 12px;font-size:11.5px;font-weight:600;"><i class="fas fa-pills mr-1" style="font-size:10px;"></i>'+medCount+' med'+(medCount!==1?'s':'')+'</span>'+
                        '<button class="visit-print-btn" onclick="printSingleVisit('+rx.id+')"><i class="fas fa-print"></i> Print</button>'+
                    '</div>'+
                '</div>'+
                '<div class="visit-body">'+buildMedTableHTML(meds)+(notes?'<div class="visit-notes"><i class="fas fa-comment-medical mr-1"></i>'+esc(notes)+'</div>':'')+'</div>';
            card.appendChild(dotEl); card.appendChild(boxEl); timelineDiv.appendChild(card);
        });
        timelineEl.appendChild(timelineDiv);
    }

    document.getElementById('history-footer-info').textContent=
        prescriptions.length+' total visit'+(prescriptions.length!==1?'s':'')+
        (prescriptions.length?' | First: '+fmtDateBD(prescriptions[0].prescription_date||prescriptions[0].created_at)+' | Latest: '+fmtDateBD(prescriptions[prescriptions.length-1].prescription_date||prescriptions[prescriptions.length-1].created_at):'');

    document.getElementById('history-loading').classList.add('d-none');
    document.getElementById('history-area').classList.remove('d-none');
}

function printSingleVisit(rxId){ $('#historyModal').modal('hide'); setTimeout(function(){ viewRoundPrescription(rxId); },400); }
</script>
@stop