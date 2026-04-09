@extends('adminlte::page')

@section('title', 'Diagnostic Payment | Professor Clinic')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0 page-main-title">
            <span class="page-title-icon"><i class="fas fa-flask"></i></span>
            Diagnostic Payment
        </h1>
        <ol class="breadcrumb mt-1 p-0" style="background:transparent; font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('nursing.index') }}">Nursing</a></li>
            <li class="breadcrumb-item active" id="breadcrumb-current">Select Patient</li>
        </ol>
    </div>
    <div>
        <a href="{{ route('nursing.index') }}" class="btn-back-modern">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>
@stop

@section('content')

{{-- ALERT --}}
<div id="save-alert" class="alert d-none mb-3 modern-alert" role="alert"></div>

{{-- STEP INDICATOR --}}
<div class="step-track-card mb-4">
    <div class="step-track-inner">
        <div class="step-item">
            <div class="step-circle step-active" id="step1-circle">1</div>
            <div class="step-text ml-2">
                <div class="step-label-main step-label-active" id="step1-label">Step 1</div>
                <div class="step-label-sub">Select Patient</div>
            </div>
        </div>
        <div class="step-connector-line" id="step-connector"></div>
        <div class="step-item">
            <div class="step-circle step-inactive" id="step2-circle">2</div>
            <div class="step-text ml-2">
                <div class="step-label-main step-label-inactive" id="step2-label">Step 2</div>
                <div class="step-label-sub">Add Tests &amp; Pay</div>
            </div>
        </div>
    </div>
</div>

{{-- ===============================
=============================            STEP 1 — SELECT PATIENT
====================================
======================== --}}
<div id="panel-step1">

    {{-- Admitted Patients Card --}}
    <div class="modern-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-diag-soft">
                    <i class="fas fa-hospital-user text-diag"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Admitted Patients</h5>
                    <small class="text-muted">Select an admitted patient to collect diagnostic payment</small>
                </div>
            </div>
            <span class="patient-total-pill diag-pill">
                <i class="fas fa-bed mr-1"></i>
                {{ $admittedPatients->total() ?? $admittedPatients->count() }} Admitted
            </span>
        </div>

        <div class="inline-search-bar">
            <div class="search-input-group search-input-group-inline">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="patientSearch" class="search-input" placeholder="Search by name, code, or mobile...">
                <button class="search-btn search-btn-diag" type="button" onclick="filterAdmitted()">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="table-scroll-wrap">
                <table class="modern-table" id="admittedTable">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="width:80px">Adm. ID</th>
                            <th>Patient Name</th>
                            <th style="width:55px">Age</th>
                            <th style="width:55px">Sex</th>
                            <th style="width:130px">Mobile</th>
                            <th style="width:110px">Adm. Date</th>
                            <th style="width:65px">Blood</th>
                            <th style="width:80px;text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admittedPatients as $ap)
                            @php $apGender = strtolower($ap->gender ?? ''); @endphp
                            <tr class="admitted-row">
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td><span class="adm-badge">#{{ $ap->admission_id }}</span></td>
                                <td>
                                    <div class="name-cell">
                                        <div class="mini-avatar avatar-diag">
                                            {{ strtoupper(substr($ap->patient_name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $ap->patient_name ?? '—' }}</strong>
                                            <br><small class="text-muted" style="font-size:11px">{{ $ap->patient_code ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $ap->patient_age ?? '—' }}</td>
                                <td>
                                    @if($apGender === 'male')
                                        <span class="gender-badge gender-male"><i class="fas fa-mars mr-1"></i>M</span>
                                    @elseif($apGender === 'female')
                                        <span class="gender-badge gender-female"><i class="fas fa-venus mr-1"></i>F</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-monospace">{{ $ap->mobile_no ?? '—' }}</td>
                                <td class="small text-muted">
                                    @if($ap->admission_date)
                                        {{ \Carbon\Carbon::parse($ap->admission_date)->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($ap->blood_group ?? null)
                                        <span class="blood-badge">{{ $ap->blood_group }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-select btn-select-diag" onclick="selectPatient(this)"
                                            data-admission-id="{{ $ap->admission_id }}"
                                            data-patient-id="{{ $ap->patient_id }}"
                                            data-name="{{ $ap->patient_name ?? '' }}"
                                            data-age="{{ $ap->patient_age ?? '' }}"
                                            data-code="{{ $ap->patient_code ?? '' }}"
                                            data-mobile="{{ $ap->mobile_no ?? '' }}"
                                            data-blood="{{ $ap->blood_group ?? '' }}"
                                            data-admission-date="{{ $ap->admission_date ?? '' }}">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-hospital-user"></i>
                                        <p>No admitted patients found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($admittedPatients, 'links'))
                <div class="pagination-bar">
                    <small class="text-muted">
                        Showing {{ $admittedPatients->firstItem() ?? 0 }}–{{ $admittedPatients->lastItem() ?? 0 }}
                        of <strong>{{ $admittedPatients->total() ?? 0 }}</strong>
                    </small>
                    {{ $admittedPatients->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Past Diagnostic Payments Card --}}
    <div class="modern-card past-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-diag-soft">
                    <i class="fas fa-receipt text-diag"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Past Diagnostic Payments</h5>
                    <small class="text-muted">All previously collected diagnostic payments</small>
                </div>
            </div>
            <span class="patient-total-pill diag-pill">
                <i class="fas fa-file-invoice-dollar mr-1"></i>
                {{ $pastPayments->total() ?? $pastPayments->count() }} Records
            </span>
        </div>

        <div class="inline-search-bar" style="border-bottom-color:#b2dfdb">
            <div class="search-input-group search-input-group-inline">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="paySearch" class="search-input" placeholder="Search by name, receipt no..." onkeyup="filterPayTable()">
                <button class="search-btn search-btn-diag" type="button" onclick="filterPayTable()">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </div>
        </div>

        <div class="modern-card-body pt-0">
            <div class="table-scroll-wrap">
                <table class="modern-table" id="payTable">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="width:115px">Receipt No</th>
                            <th>Patient</th>
                            <th style="width:110px">Date</th>
                            <th style="width:88px">Total</th>
                            <th style="width:88px">Paid</th>
                            <th style="width:88px">Due</th>
                            <th style="width:78px">Status</th>
                            <th style="width:85px;text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pastPayments as $pp)
                            <tr class="pay-row">
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td><span class="receipt-badge">{{ $pp->receipt_no }}</span></td>
                                <td>
                                    <div class="name-cell">
                                        <div class="mini-avatar avatar-diag">
                                            {{ strtoupper(substr($pp->patient_name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $pp->patient_name ?? '—' }}</strong>
                                            <br><small class="text-muted" style="font-size:11px">{{ $pp->patient_code ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    {{ \Carbon\Carbon::parse($pp->payment_date)->format('d/m/Y') }}
                                    <br><span style="font-size:10px">{{ \Carbon\Carbon::parse($pp->payment_date)->diffForHumans() }}</span>
                                </td>
                                <td class="small font-weight-bold">? {{ number_format($pp->total_amount, 0) }}</td>
                                <td class="small font-weight-bold text-success">? {{ number_format($pp->paid_amount, 0) }}</td>
                                <td class="small font-weight-bold {{ $pp->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                    ? {{ number_format($pp->due_amount, 0) }}
                                </td>
                                <td>
                                    @if($pp->status === 'paid')
                                        <span class="status-badge status-paid"><i class="fas fa-check-circle mr-1"></i>Paid</span>
                                    @elseif($pp->status === 'partial')
                                        <span class="status-badge status-partial"><i class="fas fa-adjust mr-1"></i>Partial</span>
                                    @else
                                        <span class="status-badge status-due"><i class="fas fa-exclamation-circle mr-1"></i>Due</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-view-receipt" onclick="viewReceipt({{ $pp->id }})">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-receipt"></i>
                                        <p>No diagnostic payments found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($pastPayments, 'links'))
                <div class="pagination-bar">
                    <small class="text-muted">
                        Showing {{ $pastPayments->firstItem() ?? 0 }}–{{ $pastPayments->lastItem() ?? 0 }}
                        of <strong>{{ $pastPayments->total() ?? 0 }}</strong>
                    </small>
                    {{ $pastPayments->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

</div>{{-- /#panel-step1 --}}

{{-- ===============================
=============================            STEP 2 — ADD TESTS & COLLECT PAYMENT
====================================
======================== --}}
<div id="panel-step2" style="display:none">

    {{-- Selected Patient Bar --}}
    <div class="patient-bar mb-4">
        <div class="patient-bar-left">
            <div class="psb-avatar" id="spb-avatar">A</div>
            <div>
                <div class="psb-name" id="spb-name"></div>
                <div class="psb-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="patient-bar-right">
            <span class="psb-dot"></span>
            <span class="psb-status-label">Diagnostic Payment</span>
            <button type="button" class="btn-change" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    {{-- Previous Payments for this Admission --}}
    <div id="prev-payments-box" class="modern-card prev-pay-card mb-4" style="display:none">
        <div class="modern-card-header" style="background:linear-gradient(135deg,#e0f2f1,#fff)">
            <div class="modern-card-title">
                <span class="card-title-icon" style="background:#e0f2f1">
                    <i class="fas fa-history text-diag"></i>
                </span>
                <div>
                    <h6 class="mb-0 font-weight-bold" style="color:#00695c">Previous Diagnostic Payments</h6>
                    <small class="text-muted">For this admission</small>
                </div>
            </div>
            <span class="patient-total-pill diag-pill">
                Total Paid: <strong id="prev-total-paid">? 0</strong>
            </span>
        </div>
        <div class="modern-card-body pt-2 pb-2">
            <div id="prev-payments-list"></div>
        </div>
    </div>

    {{-- Payment Form Card --}}
    <div class="modern-card" id="pay-form-card">
        <div class="modern-card-header">
            <div class="modern-card-title">
                <span class="card-title-icon bg-diag-soft">
                    <i class="fas fa-flask text-diag"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold">Add Tests &amp; Collect Payment</h5>
                    <small class="text-muted">Add X-Ray, ECG, Pathology and other diagnostic tests</small>
                </div>
            </div>
        </div>
        <div class="modern-card-body">
            <input type="hidden" id="f-patient-id">
            <input type="hidden" id="f-admission-id">
            <input type="hidden" id="f-patient-code">

            {{-- Patient Info Row --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="field-group">
                        <label class="field-label">Patient Name</label>
                        <input type="text" class="field-input" id="f-patient-name" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="field-group">
                        <label class="field-label">Age</label>
                        <input type="text" class="field-input" id="f-patient-age" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="field-group">
                        <label class="field-label">Payment Date</label>
                        <input type="date" class="field-input" id="f-date">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="field-group">
                        <label class="field-label">Payment Method</label>
                        <select class="field-input" id="f-method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="divider mb-4">

            {{-- Tests Section Header --}}
            <div class="section-header mb-3">
                <div class="section-title">
                    <i class="fas fa-vials mr-2 text-diag"></i>
                    Diagnostic Tests
                    <span class="test-count-badge" id="test-count-badge">0</span>
                </div>
                <button type="button" class="btn-add-test" onclick="addTestRow()">
                    <i class="fas fa-plus mr-1"></i> Add Test
                </button>
            </div>

            {{-- Tests Table --}}
            <div class="diag-table-wrap mb-3">
                <table class="diag-table" id="test-table">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th style="width:115px">Category</th>
                            <th>Test Name</th>
                            <th style="width:90px">Price (?)</th>
                            <th style="width:80px">Discount</th>
                            <th style="width:80px">Net Price</th>
                            <th style="width:58px">Qty</th>
                            <th style="width:92px">Subtotal</th>
                            <th style="width:105px">Remarks</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="test-tbody">
                        <tr id="empty-test-row">
                            <td colspan="10">
                                <div class="table-empty-state">
                                    <i class="fas fa-vials"></i>
                                    <span>No tests added yet. Click "Add Test" to begin.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="tfoot-row">
                            <td colspan="7" class="text-right font-weight-bold">Total:</td>
                            <td class="font-weight-bold text-diag" id="tfoot-total">? 0</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Quick Add Pills --}}
            <div class="quick-add-wrap mb-4">
                <span class="quick-add-label"><i class="fas fa-bolt mr-1"></i> Quick Add:</span>
                <div class="quick-pills">
                    @foreach($categories as $cat)
                        <button type="button" class="quick-pill" onclick="addTestRow('{{ $cat }}')">
                            <i class="fas fa-plus" style="font-size:9px;margin-right:4px"></i>{{ $cat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <hr class="divider mb-4">

            {{-- Payment Summary --}}
            <div class="pay-summary-box">
                <div class="pay-summary-grid">
                    <div class="pay-summary-item">
                        <div class="pay-summary-label">Gross Total</div>
                        <div class="pay-summary-value" id="sum-gross">? 0</div>
                    </div>
                    <div class="pay-summary-item">
                        <div class="pay-summary-label">Bill Discount</div>
                        <div class="prefix-input-wrap">
                            <span class="prefix-symbol">?</span>
                            <input type="number" class="prefix-input" id="f-discount" placeholder="0" min="0" oninput="recalcPayment()">
                        </div>
                    </div>
                    <div class="pay-summary-item">
                        <div class="pay-summary-label">Net Payable</div>
                        <div class="pay-summary-value text-diag font-weight-bold" id="sum-net">? 0</div>
                    </div>
                    <div class="pay-summary-item">
                        <div class="pay-summary-label">Paid Now <span class="text-danger">*</span></div>
                        <div class="prefix-input-wrap prefix-accent">
                            <span class="prefix-symbol">?</span>
                            <input type="number" class="prefix-input" id="f-paid" placeholder="0" min="0" oninput="recalcPayment()">
                        </div>
                    </div>
                    <div class="pay-summary-item">
                        <div class="pay-summary-label">Due Amount</div>
                        <div class="pay-summary-value text-danger font-weight-bold" id="sum-due">? 0</div>
                    </div>
                </div>
            </div>

            {{-- Received By & Notes --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="field-group">
                        <label class="field-label">Received By</label>
                        <input type="text" class="field-input" id="f-received-by" placeholder="Staff name...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label class="field-label">Notes</label>
                        <input type="text" class="field-input" id="f-notes" placeholder="Optional notes...">
                    </div>
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="form-footer mt-4">
                <button type="button" class="btn-back-step" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" class="btn-save-diag" id="btn-save" onclick="savePayment()">
                    <i class="fas fa-save mr-1"></i> Save &amp; Print Receipt
                </button>
            </div>
        </div>
    </div>

    {{-- Receipt View --}}
    <div id="receipt-view" style="display:none">
        {{-- Receipt Summary Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-card rx-card-diag">
                    <div class="rx-card-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="rx-card-label">Patient</div>
                        <div class="rx-card-value" id="ib-name">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-card rx-card-teal">
                    <div class="rx-card-icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="rx-card-label">Receipt No</div>
                        <div class="rx-card-value" id="ib-receipt">—</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="rx-card rx-card-green">
                    <div class="rx-card-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <div class="rx-card-label">Paid</div>
                        <div class="rx-card-value" id="ib-paid">? 0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="rx-card rx-card-orange">
                    <div class="rx-card-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div>
                        <div class="rx-card-label">Due</div>
                        <div class="rx-card-value" id="ib-due">? 0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <div class="modern-card-title">
                    <span class="card-title-icon bg-diag-soft">
                        <i class="fas fa-receipt text-diag"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Diagnostic Payment Receipt</h5>
                        <small class="text-muted">Ready to print</small>
                    </div>
                </div>
                <span class="rx-saved-badge">
                    <i class="fas fa-check-circle mr-1"></i> Saved
                </span>
            </div>
            <div class="modern-card-body p-0">
                <div id="prescription-print-area">
                    <div class="receipt-wrapper">
                        {{-- Receipt Header --}}
                        <div class="receipt-header">
                            <div class="receipt-logo-wrap">
                                <div class="receipt-logo">CP</div>
                                <div class="receipt-logo-sub">Professor Clinic</div>
                            </div>
                            <div class="receipt-clinic-info">
                                <div class="receipt-clinic-name">??????? ???????</div>
                                <div class="receipt-address">?????????, ????????</div>
                                <div class="receipt-phones">? 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
                                <div class="receipt-title-tag">Diagnostic Payment Receipt</div>
                            </div>
                        </div>
                        {{-- Receipt Patient Info --}}
                        <div class="receipt-patient-row">
                            <div class="receipt-field"><label>Name:</label><div class="receipt-value" id="r-name">—</div></div>
                            <div class="receipt-field"><label>Age:</label><div class="receipt-value" id="r-age">—</div></div>
                            <div class="receipt-field"><label>Code:</label><div class="receipt-value" id="r-code">—</div></div>
                            <div class="receipt-field"><label>Date:</label><div class="receipt-value" id="r-date">—</div></div>
                            <div class="receipt-field"><label>Receipt:</label><div class="receipt-value font-weight-bold" id="r-receipt">—</div></div>
                        </div>
                        {{-- Receipt Items Table --}}
                        <div style="padding:0 16px">
                            <table class="receipt-table" id="r-test-table">
                                <thead>
                                    <tr>
                                        <th style="width:32px">#</th>
                                        <th style="width:88px">Category</th>
                                        <th>Test Name</th>
                                        <th style="width:52px;text-align:center">Qty</th>
                                        <th style="width:88px;text-align:right">Price (?)</th>
                                        <th style="width:76px;text-align:right">Disc.</th>
                                        <th style="width:92px;text-align:right">Subtotal (?)</th>
                                    </tr>
                                </thead>
                                <tbody id="r-test-tbody"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;border-top:2px solid #333">Gross Total</td>
                                        <td class="text-right font-weight-bold" style="padding:6px 8px;border-top:2px solid #333" id="r-gross">? 0</td>
                                    </tr>
                                    <tr id="r-discount-row" style="display:none">
                                        <td colspan="6" class="text-right" style="padding:3px 8px;color:#e65100">Discount</td>
                                        <td class="text-right" style="padding:3px 8px;color:#e65100" id="r-discount">? 0</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;font-size:14px">Net Payable</td>
                                        <td class="text-right font-weight-bold" style="padding:6px 8px;font-size:14px" id="r-net">? 0</td>
                                    </tr>
                                    <tr style="background:#e0f2f1">
                                        <td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;color:#00695c">Paid</td>
                                        <td class="text-right font-weight-bold" style="padding:6px 8px;color:#00695c" id="r-paid">? 0</td>
                                    </tr>
                                    <tr id="r-due-row" style="display:none">
                                        <td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;color:#c62828">Due</td>
                                        <td class="text-right font-weight-bold" style="padding:6px 8px;color:#c62828" id="r-due">? 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        {{-- Receipt Footer --}}
                        <div class="receipt-footer">
                            <div class="receipt-footer-left">
                                <div>Payment Method: <strong id="r-method">Cash</strong></div>
                                <div class="receipt-note" id="r-notes-wrap" style="display:none">
                                    Note: <span id="r-notes"></span>
                                </div>
                            </div>
                            <div class="receipt-footer-right">
                                <div class="receipt-sig-line">Received By</div>
                                <div class="receipt-sig-name" id="r-received-by">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modern-card-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Generated: <span id="gen-time">—</span>
                </small>
                <div style="display:flex;gap:8px">
                    <button onclick="printRx()" class="btn-rx-action btn-rx-print">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn-rx-action btn-rx-edit" onclick="editReceipt()">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button type="button" class="btn-rx-action btn-rx-new" onclick="backToStep1()">
                        <i class="fas fa-plus mr-1"></i> New
                    </button>
                </div>
            </div>
        </div>
    </div>{{-- /#receipt-view --}}

</div>{{-- /#panel-step2 --}}

{{-- ===============================
=============================            MODAL — VIEW PAST RECEIPT
====================================
======================== --}}
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content rx-modal-content">
            <div class="modal-header rx-modal-header">
                <div class="d-flex align-items-center">
                    <div class="rx-modal-icon mr-3"><i class="fas fa-receipt"></i></div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold text-white">Diagnostic Receipt</h5>
                        <small class="modal-subtitle-text" id="modal-subtitle">Loading...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap:8px">
                    <button type="button" class="btn-modal-print" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button type="button" class="btn-modal-close" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="modal-loading" class="modal-state-wrap">
                    <div style="font-size:34px;color:#00796b;margin-bottom:12px"><i class="fas fa-spinner fa-spin"></i></div>
                    <p class="modal-state-text">Loading receipt...</p>
                </div>
                <div id="modal-error" class="modal-state-wrap d-none">
                    <div style="font-size:36px;color:#ef5350;margin-bottom:10px"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="modal-state-text" id="modal-error-msg">Failed to load receipt.</p>
                </div>
                <div id="modal-rx-area" class="d-none">
                    <div class="modal-summary-bar">
                        <div class="modal-summary-item msi-diag">
                            <i class="fas fa-user"></i>
                            <div><div class="msi-label">Patient</div><div class="msi-val" id="m-ib-name">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-teal">
                            <i class="fas fa-receipt"></i>
                            <div><div class="msi-label">Receipt</div><div class="msi-val" id="m-ib-receipt">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-green">
                            <i class="fas fa-money-bill-wave"></i>
                            <div><div class="msi-label">Paid</div><div class="msi-val" id="m-ib-paid">—</div></div>
                        </div>
                        <div class="modal-summary-item msi-orange">
                            <i class="fas fa-calendar"></i>
                            <div><div class="msi-label">Date</div><div class="msi-val" id="m-ib-date">—</div></div>
                        </div>
                    </div>
                    <div id="modal-prescription-print-area" style="padding:20px 24px"></div>
                </div>
            </div>
            <div class="modal-footer rx-modal-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Saved: <span id="m-saved-time">—</span>
                </small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Print Overlay --}}
<div id="print-overlay"></div>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
<style>
/* ===============================
====================                     ROOT VARIABLES
====================================
=============== */
:root {
    --diag-deep:  #00695C;
    --diag-mid:   #00796B;
    --diag-light: #E0F2F1;
    --diag-soft:  #B2DFDB;
    --green-deep: #2E7D32;
    --green-mid:  #43A047;
    --blue-deep:  #1565C0;
    --blue-mid:   #1976D2;
    --orange:     #E65100;
    --text-dark:  #1a2332;
    --text-muted: #6b7a90;
    --border:     #e4e9f0;
    --bg-page:    #f0f4f0;
    --radius-sm:  6px;
    --radius-md:  10px;
    --radius-lg:  16px;
    --shadow-sm:  0 1px 4px rgba(0,0,0,.06);
    --shadow-md:  0 4px 16px rgba(0,0,0,.08);
    --font:       'DM Sans','Hind Siliguri',Arial,sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body, .content-wrapper { background: var(--bg-page) !important; font-family: var(--font); }
.text-diag { color: var(--diag-mid) !important; }

/* ===============================
====================                     PAGE HEADER
====================================
=============== */
.page-main-title {
    font-size: 22px; font-weight: 700; color: var(--text-dark);
    display: flex; align-items: center; gap: 10px;
}
.page-title-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--diag-light);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--diag-mid); font-size: 17px;
}
.btn-back-modern {
    background: #fff; border: 1.5px solid var(--border);
    color: var(--text-dark); border-radius: var(--radius-sm);
    font-weight: 500; padding: 7px 16px; font-size: 13px;
    text-decoration: none; display: inline-flex; align-items: center;
    transition: all .2s;
}
.btn-back-modern:hover {
    background: var(--diag-light); border-color: var(--diag-mid);
    color: var(--diag-deep); text-decoration: none;
}

/* ===============================
====================                     STEP TRACK
====================================
=============== */
.step-track-card {
    background: #fff; border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm); border: 1px solid var(--border);
    padding: 16px 24px;
}
.step-track-inner { display: flex; align-items: center; }
.step-item { display: flex; align-items: center; }
.step-text { margin-left: 10px; }
.step-circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; flex-shrink: 0;
    transition: all .35s; border: 2.5px solid transparent;
}
.step-active   { background: var(--diag-mid); color: #fff; border-color: var(--diag-mid); box-shadow: 0 0 0 4px rgba(0,121,107,.15); }
.step-done     { background: var(--diag-deep); color: #fff; border-color: var(--diag-deep); }
.step-inactive { background: #fff; color: #ccc; border-color: #ddd; }
.step-label-main { font-size: 13px; font-weight: 700; line-height: 1.2; }
.step-label-sub  { font-size: 11px; color: var(--text-muted); }
.step-label-active   { color: var(--diag-mid); }
.step-label-inactive { color: #bbb; }
.step-connector-line {
    flex: 1; max-width: 140px; height: 3px;
    background: #e8ecf0; margin: 0 18px; border-radius: 2px;
    transition: background .4s;
}
.step-connector-line.done { background: var(--diag-deep); }

/* ===============================
====================                     ALERT
====================================
=============== */
.modern-alert {
    border-radius: var(--radius-md); border: none;
    font-size: 13.5px; font-weight: 500; box-shadow: var(--shadow-sm);
}

/* ===============================
====================                     CARDS
====================================
=============== */
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
.modern-card-title h5 { font-size: 15px; }
.card-title-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.bg-diag-soft { background: var(--diag-light); }
.modern-card-body { padding: 24px; }
.modern-card-footer {
    padding: 14px 24px; border-top: 1px solid var(--border);
    background: #fafbfd; display: flex; align-items: center;
    justify-content: space-between;
}
.patient-total-pill {
    border-radius: 20px; padding: 5px 14px;
    font-size: 12.5px; font-weight: 600;
}
.diag-pill { background: var(--diag-light); color: var(--diag-deep); }
.past-card { border-top: 3px solid var(--diag-mid); }
.prev-pay-card { border-left: 4px solid var(--diag-mid); }

/* ===============================
====================                     SEARCH BAR
====================================
=============== */
.inline-search-bar {
    padding: 12px 24px; background: #fafbff;
    border-bottom: 2px solid var(--diag-soft);
}
.search-input-group {
    display: flex; align-items: center; background: #fff;
    border: 2px solid var(--border); border-radius: 10px;
    overflow: hidden; transition: border-color .2s;
    box-shadow: var(--shadow-sm);
}
.search-input-group:focus-within {
    border-color: var(--diag-mid);
    box-shadow: 0 0 0 3px rgba(0,121,107,.1);
}
.search-input-group-inline { width: 100%; }
.search-icon { padding: 0 12px; color: #aab; font-size: 15px; }
.search-input {
    flex: 1; border: none; outline: none;
    padding: 10px 6px; font-size: 14px;
    background: transparent; color: var(--text-dark);
    font-family: var(--font);
}
.search-btn {
    border: none; padding: 10px 22px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: background .2s;
}
.search-btn-diag { background: var(--diag-mid); color: #fff; }
.search-btn-diag:hover { background: var(--diag-deep); }

/* ===============================
====================                     TABLES
====================================
=============== */
.table-scroll-wrap {
    overflow-x: auto; overflow-y: auto;
    max-height: calc(100vh - 350px);
}
.modern-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
}
.modern-table thead th {
    background: #f0faf8; color: var(--text-dark);
    font-size: 11.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .55px;
    padding: 10px 13px; border-bottom: 2px solid var(--diag-soft);
    white-space: nowrap; position: sticky; top: 0; z-index: 10;
}
.modern-table tbody tr { transition: background .15s; }
.modern-table tbody tr:hover { background: #f0faf8; }
.modern-table tbody td {
    padding: 10px 13px; border-bottom: 1px solid var(--border);
    font-size: 13px; color: var(--text-dark); vertical-align: middle;
}
.modern-table tbody tr:last-child td { border-bottom: none; }

/* BADGES */
.adm-badge {
    background: #e0f2f1; color: var(--diag-deep);
    border-radius: 5px; padding: 2px 8px;
    font-size: 11.5px; font-weight: 700; font-family: monospace;
}
.receipt-badge {
    background: var(--diag-light); color: var(--diag-deep);
    border-radius: 5px; padding: 2px 8px;
    font-size: 11.5px; font-weight: 700; font-family: monospace;
}
.blood-badge {
    background: #ffebee; color: #c62828;
    border-radius: 5px; padding: 2px 8px;
    font-size: 11px; font-weight: 700;
}
.gender-badge {
    padding: 2px 8px; border-radius: 5px; font-size: 11px; font-weight: 700;
}
.gender-male { background: #e3f2fd; color: #1565c0; }
.gender-female { background: #fce4ec; color: #c2185b; }
.status-badge {
    padding: 3px 9px; border-radius: 5px; font-size: 11px; font-weight: 600;
}
.status-paid { background: #e8f5e9; color: #2e7d32; }
.status-partial { background: #fff3e0; color: #f57c00; }
.status-due { background: #ffebee; color: #c62828; }

/* NAME CELL */
.name-cell { display: flex; align-items: center; gap: 10px; }
.mini-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.avatar-diag { background: var(--diag-mid); }

/* EMPTY STATE */
.empty-state {
    text-align: center; padding: 40px 20px; color: var(--text-muted);
}
.empty-state i { font-size: 48px; margin-bottom: 16px; opacity: .6; }
.empty-state p { margin: 0; font-size: 15px; }

/* PAGINATION */
.pagination-bar {
    padding: 16px 24px; background: #fafbff;
    border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.pagination-bar .pagination { margin: 0; }

/* ===============================
====================                     PATIENT BAR
====================================
=============== */
.patient-bar {
    background: linear-gradient(135deg, var(--diag-mid), var(--diag-deep));
    border-radius: var(--radius-lg); padding: 20px 24px;
    color: #fff; display: flex; align-items: center; justify-content: space-between;
}
.patient-bar-left { display: flex; align-items: center; gap: 14px; }
.psb-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,.2); display: flex; align-items: center;
    justify-content: center; font-size: 20px; font-weight: 700;
}
.psb-name { font-size: 18px; font-weight: 700; margin-bottom: 2px; }
.psb-meta { font-size: 13px; opacity: .9; }
.patient-bar-right { display: flex; align-items: center; gap: 12px; }
.psb-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #4caf50; animation: pulse 2s infinite;
}
.psb-status-label { font-size: 13px; font-weight: 600; }
.btn-change {
    background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: var(--radius-sm); padding: 6px 12px;
    font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s;
}
.btn-change:hover { background: rgba(255,255,255,.3); }

/* ===============================
====================                     PREV PAYMENTS
====================================
=============== */
#prev-payments-list .mini-item {
    background: #f0faf8; border-radius: var(--radius-sm);
    padding: 10px 14px; margin-bottom: 8px; border-left: 3px solid var(--diag-mid);
}
#prev-payments-list .mini-item:last-child { margin-bottom: 0; }
.mini-item-row { display: flex; justify-content: space-between; align-items: center; }
.mini-item-date { font-size: 12px; color: var(--text-muted); }
.mini-item-receipt { font-size: 12px; font-weight: 600; color: var(--diag-deep); }
.mini-item-amount { font-size: 13px; font-weight: 700; color: var(--diag-deep); }

/* ===============================
====================                     FORM FIELDS
====================================
=============== */
.field-group { margin-bottom: 16px; }
.field-label {
    display: block; font-size: 12px; font-weight: 600;
    color: var(--text-dark); margin-bottom: 6px;
}
.field-input {
    width: 100%; padding: 10px 14px; border: 2px solid var(--border);
    border-radius: var(--radius-sm); font-size: 14px;
    background: #fff; color: var(--text-dark);
    transition: border-color .2s;
}
.field-input:focus { outline: none; border-color: var(--diag-mid); }
.field-input:read-only { background: #f8f9fa; color: var(--text-muted); }

/* ===============================
====================                     DIVIDER
====================================
=============== */
.divider {
    border: none; height: 1px; background: var(--border);
    margin: 0;
}

/* ===============================
====================                     SECTION HEADER
====================================
=============== */
.section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
}
.section-title {
    font-size: 16px; font-weight: 700; color: var(--text-dark);
    display: flex; align-items: center; gap: 8px;
}
.test-count-badge {
    background: var(--diag-mid); color: #fff;
    border-radius: 12px; padding: 2px 8px;
    font-size: 11px; font-weight: 700; margin-left: 8px;
}
.btn-add-test {
    background: var(--diag-mid); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: 8px 16px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background .2s;
}
.btn-add-test:hover { background: var(--diag-deep); }

/* ===============================
====================                     DIAGNOSTIC TABLE
====================================
=============== */
.diag-table-wrap { border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }
.diag-table {
    width: 100%; border-collapse: collapse; margin: 0;
}
.diag-table thead th {
    background: #f0faf8; color: var(--text-dark);
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    padding: 8px 12px; border-bottom: 2px solid var(--diag-soft);
    white-space: nowrap;
}
.diag-table tbody td {
    padding: 8px 12px; border-bottom: 1px solid var(--border);
    font-size: 12px; vertical-align: middle;
}
.diag-table tbody tr:last-child td { border-bottom: none; }
.diag-table tbody tr:hover { background: #fafcfc; }
.diag-table select, .diag-table input {
    width: 100%; padding: 4px 8px; border: 1px solid var(--border);
    border-radius: 4px; font-size: 12px;
}
.table-empty-state {
    text-align: center; padding: 24px; color: var(--text-muted);
    font-size: 13px;
}
.table-empty-state i { display: block; font-size: 24px; margin-bottom: 8px; opacity: .6; }

/* QUICK ADD */
.quick-add-wrap { display: flex; align-items: center; gap: 12px; }
.quick-add-label {
    font-size: 12px; font-weight: 600; color: var(--text-dark);
    white-space: nowrap;
}
.quick-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.quick-pill {
    background: #f0faf8; border: 1px solid var(--diag-soft);
    color: var(--diag-deep); border-radius: 16px; padding: 4px 12px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s;
}
.quick-pill:hover {
    background: var(--diag-mid); color: #fff; border-color: var(--diag-mid);
}

/* ===============================
====================                     PAYMENT SUMMARY
====================================
=============== */
.pay-summary-box {
    background: linear-gradient(135deg, #f0faf8, #fff);
    border: 1px solid var(--diag-soft); border-radius: var(--radius-md);
    padding: 20px;
}
.pay-summary-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
}
.pay-summary-item { text-align: center; }
.pay-summary-label {
    font-size: 12px; font-weight: 600; color: var(--text-muted);
    margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px;
}
.pay-summary-value {
    font-size: 18px; font-weight: 700; color: var(--text-dark);
}
.prefix-input-wrap {
    display: inline-flex; align-items: center;
    background: #fff; border: 2px solid var(--border);
    border-radius: var(--radius-sm); overflow: hidden;
    transition: border-color .2s;
}
.prefix-input-wrap:focus-within { border-color: var(--diag-mid); }
.prefix-symbol {
    padding: 8px 12px; background: #f8f9fa;
    color: var(--text-dark); font-size: 13px; font-weight: 600;
    border-right: 1px solid var(--border);
}
.prefix-input {
    border: none; outline: none; padding: 8px 10px;
    font-size: 14px; font-weight: 600; color: var(--text-dark);
    background: transparent; width: 100px;
}
.prefix-accent .prefix-symbol { background: var(--diag-light); color: var(--diag-deep); }

/* ===============================
====================                     FORM FOOTER
====================================
=============== */
.form-footer {
    padding-top: 24px; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.btn-back-step {
    background: #fff; border: 1.5px solid var(--border);
    color: var(--text-dark); border-radius: var(--radius-sm);
    padding: 10px 20px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all .2s;
}
.btn-back-step:hover {
    background: var(--bg-page); border-color: var(--diag-mid);
}
.btn-save-diag {
    background: var(--diag-mid); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: 10px 20px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: background .2s;
}
.btn-save-diag:hover { background: var(--diag-deep); }

/* ===============================
====================                     RECEIPT VIEW
====================================
=============== */
.rx-card {
    background: #fff; border-radius: var(--radius-md);
    padding: 20px; box-shadow: var(--shadow-sm);
    border-left: 4px solid;
    display: flex; align-items: center; gap: 16px;
}
.rx-card-diag { border-color: var(--diag-mid); }
.rx-card-teal { border-color: var(--diag-mid); }
.rx-card-green { border-color: var(--green-mid); }
.rx-card-orange { border-color: var(--orange); }
.rx-card-icon {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff;
}
.rx-card-diag .rx-card-icon { background: var(--diag-mid); }
.rx-card-teal .rx-card-icon { background: var(--diag-mid); }
.rx-card-green .rx-card-icon { background: var(--green-mid); }
.rx-card-orange .rx-card-icon { background: var(--orange); }
.rx-card-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; }
.rx-card-value { font-size: 16px; font-weight: 700; color: var(--text-dark); }

.rx-saved-badge {
    background: var(--green-mid); color: #fff;
    border-radius: 20px; padding: 6px 14px;
    font-size: 12px; font-weight: 600;
}

/* ===============================
====================                     RECEIPT PRINT
====================================
=============== */
.receipt-wrapper {
    background: #fff; max-width: 480px; margin: 0 auto;
    border: 1px solid #ddd; font-family: 'Hind Siliguri',Arial,sans-serif;
}
.receipt-header {
    background: linear-gradient(135deg, var(--diag-mid), var(--diag-deep));
    color: #fff; padding: 24px; text-align: center;
}
.receipt-logo-wrap { margin-bottom: 16px; }
.receipt-logo {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,.2); margin: 0 auto 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 700;
}
.receipt-logo-sub { font-size: 11px; opacity: .9; }
.receipt-clinic-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
.receipt-address { font-size: 13px; opacity: .9; margin-bottom: 2px; }
.receipt-phones { font-size: 12px; opacity: .8; }
.receipt-title-tag {
    background: rgba(255,255,255,.2); border-radius: 20px;
    padding: 6px 16px; font-size: 12px; font-weight: 600;
    display: inline-block; margin-top: 12px;
}
.receipt-patient-row {
    display: flex; flex-wrap: wrap; gap: 12px;
    padding: 16px; background: #f8f9fa; border-bottom: 1px solid #ddd;
}
.receipt-field { flex: 1; min-width: 100px; }
.receipt-field label {
    font-size: 11px; color: #666; text-transform: uppercase;
    font-weight: 600; display: block; margin-bottom: 2px;
}
.receipt-value {
    font-size: 13px; font-weight: 600; color: #333;
}
.receipt-table {
    width: 100%; border-collapse: collapse; font-size: 12px;
}
.receipt-table th {
    background: #f0faf8; color: var(--text-dark);
    font-weight: 700; font-size: 11px; text-transform: uppercase;
    padding: 8px; border-bottom: 2px solid var(--diag-soft);
    text-align: left;
}
.receipt-table td {
    padding: 8px; border-bottom: 1px solid #eee;
    vertical-align: top;
}
.receipt-table tbody tr:last-child td { border-bottom: none; }
.receipt-footer {
    padding: 20px; background: #f8f9fa;
    display: flex; justify-content: space-between;
    align-items: flex-start;
}
.receipt-footer-left { font-size: 12px; color: #666; }
.receipt-note {
    margin-top: 8px; padding: 8px 12px;
    background: #fff3e0; border-radius: 4px; border-left: 3px solid #f57c00;
    font-size: 11px;
}
.receipt-footer-right { text-align: center; }
.receipt-sig-line {
    font-size: 11px; color: #666; margin-bottom: 4px;
    border-bottom: 1px solid #999; padding-bottom: 2px;
}
.receipt-sig-name { font-size: 12px; font-weight: 600; color: #333; }

/* ===============================
====================                     MODAL
====================================
=============== */
.rx-modal-content {
    border: none; border-radius: var(--radius-lg);
    overflow: hidden;
}
.rx-modal-header {
    background: linear-gradient(135deg, var(--diag-mid), var(--diag-deep));
    color: #fff; padding: 20px 24px;
    border: none;
}
.rx-modal-icon {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.modal-subtitle-text { font-size: 13px; opacity: .9; }
.btn-modal-print {
    background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: var(--radius-sm); padding: 8px 16px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s;
}
.btn-modal-print:hover { background: rgba(255,255,255,.3); }
.btn-modal-close {
    background: none; border: none; color: #fff;
    font-size: 18px; cursor: pointer; padding: 8px;
    opacity: .8; transition: opacity .2s;
}
.btn-modal-close:hover { opacity: 1; }
.modal-state-wrap {
    text-align: center; padding: 60px 40px;
    color: var(--text-muted);
}
.modal-state-text { font-size: 16px; margin: 0; }
.modal-summary-bar {
    display: flex; background: #f0faf8;
    padding: 16px 24px; gap: 24px; overflow-x: auto;
}
.modal-summary-item {
    display: flex; align-items: center; gap: 10px;
    white-space: nowrap;
}
.modal-summary-item i {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; color: #fff;
}
.msi-diag i { background: var(--diag-mid); }
.msi-teal i { background: var(--diag-mid); }
.msi-green i { background: var(--green-mid); }
.msi-orange i { background: var(--orange); }
.msi-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; }
.msi-val { font-size: 14px; font-weight: 700; color: var(--text-dark); }
.rx-modal-footer {
    background: #fafbfd; padding: 16px 24px;
    border-top: 1px solid var(--border);
}

/* ===============================
====================                     PRINT OVERLAY
====================================
=============== */
#print-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,.8); z-index: 9999; display: none;
}

/* ===============================
====================                     BUTTONS
====================================
=============== */
.btn-select {
    background: var(--diag-mid); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: 6px 12px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s;
}
.btn-select:hover { background: var(--diag-deep); }
.btn-select-diag { background: var(--diag-mid); }
.btn-select-diag:hover { background: var(--diag-deep); }

.btn-view-receipt {
    background: var(--blue-mid); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: 6px 12px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s;
}
.btn-view-receipt:hover { background: var(--blue-deep); }

.btn-rx-action {
    background: #fff; border: 1px solid var(--border);
    color: var(--text-dark); border-radius: var(--radius-sm);
    padding: 8px 16px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .2s;
}
.btn-rx-action:hover {
    background: var(--bg-page); border-color: var(--diag-mid);
}
.btn-rx-print:hover { background: var(--diag-light); color: var(--diag-deep); }
.btn-rx-edit:hover { background: #fff3e0; color: #f57c00; }
.btn-rx-new:hover { background: #e8f5e9; color: var(--green-mid); }

/* ===============================
====================                     UTILITIES
====================================
=============== */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
.text-monospace { font-family: monospace; }
.d-none { display: none !important; }

/* ===============================
====================                     PRINT STYLES
====================================
=============== */
@media print {
    body * { visibility: hidden; }
    #prescription-print-area, #prescription-print-area * {
        visibility: visible;
    }
    #prescription-print-area {
        position: absolute; left: 0; top: 0; width: 100%;
    }
    .receipt-wrapper {
        box-shadow: none; border: 1px solid #000;
    }
    @page { margin: 12mm; }
}
</style>
@stop

@section('js')
<script>
// ===============================
// ===================                     DATA & HELPERS
// ===============================
// ===============================
var tests = [];
var categories = [];
var currentTests = [];
var editingTestId = null;
var selectedPatient = null;

function esc(str) {
    if (!str) return '';
    return str.toString().replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
}
function taka(num) {
    return '? ' + Number(num || 0).toLocaleString('en-BD');
}
function fmtDateBD(dateStr) {
    if (!dateStr) return '—';
    var d = new Date(dateStr);
    return d.toLocaleDateString('en-BD', { day: '2-digit', month: 'short', year: 'numeric' });
}
function getTestById(id) {
    return tests.find(function(t) { return t.id == id; });
}
function getTestsByCategory(cat) {
    return tests.filter(function(t) { return t.category === cat; });
}

// ===============================
// ===================                     RENDER HELPERS
// ===============================
// ===============================
function renderTestRow(test, idx) {
    var netPrice = test.price - (test.discount || 0);
    var subtotal = netPrice * (test.qty || 1);
    return '<tr data-test-id="' + test.id + '" data-idx="' + idx + '">' +
        '<td>' + (idx + 1) + '</td>' +
        '<td>' + esc(test.category) + '</td>' +
        '<td>' + esc(test.name) + '</td>' +
        '<td style="text-align:right">' + Number(test.price).toLocaleString() + '</td>' +
        '<td><input type="number" class="form-control form-control-sm" value="' + (test.discount || 0) + '" min="0" style="width:70px" onchange="updateTestDiscount(' + idx + ', this.value)" /></td>' +
        '<td style="text-align:right">' + netPrice.toLocaleString() + '</td>' +
        '<td><input type="number" class="form-control form-control-sm" value="' + (test.qty || 1) + '" min="1" style="width:60px" onchange="updateTestQty(' + idx + ', this.value)" /></td>' +
        '<td style="text-align:right;font-weight:bold">' + subtotal.toLocaleString() + '</td>' +
        '<td><input type="text" class="form-control form-control-sm" value="' + esc(test.remarks || '') + '" placeholder="Remarks" style="width:100px" onchange="updateTestRemarks(' + idx + ', this.value)" /></td>' +
        '<td style="text-align:center"><button type="button" class="btn btn-sm btn-danger" onclick="removeTest(' + idx + ')"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}
function updateTestTable() {
    var tbody = document.getElementById('test-tbody');
    if (currentTests.length === 0) {
        tbody.innerHTML = '<tr id="empty-test-row"><td colspan="10"><div class="table-empty-state"><i class="fas fa-vials"></i><span>No tests added yet. Click "Add Test" to begin.</span></div></td></tr>';
        document.getElementById('test-count-badge').textContent = '0';
    } else {
        tbody.innerHTML = currentTests.map(renderTestRow).join('');
        document.getElementById('test-count-badge').textContent = currentTests.length;
    }
    recalcPayment();
}
function recalcPayment() {
    var gross = 0, disc = 0, paid = 0, due = 0;
    currentTests.forEach(function(t) {
        var net = (t.price || 0) - (t.discount || 0);
        gross += (t.price || 0) * (t.qty || 1);
        disc += (t.discount || 0) * (t.qty || 1);
    });
    var netPayable = gross - disc;
    paid = parseFloat(document.getElementById('f-paid').value) || 0;
    due = netPayable - paid;
    document.getElementById('sum-gross').textContent = taka(gross);
    document.getElementById('sum-net').textContent = taka(netPayable);
    document.getElementById('sum-due').textContent = taka(due);
    document.getElementById('tfoot-total').textContent = taka(netPayable);
}
function populateTestSelect(selectEl, cat) {
    var options = '<option value="">Select test...</option>';
    var list = cat ? getTestsByCategory(cat) : tests;
    list.forEach(function(t) {
        options += '<option value="' + t.id + '">' + esc(t.name) + '</option>';
    });
    selectEl.innerHTML = options;
}
function showNotification(msg, type) {
    var alertEl = document.getElementById('save-alert');
    alertEl.className = 'alert mb-3 modern-alert alert-' + (type || 'success');
    alertEl.textContent = msg;
    alertEl.classList.remove('d-none');
    setTimeout(function() { alertEl.classList.add('d-none'); }, 4000);
}

// ===============================
// ===================                     TEST MANAGEMENT
// ===============================
// ===============================
function addTestRow(category) {
    var idx = currentTests.length;
    var newTest = {
        id: null,
        category: category || '',
        name: '',
        price: 0,
        discount: 0,
        qty: 1,
        remarks: ''
    };
    currentTests.push(newTest);
    updateTestTable();
    // Focus on the newly added row's category select
    setTimeout(function() {
        var row = document.querySelector('[data-idx="' + idx + '"]');
        if (row) {
            var catSelect = row.querySelector('select');
            if (catSelect) catSelect.focus();
        }
    }, 100);
}
function removeTest(idx) {
    currentTests.splice(idx, 1);
    updateTestTable();
}
function updateTestField(idx, field, value) {
    if (!currentTests[idx]) return;
    currentTests[idx][field] = value;
    if (field === 'id') {
        var test = getTestById(value);
        if (test) {
            currentTests[idx].name = test.name;
            currentTests[idx].category = test.category;
            currentTests[idx].price = test.price;
            // Update the row display
            var row = document.querySelector('[data-idx="' + idx + '"]');
            if (row) {
                row.cells[1].textContent = test.category;
                row.cells[2].textContent = test.name;
                row.cells[3].textContent = Number(test.price).toLocaleString();
                updateTestDiscount(idx, currentTests[idx].discount || 0);
            }
        }
    }
    recalcPayment();
}
function updateTestDiscount(idx, value) {
    updateTestField(idx, 'discount', parseFloat(value) || 0);
    var row = document.querySelector('[data-idx="' + idx + '"]');
    if (row) {
        var netPrice = (currentTests[idx].price || 0) - (currentTests[idx].discount || 0);
        var subtotal = netPrice * (currentTests[idx].qty || 1);
        row.cells[5].textContent = netPrice.toLocaleString();
        row.cells[7].textContent = subtotal.toLocaleString();
    }
}
function updateTestQty(idx, value) {
    updateTestField(idx, 'qty', parseInt(value) || 1);
    var row = document.querySelector('[data-idx="' + idx + '"]');
    if (row) {
        var netPrice = (currentTests[idx].price || 0) - (currentTests[idx].discount || 0);
        var subtotal = netPrice * (currentTests[idx].qty || 1);
        row.cells[7].textContent = subtotal.toLocaleString();
    }
}
function updateTestRemarks(idx, value) {
    updateTestField(idx, 'remarks', value);
}

// ===============================
// ===================                     PATIENT SELECTION
// ===============================
// ===============================
function selectPatient(btn) {
    selectedPatient = {
        admission_id: btn.dataset.admissionId,
        patient_id: btn.dataset.patientId,
        name: btn.dataset.name,
        age: btn.dataset.age,
        code: btn.dataset.code,
        mobile: btn.dataset.mobile,
        blood: btn.dataset.blood,
        admission_date: btn.dataset.admissionDate
    };
    // Update UI
    document.getElementById('f-patient-id').value = selectedPatient.patient_id;
    document.getElementById('f-admission-id').value = selectedPatient.admission_id;
    document.getElementById('f-patient-code').value = selectedPatient.code;
    document.getElementById('f-patient-name').value = selectedPatient.name;
    document.getElementById('f-patient-age').value = selectedPatient.age;
    document.getElementById('spb-name').textContent = selectedPatient.name;
    document.getElementById('spb-meta').textContent = 'Code: ' + selectedPatient.code + ' | Age: ' + selectedPatient.age + ' | Adm. ID: ' + selectedPatient.admission_id;
    document.getElementById('spb-avatar').textContent = (selectedPatient.name || 'P').charAt(0).toUpperCase();
    // Set today's date as default
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('f-date').value = today;
    // Move to step 2
    moveToStep2();
    // Load previous payments for this admission
    loadPreviousPayments();
}
function moveToStep2() {
    var s1c = document.getElementById('step1-circle');
    s1c.className = 'step-circle step-done';
    s1c.textContent = '';
    s1c.innerHTML = '<i class="fas fa-check"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className = 'step-circle step-active';
    document.getElementById('step2-label').className = 'step-label-main step-label-active';
    document.getElementById('breadcrumb-current').textContent = 'Add Tests & Pay';
    document.getElementById('panel-step1').style.display = 'none';
    document.getElementById('panel-step2').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function loadPreviousPayments() {
    if (!selectedPatient) return;
    fetch('/api/diagnostic-payments?admission_id=' + selectedPatient.admission_id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var box = document.getElementById('prev-payments-box');
            var list = document.getElementById('prev-payments-list');
            if (data && data.length > 0) {
                box.style.display = 'block';
                var totalPaid = data.reduce(function(sum, p) { return sum + parseFloat(p.paid_amount || 0); }, 0);
                document.getElementById('prev-total-paid').textContent = taka(totalPaid);
                list.innerHTML = data.map(function(p) {
                    return '<div class="mini-item">' +
                        '<div class="mini-item-row">' +
                        '<div><span class="mini-item-receipt">' + esc(p.receipt_no) + '</span> <span class="mini-item-date">' + fmtDateBD(p.payment_date) + '</span></div>' +
                        '<div class="mini-item-amount">' + taka(p.paid_amount) + '</div>' +
                        '</div></div>';
                }).join('');
            } else {
                box.style.display = 'none';
            }
        })
        .catch(function() { /* ignore */ });
}

// ===============================
// ===================                     SAVE PAYMENT
// ===============================
// ===============================
function savePayment() {
    if (!selectedPatient) {
        showNotification('Please select a patient first.', 'danger');
        return;
    }
    if (currentTests.length === 0) {
        showNotification('Please add at least one test.', 'danger');
        return;
    }
    var gross = 0, disc = 0;
    currentTests.forEach(function(t) {
        gross += (t.price || 0) * (t.qty || 1);
        disc += (t.discount || 0) * (t.qty || 1);
    });
    var netPayable = gross - disc;
    var paid = parseFloat(document.getElementById('f-paid').value) || 0;
    var due = netPayable - paid;
    var payload = {
        patient_id: selectedPatient.patient_id,
        admission_id: selectedPatient.admission_id,
        patient_name: selectedPatient.name,
        patient_age: selectedPatient.age,
        patient_code: selectedPatient.code,
        payment_date: document.getElementById('f-date').value,
        payment_method: document.getElementById('f-method').value,
        total_amount: netPayable,
        paid_amount: paid,
        due_amount: due,
        discount_amount: disc,
        received_by: document.getElementById('f-received-by').value,
        notes: document.getElementById('f-notes').value,
        tests: currentTests.map(function(t, idx) {
            return {
                test_id: t.id,
                test_name: t.name,
                category: t.category,
                price: t.price,
                discount: t.discount || 0,
                qty: t.qty || 1,
                subtotal: ((t.price || 0) - (t.discount || 0)) * (t.qty || 1),
                remarks: t.remarks || '',
                sort_order: idx
            };
        })
    };
    document.getElementById('btn-save').disabled = true;
    fetch('/api/diagnostic-payments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('btn-save').disabled = false;
        if (data.success) {
            showReceipt(data.payment);
            showNotification('Payment saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Failed to save payment.', 'danger');
        }
    })
    .catch(function(err) {
        document.getElementById('btn-save').disabled = false;
        showNotification('Network error. Please try again.', 'danger');
        console.error(err);
    });
}
function showReceipt(payment) {
    // Update receipt info cards
    document.getElementById('ib-name').textContent = payment.patient_name || '—';
    document.getElementById('ib-receipt').textContent = payment.receipt_no || '—';
    document.getElementById('ib-paid').textContent = taka(payment.paid_amount);
    document.getElementById('ib-due').textContent = taka(payment.due_amount);
    // Update receipt details
    document.getElementById('r-name').textContent = payment.patient_name || '—';
    document.getElementById('r-age').textContent = payment.patient_age || '—';
    document.getElementById('r-code').textContent = payment.patient_code || '—';
    document.getElementById('r-date').textContent = fmtDateBD(payment.payment_date);
    document.getElementById('r-receipt').textContent = payment.receipt_no || '—';
    document.getElementById('r-gross').textContent = taka(payment.total_amount + (payment.discount_amount || 0));
    document.getElementById('r-net').textContent = taka(payment.total_amount);
    document.getElementById('r-paid').textContent = taka(payment.paid_amount);
    document.getElementById('r-method').textContent = payment.payment_method || 'Cash';
    document.getElementById('r-received-by').textContent = payment.received_by || '—';
    document.getElementById('gen-time').textContent = new Date().toLocaleString('en-BD');
    // Discount row
    if (payment.discount_amount > 0) {
        document.getElementById('r-discount-row').style.display = '';
        document.getElementById('r-discount').textContent = taka(payment.discount_amount);
    } else {
        document.getElementById('r-discount-row').style.display = 'none';
    }
    // Due row
    if (payment.due_amount > 0) {
        document.getElementById('r-due-row').style.display = '';
        document.getElementById('r-due').textContent = taka(payment.due_amount);
    } else {
        document.getElementById('r-due-row').style.display = 'none';
    }
    // Notes
    if (payment.notes) {
        document.getElementById('r-notes-wrap').style.display = '';
        document.getElementById('r-notes').textContent = payment.notes;
    } else {
        document.getElementById('r-notes-wrap').style.display = 'none';
    }
    // Tests table
    var itemRows = (payment.tests || []).map(function(it, idx) {
        return '<tr>' +
            '<td>' + (idx + 1) + '</td>' +
            '<td>' + esc(it.category) + '</td>' +
            '<td>' + esc(it.test_name) + '</td>' +
            '<td class="text-center">' + it.qty + '</td>' +
            '<td class="text-right">' + Number(it.price).toLocaleString() + '</td>' +
            '<td class="text-right">' + (it.discount > 0 ? Number(it.discount).toLocaleString() : '—') + '</td>' +
            '<td class="text-right font-weight-bold">' + Number(it.subtotal).toLocaleString() + '</td>' +
            '</tr>';
    }).join('');
    document.getElementById('r-test-tbody').innerHTML = itemRows;
    // Show receipt view
    document.getElementById('pay-form-card').style.display = 'none';
    document.getElementById('receipt-view').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ===============================
// ===================                     VIEW PAST RECEIPT
// ===============================
// ===============================
function viewReceipt(id) {
    var modal = $('#receiptModal');
    modal.modal('show');
    // Reset modal state
    document.getElementById('modal-loading').style.display = 'block';
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');
    // Load receipt data
    fetch('/api/diagnostic-payments/' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                renderModalReceipt(data.payment);
            } else {
                showModalError(data.message || 'Failed to load receipt');
            }
        })
        .catch(function(err) {
            showModalError('Network error. Please try again.');
            console.error(err);
        });
}
function renderModalReceipt(d) {
    var gross = 0, disc = 0, paid = 0, due = 0;
    var itemRows = (d.tests || []).map(function(it, idx) {
        var net = (it.price || 0) - (it.discount || 0);
        var subtotal = net * (it.qty || 1);
        gross += (it.price || 0) * (it.qty || 1);
        disc += (it.discount || 0) * (it.qty || 1);
        return '<tr>' +
            '<td>' + (idx + 1) + '</td>' +
            '<td>' + esc(it.category) + '</td>' +
            '<td>' + esc(it.test_name) + '</td>' +
            '<td class="text-center">' + it.qty + '</td>' +
            '<td class="text-right">' + Number(it.price).toLocaleString() + '</td>' +
            '<td class="text-right">' + (it.discount > 0 ? Number(it.discount).toLocaleString() : '—') + '</td>' +
            '<td class="text-right font-weight-bold">' + Number(it.subtotal).toLocaleString() + '</td>' +
            '</tr>';
    }).join('');
    var discRow = disc > 0
        ? '<tr><td colspan="6" class="text-right" style="padding:3px 8px;color:#e65100">Discount</td>' +
          '<td class="text-right" style="padding:3px 8px;color:#e65100">' + taka(disc) + '</td></tr>'
        : '';
    var dueRow = due > 0
        ? '<tr><td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;color:#c62828">Due</td>' +
          '<td class="text-right font-weight-bold" style="padding:6px 8px;color:#c62828">' + taka(due) + '</td></tr>'
        : '';
    var html = '<div class="receipt-wrapper">' +
        '<div class="receipt-header">' +
        '<div class="receipt-logo-wrap"><div class="receipt-logo">CP</div><div class="receipt-logo-sub">Professor Clinic</div></div>' +
        '<div class="receipt-clinic-info">' +
        '<div class="receipt-clinic-name">??????? ???????</div>' +
        '<div class="receipt-address">?????????, ????????</div>' +
        '<div class="receipt-phones">? 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>' +
        '<div class="receipt-title-tag">Diagnostic Payment Receipt</div>' +
        '</div></div>' +
        '<div class="receipt-patient-row">' +
        '<div class="receipt-field"><label>Name:</label><div class="receipt-value">' + esc(d.patient_name || '—') + '</div></div>' +
        '<div class="receipt-field"><label>Age:</label><div class="receipt-value">' + esc(d.patient_age || '—') + '</div></div>' +
        '<div class="receipt-field"><label>Code:</label><div class="receipt-value">' + esc(d.patient_code || '—') + '</div></div>' +
        '<div class="receipt-field"><label>Date:</label><div class="receipt-value">' + fmtDateBD(d.payment_date) + '</div></div>' +
        '<div class="receipt-field"><label>Receipt:</label><div class="receipt-value font-weight-bold">' + esc(d.receipt_no || '—') + '</div></div>' +
        '</div>' +
        '<div style="padding:0 16px">' +
        '<table class="receipt-table">' +
        '<thead><tr>' +
        '<th style="width:32px">#</th>' +
        '<th style="width:88px">Category</th>' +
        '<th>Test Name</th>' +
        '<th style="width:52px;text-align:center">Qty</th>' +
        '<th style="width:88px;text-align:right">Price (?)</th>' +
        '<th style="width:76px;text-align:right">Disc.</th>' +
        '<th style="width:92px;text-align:right">Subtotal (?)</th>' +
        '</tr></thead>' +
        '<tbody>' + itemRows + '</tbody>' +
        '<tfoot>' +
        '<tr><td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;border-top:2px solid #333">Gross Total</td>' +
        '<td class="text-right font-weight-bold" style="padding:6px 8px;border-top:2px solid #333">' + taka(gross) + '</td></tr>' +
        discRow +
        '<tr><td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;font-size:14px">Net Payable</td>' +
        '<td class="text-right font-weight-bold" style="padding:6px 8px;font-size:14px">' + taka(d.total_amount) + '</td></tr>' +
        '<tr style="background:#e0f2f1"><td colspan="6" class="text-right font-weight-bold" style="padding:6px 8px;color:#00695c">Paid</td>' +
        '<td class="text-right font-weight-bold" style="padding:6px 8px;color:#00695c">' + taka(paid) + '</td></tr>' +
        dueRow +
        '</tfoot></table>' +
        '</div>' +
        '<div class="receipt-footer">' +
        '<div class="receipt-footer-left">' +
        '<div>Payment Method: <strong>' + esc(d.payment_method || 'Cash') + '</strong></div>' +
        (d.notes ? '<div class="receipt-note">Note: ' + esc(d.notes) + '</div>' : '') +
        '</div>' +
        '<div class="receipt-footer-right">' +
        '<div class="receipt-sig-line">Received By</div>' +
        '<div class="receipt-sig-name">' + esc(d.received_by || '—') + '</div>' +
        '</div></div>' +
        '</div>';
    document.getElementById('modal-prescription-print-area').innerHTML = html;
    setText('m-saved-time', d.created_at
        ? new Date(d.created_at).toLocaleString('en-BD', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        })
        : '—'
    );
    // Update modal summary bar
    document.getElementById('m-ib-name').textContent = d.patient_name || '—';
    document.getElementById('m-ib-receipt').textContent = d.receipt_no || '—';
    document.getElementById('m-ib-paid').textContent = taka(d.paid_amount);
    document.getElementById('m-ib-date').textContent = fmtDateBD(d.payment_date);
    // Show receipt area
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.remove('d-none');
}
function showModalError(msg) {
    document.getElementById('modal-error-msg').textContent = msg;
    document.getElementById('modal-loading').classList.add('d-none');
    document.getElementById('modal-error').classList.remove('d-none');
}
function setText(id, text) {
    var el = document.getElementById(id);
    if (el) el.textContent = text;
}

// ===============================
// ===================                     NAVIGATION
// ===============================
// ===============================
function backToStep1() {
    var s1c = document.getElementById('step1-circle');
    s1c.className = 'step-circle step-active';
    s1c.textContent = '1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className = 'step-circle step-inactive';
    document.getElementById('step2-label').className = 'step-label-main step-label-inactive';
    document.getElementById('breadcrumb-current').textContent = 'Select Patient';
    document.getElementById('panel-step1').style.display = 'block';
    document.getElementById('panel-step2').style.display = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function editReceipt() {
    document.getElementById('receipt-view').style.display = 'none';
    document.getElementById('pay-form-card').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ===============================
// ===================                     TABLE FILTERS
// ===============================
// ===============================
function filterAdmitted() {
    var q = (document.getElementById('patientSearch').value || '').toLowerCase();
    document.querySelectorAll('#admittedTable tbody tr.admitted-row').forEach(function (r) {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
function filterPayTable() {
    var q = (document.getElementById('paySearch').value || '').toLowerCase();
    document.querySelectorAll('#payTable tbody tr.pay-row').forEach(function (r) {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ===============================
// ===================                     PRINT
// ===============================
// ===============================
function printRx() {
    window.print();
}
function printModal() {
    var printContent = document.getElementById('modal-prescription-print-area').innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

// ===============================
// ===================                     INIT
// ===============================
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    var ps = document.getElementById('patientSearch');
    if (ps) ps.addEventListener('keyup', filterAdmitted);
});
</script>
@stop
