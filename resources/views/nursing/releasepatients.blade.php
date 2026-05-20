@extends('adminlte::page')

@section('title', 'Release Patient | Professor Clinic')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-sign-out-alt"></i></span>
                Release Patient
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
                <div class="step-connector-line" id="step-connector-12"></div>
                <div class="step-item">
                    <div class="step-circle step-inactive" id="step2-circle">2</div>
                    <div class="step-text ml-2">
                        <div class="step-label-main step-label-inactive" id="step2-label">Step 2</div>
                        <div class="step-label-sub step-label-inactive" id="step2-sublabel">Bill & Release</div>
                    </div>
                </div>
                <div class="step-connector-line" id="step-connector-23"></div>
                <div class="step-item">
                    <div class="step-circle step-inactive" id="step3-circle">3</div>
                    <div class="step-text ml-2">
                        <div class="step-label-main step-label-inactive" id="step3-label">Step 3</div>
                        <div class="step-label-sub step-label-inactive" id="step3-sublabel">Summary</div>
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

    <div class="gov-panel gov-panel-teal">

        {{-- Panel Title Bar --}}
        <div class="gov-panel-titlebar gov-panel-titlebar-teal">
            <div class="gov-panel-titlebar-left">
                <div class="gov-panel-icon gov-panel-icon-teal">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="gov-panel-title">Patient Selection — Release</div>
                    <div class="gov-panel-subtitle">Search and select a discharged patient to proceed with release</div>
                </div>
            </div>
            <div class="gov-panel-titlebar-right">
                <span class="gov-counter-badge gov-counter-badge-teal">
                    <i class="fas fa-bed mr-1"></i>
                    Total Records: <strong>{{ $patients->total() }}</strong>
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
                    <input type="text" id="patientSearch" class="gov-search-input gov-search-input-teal"
                           placeholder="Search by Name / Patient Code / Mobile Number…"
                           onkeyup="filterTable()">
                    <button class="gov-search-btn gov-search-btn-teal" type="button" onclick="filterTable()">
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
                        <th class="gov-th" style="width:90px;">Status</th>
                        <th class="gov-th gov-th-action" style="width:76px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        @php $g = strtolower($patient->gender ?? ''); @endphp
                        <tr class="gov-tr">
                            <td class="gov-td gov-td-sl">{{ $patient->id }}</td>
                            <td class="gov-td">
                                <span class="gov-code-badge gov-code-badge-teal">{{ $patient->patientcode ?? '—' }}</span>
                            </td>
                            <td class="gov-td">
                                <div class="gov-name-cell">
                                    <div class="gov-avatar gov-avatar-teal">
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
                                        <i class="fas fa-calendar-alt mr-1" style="color:var(--gov-teal-hdr);font-size:10px;"></i>
                                        {{ \Carbon\Carbon::parse($patient->admission_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="gov-muted">—</span>
                                @endif
                            </td>
                            <td class="gov-td gov-td-center">
                                <span class="gov-status-badge gov-status-discharged">
                                    <i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i>
                                    Discharged
                                </span>
                            </td>
                            <td class="gov-td gov-td-action">
                                <button type="button"
                                    class="gov-select-btn"
                                    onclick="selectPatient(this)"
                                    data-id="{{ $patient->id }}"
                                    data-name="{{ $patient->patientname ?? '' }}"
                                    data-age="{{ $patient->age ?? '' }}"
                                    data-code="{{ $patient->patientcode ?? '' }}"
                                    data-mobile="{{ $patient->mobile_no ?? '' }}"
                                    data-upozila="{{ $patient->upozila ?? '' }}"
                                    data-blood="{{ $patient->blood_group ?? '' }}"
                                    data-gender="{{ $patient->gender ?? '' }}"
                                    data-admission-id="{{ $patient->admission_id ?? '' }}"
                                    data-admission-date="{{ $patient->admission_date ?? '' }}"
                                    title="Select this patient">
                                    <i class="fas fa-arrow-right mr-1"></i> Select
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="gov-empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No patients found for release.</p>
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
                Click <strong>Select</strong> to proceed with Bill & Release
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ══════════════════════════════════════════
     STEP 2 — BILL & RELEASE FORM
══════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none;">

    <div class="patient-selected-bar patient-selected-bar-teal mb-4">
        <div class="psb-left">
            <div class="psb-avatar" id="spb-avatar">A</div>
            <div class="psb-info">
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="psb-right">
            <span class="psb-status-dot psb-status-dot-teal"></span>
            <span class="psb-status-label">Release Process</span>
            <button type="button" class="btn btn-psb-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    <div id="release-form-card">
        <div class="row">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">

                {{-- Release Info --}}
                <div class="modern-card mb-4">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-teal-soft"><i class="fas fa-user-check text-teal"></i></span>
                            <div>
                                <h5 class="mb-0 font-weight-bold">Release Information</h5>
                                <small class="text-muted">Patient discharge details</small>
                            </div>
                        </div>
                    </div>
                    <div class="modern-card-body">
                        <input type="hidden" id="f-patient-id">
                        <input type="hidden" id="f-admission-id">

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
                                <div class="modern-field-group">
                                    <label class="modern-label">Age / Gender</label>
                                    <input type="text" class="modern-input" id="f-patient-age-gender" readonly>
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label">Mobile</label>
                                    <input type="text" class="modern-input" id="f-patient-mobile" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modern-field-group">
                                    <label class="modern-label">Admission Date</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-calendar-plus input-icon text-teal"></i>
                                        <input type="date" class="modern-input with-icon" id="f-admission-date" readonly>
                                    </div>
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label">Release Date <span class="text-danger">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-calendar-check input-icon text-teal"></i>
                                        <input type="date" class="modern-input with-icon" id="f-release-date">
                                    </div>
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label">Total Days Stay</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-clock input-icon text-teal"></i>
                                        <input type="text" class="modern-input with-icon" id="f-total-days" readonly placeholder="Auto calculated">
                                    </div>
                                </div>
                                <div class="modern-field-group">
                                    <label class="modern-label">Discharge Condition</label>
                                    <select class="modern-input" id="f-condition" onchange="updateConditionLabel()">
                                        <option value="recovered">Recovered / ভালো হয়েছেন</option>
                                        <option value="improved">Improved / উন্নতি হয়েছে</option>
                                        <option value="referred">Referred / রেফার করা হয়েছে</option>
                                        <option value="lama">LAMA / নিজ দায়িত্বে</option>
                                        <option value="absconded">Absconded / পালিয়ে গেছেন</option>
                                        <option value="expired">Expired / মৃত্যু</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modern-field-group">
                            <label class="modern-label">Release Notes / Remarks</label>
                            <textarea class="modern-input" id="f-notes" rows="2"
                                placeholder="Any additional notes about the release..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- BILL SECTION --}}
                <div class="modern-card mb-4">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-orange-soft"><i class="fas fa-file-invoice-dollar text-orange"></i></span>
                            <div>
                                <h5 class="mb-0 font-weight-bold">Hospital Bill</h5>
                                <small class="text-muted">Add all charges before release</small>
                            </div>
                        </div>
                        <button type="button" class="btn-med-action btn-add-bill-row" onclick="addBillRow()">
                            <i class="fas fa-plus mr-1"></i> Add Row
                        </button>
                    </div>
                    <div class="modern-card-body p-0">
                        <div style="overflow-x:auto;">
                            <table class="table bill-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:35px;">#</th>
                                        <th>Description / বিবরণ</th>
                                        <th style="width:80px;">Qty</th>
                                        <th style="width:110px;">Unit Price (৳)</th>
                                        <th style="width:120px;">Total (৳)</th>
                                        <th style="width:42px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="bill-tbody"></tbody>
                                <tfoot>
                                    <tr class="bill-subtotal-row">
                                        <td colspan="4" class="text-right font-weight-bold" style="padding:10px 16px;">Subtotal:</td>
                                        <td class="font-weight-bold text-teal" style="padding:10px 8px;" id="bill-subtotal">৳ 0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr class="bill-discount-row">
                                        <td colspan="3" class="text-right font-weight-bold" style="padding:8px 16px;">Discount:</td>
                                        <td style="padding:6px 8px;">
                                            <select class="modern-input" id="discount-type" onchange="recalcBill()" style="padding:5px 8px;font-size:12px;">
                                                <option value="flat">৳ Flat</option>
                                                <option value="percent">% Percent</option>
                                            </select>
                                        </td>
                                        <td style="padding:6px 8px;">
                                            <input type="number" class="modern-input" id="bill-discount" value="0" min="0" oninput="recalcBill()" style="padding:5px 8px;font-size:12px;">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bill-paid-row">
                                        <td colspan="4" class="text-right font-weight-bold" style="padding:8px 16px;">Advance Paid:</td>
                                        <td style="padding:6px 8px;">
                                            <input type="number" class="modern-input" id="bill-advance" value="0" min="0" oninput="recalcBill()" style="padding:5px 8px;font-size:12px;" placeholder="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bill-grand-row">
                                        <td colspan="4" class="text-right" style="padding:14px 16px;font-size:15px;font-weight:800;color:#2c3e50;">Grand Total:</td>
                                        <td style="padding:14px 8px;" id="bill-grand"><span class="grand-total-value">৳ 0.00</span></td>
                                        <td></td>
                                    </tr>
                                    <tr class="bill-due-row">
                                        <td colspan="4" class="text-right" style="padding:8px 16px;font-size:14px;font-weight:700;color:#c62828;">Due Amount:</td>
                                        <td style="padding:8px 8px;" id="bill-due"><span class="due-amount-value">৳ 0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PRESCRIPTION HISTORY --}}
                <div class="modern-card mb-4" id="prescription-summary-card" style="display:none;">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-indigo-soft"><i class="fas fa-notes-medical text-indigo"></i></span>
                            <div>
                                <h5 class="mb-0 font-weight-bold">Prescription History</h5>
                                <small class="text-muted">During admission</small>
                            </div>
                        </div>
                        <button type="button" class="btn-toggle-rx" onclick="toggleRxHistory()">
                            <i class="fas fa-eye mr-1"></i> <span id="rx-toggle-label">Show</span>
                        </button>
                    </div>
                    <div id="rx-history-body" style="display:none;">
                        <div class="modern-card-body">
                            <div id="fresh-rx-list"></div>
                            <div id="round-rx-list"></div>
                        </div>
                    </div>
                </div>

            </div>{{-- /col-lg-8 --}}

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">

                {{-- Admission Summary --}}
                <div class="modern-card mb-4">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-blue-soft"><i class="fas fa-clipboard-list text-blue"></i></span>
                            <div><h5 class="mb-0 font-weight-bold">Admission Summary</h5></div>
                        </div>
                    </div>
                    <div class="modern-card-body p-0">
                        <div class="summary-list">
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-id-card mr-2 text-teal"></i>Patient Code</span><span class="summary-value" id="sum-code">—</span></div>
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-user mr-2 text-teal"></i>Name</span><span class="summary-value" id="sum-name">—</span></div>
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-calendar-plus mr-2 text-teal"></i>Admitted</span><span class="summary-value" id="sum-admit">—</span></div>
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-calendar-check mr-2 text-orange"></i>Release</span><span class="summary-value" id="sum-release">—</span></div>
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-moon mr-2 text-blue"></i>Total Days</span><span class="summary-value font-weight-bold" id="sum-days">—</span></div>
                            <div class="summary-item"><span class="summary-label"><i class="fas fa-heartbeat mr-2 text-red"></i>Condition</span><span class="summary-value" id="sum-condition">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Bill Summary --}}
                <div class="modern-card mb-4">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-orange-soft"><i class="fas fa-receipt text-orange"></i></span>
                            <div><h5 class="mb-0 font-weight-bold">Bill Summary</h5></div>
                        </div>
                    </div>
                    <div class="modern-card-body p-0">
                        <div class="summary-list">
                            <div class="summary-item"><span class="summary-label">Subtotal</span><span class="summary-value" id="sb-subtotal">৳ 0.00</span></div>
                            <div class="summary-item"><span class="summary-label">Discount</span><span class="summary-value text-danger" id="sb-discount">- ৳ 0.00</span></div>
                            <div class="summary-item"><span class="summary-label">Advance Paid</span><span class="summary-value text-success" id="sb-advance">৳ 0.00</span></div>
                            <div class="summary-item summary-item-grand"><span class="summary-label font-weight-bold" style="font-size:14px;">Grand Total</span><span class="summary-value font-weight-bold text-teal" style="font-size:15px;" id="sb-grand">৳ 0.00</span></div>
                            <div class="summary-item summary-item-due"><span class="summary-label font-weight-bold text-danger" style="font-size:14px;">Due Amount</span><span class="summary-value font-weight-bold text-danger" style="font-size:15px;" id="sb-due">৳ 0.00</span></div>
                        </div>
                    </div>
                </div>

                {{-- Doctor --}}
                <div class="modern-card mb-4">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <span class="card-title-icon bg-teal-soft"><i class="fas fa-user-md text-teal"></i></span>
                            <div><h5 class="mb-0 font-weight-bold">Attending Doctor</h5></div>
                        </div>
                    </div>
                    <div class="modern-card-body">
                        <div class="modern-field-group mb-0">
                            <label class="modern-label">Select Doctor</label>
                            <select class="modern-input" id="f-doctor">
                                @forelse($doctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                @empty
                                    <option value="">No doctors found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="modern-card action-card">
                    <div class="modern-card-body">
                        <div class="pending-approval-notice mb-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Submit করলে manager approval এর পর patient released হবে।
                        </div>
                        <button type="button" class="btn-release-action btn-release-save" id="btn-release" onclick="processRelease()">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit for Approval
                        </button>
                        <button type="button" class="btn-release-action btn-release-print-bill mt-2" onclick="printBillOnly()">
                            <i class="fas fa-print mr-2"></i>
                            Print Bill Only
                        </button>
                        <button type="button" class="btn-release-action btn-release-back mt-2" onclick="backToStep1()">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </button>
                    </div>
                </div>

            </div>{{-- /col-lg-4 --}}
        </div>
    </div>
</div>{{-- /#panel-step2 --}}

{{-- ══════════════════════════════════════════
     STEP 3 — SUBMITTED CONFIRMATION
══════════════════════════════════════════ --}}
<div id="panel-step3" style="display:none;">

    <div class="row mb-4">
        <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
            <div class="rx-summary-card rx-card-teal">
                <div class="rx-summary-icon"><i class="fas fa-user"></i></div>
                <div class="rx-summary-content">
                    <div class="rx-summary-label">Patient</div>
                    <div class="rx-summary-value" id="ib-name">—</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
            <div class="rx-summary-card rx-card-orange">
                <div class="rx-summary-icon"><i class="fas fa-moon"></i></div>
                <div class="rx-summary-content">
                    <div class="rx-summary-label">Total Days</div>
                    <div class="rx-summary-value" id="ib-days">—</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
            <div class="rx-summary-card rx-card-blue">
                <div class="rx-summary-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="rx-summary-content">
                    <div class="rx-summary-label">Grand Total</div>
                    <div class="rx-summary-value" id="ib-total">—</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="rx-summary-card rx-card-red">
                <div class="rx-summary-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="rx-summary-content">
                    <div class="rx-summary-label">Due Amount</div>
                    <div class="rx-summary-value" id="ib-due">৳ 0.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-teal-soft"><i class="fas fa-file-alt text-teal"></i></span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Release Summary & Bill</h5>
                    <small class="text-muted">Ready to print</small>
                </div>
            </div>
            <span class="rx-saved-badge rx-saved-badge-orange">
                <i class="fas fa-hourglass-half mr-1"></i> Pending Approval
                <span class="ml-1" id="rx-badge-name">—</span>
            </span>
        </div>
        <div class="modern-card-body p-0">
            <div id="release-print-area">

                {{-- ═══════════════════════════════════════════
                     PHYSICAL BILL BOOK LAYOUT — Professor Clinic
                ═══════════════════════════════════════════ --}}
                <div class="bill-book-wrapper">

                    {{-- CLINIC HEADER STRIP --}}
                    <div class="bb-header">
                        <div class="bb-header-logo-col">
                            <div class="bb-cp-logo"><span class="bb-cp-letter">P</span></div>
                            <div class="bb-clinic-badge">PROFESSOR CLINIC</div>
                        </div>
                        <div class="bb-header-center-col">
                            <div class="bb-clinic-title">PROFESSOR CLINIC</div>
                            <div class="bb-clinic-sub">MAJHIRA, SAJAHANPUR, BOGURA.</div>
                        </div>
                        <div class="bb-header-phone-col">
                            <div class="bb-phone-line">&#9742; 01720-039005</div>
                            <div class="bb-phone-line">01720-039006</div>
                            <div class="bb-phone-line">01720-039007</div>
                            <div class="bb-phone-line">01720-039008</div>
                        </div>
                    </div>

                    {{-- PATIENT META ROW 1 --}}
                    <div class="bb-meta-row">
                        <div class="bb-meta-field" style="flex:2.5;">
                            <span class="bb-meta-label">Name of Patient :</span>
                            <span class="bb-meta-value" id="print-name">—</span>
                        </div>
                        <div class="bb-meta-field">
                            <span class="bb-meta-label">Date :</span>
                            <span class="bb-meta-value" id="print-release-date">—</span>
                        </div>
                        <div class="bb-meta-field">
                            <span class="bb-meta-label">Serial No. :</span>
                            <span class="bb-meta-value" id="print-code">—</span>
                        </div>
                    </div>
                    {{-- PATIENT META ROW 2 --}}
                    <div class="bb-meta-row" style="border-bottom:1.5px solid #bbb;">
                        <div class="bb-meta-field" style="flex:2.5;">
                            <span class="bb-meta-label">Cabin/Ward No :</span>
                            <span class="bb-meta-value" id="print-cabin">—</span>
                        </div>
                        <div class="bb-meta-field">
                            <span class="bb-meta-label">Age :</span>
                            <span class="bb-meta-value" id="print-age">—</span>
                        </div>
                        <div class="bb-meta-field">
                            <span class="bb-meta-label">Mobile :</span>
                            <span class="bb-meta-value" id="print-mobile">—</span>
                        </div>
                    </div>

                    {{-- BILL/ESTIMATE TITLE --}}
                    <div class="bb-bill-title-row">
                        <span class="bb-bill-title-badge">BILL/ESTIMATE</span>
                    </div>

                    {{-- BILL TABLE --}}
                    <table class="bb-table">
                        <thead>
                            <tr>
                                <th class="bb-col-sl">Sl.</th>
                                <th class="bb-col-desc">Particulars</th>
                                <th class="bb-col-qty">Qty</th>
                                <th class="bb-col-rate">Rate (৳)</th>
                                <th class="bb-col-amt">Amount (৳)</th>
                            </tr>
                        </thead>
                        <tbody id="print-bill-tbody">
                            {{-- populated by JS --}}
                        </tbody>
                        <tfoot>
                            <tr class="bb-row-total">
                                <td colspan="4" class="bb-foot-label">Total</td>
                                <td class="bb-foot-val" id="print-subtotal">৳ 0.00</td>
                            </tr>
                            <tr class="bb-row-less">
                                <td colspan="4" class="bb-foot-label">Less (Discount)</td>
                                <td class="bb-foot-val" id="print-discount-val">৳ 0.00</td>
                            </tr>
                            <tr class="bb-row-net">
                                <td colspan="4" class="bb-foot-label bb-foot-label-net">Net Total</td>
                                <td class="bb-foot-val bb-foot-val-net" id="print-grand-total">৳ 0.00</td>
                            </tr>
                            <tr class="bb-row-advance">
                                <td colspan="4" class="bb-foot-label">Advance Paid</td>
                                <td class="bb-foot-val" id="print-advance-val">৳ 0.00</td>
                            </tr>
                            <tr class="bb-row-due">
                                <td colspan="4" class="bb-foot-label bb-foot-label-due">Due Amount</td>
                                <td class="bb-foot-val bb-foot-val-due" id="print-due-val">৳ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- EXTRA DETAILS STRIP --}}
                    <div class="bb-details-strip">
                        <div class="bb-detail-item">
                            <span class="bb-detail-label">Admitted :</span>
                            <span class="bb-detail-value" id="print-admit-date">—</span>
                        </div>
                        <div class="bb-detail-item">
                            <span class="bb-detail-label">Released :</span>
                            <span class="bb-detail-value" id="print-rel-date">—</span>
                        </div>
                        <div class="bb-detail-item">
                            <span class="bb-detail-label">Days :</span>
                            <span class="bb-detail-value font-weight-bold" id="print-days">—</span>
                        </div>
                        <div class="bb-detail-item">
                            <span class="bb-detail-label">Condition :</span>
                            <span class="bb-detail-value" id="print-condition">—</span>
                        </div>
                        <div class="bb-detail-item">
                            <span class="bb-detail-label">Doctor :</span>
                            <span class="bb-detail-value font-weight-bold" id="print-doctor">—</span>
                        </div>
                    </div>

                    {{-- NOTES --}}
                    <div class="bb-notes" id="print-notes"></div>

                    {{-- STATUS / FOOTER META --}}
                    <div class="bb-status-bar">
                        <span class="bb-pending-badge">&#8987; Pending Manager Approval</span>
                        <span class="bb-gen-info">By: <strong id="print-released-by">—</strong> &nbsp;|&nbsp; <span id="gen-time">—</span></span>
                    </div>

                    {{-- SIGNATURE --}}
                    <div class="bb-signature-row">
                        <div class="bb-sig">
                            <div class="bb-sig-line"></div>
                            <div class="bb-sig-label">Patient / Attendant Signature</div>
                        </div>
                        <div class="bb-sig bb-sig-right">
                            <div class="bb-sig-line"></div>
                            <div class="bb-sig-label">Authorized Signature</div>
                        </div>
                    </div>

                </div>{{-- /.bill-book-wrapper --}}

            </div>{{-- /#release-print-area --}}
        </div>
        <div class="modern-card-footer">
            <small class="text-muted">
                <i class="fas fa-clock mr-1"></i> Generated: <span id="gen-time-display">—</span>
            </small>
            <div style="display:flex; gap:8px;">
                <button onclick="window.print()" class="btn-rx-action btn-rx-print">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <button type="button" class="btn-rx-action btn-rx-new" onclick="backToStep1()">
                    <i class="fas fa-plus mr-1"></i> New Release
                </button>
            </div>
        </div>
    </div>

</div>{{-- /#panel-step3 --}}

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   ROOT VARIABLES
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
    --indigo-mid:   #3949AB;
    --indigo-light: #E8EAF6;
    --text-primary: #1a2332;
    --text-muted:   #6b7a90;
    --border:       #e4e9f0;
    --radius-sm:    6px;
    --radius-md:    10px;
    --radius-lg:    16px;
    --shadow-sm:    0 1px 4px rgba(0,0,0,.06);
    --shadow-md:    0 4px 16px rgba(0,0,0,.08);
    --font-base:    'DM Sans', 'Hind Siliguri', Arial, sans-serif;

    /* Gov palette */
    --gov-bg:        #f2f4f7;
    --gov-header:    #1a3a5c;
    --gov-header2:   #1e4976;
    --gov-accent:    #c9972a;
    --gov-border:    #c8cdd6;
    --gov-row-odd:   #ffffff;
    --gov-row-even:  #f6f8fb;
    --gov-row-hover: #e6f7f5;
    --gov-text:      #1c2b3a;
    --gov-muted:     #6b7890;

    /* Teal gov palette */
    --gov-teal-hdr:  #0d4a42;
    --gov-teal-hdr2: #105c54;
}
body, .content-wrapper { background: var(--gov-bg) !important; font-family: var(--font-base); }
.text-teal   { color: var(--teal-mid)   !important; }
.text-orange { color: var(--orange)     !important; }
.text-blue   { color: var(--blue-mid)   !important; }
.text-red    { color: var(--red-mid)    !important; }
.text-indigo { color: var(--indigo-mid) !important; }

/* ═══════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════ */
.page-main-title { font-size:22px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:38px;height:38px;border-radius:10px;background:var(--teal-light);display:inline-flex;align-items:center;justify-content:center;color:var(--teal-mid);font-size:17px; }
.btn-back-modern { background:#fff;border:1.5px solid var(--border);color:var(--text-primary);border-radius:var(--radius-sm);font-weight:500;padding:6px 14px;font-size:13px;transition:all .2s;text-decoration:none; }
.btn-back-modern:hover { background:var(--teal-light);border-color:var(--teal-mid);color:var(--teal-deep); }

/* ═══════════════════════════════════════════════════════
   STEP INDICATOR
═══════════════════════════════════════════════════════ */
.step-track-card { background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);border:1px solid var(--border);padding:16px 24px; }
.step-track-inner { display:flex;align-items:center; }
.step-item { display:flex;align-items:center; }
.step-text { margin-left:10px; }
.step-circle { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s ease;border:2.5px solid transparent; }
.step-active   { background:var(--teal-mid);color:#fff;border-color:var(--teal-mid);box-shadow:0 0 0 4px rgba(0,137,123,.15); }
.step-done     { background:var(--teal-deep);color:#fff;border-color:var(--teal-deep); }
.step-inactive { background:#fff;color:#ccc;border-color:#ddd; }
.step-label-main   { font-size:13px;font-weight:700;line-height:1.2; }
.step-label-sub    { font-size:11px;color:var(--text-muted); }
.step-label-active   { color:var(--teal-mid); }
.step-label-inactive { color:#bbb; }
.step-connector-line { flex:1;max-width:100px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s; }
.step-connector-line.done { background:var(--teal-deep); }

/* ALERT */
.modern-alert { border-radius:var(--radius-md);border:none;font-size:13.5px;font-weight:500;box-shadow:var(--shadow-sm); }

/* ═══════════════════════════════════════════════════════
   GOV PANEL — TEAL VARIANT (Step 1)
═══════════════════════════════════════════════════════ */
.gov-panel {
    background: #fff;
    border: 1px solid var(--gov-border);
    border-top: 3px solid var(--gov-header);
    border-radius: 0 0 4px 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    margin-bottom: 24px;
    overflow: hidden;
}
.gov-panel-teal { border-top-color: var(--gov-teal-hdr); }

/* Title bar */
.gov-panel-titlebar {
    background: linear-gradient(90deg, var(--gov-header) 0%, var(--gov-header2) 100%);
    padding: 10px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    border-bottom: 2px solid var(--gov-accent);
}
.gov-panel-titlebar-teal {
    background: linear-gradient(90deg, var(--gov-teal-hdr) 0%, var(--gov-teal-hdr2) 100%);
    border-bottom-color: #4db6ac;
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
.gov-panel-icon-teal { background: rgba(255,255,255,.12); }
.gov-panel-title    { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; letter-spacing: .2px; }
.gov-panel-subtitle { font-size: 11px; color: rgba(255,255,255,.7); margin-top: 1px; }

.gov-counter-badge {
    background: rgba(255,255,255,.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 3px; padding: 4px 12px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.gov-counter-badge-teal { background: rgba(255,255,255,.12); }

/* Toolbar */
.gov-toolbar {
    background: #f0f3f8;
    border-bottom: 1.5px solid var(--gov-border);
    padding: 8px 16px;
}
.gov-toolbar-teal { background: #f0f6f5; border-bottom-color: #b2dfdb; }
.gov-toolbar-inner { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.gov-toolbar-label {
    font-size: 11px; font-weight: 800; color: var(--gov-header);
    text-transform: uppercase; letter-spacing: .8px;
    white-space: nowrap; flex-shrink: 0;
}
.gov-toolbar-label-teal { color: var(--gov-teal-hdr); }
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
.gov-search-input-teal:focus { border-color: var(--teal-mid); box-shadow: 0 0 0 2px rgba(0,137,123,.12); }
.gov-search-btn {
    border: none; border-radius: 3px; padding: 0 14px;
    height: 32px; font-size: 12px; font-weight: 700;
    cursor: pointer; transition: background .2s;
    background: var(--gov-header); color: #fff;
    display: inline-flex; align-items: center;
    white-space: nowrap; letter-spacing: .2px;
}
.gov-search-btn-teal { background: var(--gov-teal-hdr); }
.gov-search-btn-teal:hover { background: var(--teal-mid); }
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
    background: #e8ecf4;
    color: var(--gov-teal-hdr);
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    padding: 8px 10px;
    border-bottom: 2px solid #b2dfdb;
    border-right: 1px solid #c8d8d6;
    white-space: nowrap; position: sticky; top: 0; z-index: 5;
}
.gov-th:last-child { border-right: none; }
.gov-th-action { text-align: center; }

.gov-tr { transition: background .12s; }
.gov-tr:nth-child(odd)  { background: var(--gov-row-odd); }
.gov-tr:nth-child(even) { background: #f5faf9; }
.gov-tr:hover { background: #e6f7f5 !important; }

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
.gov-avatar-teal { background: var(--gov-teal-hdr); }
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
.gov-code-badge-teal { background: #e0f2f1; color: var(--teal-deep); border-color: var(--teal-soft); }

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
.gov-status-discharged { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }

/* Select button */
.gov-select-btn {
    background: var(--gov-teal-hdr);
    color: #fff; border: none; border-radius: 3px;
    padding: 5px 12px; font-size: 11.5px; font-weight: 700;
    cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center;
    letter-spacing: .2px; white-space: nowrap;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.gov-select-btn:hover { background: var(--teal-mid); transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,105,92,.3); }

/* Empty state */
.gov-empty-state { text-align: center; padding: 44px; color: #b0bec5; }
.gov-empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
.gov-empty-state p { font-size: 14px; margin: 0; }

/* ═══════════════════════════════════════════════════════
   PANEL FOOTER / PAGINATION
═══════════════════════════════════════════════════════ */
.gov-panel-footer {
    background: #f0f6f5;
    border-top: 1.5px solid #b2dfdb;
    padding: 8px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
}
.gov-footer-info  { font-size: 12px; color: var(--gov-muted); white-space: nowrap; }
.gov-footer-hint  { font-size: 11.5px; color: var(--gov-muted); }
.gov-footer-hint strong { color: var(--gov-teal-hdr); }
.gov-pagination-wrap .pagination { margin-bottom: 0; }
.gov-pagination-wrap .page-link  {
    border-radius: 3px !important; border-color: var(--gov-border);
    color: var(--teal-mid); font-size: 12.5px; padding: 5px 10px;
}
.gov-pagination-wrap .page-item.active .page-link {
    background: var(--teal-mid); border-color: var(--teal-mid);
}

/* ═══════════════════════════════════════════════════════
   MODERN CARD (Step 2 & 3)
═══════════════════════════════════════════════════════ */
.modern-card { background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:24px; }
.modern-card-header { padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd; }
.modern-card-title { display:flex;align-items:center;gap:12px; }
.card-title-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.bg-teal-soft   { background:var(--teal-light); }
.bg-orange-soft { background:var(--orange-light); }
.bg-blue-soft   { background:var(--blue-light); }
.bg-indigo-soft { background:#E8EAF6; }
.modern-card-body { padding:24px; }
.modern-card-footer { padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between; }
.action-card { border:2px solid var(--teal-soft); }

/* SELECTED PATIENT BAR */
.patient-selected-bar { border-radius:var(--radius-md);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px; }
.patient-selected-bar-teal { background:linear-gradient(135deg,#00695C 0%,#00897B 100%);box-shadow:0 4px 18px rgba(0,105,92,.18); }
.psb-left { display:flex;align-items:center;gap:14px; }
.psb-avatar { width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.22);border:2.5px solid rgba(255,255,255,.55);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.psb-name { color:#fff;font-size:16px;font-weight:700;line-height:1.2; }
.psb-meta { color:rgba(255,255,255,.78);font-size:12px;margin-top:2px; }
.psb-right { display:flex;align-items:center;gap:12px; }
.psb-status-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.psb-status-dot-teal { background:#80cbc4;box-shadow:0 0 0 3px rgba(128,203,196,.3); }
.psb-status-label { color:rgba(255,255,255,.85);font-size:12.5px;font-weight:500; }
.btn-psb-change { background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.45);color:#fff;border-radius:var(--radius-sm);padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;transition:all .2s; }
.btn-psb-change:hover { background:rgba(255,255,255,.28); }

/* FORM */
.modern-field-group { margin-bottom:16px; }
.modern-label { display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.modern-input { width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:13.5px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none;font-family:var(--font-base); }
.modern-input:focus { border-color:var(--teal-mid);box-shadow:0 0 0 3px rgba(0,137,123,.1); }
.modern-input[readonly] { background:#f8fafb;color:var(--text-muted); }
.input-with-icon { position:relative; }
.input-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none; }
.modern-input.with-icon { padding-left:30px; }

/* BILL TABLE */
.btn-med-action { border-radius:var(--radius-sm);padding:6px 14px;font-size:12px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .18s;display:inline-flex;align-items:center; }
.btn-add-bill-row { background:var(--teal-light);color:var(--teal-deep);border-color:var(--teal-soft); }
.btn-add-bill-row:hover { background:var(--teal-mid);color:#fff; }
.bill-table { border-collapse:collapse;width:100%; }
.bill-table thead tr th { background:#f0f8f7;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);padding:10px 14px;border-bottom:2px solid var(--teal-soft);white-space:nowrap; }
.bill-table tbody td { padding:7px 10px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle; }
.bill-table tfoot td { border-top:none; }
.bill-subtotal-row td { background:#f9fafb;border-top:2px solid var(--border); }
.bill-discount-row td, .bill-paid-row td { background:#fafbfb; }
.bill-grand-row td { background:#e0f2f1 !important;-webkit-print-color-adjust:exact; }
.bill-due-row td { background:#ffebee !important;-webkit-print-color-adjust:exact; }
.grand-total-value { font-size:15px;font-weight:800;color:var(--teal-deep); }
.due-amount-value  { font-size:14px;font-weight:800;color:var(--red-mid); }
.bill-table .form-control { padding:4px 8px !important;font-size:12.5px !important;border:1.5px solid var(--border);border-radius:5px; }
.btn-rm-bill { width:26px;height:26px;border-radius:6px;background:#ffebee;color:#c62828;border:1.5px solid #ffcdd2;font-size:11px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .18s; }
.btn-rm-bill:hover { background:#c62828;color:#fff; }

/* SUMMARY LIST */
.summary-list { padding:0; }
.summary-item { display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid var(--border);font-size:13px; }
.summary-item:last-child { border-bottom:none; }
.summary-item-grand { background:#e0f2f1 !important;-webkit-print-color-adjust:exact; }
.summary-item-due   { background:#ffebee !important;-webkit-print-color-adjust:exact; }
.summary-label { color:var(--text-muted);font-size:12.5px; }
.summary-value { font-weight:600;color:var(--text-primary); }

/* PENDING NOTICE */
.pending-approval-notice { background:var(--orange-light);color:var(--orange);border:1.5px solid var(--orange-soft);border-radius:var(--radius-sm);padding:10px 14px;font-size:12.5px;font-weight:600;line-height:1.5; }

/* ACTION BUTTONS */
.btn-release-action { width:100%;border-radius:var(--radius-sm);padding:13px 22px;font-size:14px;font-weight:700;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center; }
.btn-release-save { background:linear-gradient(135deg,#00695C,#00897B);color:#fff;box-shadow:0 4px 14px rgba(0,105,92,.28); }
.btn-release-save:hover { background:linear-gradient(135deg,#004d40,#00695C);transform:translateY(-1px); }
.btn-release-print-bill { background:var(--blue-light);color:var(--blue-deep);border:1.5px solid var(--blue-soft); }
.btn-release-print-bill:hover { background:var(--blue-mid);color:#fff; }
.btn-release-back { background:#fff;color:var(--text-muted);border:1.5px solid var(--border); }
.btn-release-back:hover { background:#f0f4f8; }
.btn-toggle-rx { background:#E8EAF6;color:#3949AB;border:1.5px solid #c5cae9;border-radius:var(--radius-sm);padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s; }

/* RX SUMMARY CARDS */
.rx-summary-card { border-radius:var(--radius-md);padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);height:100%; }
.rx-card-teal   { background:linear-gradient(135deg,#00695C,#00897B); }
.rx-card-orange { background:linear-gradient(135deg,#E65100,#F57C00); }
.rx-card-blue   { background:linear-gradient(135deg,#1565C0,#1976D2); }
.rx-card-red    { background:linear-gradient(135deg,#c62828,#e53935); }
.rx-summary-icon { width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.22);color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.rx-summary-label { color:rgba(255,255,255,.75);font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;font-weight:600; }
.rx-summary-value { color:#fff;font-size:14px;font-weight:700;margin-top:2px; }
.rx-saved-badge { border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center; }
.rx-saved-badge-orange { background:var(--orange-light);color:var(--orange);border:1.5px solid var(--orange-soft); }
.btn-rx-action { border-radius:var(--radius-sm);padding:8px 18px;font-size:13px;font-weight:600;border:1.5px solid transparent;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center; }
.btn-rx-print { background:var(--blue-deep);color:#fff;border-color:var(--blue-deep); }
.btn-rx-print:hover { background:var(--blue-mid); }
.btn-rx-new   { background:#f0f4f8;color:var(--text-primary);border-color:var(--border); }
.btn-rx-new:hover { background:#e8ecf2; }

/* RX HISTORY */
.rx-history-item { background:#f9fafb;border-radius:var(--radius-sm);border:1px solid var(--border);padding:10px 14px;margin-bottom:8px; }
.rx-history-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.rx-history-line { font-size:12px;color:var(--text-primary);padding:2px 0;border-bottom:1px dashed var(--border); }
.rx-history-line:last-child { border-bottom:none; }

/* ═══════════════════════════════════════════════════════════════
   BILL BOOK PRINT LAYOUT — Matches Professor Clinic carbon book
═══════════════════════════════════════════════════════════════ */
#release-print-area { padding:10px;background:#e8e8e8; }
.bill-book-wrapper { width:100%;max-width:750px;margin:0 auto;background:#fff;border:1.5px solid #777;font-family:Arial,'Hind Siliguri',sans-serif;font-size:12px;color:#111;box-shadow:0 3px 14px rgba(0,0,0,.15); }
.bb-header { display:flex;align-items:stretch;min-height:64px;background:linear-gradient(90deg,#2d2db0 0%,#4040cc 50%,#2d2db0 100%) !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;border-bottom:3px solid #1a1a99; }
.bb-header-logo-col { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:8px 12px;border-right:2px solid rgba(255,255,255,.25);min-width:90px; }
.bb-cp-logo { width:40px;height:40px;border-radius:50%;border:2.5px solid #fff;background:#fff !important;-webkit-print-color-adjust:exact;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.bb-cp-letter { font-size:22px;font-weight:900;color:#2d2db0; }
.bb-clinic-badge { color:#fff;font-size:6.5px;font-weight:700;text-align:center;letter-spacing:.4px;text-transform:uppercase;line-height:1.3; }
.bb-header-center-col { flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:8px 14px; }
.bb-clinic-title { font-size:24px;font-weight:900;color:#fff;letter-spacing:1.5px;text-transform:uppercase;line-height:1.1;text-shadow:1px 1px 3px rgba(0,0,0,.35); }
.bb-clinic-sub { font-size:11px;font-weight:600;color:rgba(255,255,255,.9);margin-top:4px;letter-spacing:.5px; }
.bb-header-phone-col { display:flex;flex-direction:column;align-items:flex-end;justify-content:center;padding:8px 12px 8px 8px;border-left:2px solid rgba(255,255,255,.25);min-width:115px; }
.bb-phone-line { color:#fff;font-size:9.5px;line-height:1.55; }
.bb-meta-row { display:flex;flex-wrap:wrap;align-items:center;padding:5px 12px;border-bottom:1px solid #ccc;background:#fafafa !important;-webkit-print-color-adjust:exact;gap:4px; }
.bb-meta-field { display:flex;align-items:baseline;gap:5px;flex:1;min-width:150px;padding:2px 4px; }
.bb-meta-label { font-size:11px;font-weight:700;color:#222;white-space:nowrap; }
.bb-meta-value { flex:1;border-bottom:1px dotted #888;font-size:12px;color:#111;padding:0 4px 1px;min-width:70px; }
.bb-bill-title-row { text-align:center;padding:7px 0 5px;border-bottom:2px solid #333;background:#f8f8f8 !important;-webkit-print-color-adjust:exact; }
.bb-bill-title-badge { display:inline-block;font-size:13px;font-weight:900;letter-spacing:3px;color:#fff;background:#333 !important;-webkit-print-color-adjust:exact;padding:4px 32px;border-radius:2px; }
.bb-table { width:100%;border-collapse:collapse;font-size:12px; }
.bb-table th,.bb-table td { border:1px solid #aaa;padding:5px 8px;vertical-align:middle; }
.bb-table thead th { background:#efefef !important;-webkit-print-color-adjust:exact;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.4px;text-align:center; }
.bb-col-sl { width:34px;text-align:center; } .bb-col-desc { text-align:left; } .bb-col-qty { width:48px;text-align:center; } .bb-col-rate { width:88px;text-align:right; } .bb-col-amt { width:100px;text-align:right; }
.bb-table tbody tr td { font-size:12px; }
.bb-table tbody td:first-child { text-align:center;color:#555;font-size:11px; }
.bb-table tbody td:nth-child(3) { text-align:center; }
.bb-table tbody td:nth-child(4),.bb-table tbody td:nth-child(5) { text-align:right; }
.bb-table tbody tr:nth-child(odd)  { background:#fff !important; }
.bb-table tbody tr:nth-child(even) { background:#f9f9f9 !important; }
.bb-table tfoot td { border:1px solid #aaa;padding:5px 8px; }
.bb-foot-label { text-align:right;font-weight:700;font-size:12px; }
.bb-foot-label-net { font-size:13px;font-weight:900; }
.bb-foot-label-due { font-size:13px;font-weight:900;color:#b71c1c; }
.bb-foot-val { text-align:right;font-weight:700;font-size:12px; }
.bb-foot-val-net { background:#e0f2f1 !important;-webkit-print-color-adjust:exact;font-size:14px;font-weight:900;color:#004d40; }
.bb-foot-val-due { background:#ffebee !important;-webkit-print-color-adjust:exact;font-size:14px;font-weight:900;color:#b71c1c; }
.bb-row-total   { background:#f4f4f4 !important;-webkit-print-color-adjust:exact; }
.bb-row-less    { background:#fff8ee !important;-webkit-print-color-adjust:exact; }
.bb-row-net     { background:#e0f2f1 !important;-webkit-print-color-adjust:exact; }
.bb-row-advance { background:#f0fff4 !important;-webkit-print-color-adjust:exact; }
.bb-row-due     { background:#ffebee !important;-webkit-print-color-adjust:exact; }
.bb-details-strip { display:flex;flex-wrap:wrap;padding:5px 12px 4px;border-top:1.5px solid #aaa;border-bottom:1px solid #ddd;background:#f4fff8 !important;-webkit-print-color-adjust:exact;gap:2px 14px; }
.bb-detail-item { display:flex;align-items:baseline;gap:4px;flex:1;min-width:120px;padding:2px 2px; }
.bb-detail-label { font-size:10.5px;font-weight:700;color:#444;white-space:nowrap; }
.bb-detail-value { font-size:11px;color:#111;border-bottom:1px dotted #999;flex:1;padding:0 2px 1px; }
.bb-notes { padding:4px 14px;min-height:16px;font-size:11px;color:#444;border-bottom:1px dashed #ccc;white-space:pre-wrap; }
.bb-status-bar { display:flex;align-items:center;justify-content:space-between;padding:5px 14px;border-bottom:1px solid #e0e0e0;flex-wrap:wrap;gap:4px; }
.bb-pending-badge { background:#fff3e0 !important;-webkit-print-color-adjust:exact;color:#e65100;font-size:11px;font-weight:700;padding:2px 10px;border-radius:3px;border:1px solid #ffcc80; }
.bb-gen-info { font-size:10px;color:#777; }
.bb-signature-row { display:flex;justify-content:space-between;padding:24px 44px 16px;border-top:1px solid #e0e0e0; }
.bb-sig { width:38%; }
.bb-sig-right { text-align:right; }
.bb-sig-line { border-bottom:1px solid #333;margin-bottom:6px;height:30px; }
.bb-sig-label { font-size:10px;color:#666;text-align:center; }

/* ── PRINT ── */
@media print {
    * { -webkit-print-color-adjust:exact !important;print-color-adjust:exact !important; }
    body * { visibility:hidden !important; }
    #release-print-area, #release-print-area * { visibility:visible !important; }
    #release-print-area { position:fixed;top:0;left:0;width:100%;background:white !important;padding:0 !important; }
    .bill-book-wrapper { border:none !important;max-width:100% !important;box-shadow:none !important; }
    .gov-toolbar,.gov-panel-titlebar { display:none !important; }
    .bb-header { -webkit-print-color-adjust:exact !important; }
    .bb-row-net,.bb-row-due,.bb-foot-val-net,.bb-foot-val-due { -webkit-print-color-adjust:exact !important; }
}
</style>
@stop

@section('js')
<script>
var CSRF_TOKEN        = '{{ csrf_token() }}';
var RELEASE_STORE_URL = '{{ url("/nursing/releasepatients/store") }}';
var PATIENT_DATA_URL  = '{{ url("/nursing/releasepatients/patient-data") }}';

// ══ All 16 charge items matching the physical Professor Clinic bill book ══
var DEFAULT_BILL_ITEMS = [
    { description:'Operation Charge',              qty:1, unit_price:0 },
    { description:'Assistance Charge',             qty:1, unit_price:0 },
    { description:'Anaesthesia Charge',            qty:1, unit_price:0 },
    { description:'O.T. Charge',                   qty:1, unit_price:0 },
    { description:'Advance',                       qty:1, unit_price:0 },
    { description:'Dressing Charge',               qty:1, unit_price:0 },
    { description:'Bed Charge',                    qty:1, unit_price:0 },
    { description:'Service Charge',                qty:1, unit_price:0 },
    { description:'Admission Fee',                 qty:1, unit_price:0 },
    { description:'Oxygen Charge',                 qty:1, unit_price:0 },
    { description:'Delivery Charge',               qty:1, unit_price:0 },
    { description:'Consultation Fee / Doctor Fee', qty:1, unit_price:0 },
    { description:'D & C',                         qty:1, unit_price:0 },
    { description:'Transfusion Charge',            qty:1, unit_price:0 },
    { description:'Baby Manage',                   qty:1, unit_price:0 },
    { description:'Other',                         qty:1, unit_price:0 },
];

var billItems = [];

/* ══ HELPERS ══ */
function todayISO(){ return new Date().toISOString().split('T')[0]; }
function fmtDateBD(iso){ if(!iso) return '—'; var p=String(iso).slice(0,10).split('-'); return p[2]+'/'+p[1]+'/'+p[0]; }
function gVal(id){ var el=document.getElementById(id); return el?el.value.trim():''; }
function setText(id,txt){ var el=document.getElementById(id); if(el) el.textContent=(txt!==null&&txt!==undefined&&txt!=='')?String(txt):'—'; }
function esc(str){ return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function taka(n){ return '৳ '+parseFloat(n||0).toFixed(2); }

function showAlert(type,msg){
    var el=document.getElementById('save-alert');
    el.className='alert alert-'+type+' modern-alert';
    el.innerHTML=msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){ el.classList.add('d-none'); },6000);
}
function showToast(msg,type){
    var bg=type==='success'?'#00695C':(type==='error'?'#c62828':'#1565C0');
    var t=document.createElement('div');
    t.style.cssText='position:fixed;bottom:20px;right:20px;z-index:9999;background:'+bg+';color:#fff;padding:12px 20px;border-radius:4px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.2);max-width:320px;';
    t.innerHTML='<i class="fas fa-check-circle mr-2"></i>'+msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(function(){ t.remove(); },300); },3000);
}

/* ══ BILL TABLE ══ */
function renderBillTable(){
    var tbody=document.getElementById('bill-tbody');
    if(!billItems.length){
        tbody.innerHTML='<tr><td colspan="6" class="text-center text-muted py-3"><i class="fas fa-plus-circle mr-1"></i> Add bill items above</td></tr>';
        recalcBill(); return;
    }
    tbody.innerHTML=billItems.map(function(item,i){
        return '<tr>'+
            '<td class="text-muted small">'+(i+1)+'</td>'+
            '<td><input type="text" class="form-control form-control-sm" value="'+esc(item.description)+'" onchange="billItems['+i+'].description=this.value" placeholder="Description..."></td>'+
            '<td><input type="number" class="form-control form-control-sm text-center" value="'+item.qty+'" min="1" onchange="billItems['+i+'].qty=parseFloat(this.value)||1;recalcBill()"></td>'+
            '<td><input type="number" class="form-control form-control-sm text-right" value="'+item.unit_price+'" min="0" step="0.01" onchange="billItems['+i+'].unit_price=parseFloat(this.value)||0;recalcBill()"></td>'+
            '<td class="font-weight-bold text-teal" style="background:#f0faf9;">'+taka(item.qty*item.unit_price)+'</td>'+
            '<td class="text-center"><button type="button" class="btn-rm-bill" onclick="removeBillRow('+i+')"><i class="fas fa-times"></i></button></td>'+
        '</tr>';
    }).join('');
    recalcBill();
}

function addBillRow(){ billItems.push({description:'',qty:1,unit_price:0}); renderBillTable(); }
function removeBillRow(idx){ billItems.splice(idx,1); renderBillTable(); }

function recalcBill(){
    var subtotal=billItems.reduce(function(s,item){ return s+(item.qty*item.unit_price); },0);
    var discountVal=parseFloat(document.getElementById('bill-discount').value)||0;
    var discountType=document.getElementById('discount-type').value;
    var advance=parseFloat(document.getElementById('bill-advance').value)||0;
    var discountAmt=discountType==='percent'?(subtotal*discountVal/100):discountVal;
    var grand=Math.max(0,subtotal-discountAmt);
    var due=Math.max(0,grand-advance);

    var el;
    el=document.getElementById('bill-subtotal'); if(el) el.textContent=taka(subtotal);
    el=document.getElementById('bill-grand');    if(el) el.querySelector('.grand-total-value').textContent=taka(grand);
    el=document.getElementById('bill-due');      if(el) el.querySelector('.due-amount-value').textContent=taka(due);
    setText('sb-subtotal', taka(subtotal));
    setText('sb-discount',  '- '+taka(discountAmt));
    setText('sb-advance',   taka(advance));
    setText('sb-grand',     taka(grand));
    setText('sb-due',       taka(due));
    setText('ib-total', taka(grand));
    setText('ib-due',   taka(due));
    return { subtotal:subtotal, discountAmt:discountAmt, grand:grand, due:due, advance:advance };
}

/* ══ DAYS CALC ══ */
function calcDays(){
    var admitDate=gVal('f-admission-date'), relDate=gVal('f-release-date');
    if(!admitDate||!relDate){ document.getElementById('f-total-days').value=''; return 0; }
    var diff=Math.round((new Date(relDate)-new Date(admitDate))/(1000*60*60*24));
    var days=Math.max(0,diff);
    document.getElementById('f-total-days').value=days+' day'+(days!==1?'s':'');
    setText('sum-days', days+' day'+(days!==1?'s':''));
    return days;
}

/* ══ SELECT PATIENT ══ */
function selectPatient(btn){
    var d=btn.dataset;
    document.getElementById('f-patient-id').value           = d.id;
    document.getElementById('f-admission-id').value         = d.admissionId||'';
    document.getElementById('f-patient-code').value         = d.code;
    document.getElementById('f-patient-name').value         = d.name;
    document.getElementById('f-patient-age-gender').value   = [d.age,d.gender].filter(Boolean).join(' / ');
    document.getElementById('f-patient-mobile').value       = d.mobile;
    document.getElementById('f-release-date').value         = todayISO();
    if(d.admissionDate) document.getElementById('f-admission-date').value = d.admissionDate.slice(0,10);

    document.getElementById('spb-avatar').textContent = (d.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent   = d.name;
    document.getElementById('spb-meta').textContent   = [d.code,d.age,d.gender,d.mobile,d.upozila,d.blood].filter(Boolean).join(' · ');

    setText('sum-code',    d.code||'—');
    setText('sum-name',    d.name||'—');
    setText('sum-admit',   d.admissionDate ? fmtDateBD(d.admissionDate) : '—');
    setText('sum-release', fmtDateBD(todayISO()));

    billItems=DEFAULT_BILL_ITEMS.map(function(item){ return Object.assign({},item); });
    renderBillTable();
    calcDays();
    updateConditionLabel();

    document.getElementById('step1-circle').className='step-circle step-done';
    document.getElementById('step1-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector-12').classList.add('done');
    document.getElementById('step2-circle').className='step-circle step-active';
    document.getElementById('step2-label').className='step-label-main step-label-active';
    document.getElementById('step2-sublabel').className='step-label-sub';
    document.getElementById('breadcrumb-current').textContent='Bill & Release';

    document.getElementById('panel-step1').style.display='none';
    document.getElementById('panel-step2').style.display='block';
    document.getElementById('panel-step3').style.display='none';

    loadPatientData(d.id);
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ══ LOAD PATIENT DATA (AJAX) ══ */
function loadPatientData(patientId){
    fetch(PATIENT_DATA_URL+'/'+patientId, {
        headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF_TOKEN}
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        if(data.success){
            renderPrescriptionHistory(data.fresh_prescriptions||[], data.round_prescriptions||[]);
        }
    })
    .catch(function(e){ console.warn('AJAX load failed:', e); });
}

function renderPrescriptionHistory(fresh, round){
    if(!fresh.length&&!round.length) return;
    document.getElementById('prescription-summary-card').style.display='block';
    document.getElementById('fresh-rx-list').innerHTML=fresh.map(function(rx){
        return '<div class="rx-history-item">'+
            '<div class="rx-history-label text-teal"><i class="fas fa-capsules mr-1"></i>Fresh Rx — '+esc(rx.date||'')+(rx.doctor?' · Dr. '+esc(rx.doctor):'')+'</div>'+
            rx.lines.map(function(l){ return '<div class="rx-history-line">• '+esc(l)+'</div>'; }).join('')+
        '</div>';
    }).join('');
    document.getElementById('round-rx-list').innerHTML=round.map(function(rx){
        return '<div class="rx-history-item">'+
            '<div class="rx-history-label text-blue"><i class="fas fa-sync-alt mr-1"></i>Round Rx — '+esc(rx.date||'')+(rx.doctor?' · Dr. '+esc(rx.doctor):'')+'</div>'+
            rx.lines.map(function(l){ return '<div class="rx-history-line">• '+esc(l)+'</div>'; }).join('')+
        '</div>';
    }).join('');
}

function toggleRxHistory(){
    var body=document.getElementById('rx-history-body'), label=document.getElementById('rx-toggle-label');
    var hidden=body.style.display==='none';
    body.style.display=hidden?'block':'none';
    label.textContent=hidden?'Hide':'Show';
}

function updateConditionLabel(){
    var sel=document.getElementById('f-condition');
    setText('sum-condition', sel?sel.options[sel.selectedIndex].text:'—');
}

/* ══ PROCESS RELEASE ══ */
function processRelease(){
    var patientId=gVal('f-patient-id'), admissionId=gVal('f-admission-id'), releaseDate=gVal('f-release-date');
    if(!patientId){ showAlert('warning','Please select a patient first.'); return; }
    if(!releaseDate){ showAlert('warning','Please set a release date.'); return; }
    if(!confirm('এই patient কে release এর জন্য submit করবেন?\nManager approval পেলে তবেই released হবে।')) return;

    var billData=recalcBill();
    var doctorSel=document.getElementById('f-doctor');
    var doctorName=doctorSel&&doctorSel.options.length?doctorSel.options[doctorSel.selectedIndex].text:'';

    var payload={
        patient_id    : patientId,
        admission_id  : admissionId,
        release_date  : releaseDate,
        notes         : gVal('f-notes'),
        condition     : gVal('f-condition'),
        doctor_name   : doctorName,
        bill_items    : billItems.filter(function(item){ return item.description.trim()!==''; }),
        bill_subtotal : billData.subtotal,
        bill_discount : billData.discountAmt,
        bill_grand    : billData.grand,
        bill_advance  : billData.advance,
        bill_due      : billData.due,
    };

    var btn=document.getElementById('btn-release');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';

    fetch(RELEASE_STORE_URL, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'},
        body:JSON.stringify(payload)
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        btn.disabled=false;
        btn.innerHTML='<i class="fas fa-paper-plane mr-2"></i> Submit for Approval';
        if(data.success){
            showToast(data.message||'Release request submitted! Waiting for manager approval.','success');
            showAlert('success','<i class="fas fa-check-circle mr-2"></i>'+(data.message||'Submitted for approval. Manager approval এর পর patient released হবে।'));
            generateReleaseSummary(billData, doctorName);
            setTimeout(function(){ backToStep1(); }, 4000);
        } else {
            showAlert('danger','<i class="fas fa-exclamation-circle mr-2"></i>'+(data.message||'Release failed.'));
        }
    })
    .catch(function(e){
        btn.disabled=false;
        btn.innerHTML='<i class="fas fa-paper-plane mr-2"></i> Submit for Approval';
        showAlert('danger','<i class="fas fa-exclamation-circle mr-2"></i> Network error: '+e.message);
    });
}

/* ══ GENERATE SUMMARY (STEP 3) ══ */
function generateReleaseSummary(billData, doctorName){
    var pName=gVal('f-patient-name')||'—', pCode=gVal('f-patient-code')||'—';
    var pAge=gVal('f-patient-age-gender')||'—', pMobile=gVal('f-patient-mobile')||'—';
    var admDate=gVal('f-admission-date'), relDate=gVal('f-release-date');
    var days=calcDays();
    var condSel=document.getElementById('f-condition');
    var condText=condSel?condSel.options[condSel.selectedIndex].text:'—';
    var notes=gVal('f-notes');

    setText('ib-name', pName); setText('ib-days', days+' day'+(days!==1?'s':''));
    setText('ib-total', taka(billData.grand)); setText('ib-due', taka(billData.due));
    setText('rx-badge-name', pName);

    setText('print-code',         pCode);
    setText('print-name',         pName);
    setText('print-age',          pAge);
    setText('print-mobile',       pMobile);
    setText('print-release-date', fmtDateBD(relDate));
    setText('print-admit-date',   fmtDateBD(admDate));
    setText('print-rel-date',     fmtDateBD(relDate));
    setText('print-days',         days+' দিন');
    setText('print-condition',    condText);
    setText('print-doctor',       doctorName||'—');

    setText('print-subtotal',     taka(billData.subtotal));
    setText('print-discount-val', taka(billData.discountAmt));
    setText('print-grand-total',  taka(billData.grand));
    setText('print-advance-val',  taka(billData.advance));
    setText('print-due-val',      taka(billData.due));

    var allItems = billItems.filter(function(item){ return item.description.trim() !== ''; });
    document.getElementById('print-bill-tbody').innerHTML = allItems.length
        ? allItems.map(function(item, i){
            var rowTotal = item.qty * item.unit_price;
            return '<tr>'+
                '<td>'+(i+1)+'</td>'+
                '<td>'+esc(item.description)+'</td>'+
                '<td style="text-align:center;">'+item.qty+'</td>'+
                '<td style="text-align:right;">'+(item.unit_price > 0 ? taka(item.unit_price) : '')+'</td>'+
                '<td style="text-align:right;">'+(rowTotal > 0 ? taka(rowTotal) : '')+'</td>'+
            '</tr>';
          }).join('')
        : '<tr><td colspan="5" class="text-center" style="padding:8px;color:#999;">No charges entered.</td></tr>';

    var notesEl=document.getElementById('print-notes');
    if(notesEl) notesEl.textContent=notes?'Notes: '+notes:'';

    var now=new Date().toLocaleString('en-BD',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
    setText('gen-time', now); setText('gen-time-display', now);
    setText('print-released-by','Nursing Staff');

    document.getElementById('step2-circle').className='step-circle step-done';
    document.getElementById('step2-circle').innerHTML='<i class="fas fa-check" style="font-size:11px;"></i>';
    document.getElementById('step-connector-23').classList.add('done');
    document.getElementById('step3-circle').className='step-circle step-active';
    document.getElementById('step3-label').className='step-label-main step-label-active';
    document.getElementById('step3-sublabel').className='step-label-sub';
    document.getElementById('breadcrumb-current').textContent='Summary';

    document.getElementById('panel-step2').style.display='none';
    document.getElementById('panel-step3').style.display='block';
    window.scrollTo({top:0,behavior:'smooth'});
}

function printBillOnly(){
    var billData=recalcBill();
    var doctorSel=document.getElementById('f-doctor');
    var doctorName=doctorSel&&doctorSel.options.length?doctorSel.options[doctorSel.selectedIndex].text:'';
    generateReleaseSummary(billData, doctorName);
    setTimeout(function(){ window.print(); },400);
}

/* ══ NAVIGATION ══ */
function backToStep1(){
    ['step1-circle','step2-circle','step3-circle'].forEach(function(id,i){
        document.getElementById(id).className=i===0?'step-circle step-active':'step-circle step-inactive';
        document.getElementById(id).innerHTML=String(i+1);
    });
    document.getElementById('step-connector-12').classList.remove('done');
    document.getElementById('step-connector-23').classList.remove('done');
    document.getElementById('step2-label').className='step-label-main step-label-inactive';
    document.getElementById('step2-sublabel').className='step-label-sub step-label-inactive';
    document.getElementById('step3-label').className='step-label-main step-label-inactive';
    document.getElementById('step3-sublabel').className='step-label-sub step-label-inactive';
    document.getElementById('breadcrumb-current').textContent='Select Patient';
    document.getElementById('panel-step1').style.display='block';
    document.getElementById('panel-step2').style.display='none';
    document.getElementById('panel-step3').style.display='none';
    window.scrollTo({top:0,behavior:'smooth'});
}

/* ══ TABLE FILTER ══ */
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
document.getElementById('f-release-date').addEventListener('change', function(){
    calcDays(); setText('sum-release', fmtDateBD(this.value));
});
document.getElementById('f-condition').addEventListener('change', updateConditionLabel);
document.addEventListener('DOMContentLoaded', function(){ renderBillTable(); });
</script>
@stop