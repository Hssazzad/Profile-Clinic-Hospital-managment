@extends('adminlte::page')

@section('title', 'Discharge | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-sign-out-alt"></i></span>
                Discharge
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
                        <div class="step-label-sub step-label-inactive" id="step2-sublabel">Discharge</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SAVE ALERT ══ --}}
<div id="save-alert" class="alert d-none mb-3 modern-alert" role="alert"></div>

{{-- ══════════════════════════════════════════
     STEP 1 — SELECT PATIENT (GOV-PANEL)
══════════════════════════════════════════ --}}
<div id="panel-step1">

    <div class="gov-panel gov-panel-warning">

        {{-- Panel Title Bar --}}
        <div class="gov-panel-titlebar gov-panel-titlebar-warning">
            <div class="gov-panel-titlebar-left">
                <div class="gov-panel-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="gov-panel-title">Patient Selection — Discharge</div>
                    <div class="gov-panel-subtitle">Search and select an admitted patient to proceed with discharge</div>
                </div>
            </div>
            <div class="gov-panel-titlebar-right">
                <span class="gov-counter-badge">
                    <i class="fas fa-procedures mr-1"></i>
                    Total Records: <strong>{{ $patients->total() }}</strong>
                </span>
            </div>
        </div>

        {{-- Search Toolbar --}}
        <div class="gov-toolbar gov-toolbar-warning">
            <div class="gov-toolbar-inner">
                <div class="gov-toolbar-label gov-toolbar-label-warning">
                    <i class="fas fa-search mr-1"></i> SEARCH FILTER
                </div>
                <div class="gov-search-group">
                    <input type="text" id="patientSearch" class="gov-search-input gov-search-input-warning"
                           placeholder="Search by Name / Patient Code / Mobile Number…"
                           onkeyup="filterTable()">
                    <button class="gov-search-btn gov-search-btn-warning" type="button" onclick="filterTable()">
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
                        <th class="gov-th" style="width:72px;">Blood</th>
                        <th class="gov-th" style="width:112px;">Admission</th>
                        <th class="gov-th" style="width:90px;">Fresh Rx</th>
                        <th class="gov-th gov-th-action" style="width:76px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        @php
                            $freshCount = \Illuminate\Support\Facades\DB::table('nursing_fresh_prescriptions')
                                ->where('patient_id', $patient->id)->count();
                            $g = strtolower($patient->gender ?? '');
                        @endphp
                        <tr class="gov-tr">
                            <td class="gov-td gov-td-sl">{{ $patient->id }}</td>
                            <td class="gov-td">
                                <span class="gov-code-badge gov-code-badge-warning">{{ $patient->patientcode ?? '—' }}</span>
                            </td>
                            <td class="gov-td">
                                <div class="gov-name-cell">
                                    <div class="gov-avatar gov-avatar-warning">
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
                            <td class="gov-td gov-td-center">
                                @if($patient->blood_group ?? null)
                                    <span class="gov-blood-badge">{{ $patient->blood_group }}</span>
                                @else
                                    <span class="gov-muted">—</span>
                                @endif
                            </td>
                            <td class="gov-td">
                                @if($patient->admission_date ?? null)
                                    <span class="gov-date-text">
                                        <i class="fas fa-calendar-alt mr-1" style="color:var(--warning-mid);font-size:10px;"></i>
                                        {{ \Carbon\Carbon::parse($patient->admission_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="gov-muted">—</span>
                                @endif
                            </td>
                            <td class="gov-td gov-td-center">
                                @if($freshCount > 0)
                                    <span class="gov-fresh-done">
                                        <i class="fas fa-check-circle mr-1"></i>{{ $freshCount }} Rx
                                    </span>
                                @else
                                    <span class="gov-fresh-none">No Rx</span>
                                @endif
                            </td>
                            <td class="gov-td gov-td-action">
                                <button type="button"
                                    class="gov-select-btn gov-select-btn-warning"
                                    onclick="selectPatient(this)"
                                    data-id="{{ $patient->id }}"
                                    data-name="{{ $patient->patientname ?? '' }}"
                                    data-age="{{ $patient->age ?? '' }}"
                                    data-code="{{ $patient->patientcode ?? '' }}"
                                    data-mobile="{{ $patient->mobile_no ?? '' }}"
                                    data-upozila="{{ $patient->upozila ?? '' }}"
                                    data-blood="{{ $patient->blood_group ?? '' }}"
                                    data-admission-id="{{ $patient->admission_id ?? '' }}"
                                    data-admission-date="{{ $patient->admission_date ?? '' }}"
                                    title="Select this patient">
                                    <i class="fas fa-arrow-right mr-1"></i> Select
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="gov-empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No patients found.</p>
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
                of <strong>{{ $patients->total() }}</strong> records
            </div>
            <div class="gov-pagination-wrap">
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>
            <div class="gov-footer-hint">
                <i class="fas fa-hand-pointer mr-1"></i>
                Click <strong>Select</strong> to proceed to Discharge
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ══ STEP 2 ══ --}}
<div id="panel-step2" style="display:none;">

    <div class="patient-selected-bar patient-selected-bar-warning mb-4">
        <div class="psb-left">
            <div class="psb-avatar psb-avatar-warning" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot psb-status-dot-yellow"></span>
            <span class="psb-status-label">Discharge</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    <div id="history-loading" class="admission-status-bar admission-loading-bar" style="display:none;">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        রোগীর ইতিহাস লোড হচ্ছে...
    </div>

    {{-- Fresh Rx history --}}
    <div id="fresh-info-box" class="modern-card mb-4" style="display:none; border-left:4px solid #2e7d32;">
        <div style="background:linear-gradient(135deg,#2e7d32 0%,#43a047 100%);padding:12px 20px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-leaf text-white" style="font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;">
                            Fresh Prescription Records
                            <span class="badge badge-warning ml-2" id="fresh-count-badge" style="font-size:11px;"></span>
                        </div>
                        <div style="color:rgba(255,255,255,0.7);font-size:11px;">রোগীর Fresh Prescription ইতিহাস</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm"
                    style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:11px;"
                    onclick="toggleFreshDetails()">
                    <i class="fas fa-chevron-down mr-1" id="fresh-toggle-icon"></i>
                    <span id="fresh-toggle-text">Details Hide</span>
                </button>
            </div>
        </div>
        <div id="fresh-details-body" style="background:#fff;padding:16px 20px;">
            <div id="fresh-history-list"></div>
        </div>
    </div>

    {{-- Round Rx history --}}
    <div id="round-info-box" class="modern-card mb-4" style="display:none; border-left:4px solid #1565c0;">
        <div style="background:linear-gradient(135deg,#1565c0 0%,#1976d2 100%);padding:12px 20px;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-sync text-white" style="font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-weight:700;font-size:14px;">
                            Round Prescription Records
                            <span class="badge badge-warning ml-2" id="round-count-badge" style="font-size:11px;"></span>
                        </div>
                        <div style="color:rgba(255,255,255,0.7);font-size:11px;">রোগীর Round Prescription ইতিহাস</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm"
                    style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:11px;"
                    onclick="toggleRoundDetails()">
                    <i class="fas fa-chevron-down mr-1" id="round-toggle-icon"></i>
                    <span id="round-toggle-text">Details Hide</span>
                </button>
            </div>
        </div>
        <div id="round-details-body" style="background:#fff;padding:16px 20px;">
            <div id="round-history-list"></div>
        </div>
    </div>

    {{-- ══ DISCHARGE FORM ══ --}}
    <div class="modern-card" id="discharge-form-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-warning-soft"><i class="fas fa-sign-out-alt text-warning"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Discharge Patient</h5>
                    <small class="text-muted">Confirm discharge details</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">
            <input type="hidden" id="f-patient-id">
            <input type="hidden" id="f-admission-id">
            <input type="hidden" id="f-admission-date-raw">

            <div class="section-heading mb-3">
                <i class="fas fa-user-injured mr-2 text-warning"></i>
                <span>Patient Information</span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="modern-field-group">
                        <label class="modern-label">Patient Code</label>
                        <input type="text" class="modern-input" id="f-patient-code" readonly>
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">Patient Name</label>
                        <input type="text" class="modern-input" id="f-patient-name" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-field-group">
                        <label class="modern-label">Age</label>
                        <input type="text" class="modern-input" id="f-patient-age" readonly>
                    </div>
                    <div class="modern-field-group">
                        <label class="modern-label">Discharge Date</label>
                        <input type="date" class="modern-input" id="f-discharge-date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="modern-field-group">
                <label class="modern-label">Discharge Notes</label>
                <textarea class="modern-input" id="f-notes" rows="3" placeholder="Discharge notes..."></textarea>
            </div>

            <div class="form-footer mt-4">
                <button type="button" class="btn btn-footer-back" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" class="btn btn-footer-save-warning" id="btn-discharge" onclick="confirmDischarge()">
                    <i class="fas fa-sign-out-alt mr-1"></i> Confirm Discharge
                </button>
            </div>
        </div>
    </div>

    {{-- ══ DISCHARGE SUCCESS VIEW ══ --}}
    <div id="discharge-view" style="display:none;">
        <div class="modern-card no-print">
            <div class="modern-card-header">
                <div class="modern-card-title">
                    <span class="card-title-icon bg-warning-soft"><i class="fas fa-check-circle text-warning"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Discharge Successful</h5>
                        <small class="text-muted">Patient has been discharged</small>
                    </div>
                </div>
                <span class="rx-saved-badge rx-saved-badge-warning">
                    <i class="fas fa-check-circle mr-1"></i> Discharged
                    <span class="ml-1" id="discharged-badge-name">—</span>
                </span>
            </div>
            <div class="modern-card-body">
                <div class="row mb-4">
                    <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                        <div class="rx-summary-card rx-card-warning">
                            <div class="rx-summary-icon"><i class="fas fa-user"></i></div>
                            <div class="rx-summary-content">
                                <div class="rx-summary-label">Patient</div>
                                <div class="rx-summary-value" id="dv-name">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                        <div class="rx-summary-card rx-card-teal">
                            <div class="rx-summary-icon"><i class="fas fa-birthday-cake"></i></div>
                            <div class="rx-summary-content">
                                <div class="rx-summary-label">Age</div>
                                <div class="rx-summary-value" id="dv-age">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                        <div class="rx-summary-card rx-card-orange">
                            <div class="rx-summary-icon"><i class="fas fa-calendar"></i></div>
                            <div class="rx-summary-content">
                                <div class="rx-summary-label">Discharge Date</div>
                                <div class="rx-summary-value" id="dv-date">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="rx-summary-card rx-card-blue">
                            <div class="rx-summary-icon"><i class="fas fa-id-card"></i></div>
                            <div class="rx-summary-content">
                                <div class="rx-summary-label">Code</div>
                                <div class="rx-summary-value" id="dv-code">—</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    Patient <strong id="dv-name2">—</strong> has been successfully discharged on <strong id="dv-date2">—</strong>.
                </div>
            </div>
            <div class="modern-card-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Discharged at: <span id="discharge-time">—</span>
                </small>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-rx-action btn-rx-new" onclick="backToStep1()">
                        <i class="fas fa-plus mr-1"></i> New Discharge
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ DISCHARGE PAPERS SECTION ══ --}}
        <div class="modern-card no-print">
            <div class="modern-card-header">
                <div class="modern-card-title">
                    <span class="card-title-icon" style="background:#e8f5e9;"><i class="fas fa-file-medical" style="color:#2e7d32;"></i></span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Discharge Papers</h5>
                        <small class="text-muted">Preview — click Print to send to printer</small>
                    </div>
                </div>
                <button type="button" class="btn-dc-print-btn" onclick="printDischargePapers()">
                    <i class="fas fa-print mr-2"></i>Print All Papers (4 পাতা)
                </button>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    {{-- Card 1 --}}
                    <div class="col-md-6 mb-3">
                        <div class="discharge-paper-card">
                            <div class="discharge-paper-header">
                                <h6 class="mb-0">ডিসচার্জ পেপার</h6>
                                <small style="opacity:.8;">Discharge Paper</small>
                            </div>
                            <div class="discharge-paper-content">
                                <div class="clinic-header">
                                    <h4 class="text-center mb-1">প্রফেসর ক্লিনিক</h4>
                                    <p class="text-center mb-1" style="font-size:12px;color:#555;">মাঝিড়া, শাজাহানপুর, বগুড়া</p>
                                    <p class="text-center mb-3" style="font-size:11px;color:#777;">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</p>
                                </div>
                                <div class="patient-info-grid">
                                    <div class="info-row"><span class="info-label">রোগীর নাম:</span><span class="dotted-line" id="dp-patient-name"></span></div>
                                    <div class="info-row"><span class="info-label">বয়স:</span><span class="dotted-line" id="dp-age"></span></div>
                                    <div class="info-row"><span class="info-label">ভর্তির তারিখ:</span><span class="dotted-line" id="dp-admission-date"></span></div>
                                    <div class="info-row"><span class="info-label">ছাড়পত্রের তারিখ:</span><span class="dotted-line" id="dp-discharge-date"></span></div>
                                </div>
                                <div class="doctor-signature mt-4">
                                    <div class="signature-line">
                                        <span style="font-size:12px;color:#555;">চিকিৎসকের স্বাক্ষর</span>
                                        <div class="line"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Card 2 --}}
                    <div class="col-md-6 mb-3">
                        <div class="discharge-paper-card">
                            <div class="discharge-paper-header">
                                <h6 class="mb-0">অপারেশন নোট</h6>
                                <small style="opacity:.8;">Operation Note</small>
                            </div>
                            <div class="discharge-paper-content">
                                <div class="operation-note-fields">
                                    <div class="op-field"><label class="op-label">অপারেশন নাম ও তাং</label><div class="op-input-area"></div></div>
                                    <div class="op-field"><label class="op-label">অপারেশন পদ্ধতি ও প্রাপ্ত তথ্যাবলী নাম ও তাং</label><div class="op-input-area" style="height:55px;"></div></div>
                                    <div class="op-field"><label class="op-label">সার্জন</label><div class="op-input-area"></div></div>
                                    <div class="op-field"><label class="op-label">এ্যানেসথেটিষ্ট</label><div class="op-input-area"></div></div>
                                    <div class="op-field"><label class="op-label">এ্যাসিসট্যান্ট</label><div class="op-input-area"></div></div>
                                    <div class="op-field"><label class="op-label">এ্যাসিসট্যান্ট</label><div class="op-input-area"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Card 3 --}}
                    <div class="col-md-6 mb-3">
                        <div class="discharge-paper-card">
                            <div class="discharge-paper-header">
                                <h6 class="mb-0">চিকিৎসা পরামর্শ</h6>
                                <small style="opacity:.8;">Medical Advice</small>
                            </div>
                            <div class="discharge-paper-content">
                                <div class="advice-section">
                                    <div class="advice-box">
                                        <div class="advice-header">প্রদত্ত চিকিৎসা পরামর্শ</div>
                                        <div class="advice-content"></div>
                                    </div>
                                    <div class="advice-box">
                                        <div class="advice-header">পরীক্ষা নিরীক্ষা</div>
                                        <div class="advice-content"></div>
                                    </div>
                                    <div class="bottom-info">
                                        <div class="info-row"><span class="info-label">ব্লাড গ্রুপ:</span><span class="dotted-line"></span></div>
                                        <div class="info-row"><span class="info-label">ড্রাগ এলার্জি:</span><span class="dotted-line"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Card 4 --}}
                    <div class="col-md-6 mb-3">
                        <div class="discharge-paper-card">
                            <div class="discharge-paper-header">
                                <h6 class="mb-0">চিকিৎসা পরামর্শ ও উপদেশাবলী</h6>
                                <small style="opacity:.8;">Medical Advice & Guidelines</small>
                            </div>
                            <div class="discharge-paper-content">
                                <div class="guidelines-section">
                                    <h5 class="text-center mb-3" style="color:#2e7d32;font-size:16px;">চিকিৎসা পরামর্শ ও উপদেশাবলী</h5>
                                    <div class="guidelines-list">
                                        <div class="guideline-item"><i class="fas fa-check-circle text-success mr-2"></i><span>২৪ ঘন্টা ইমার্জেন্সী সার্ভিস প্রদান</span></div>
                                        <div class="guideline-item"><i class="fas fa-check-circle text-success mr-2"></i><span>দিবা-রাত্রি সব সময় সিজারসহ যেকোন অপারেশনের সু-ব্যবস্থা</span></div>
                                        <div class="guideline-item"><i class="fas fa-check-circle text-success mr-2"></i><span>মহিলা এম,বি,বি,এস ডাক্তার দ্বারা নরমাল ডেলিভারির সু-ব্যবস্থা</span></div>
                                        <div class="guideline-item"><i class="fas fa-check-circle text-success mr-2"></i><span>গরীব ও দুঃস্থ রোগীদের সুবিধার্থে প্রতি সোমবার বিকালে ফ্রি প্রেসক্রিপশন প্রদান</span></div>
                                        <div class="guideline-item"><i class="fas fa-check-circle text-success mr-2"></i><span>অন্তঃসত্ত্বা মহিলাদের জন্য চেক-আপের বিশেষ সুবিধাদি প্রদান</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ HIDDEN PRINT-ONLY AREA ══ --}}
        <div id="discharge-print-only">

            {{-- PAGE 1: ডিসচার্জ পেপার --}}
            <div class="dp-page">
                <div class="dp-header">
                    <div class="dp-logo-row">
                        <div class="dp-logo"><span style="color:#c0392b;">C</span><span style="color:#2e7d32;">P</span></div>
                        <div>
                            <div class="dp-clinic-name">প্রফেসর ক্লিনিক</div>
                            <div class="dp-clinic-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                            <div class="dp-clinic-sub">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬, ০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                        </div>
                    </div>
                </div>
                <div class="dp-title">ছাড় পত্র</div>
                <div class="dp-body">
                    <div class="dp-field-row">প্রত্যয়ন করা যাচ্ছে যে, <span class="dp-val dp-val-lg" id="p1-name"></span></div>
                    <div class="dp-field-row">বয়স <span class="dp-val" id="p1-age"></span> &nbsp;&nbsp; পিতা/স্বামী <span class="dp-val dp-val-lg" id="p1-father"></span></div>
                    <div class="dp-field-row">ঠিকানা ঃ <span class="dp-val dp-val-xl" id="p1-address"></span></div>
                    <div class="dp-field-row">
                        অত্র হাসপাতালে <span class="dp-val" id="p1-admit"></span> তারিখ হতে <span class="dp-val" id="p1-discharge"></span> তারিখ পর্যন্ত চিকৎসাধীন ছিলেন।
                    </div>
                    <div class="dp-field-row">তিনি <span class="dp-val dp-val-xl" id="p1-disease"></span> রোগে ভুগছিলেন।</div>
                    <div class="dp-field-row">তিনি নিম্নোক্ত চিকিৎসকের অধীনে চিকিৎসাধীন ছিলেন।</div>
                    <div class="dp-doctor-box" id="p1-doctor"></div>
                    <div class="dp-sign-row">
                        <div><div class="dp-sign-box"></div><div class="dp-sign-lbl">তাং-</div></div>
                        <div><div class="dp-sign-box"></div><div class="dp-sign-lbl" style="text-align:right;">কর্তৃপক্ষের/চিকিৎসকের স্বাক্ষর</div></div>
                    </div>
                </div>
                <div class="dp-footer">প্রফেসর ক্লিনিক, মাঝিড়া, শাজাহানপুর, বগুড়া &nbsp;|&nbsp; পৃষ্ঠা ১/৪</div>
            </div>

            {{-- PAGE 2: অপারেশন নোট --}}
            <div class="dp-page">
                <div class="dp-header dp-header-sm">
                    <div class="dp-logo-row">
                        <div class="dp-logo dp-logo-sm"><span style="color:#c0392b;">C</span><span style="color:#2e7d32;">P</span></div>
                        <div>
                            <div class="dp-clinic-name" style="font-size:17px;">প্রফেসর ক্লিনিক</div>
                            <div class="dp-clinic-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        </div>
                    </div>
                </div>
                <div class="dp-title">অপারেশন নোট</div>
                <div class="dp-mini-info">
                    <span>রোগীর নাম: <strong id="p2-name"></strong></span>
                    <span>বয়স: <strong id="p2-age"></strong></span>
                    <span>তারিখ: <strong id="p2-date"></strong></span>
                </div>
                <div class="dp-body">
                    <table class="dp-op-table">
                        <tr><td class="dp-op-lbl">অপারেশন<br>নাম ও তাং</td><td class="dp-op-val" style="height:65px;"></td></tr>
                        <tr><td class="dp-op-lbl">অপারেশন পদ্ধতি<br>ও প্রাপ্ত তথ্যাবলী<br>নাম ও তাং</td><td class="dp-op-val" style="height:95px;"></td></tr>
                        <tr><td class="dp-op-lbl">সার্জন</td><td class="dp-op-val" style="height:55px;"></td></tr>
                        <tr><td class="dp-op-lbl">এ্যানেসথেটিষ্ট</td><td class="dp-op-val" style="height:55px;"></td></tr>
                        <tr><td class="dp-op-lbl">এ্যাসিসট্যান্ট</td><td class="dp-op-val" style="height:55px;"></td></tr>
                        <tr><td class="dp-op-lbl">এ্যাসিসট্যান্ট</td><td class="dp-op-val" style="height:55px;"></td></tr>
                    </table>
                </div>
                <div class="dp-footer">প্রফেসর ক্লিনিক, মাঝিড়া, শাজাহানপুর, বগুড়া &nbsp;|&nbsp; পৃষ্ঠা ২/৪</div>
            </div>

            {{-- PAGE 3: প্রদত্ত চিকিৎসা পরামর্শ --}}
            <div class="dp-page">
                <div class="dp-header dp-header-sm">
                    <div class="dp-logo-row">
                        <div class="dp-logo dp-logo-sm"><span style="color:#c0392b;">C</span><span style="color:#2e7d32;">P</span></div>
                        <div>
                            <div class="dp-clinic-name" style="font-size:17px;">প্রফেসর ক্লিনিক</div>
                            <div class="dp-clinic-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        </div>
                    </div>
                </div>
                <div class="dp-title">প্রদত্ত চিকিৎসা পরামর্শ</div>
                <div class="dp-mini-info">
                    <span>রোগীর নাম: <strong id="p3-name"></strong></span>
                    <span>বয়স: <strong id="p3-age"></strong></span>
                    <span>তারিখ: <strong id="p3-date"></strong></span>
                </div>
                <div class="dp-body">
                    <div class="dp-advice-box" style="min-height:240px;"></div>
                    <div class="dp-sub-title">পরীক্ষা নিরীক্ষা</div>
                    <div class="dp-advice-box" style="min-height:120px;"></div>
                    <div class="dp-blood-row">
                        <span>রাড গ্রপ <span class="dp-dotline" style="min-width:100px;"></span></span>
                        <span>ড্রাগ এ্যালাজী <span class="dp-dotline" style="min-width:100px;"></span></span>
                    </div>
                </div>
                <div class="dp-footer">প্রফেসর ক্লিনিক, মাঝিড়া, শাজাহানপুর, বগুড়া &nbsp;|&nbsp; পৃষ্ঠা ৩/৪</div>
            </div>

            {{-- PAGE 4: চিকিৎসা পরামর্শ ও উপদেশাবলী --}}
            <div class="dp-page">
                <div class="dp-header dp-header-sm">
                    <div class="dp-logo-row">
                        <div class="dp-logo dp-logo-sm"><span style="color:#c0392b;">C</span><span style="color:#2e7d32;">P</span></div>
                        <div>
                            <div class="dp-clinic-name" style="font-size:17px;">প্রফেসর ক্লিনিক</div>
                            <div class="dp-clinic-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        </div>
                    </div>
                </div>
                <div class="dp-title">চিকিৎসা পরামর্শ ও উপদেশাবলী</div>
                <div class="dp-mini-info">
                    <span>রোগীর নাম: <strong id="p4-name"></strong></span>
                    <span>বয়স: <strong id="p4-age"></strong></span>
                    <span>তারিখ: <strong id="p4-date"></strong></span>
                </div>
                <div class="dp-body">
                    <div class="dp-advice-box" style="min-height:180px;" id="p4-notes-box"></div>
                    <div class="dp-special-title">ক্লিনিকের বিশেষ আকর্ষণ ঃ</div>
                    <ul class="dp-special-list">
                        <li>২৪ ঘটা ইমার্জেন্সী সার্ভিস প্রদান।</li>
                        <li>দিবা-রাত্রি সব সময় সিজারসহ যেকোন অপারেশনের সু-ব্যবস্থা।</li>
                        <li>মহিলা এম.বি.বি.এস ডাক্তার দ্বারা নরমাল ডেলিভারির সু-ব্যবস্থা।</li>
                        <li>গরীব ও দুঃস্থ রোগীদের সুবিধার্থে প্রতি সোমবার বিকালে ফ্রি প্রেসক্রিপশন প্রদান।</li>
                        <li>অন্তঃসত্তা মহিলাদের জন্য চেক-আপের বিশেষ সুবিধাদি প্রদান।</li>
                    </ul>
                    <div class="dp-followup-row">
                        <span style="font-size:12px;">পরবর্তী ভিজিট তারিখঃ <span class="dp-dotline" style="min-width:160px;"></span></span>
                        <div style="text-align:right;">
                            <div style="width:150px;height:48px;border:1.5px solid #2e7d32;border-radius:5px;margin-left:auto;margin-bottom:4px;"></div>
                            <div style="font-size:11px;color:#444;">চিকিৎসকের স্বাক্ষর</div>
                        </div>
                    </div>
                </div>
                <div class="dp-footer">প্রফেসর ক্লিনিক, মাঝিড়া, শাজাহানপুর, বগুড়া &nbsp;|&nbsp; পৃষ্ঠা ৪/৪</div>
            </div>

        </div>{{-- /#discharge-print-only --}}

    </div>{{-- /#discharge-view --}}

</div>{{-- /#panel-step2 --}}
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   ROOT VARIABLES
═══════════════════════════════════════════════════════ */
:root {
    --warning-deep:  #e65100;
    --warning-mid:   #f57c00;
    --warning-light: #fff3e0;
    --warning-soft:  #ffe0b2;
    --green-deep:    #2E7D32;
    --green-mid:     #43A047;
    --green-light:   #E8F5E9;
    --blue-deep:     #1565C0;
    --blue-mid:      #1976D2;
    --blue-light:    #E3F2FD;
    --teal:          #00695C;
    --orange:        #E65100;
    --text-primary:  #1a2332;
    --text-muted:    #6b7a90;
    --border:        #e4e9f0;
    --radius-sm:     6px;
    --radius-md:     10px;
    --radius-lg:     16px;
    --shadow-sm:     0 1px 4px rgba(0,0,0,.06);
    --shadow-md:     0 4px 16px rgba(0,0,0,.08);
    --font-base:     'DM Sans','Hind Siliguri',Arial,sans-serif;

    /* Gov palette */
    --gov-bg:       #f2f4f7;
    --gov-header:   #1a3a5c;
    --gov-header2:  #1e4976;
    --gov-accent:   #c9972a;
    --gov-border:   #c8cdd6;
    --gov-text:     #1c2b3a;
    --gov-muted:    #6b7890;

    /* Warning gov palette */
    --gov-warn-hdr:  #7a2800;
    --gov-warn-hdr2: #a33600;
}
body, .content-wrapper { background: var(--gov-bg) !important; font-family: var(--font-base); }
.text-warning { color: var(--warning-mid) !important; }

/* ═══════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════ */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--warning-light);display:inline-flex;align-items:center;justify-content:center;color:var(--warning-mid);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--warning-light);border-color:var(--warning-mid);color:var(--warning-deep); }

/* ═══════════════════════════════════════════════════════
   STEP INDICATOR
═══════════════════════════════════════════════════════ */
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:16px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s ease;border:2.5px solid transparent; }
.step-active   { background:var(--warning-mid);color:#fff;border-color:var(--warning-mid);box-shadow:0 0 0 4px rgba(245,124,0,.15); }
.step-done     { background:var(--warning-deep);color:#fff;border-color:var(--warning-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main   { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub    { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--warning-mid); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--warning-deep); }

.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }
.admission-status-bar { border-radius:var(--radius-md);padding:12px 18px;margin-bottom:16px;font-size:13.5px;font-weight:500;display:flex;align-items:center;border:1.5px solid transparent; }
.admission-loading-bar { background:var(--blue-light);color:var(--blue-deep);border-color:#90caf9; }

/* ═══════════════════════════════════════════════════════
   GOV PANEL — WARNING VARIANT (Step 1)
═══════════════════════════════════════════════════════ */
.gov-panel {
    background: #fff;
    border: 1px solid var(--gov-border);
    border-top: 3px solid var(--gov-warn-hdr);
    border-radius: 0 0 4px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    margin-bottom: 24px;
    overflow: hidden;
}
.gov-panel-warning { border-top-color: var(--gov-warn-hdr); }

/* Title bar */
.gov-panel-titlebar {
    background: linear-gradient(90deg, var(--gov-header) 0%, var(--gov-header2) 100%);
    padding: 10px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    border-bottom: 2px solid var(--gov-accent);
}
.gov-panel-titlebar-warning {
    background: linear-gradient(90deg, var(--gov-warn-hdr) 0%, var(--gov-warn-hdr2) 100%);
    border-bottom-color: #ffa726;
}
.gov-panel-titlebar-left  { display: flex; align-items: center; gap: 10px; }
.gov-panel-titlebar-right { display: flex; align-items: center; gap: 10px; }

.gov-panel-icon {
    width: 34px; height: 34px; border-radius: 4px;
    background: rgba(255,255,255,.13);
    color: #fff; font-size: 15px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; border: 1px solid rgba(255,255,255,.2);
}
.gov-panel-title    { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; letter-spacing: .2px; }
.gov-panel-subtitle { font-size: 11px; color: rgba(255,255,255,.7); margin-top: 1px; }

.gov-counter-badge {
    background: rgba(255,255,255,.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 3px; padding: 4px 12px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}

/* Toolbar */
.gov-toolbar { background: #f0f3f8; border-bottom: 1.5px solid var(--gov-border); padding: 8px 16px; }
.gov-toolbar-warning { background: #fff8f0; border-bottom-color: #ffe0b2; }
.gov-toolbar-inner { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.gov-toolbar-label { font-size: 11px; font-weight: 800; color: var(--gov-header); text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; flex-shrink: 0; }
.gov-toolbar-label-warning { color: var(--gov-warn-hdr); }
.gov-toolbar-hint { font-size: 11px; color: var(--gov-muted); white-space: nowrap; }
.gov-toolbar-hint kbd { background: #fff; border: 1px solid var(--gov-border); border-radius: 3px; padding: 1px 5px; font-size: 10px; color: var(--gov-header); }
.gov-search-group { display: flex; align-items: center; gap: 4px; flex: 1; min-width: 260px; }
.gov-search-input { flex: 1; border: 1.5px solid var(--gov-border); border-radius: 3px; padding: 6px 10px; font-size: 13px; color: var(--gov-text); background: #fff; outline: none; transition: border-color .2s; font-family: var(--font-base); height: 32px; }
.gov-search-input-warning:focus { border-color: var(--warning-mid); box-shadow: 0 0 0 2px rgba(245,124,0,.12); }
.gov-search-btn { border: none; border-radius: 3px; padding: 0 14px; height: 32px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background .2s; background: var(--gov-header); color: #fff; display: inline-flex; align-items: center; white-space: nowrap; }
.gov-search-btn-warning { background: var(--gov-warn-hdr); }
.gov-search-btn-warning:hover { background: var(--warning-mid); }
.gov-clear-btn { border: 1.5px solid var(--gov-border); border-radius: 3px; padding: 0 10px; height: 32px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; background: #fff; color: var(--gov-muted); display: inline-flex; align-items: center; white-space: nowrap; }
.gov-clear-btn:hover { background: #ffebee; color: #c62828; border-color: #ffcdd2; }

/* ═══════════════════════════════════════════════════════
   GOV TABLE
═══════════════════════════════════════════════════════ */
.gov-table-wrap { overflow-x: auto; }
.gov-table { border-collapse: collapse; width: 100%; font-size: 12.5px; }
.gov-th { background: #fff4e6; color: var(--gov-warn-hdr); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; padding: 8px 10px; border-bottom: 2px solid #ffe0b2; border-right: 1px solid #f0d8c0; white-space: nowrap; position: sticky; top: 0; z-index: 5; }
.gov-th:last-child { border-right: none; }
.gov-th-action { text-align: center; }

.gov-tr:nth-child(odd)  { background: #ffffff; }
.gov-tr:nth-child(even) { background: #fdf9f5; }
.gov-tr:hover { background: #fff8f0 !important; }
.gov-tr { transition: background .12s; }

.gov-td { padding: 7px 10px; border-bottom: 1px solid #eaedf2; border-right: 1px solid #f0f2f6; vertical-align: middle; color: var(--gov-text); }
.gov-td:last-child { border-right: none; }
.gov-td-sl     { color: var(--gov-muted); font-size: 11.5px; text-align: center; }
.gov-td-center { text-align: center; }
.gov-td-mono   { font-family: 'Courier New', monospace; font-size: 12px; letter-spacing: .3px; }
.gov-td-muted  { color: var(--gov-muted); font-size: 12px; }
.gov-td-action { text-align: center; }

.gov-name-cell { display: flex; align-items: center; gap: 7px; }
.gov-avatar { width: 26px; height: 26px; border-radius: 3px; background: var(--gov-header); color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.gov-avatar-warning { background: linear-gradient(135deg, var(--gov-warn-hdr), var(--warning-mid)); }
.gov-name-info  { display: flex; flex-direction: column; gap: 1px; }
.gov-name-text  { font-weight: 600; font-size: 13px; color: var(--gov-text); line-height: 1.2; }
.gov-father-text{ font-size: 10.5px; color: var(--gov-muted); }

.gov-code-badge { background: #e8ecf4; color: var(--gov-header); border: 1px solid #c8cdd6; border-radius: 2px; padding: 1px 7px; font-size: 11.5px; font-weight: 700; font-family: 'Courier New', monospace; letter-spacing: .3px; }
.gov-code-badge-warning { background: #fff3e0; color: var(--warning-deep); border-color: #ffe0b2; }

.gov-gender { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; font-size: 11px; font-weight: 800; }
.gov-gender-m { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.gov-gender-f { background: #fce7f3; color: #be185d; border: 1px solid #f9a8d4; }

.gov-blood-badge { background: #ffebee; color: #c62828; border-radius: 2px; padding: 1px 7px; font-size: 11.5px; font-weight: 700; border: 1px solid #ffcdd2; }
.gov-date-text { display: block; font-size: 12.5px; font-weight: 600; color: var(--gov-text); }
.gov-muted     { color: var(--gov-muted); font-size: 12px; }

.gov-fresh-done { background: #e8f5e9; color: #2e7d32; border-radius: 3px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; border: 1px solid #a5d6a7; }
.gov-fresh-none { background: #f5f5f5; color: #9e9e9e; border-radius: 3px; padding: 2px 8px; font-size: 11.5px; }

.gov-select-btn { border: none; border-radius: 3px; padding: 5px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer; transition: all .18s; display: inline-flex; align-items: center; letter-spacing: .2px; white-space: nowrap; box-shadow: 0 1px 3px rgba(0,0,0,.18); }
.gov-select-btn-warning { background: var(--gov-warn-hdr); color: #fff; }
.gov-select-btn-warning:hover { background: var(--warning-mid); transform: translateY(-1px); box-shadow: 0 3px 8px rgba(230,81,0,.3); }

.gov-empty-state { text-align: center; padding: 44px; color: #b0bec5; }
.gov-empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
.gov-empty-state p { font-size: 14px; margin: 0; }

/* Footer / Pagination */
.gov-panel-footer { background: #fff8f0; border-top: 1.5px solid #ffe0b2; padding: 8px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
.gov-footer-info  { font-size: 12px; color: var(--gov-muted); white-space: nowrap; }
.gov-footer-hint  { font-size: 11.5px; color: var(--gov-muted); }
.gov-footer-hint strong { color: var(--gov-warn-hdr); }
.gov-pagination-wrap .pagination { margin-bottom: 0; }
.gov-pagination-wrap .page-link { border-radius: 3px !important; border-color: var(--gov-border); color: var(--warning-mid); font-size: 12.5px; padding: 5px 10px; }
.gov-pagination-wrap .page-item.active .page-link { background: var(--warning-mid); border-color: var(--warning-mid); }

/* ═══════════════════════════════════════════════════════
   MODERN CARD (Step 2)
═══════════════════════════════════════════════════════ */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-warning-soft { background:var(--warning-light); }
.modern-card-body { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }

/* SELECTED PATIENT BAR */
.patient-selected-bar { border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;box-shadow:0 4px 18px rgba(230,81,0,.18); }
.patient-selected-bar-warning { background:linear-gradient(135deg,#e65100 0%,#f57c00 100%); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.psb-status-dot-yellow { background:#ffcc02;box-shadow:0 0 0 3px rgba(255,204,2,.3); }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28); }

.rx-history-card { border:1px solid #e0e0e0;border-radius:6px;padding:10px 12px;margin-bottom:8px;background:#fafafa;font-size:12px; }
.rx-history-card .rx-history-title { font-weight:700;color:#1a237e;margin-bottom:5px;font-size:12px; }
.rx-history-card ul { margin:0;padding-left:16px; }
.rx-history-card ul li { line-height:1.7; }

/* FORM */
.section-heading { display:flex;align-items:center;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:16px; }
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--warning-mid);box-shadow:0 0 0 3px rgba(245,124,0,.1); }
.modern-input[readonly] { background:#f8fafb;color:var(--text-muted); }
.form-footer { display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1.5px solid var(--border); }
.btn-footer-back { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:600;transition:all .2s; }
.btn-footer-back:hover { background:#f0f4f8; }
.btn-footer-save-warning { background:linear-gradient(135deg,#e65100,#f57c00);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(230,81,0,.28);transition:all .2s;display:inline-flex;align-items:center;gap:7px; }
.btn-footer-save-warning:hover { background:linear-gradient(135deg,#bf360c,#e65100);transform:translateY(-1px);color:#fff; }

/* SUMMARY CARDS */
.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-warning { background:linear-gradient(135deg,#e65100,#f57c00); }
.rx-card-teal    { background:linear-gradient(135deg,#00695C,#00897B); }
.rx-card-orange  { background:linear-gradient(135deg,#E65100,#F57C00); }
.rx-card-blue    { background:linear-gradient(135deg,#1565C0,#1976D2); }
.rx-summary-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.22);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.rx-summary-label { color:rgba(255,255,255,.75);font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;font-weight:600; }
.rx-summary-value { color:#fff;font-size:14px;font-weight:700;margin-top:2px; }
.rx-saved-badge-warning { background:var(--warning-light);color:var(--warning-deep);border:1.5px solid var(--warning-soft);border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center; }
.btn-rx-action { border-radius:var(--radius-sm);padding:8px 18px;font-size:13px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-new { background:#f0f4f8;color:var(--text-primary);border-color:var(--border); }
.btn-rx-new:hover { background:#e8ecf2; }

/* DISCHARGE PAPER CARDS */
.discharge-paper-card { border:2px solid #4caf50;border-radius:var(--radius-lg);background:#fff;overflow:hidden;box-shadow:0 4px 12px rgba(76,175,80,.15); }
.discharge-paper-header { background:linear-gradient(135deg,#2e7d32 0%,#43a047 100%);color:#fff;padding:10px 16px;text-align:center; }
.discharge-paper-header h6 { font-size:15px;font-weight:700;margin:0; }
.discharge-paper-content { padding:16px;min-height:380px;font-family:'Hind Siliguri',Arial,sans-serif; }
.clinic-header h4 { font-size:18px;font-weight:700;color:#2e7d32; }
.patient-info-grid { margin:16px 0; }
.info-row { display:flex;align-items:center;margin-bottom:10px;gap:8px; }
.info-label { font-size:12px;font-weight:600;color:#333;min-width:110px; }
.dotted-line { flex:1;border-bottom:2px dotted #aaa;min-height:18px;padding-bottom:2px;font-size:12px;font-weight:600;color:#1a3a1a; }
.doctor-signature { margin-top:24px; }
.signature-line .line { width:180px;height:2px;background:#555;margin-top:6px; }
.operation-note-fields { padding:4px 0; }
.op-field { margin-bottom:10px; }
.op-label { display:block;font-size:11.5px;font-weight:600;color:#2e7d32;padding:5px 10px;background:#e8f5e9;border:1.5px solid #4caf50;border-radius:5px;margin-bottom:4px; }
.op-input-area { width:100%;height:36px;border:1.5px solid #4caf50;border-radius:5px;background:#fafffe; }
.advice-section { padding:4px 0; }
.advice-box { margin-bottom:14px; }
.advice-header { display:inline-block;font-size:12px;font-weight:600;color:#fff;background:#2e7d32;padding:5px 14px;border-radius:20px;margin-bottom:6px; }
.advice-content { width:100%;height:70px;border:1.5px solid #4caf50;border-radius:6px;background:#fafffe; }
.bottom-info { margin-top:20px;padding-top:14px;border-top:1px solid #ddd; }
.guidelines-list { margin-top:12px; }
.guideline-item { display:flex;align-items:flex-start;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #f0f0f0; }
.guideline-item span { font-size:12px;color:#333;line-height:1.4; }
.btn-dc-print-btn { background:#2e7d32;color:#fff;border:none;border-radius:var(--radius-sm);padding:9px 22px;font-size:13.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .2s; }
.btn-dc-print-btn:hover { background:#1b5e20; }

/* ══ HIDDEN PRINT-ONLY AREA ══ */
#discharge-print-only { display: none; }

/* ══ PRINT STYLES ══ */
@media print {
    body * { visibility: hidden !important; }
    #discharge-print-only,
    #discharge-print-only * { visibility: visible !important; }
    #discharge-print-only { display: block !important; position: fixed !important; top: 0; left: 0; width: 100% !important; background: #fff !important; }
    .dp-page { width: 100% !important; min-height: 100vh !important; margin: 0 !important; padding: 9mm 12mm 7mm !important; border: none !important; box-shadow: none !important; page-break-after: always !important; break-after: page !important; display: flex !important; flex-direction: column !important; }
    .dp-page:last-child { page-break-after: avoid !important; break-after: avoid !important; }
    .dp-title { background: #2e7d32 !important; color: #fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .dp-sub-title { background: #43a047 !important; color: #fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .dp-special-title { background: #2e7d32 !important; color: #fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .dp-op-lbl { background: #f1f8e9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .dp-mini-info { background: #f1f8e9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .gov-toolbar, .gov-panel-titlebar { display: none !important; }
    @page { size: A4 portrait; margin: 0; }
}

/* ══ PRINT PAGE STYLES ══ */
.dp-page { font-family:'Hind Siliguri',Arial,sans-serif;font-size:13px;color:#1a3a1a;background:#fff;box-sizing:border-box; }
.dp-header { border-bottom:2.5px solid #2e7d32;padding-bottom:8px;margin-bottom:8px; }
.dp-header-sm { padding-bottom:5px;margin-bottom:5px; }
.dp-logo-row { display:flex;align-items:center;gap:10px; }
.dp-logo { width:50px;height:50px;border-radius:50%;border:2.5px solid #2e7d32;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:900;background:#fff;flex-shrink:0; }
.dp-logo-sm { width:36px;height:36px;font-size:13px; }
.dp-clinic-name { font-size:20px;font-weight:700;color:#1a3a1a;line-height:1.2; }
.dp-clinic-sub { font-size:10.5px;color:#555; }
.dp-title { background:#2e7d32;color:#fff;font-size:17px;font-weight:700;text-align:center;border-radius:6px;padding:6px 16px;margin:8px 0 10px;letter-spacing:1px;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
.dp-sub-title { display:inline-block;background:#43a047;color:#fff;font-size:12px;font-weight:700;border-radius:5px;padding:4px 14px;margin:8px 0 6px;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
.dp-special-title { background:#2e7d32;color:#fff;font-size:13px;font-weight:700;border-radius:5px;padding:5px 14px;margin:10px 0 7px;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
.dp-mini-info { display:flex;gap:18px;flex-wrap:wrap;font-size:11.5px;background:#f1f8e9;border:1px solid #c8e6c9;border-radius:5px;padding:5px 10px;margin-bottom:6px;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
.dp-body { flex:1; }
.dp-field-row { margin-bottom:10px;line-height:1.9;font-size:12.5px; }
.dp-val { border-bottom:1px solid #555;display:inline-block;padding:0 4px;font-weight:600;min-width:80px;vertical-align:bottom; }
.dp-val-lg { min-width:180px; }
.dp-val-xl { min-width:260px; }
.dp-doctor-box { border:1.5px solid #2e7d32;border-radius:6px;min-height:40px;width:76%;margin:12px auto;padding:7px 14px;text-align:center;font-size:13px;font-weight:600; }
.dp-sign-row { display:flex;justify-content:space-between;align-items:flex-end;margin-top:20px; }
.dp-sign-box { width:100%;height:46px;border:1.5px solid #2e7d32;border-radius:5px;margin-bottom:4px; }
.dp-sign-lbl { font-size:10.5px;color:#444; }
.dp-op-table { width:100%;border-collapse:collapse;margin-top:6px; }
.dp-op-lbl { width:30%;border:1.5px solid #a5d6a7;padding:7px 9px;font-size:11.5px;font-weight:600;color:#2e7d32;vertical-align:middle;background:#f1f8e9;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
.dp-op-val { border:1.5px solid #a5d6a7;padding:5px 8px;vertical-align:top; }
.dp-advice-box { border:1.5px solid #a5d6a7;border-radius:5px;margin-bottom:7px;background:#fafff8; }
.dp-blood-row { display:flex;gap:28px;flex-wrap:wrap;font-size:12px;margin-top:7px;padding:5px 0;border-top:1px dotted #aaa; }
.dp-dotline { border-bottom:1px dotted #888;display:inline-block;min-width:50px;vertical-align:bottom; }
.dp-special-list { list-style:none;padding:0;margin:0 0 10px; }
.dp-special-list li { font-size:12px;color:#1a3a1a;padding:4px 0 4px 16px;position:relative;border-bottom:1px dotted #c8e6c9; }
.dp-special-list li::before { content:'▶';position:absolute;left:0;color:#2e7d32;font-size:8px;top:7px; }
.dp-followup-row { display:flex;justify-content:space-between;align-items:flex-end;margin-top:14px; }
.dp-footer { text-align:center;font-size:10px;color:#888;border-top:1px solid #c8e6c9;padding-top:5px;margin-top:auto; }
</style>
@stop

@section('js')
<script>
var CSRF_TOKEN         = '{{ csrf_token() }}';
var DISCHARGE_DATA_URL = '{{ url("nursing/discharge/patient-data") }}';
var DISCHARGE_STORE_URL= '{{ route("nursing.discharge.store") }}';
var freshDetailsOpen   = true;
var roundDetailsOpen   = true;

function gVal(id){ var el=document.getElementById(id); return el?el.value.trim():''; }
function setText(id,txt){ var el=document.getElementById(id); if(el) el.textContent=(txt!==null&&txt!==undefined&&txt!=='')?txt:'—'; }
function showEl(id){ var el=document.getElementById(id); if(el) el.style.display=''; }
function hideEl(id){ var el=document.getElementById(id); if(el) el.style.display='none'; }
function esc(str){ return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtDateBD(iso){ if(!iso||iso==='—') return '—'; var p=String(iso).slice(0,10).split('-'); return p[2]+'/'+p[1]+'/'+p[0].slice(2); }

function showAlert(type,msg){
    var el=document.getElementById('save-alert');
    el.className='alert alert-'+type+' modern-alert';
    el.innerHTML=msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){el.classList.add('d-none');},6000);
}

function selectPatient(btn){
    var d=btn.dataset;
    document.getElementById('f-patient-id').value          = d.id;
    document.getElementById('f-admission-id').value        = d.admissionId||'';
    document.getElementById('f-patient-code').value        = d.code;
    document.getElementById('f-patient-name').value        = d.name;
    document.getElementById('f-patient-age').value         = d.age;
    document.getElementById('f-admission-date-raw').value  = d.admissionDate||'';

    document.getElementById('spb-avatar').textContent = (d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent   = d.name;
    document.getElementById('spb-meta').textContent   = [d.code,d.age,d.mobile,d.blood,d.upozila].filter(Boolean).join(' · ');

    document.getElementById('step1-circle').className='step-circle step-done';
    document.getElementById('step1-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className='step-circle step-active';
    document.getElementById('step2-label').className='step-label-main step-label-active';
    document.getElementById('breadcrumb-current').textContent='Discharge';

    document.getElementById('panel-step1').style.display='none';
    document.getElementById('panel-step2').style.display='block';
    document.getElementById('discharge-view').style.display='none';
    document.getElementById('discharge-form-card').style.display='block';

    hideEl('fresh-info-box');
    hideEl('round-info-box');
    fetchPatientData(d.id);
    window.scrollTo({top:0,behavior:'smooth'});
}

function fetchPatientData(patientId){
    showEl('history-loading');
    fetch(DISCHARGE_DATA_URL+'/'+patientId,{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}})
    .then(function(r){return r.json();})
    .then(function(data){
        hideEl('history-loading');
        if(!data.success) return;
        if(data.admission&&data.admission.id)
            document.getElementById('f-admission-id').value=data.admission.id;
        renderFreshBox(data.fresh_prescriptions||[]);
        renderRoundBox(data.round_prescriptions||[]);
    })
    .catch(function(){hideEl('history-loading');});
}

function renderFreshBox(list){
    if(!list.length){hideEl('fresh-info-box');return;}
    setText('fresh-count-badge',list.length+' টি');
    document.getElementById('fresh-history-list').innerHTML=buildHistoryCards(list);
    showEl('fresh-info-box');
}
function renderRoundBox(list){
    if(!list.length){hideEl('round-info-box');return;}
    setText('round-count-badge',list.length+' টি');
    document.getElementById('round-history-list').innerHTML=buildHistoryCards(list);
    showEl('round-info-box');
}
function buildHistoryCards(list){
    return list.map(function(rx){
        var badge='';
        if(rx.type==='fresh') badge='<span class="badge badge-success ml-1">Fresh</span>';
        if(rx.type==='round') badge='<span class="badge badge-primary ml-1">Round</span>';
        var dateStr=rx.date?fmtDateBD(String(rx.date).slice(0,10)):'—';
        var lines=(rx.lines||[]).map(function(l){return '<li>'+esc(l)+'</li>';}).join('');
        return '<div class="rx-history-card"><div class="rx-history-title">Prescription #'+esc(rx.id||'—')+badge+
            ' <span class="text-muted font-weight-normal">('+dateStr+(rx.doctor?' · Dr. '+esc(rx.doctor):'')+')</span></div>'+
            '<ul>'+(lines||'<li class="text-muted">No medicines recorded.</li>')+'</ul></div>';
    }).join('');
}
function toggleFreshDetails(){
    var body=document.getElementById('fresh-details-body');
    freshDetailsOpen=!freshDetailsOpen;
    body.style.display=freshDetailsOpen?'':'none';
    document.getElementById('fresh-toggle-icon').className=freshDetailsOpen?'fas fa-chevron-down mr-1':'fas fa-chevron-right mr-1';
    document.getElementById('fresh-toggle-text').textContent=freshDetailsOpen?'Details Hide':'Details Show';
}
function toggleRoundDetails(){
    var body=document.getElementById('round-details-body');
    roundDetailsOpen=!roundDetailsOpen;
    body.style.display=roundDetailsOpen?'':'none';
    document.getElementById('round-toggle-icon').className=roundDetailsOpen?'fas fa-chevron-down mr-1':'fas fa-chevron-right mr-1';
    document.getElementById('round-toggle-text').textContent=roundDetailsOpen?'Details Hide':'Details Show';
}

function confirmDischarge(){
    var patientId   = gVal('f-patient-id');
    var admissionId = gVal('f-admission-id');
    var date        = gVal('f-discharge-date');
    var notes       = gVal('f-notes');
    var name        = gVal('f-patient-name');

    if(!patientId){showAlert('warning','Please select a patient first.');return;}
    if(!admissionId){showAlert('warning','Admission ID not found. Please try again.');return;}

    var btn=document.getElementById('btn-discharge');
    btn.disabled=true;
    btn.innerHTML='<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';

    fetch(DISCHARGE_STORE_URL,{
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
        body:JSON.stringify({patient_id:patientId,admission_id:admissionId,discharge_date:date,notes:notes})
    })
    .then(function(r){return r.json();})
    .then(function(data){
        btn.disabled=false;
        btn.innerHTML='<i class="fas fa-sign-out-alt mr-1"></i> Confirm Discharge';

        if(data.success){
            var age  = gVal('f-patient-age');
            var code = gVal('f-patient-code');
            var admitRaw = gVal('f-admission-date-raw');

            setText('dv-name',name); setText('dv-name2',name);
            setText('dv-age',age);   setText('dv-code',code);
            setText('dv-date',fmtDateBD(date)); setText('dv-date2',fmtDateBD(date));
            setText('discharged-badge-name',name);
            setText('discharge-time',new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}));

            populateDischargePapers(name,age,fmtDateBD(date),fmtDateBD(admitRaw));
            fillPrintPages(name,age,code,date,fmtDateBD(date),fmtDateBD(admitRaw),notes);

            document.getElementById('step2-circle').className='step-circle step-done';
            document.getElementById('step2-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
            document.getElementById('discharge-form-card').style.display='none';
            document.getElementById('discharge-view').style.display='block';
            window.scrollTo({top:0,behavior:'smooth'});
        } else {
            showAlert('danger','<i class="fas fa-exclamation-triangle mr-1"></i>'+(data.message||'Discharge failed.'));
        }
    })
    .catch(function(){
        btn.disabled=false;
        btn.innerHTML='<i class="fas fa-sign-out-alt mr-1"></i> Confirm Discharge';
        showAlert('danger','<i class="fas fa-exclamation-triangle mr-1"></i>Network error. Please try again.');
    });
}

function populateDischargePapers(name,age,dischargeDate,admitDate){
    setText('dp-patient-name', name);
    setText('dp-age',          age);
    setText('dp-discharge-date', dischargeDate);
    setText('dp-admission-date', admitDate||'—');
}

function fillPrintPages(name,age,code,dateRaw,dateBD,admitBD,notes){
    function sv(id,val){ var el=document.getElementById(id); if(el) el.textContent=val||''; }
    sv('p1-name',     name);
    sv('p1-age',      age);
    sv('p1-admit',    admitBD);
    sv('p1-discharge',dateBD);
    sv('p2-name', name);
    sv('p2-age',  age);
    sv('p2-date', dateBD);
    sv('p3-name', name);
    sv('p3-age',  age);
    sv('p3-date', dateBD);
    sv('p4-name', name);
    sv('p4-age',  age);
    sv('p4-date', dateBD);
    var nb = document.getElementById('p4-notes-box');
    if(nb) nb.style.padding='10px 12px', nb.textContent = notes||'';
}

function printDischargePapers(){
    window.print();
}

function backToStep1(){
    document.getElementById('step1-circle').className='step-circle step-active';
    document.getElementById('step1-circle').innerHTML='1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className='step-circle step-inactive';
    document.getElementById('step2-label').className='step-label-main step-label-inactive';
    document.getElementById('breadcrumb-current').textContent='Select Patient';
    document.getElementById('panel-step1').style.display='block';
    document.getElementById('panel-step2').style.display='none';
    hideEl('fresh-info-box');
    hideEl('round-info-box');
    window.scrollTo({top:0,behavior:'smooth'});
}

function filterTable(){
    var q=document.getElementById('patientSearch').value.toLowerCase();
    document.querySelectorAll('#patientTable tbody tr').forEach(function(row){
        row.style.display=row.textContent.toLowerCase().includes(q)?'':'none';
    });
}
document.getElementById('patientSearch').addEventListener('keyup', function(e){
    if(e.key === 'Enter') filterTable();
    else filterTable();
});
</script>
@stop