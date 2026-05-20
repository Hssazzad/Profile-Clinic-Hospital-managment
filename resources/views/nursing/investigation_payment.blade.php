@extends('adminlte::page')

@section('title', 'Investigation Bill | Professor Clinic')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0 page-main-title">
            <span class="page-title-icon"><i class="fas fa-flask"></i></span>
            Diagnostic / Investigation Bill
        </h1>
        <ol class="breadcrumb mt-1 p-0" style="background:transparent;font-size:12px">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('nursing.index') }}">Nursing</a></li>
            <li class="breadcrumb-item active" id="breadcrumb-current">Select Patient</li>
        </ol>
    </div>
    <a href="{{ route('nursing.index') }}" class="btn-back-modern">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>
@stop

@section('content')

<div id="save-alert" class="alert d-none mb-3" role="alert"></div>

{{-- ── STEP INDICATOR ── --}}
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
                <div class="step-label-sub">Investigation Bill</div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     STEP 1 — SELECT PATIENT
══════════════════════════════════════════════════ --}}
<div id="panel-step1">

    {{-- Admitted Patients --}}
    <div class="pc-card">
        <div class="pc-card-header">
            <div class="pc-card-title">
                <span class="pc-icon-wrap"><i class="fas fa-hospital-user"></i></span>
                <div>
                    <h5 class="mb-0">Admitted Patients</h5>
                    <small class="text-muted">Select a patient to create an Investigation Bill</small>
                </div>
            </div>
            <span class="pc-badge">
                <i class="fas fa-bed mr-1"></i>
                {{ $admittedPatients->total() ?? $admittedPatients->count() }} Admitted
            </span>
        </div>

        <div class="pc-search-bar">
            <div class="pc-search-group">
                <span class="pc-search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="patientSearch" class="pc-search-input"
                       placeholder="Search by name, code or mobile…" onkeyup="filterAdmitted()">
                <button class="pc-search-btn" onclick="filterAdmitted()">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </div>
        </div>

        <div class="pc-card-body pt-0">
            <div class="pc-table-wrap">
                <table class="pc-table" id="admittedTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adm. ID</th>
                            <th>Patient Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Mobile</th>
                            <th>Adm. Date</th>
                            <th>Blood</th>
                            <th style="text-align:center">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admittedPatients as $ap)
                        @php $g = strtolower($ap->gender ?? ''); @endphp
                        <tr class="admitted-row">
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td><span class="pc-adm-badge">#{{ $ap->admission_id }}</span></td>
                            <td>
                                <div class="pc-name-cell">
                                    <div class="pc-avatar">{{ strtoupper(substr($ap->patient_name ?? 'P',0,1)) }}</div>
                                    <div>
                                        <strong>{{ $ap->patient_name ?? '—' }}</strong>
                                        <br><small class="text-muted">{{ $ap->patient_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $ap->patient_age ?? '—' }}</td>
                            <td>
                                @if($g==='male') <span class="pc-gender-m">M</span>
                                @elseif($g==='female') <span class="pc-gender-f">F</span>
                                @else <span class="text-muted">—</span> @endif
                            </td>
                            <td>{{ $ap->mobile_no ?? '—' }}</td>
                            <td>{{ $ap->admission_date ? \Carbon\Carbon::parse($ap->admission_date)->format('d/m/Y') : '—' }}</td>
                            <td>
                                @if($ap->blood_group ?? null)
                                    <span class="pc-blood">{{ $ap->blood_group }}</span>
                                @else <span class="text-muted">—</span> @endif
                            </td>
                            <td class="text-center">
                                <button class="pc-select-btn" onclick="selectPatient(this)"
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
                        <tr><td colspan="9">
                            <div class="pc-empty"><i class="fas fa-hospital-user"></i><p>No admitted patients found.</p></div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($admittedPatients,'links'))
            <div class="pc-pagination">
                <small class="text-muted">
                    Showing {{ $admittedPatients->firstItem() ?? 0 }}–{{ $admittedPatients->lastItem() ?? 0 }}
                    of <strong>{{ $admittedPatients->total() ?? 0 }}</strong>
                </small>
                {{ $admittedPatients->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Past Payments --}}
    <div class="pc-card" style="border-top:3px solid #00796B">
        <div class="pc-card-header">
            <div class="pc-card-title">
                <span class="pc-icon-wrap"><i class="fas fa-receipt"></i></span>
                <div>
                    <h5 class="mb-0">Past Investigation Payments</h5>
                    <small class="text-muted">All previously saved bills</small>
                </div>
            </div>
            <span class="pc-badge">
                <i class="fas fa-file-invoice-dollar mr-1"></i>
                {{ $pastPayments->total() ?? $pastPayments->count() }} Records
            </span>
        </div>
        <div class="pc-search-bar">
            <div class="pc-search-group">
                <span class="pc-search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="paySearch" class="pc-search-input"
                       placeholder="Search by name, receipt no…" onkeyup="filterPayTable()">
                <button class="pc-search-btn" onclick="filterPayTable()">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </div>
        </div>
        <div class="pc-card-body pt-0">
            <div class="pc-table-wrap">
                <table class="pc-table" id="payTable">
                    <thead>
                        <tr>
                            <th>#</th><th>Receipt No</th><th>Patient</th><th>Date</th>
                            <th>Total</th><th>Paid</th><th>Due</th><th>Status</th>
                            <th style="text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pastPayments as $pp)
                        <tr class="pay-row">
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td><span class="pc-adm-badge">{{ $pp->receipt_no }}</span></td>
                            <td>
                                <div class="pc-name-cell">
                                    <div class="pc-avatar">{{ strtoupper(substr($pp->patient_name ?? 'P',0,1)) }}</div>
                                    <div>
                                        <strong>{{ $pp->patient_name ?? '—' }}</strong>
                                        <br><small class="text-muted">{{ $pp->patient_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($pp->payment_date)->format('d/m/Y') }}</td>
                            <td class="font-weight-bold">৳ {{ number_format($pp->total_amount,0) }}</td>
                            <td class="text-success font-weight-bold">৳ {{ number_format($pp->paid_amount,0) }}</td>
                            <td class="{{ $pp->due_amount > 0 ? 'text-danger' : 'text-muted' }} font-weight-bold">
                                ৳ {{ number_format($pp->due_amount,0) }}
                            </td>
                            <td>
                                @if($pp->status==='paid') <span class="pc-status-paid">Paid</span>
                                @elseif($pp->status==='partial') <span class="pc-status-partial">Partial</span>
                                @else <span class="pc-status-due">Due</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="pc-view-btn" onclick="viewReceipt({{ $pp->id }})">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9">
                            <div class="pc-empty"><i class="fas fa-receipt"></i><p>No payments found.</p></div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($pastPayments,'links'))
            <div class="pc-pagination">
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

{{-- ══════════════════════════════════════════════════
     STEP 2 — INVESTIGATION BILL FORM
══════════════════════════════════════════════════ --}}
<div id="panel-step2" style="display:none">

    {{-- Selected patient bar --}}
    <div class="pc-patient-bar mb-4">
        <div class="d-flex align-items-center" style="gap:14px">
            <div class="pc-bar-avatar" id="spb-avatar">A</div>
            <div>
                <div class="pc-bar-name" id="spb-name"></div>
                <div class="pc-bar-meta" id="spb-meta"></div>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap:10px">
            <span class="pc-bar-dot"></span>
            <span style="font-size:13px;font-weight:600;color:#fff">Investigation Bill</span>
            <button class="pc-change-btn" onclick="backToStep1()">
                <i class="fas fa-exchange-alt mr-1"></i> Change Patient
            </button>
        </div>
    </div>

    {{-- Previous payments for this admission --}}
    <div id="prev-payments-box" class="pc-card mb-4" style="display:none;border-left:4px solid #00796B">
        <div class="pc-card-header" style="background:#f0faf8">
            <div class="pc-card-title">
                <span class="pc-icon-wrap"><i class="fas fa-history"></i></span>
                <div>
                    <h6 class="mb-0 font-weight-bold" style="color:#00695c">Previous Bills — This Admission</h6>
                </div>
            </div>
            <span class="pc-badge">Total Paid: <strong id="prev-total-paid">৳ 0</strong></span>
        </div>
        <div class="pc-card-body pt-2 pb-2" id="prev-payments-list"></div>
    </div>

    {{-- ══ THE BILL FORM (matches Professor Clinic format) ══ --}}
    <div class="pc-card" id="bill-form-card">

        {{-- Bill header (matches your letterhead) --}}
        <div class="bill-letterhead">
            <div class="bill-lh-left">
                <div class="bill-reg-no">
                    Registration no: <strong id="lh-reg-no">—</strong>
                </div>
            </div>
            <div class="bill-lh-center">
                <div class="bill-clinic-name">Professor Clinic</div>
                <div class="bill-clinic-addr">Majhira, Shajahanpur, Bogura</div>
            </div>
            <div class="bill-lh-right">
                <div>☎ 01713-740680</div>
                <div>01720-039005</div>
                <div>01720-039006</div>
                <div>01720-039007</div>
                <div>01720-039008</div>
            </div>
        </div>

        <div class="pc-card-body">
            <input type="hidden" id="f-patient-id">
            <input type="hidden" id="f-admission-id">
            <input type="hidden" id="f-patient-code">

            {{-- Patient info row (matches bill header fields) --}}
            <div class="bill-info-row">
                <div class="bill-info-field">
                    <label>Patient's Name</label>
                    <input type="text" class="bill-input" id="f-patient-name" readonly>
                </div>
                <div class="bill-info-field">
                    <label>Registration No.</label>
                    <input type="text" class="bill-input" id="f-reg-no" readonly>
                </div>
                <div class="bill-info-field">
                    <label>Ref. by Dr.</label>
                    <select class="bill-input" id="f-ref-dr">
                        <option value="">— Select Doctor —</option>
                        <option>Dr. Sabbir</option>
                        <option>Dr. Sagar</option>
                        <option>Dr. Other</option>
                    </select>
                </div>
                <div class="bill-info-field" style="max-width:100px">
                    <label>Age</label>
                    <input type="text" class="bill-input" id="f-patient-age" readonly>
                </div>
                <div class="bill-info-field" style="max-width:140px">
                    <label>Date</label>
                    <input type="date" class="bill-input" id="f-date">
                </div>
            </div>

            <div class="bill-title-bar">INVESTIGATION BILL</div>

            {{-- ── Bill Table (exact rows from your format) ── --}}
            <table class="bill-table" id="bill-table">
                <thead>
                    <tr>
                        <th style="width:42px">Sl.</th>
                        <th>Investigation</th>
                        <th style="width:130px;text-align:right">Amount (৳)</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Row 01: Admission Fee --}}
                    <tr class="bill-row">
                        <td class="bill-sl">01</td>
                        <td class="bill-name-cell">Admission Fee</td>
                        <td><input type="number" class="bill-amount-input" id="amt-admission"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 02: Ultrasonography (sub-items a/b/c) --}}
                    <tr class="bill-row">
                        <td class="bill-sl">02</td>
                        <td class="bill-name-cell">
                            <strong>Ultrasonography</strong>
                            <div class="bill-sub-row mt-1">
                                <label class="bill-sub-label">a) L/A</label>
                                <select class="bill-sub-select" id="usg-la-name" onchange="onUsgChange('la')">
                                    <option value="">— Select —</option>
                                    <option value="500">USG Lower Abdomen — ৳500</option>
                                    <option value="600">USG Lower Abdomen (Special) — ৳600</option>
                                    <option value="custom">Custom…</option>
                                </select>
                                <input type="number" class="bill-sub-amt" id="usg-la-amt" placeholder="0" oninput="recalc()">
                            </div>
                            <div class="bill-sub-row mt-1">
                                <label class="bill-sub-label">b) W/A</label>
                                <select class="bill-sub-select" id="usg-wa-name" onchange="onUsgChange('wa')">
                                    <option value="">— Select —</option>
                                    <option value="800">USG Whole Abdomen — ৳800</option>
                                    <option value="900">USG Whole Abdomen (Special) — ৳900</option>
                                    <option value="custom">Custom…</option>
                                </select>
                                <input type="number" class="bill-sub-amt" id="usg-wa-amt" placeholder="0" oninput="recalc()">
                            </div>
                            <div class="bill-sub-row mt-1">
                                <label class="bill-sub-label">c) P/P</label>
                                <select class="bill-sub-select" id="usg-pp-name" onchange="onUsgChange('pp')">
                                    <option value="">— Select —</option>
                                    <option value="700">USG Obstetric/P.P — ৳700</option>
                                    <option value="800">USG Obstetric (Special) — ৳800</option>
                                    <option value="custom">Custom…</option>
                                </select>
                                <input type="number" class="bill-sub-amt" id="usg-pp-amt" placeholder="0" oninput="recalc()">
                            </div>
                        </td>
                        <td class="bill-sub-total-cell text-right font-weight-bold" id="usg-subtotal">৳ 0</td>
                    </tr>

                    {{-- Row 03: ECG --}}
                    <tr class="bill-row">
                        <td class="bill-sl">03</td>
                        <td class="bill-name-cell">E.C.G</td>
                        <td><input type="number" class="bill-amount-input" id="amt-ecg"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 04: X-Ray (dropdown) --}}
                    <tr class="bill-row">
                        <td class="bill-sl">04</td>
                        <td class="bill-name-cell">
                            <strong>X-RAY</strong>
                            <div class="bill-dropdown-row mt-1">
                                <select class="bill-dropdown-select" id="xray-select" onchange="onXrayChange()">
                                    <option value="">— Select X-Ray Type —</option>
                                    <option value="350">Chest PA View — ৳350</option>
                                    <option value="300">Abdomen — ৳300</option>
                                    <option value="450">Spine (LS) — ৳450</option>
                                    <option value="350">KUB — ৳350</option>
                                    <option value="350">Pelvis — ৳350</option>
                                    <option value="400">Skull — ৳400</option>
                                    <option value="300">Hand / Foot — ৳300</option>
                                    <option value="custom">Custom…</option>
                                </select>
                                <input type="text" class="bill-dropdown-custom" id="xray-custom-name"
                                       placeholder="Custom name" style="display:none">
                            </div>
                        </td>
                        <td><input type="number" class="bill-amount-input" id="amt-xray"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 05: Pathology (dropdown) --}}
                    <tr class="bill-row">
                        <td class="bill-sl">05</td>
                        <td class="bill-name-cell">
                            <strong>Pathology</strong>
                            <div class="bill-dropdown-row mt-1">
                                <select class="bill-dropdown-select" id="path-select" onchange="onPathChange()">
                                    <option value="">— Select Pathology Test —</option>
                                    <option value="350">CBC / Complete Blood Count — ৳350</option>
                                    <option value="100">Blood Glucose (FBS/RBS) — ৳100</option>
                                    <option value="150">Urine R/M/E — ৳150</option>
                                    <option value="250">Serum Creatinine — ৳250</option>
                                    <option value="200">Blood Urea — ৳200</option>
                                    <option value="250">SGPT / ALT — ৳250</option>
                                    <option value="250">SGOT / AST — ৳250</option>
                                    <option value="300">HBsAg — ৳300</option>
                                    <option value="200">VDRL — ৳200</option>
                                    <option value="150">Blood Grouping — ৳150</option>
                                    <option value="300">Serum Bilirubin (T/D) — ৳300</option>
                                    <option value="600">Thyroid Profile (TSH/T3/T4) — ৳600</option>
                                    <option value="200">Widal Test — ৳200</option>
                                    <option value="800">Dengue NS1/IgM/IgG — ৳800</option>
                                    <option value="100">Pregnancy Test (Urine) — ৳100</option>
                                    <option value="500">Coagulation Profile (PT/APTT) — ৳500</option>
                                    <option value="custom">Custom…</option>
                                </select>
                                <input type="text" class="bill-dropdown-custom" id="path-custom-name"
                                       placeholder="Custom test name" style="display:none">
                            </div>
                        </td>
                        <td><input type="number" class="bill-amount-input" id="amt-path"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 06: Stitch Cutting / Dressing --}}
                    <tr class="bill-row">
                        <td class="bill-sl">06</td>
                        <td class="bill-name-cell">Stitch Cutting / Dressing</td>
                        <td><input type="number" class="bill-amount-input" id="amt-stitch"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 07: Nebulize --}}
                    <tr class="bill-row">
                        <td class="bill-sl">07</td>
                        <td class="bill-name-cell">Nebulize</td>
                        <td><input type="number" class="bill-amount-input" id="amt-nebulize"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 08: Oxygen --}}
                    <tr class="bill-row">
                        <td class="bill-sl">08</td>
                        <td class="bill-name-cell">Oxygen</td>
                        <td><input type="number" class="bill-amount-input" id="amt-oxygen"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 09: Injection --}}
                    <tr class="bill-row">
                        <td class="bill-sl">09</td>
                        <td class="bill-name-cell">Injection</td>
                        <td><input type="number" class="bill-amount-input" id="amt-injection"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 10: Others --}}
                    <tr class="bill-row">
                        <td class="bill-sl">10</td>
                        <td class="bill-name-cell">
                            Others
                            <input type="text" class="bill-other-desc mt-1" id="others-desc"
                                   placeholder="Description (optional)">
                        </td>
                        <td><input type="number" class="bill-amount-input" id="amt-others"
                                   min="0" placeholder="0" oninput="recalc()"></td>
                    </tr>

                    {{-- Row 11: Total --}}
                    <tr class="bill-total-row">
                        <td class="bill-sl">10</td>
                        <td class="font-weight-bold" style="font-size:14px">Total</td>
                        <td class="text-right font-weight-bold" id="disp-total" style="font-size:15px">৳ 0</td>
                    </tr>

                    {{-- Row 12: Less (discount — dropdown + custom input) --}}
                    <tr class="bill-row">
                        <td class="bill-sl">11</td>
                        <td class="bill-name-cell">
                            <strong>Less (Discount)</strong>
                            <div class="bill-dropdown-row mt-1">
                                <select class="bill-dropdown-select" id="less-type" onchange="onLessChange()">
                                    <option value="0">No discount</option>
                                    <option value="pct5">5%</option>
                                    <option value="pct10">10%</option>
                                    <option value="pct15">15%</option>
                                    <option value="pct20">20%</option>
                                    <option value="custom">Custom amount…</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="bill-amount-input" id="amt-less"
                                   min="0" placeholder="0" oninput="recalc()" style="color:#c62828">
                        </td>
                    </tr>

                    {{-- Row 13: Net Total --}}
                    <tr class="bill-total-row" style="background:#e0f2f1">
                        <td class="bill-sl">12</td>
                        <td style="font-size:15px;font-weight:700;color:#00695c">Net Total</td>
                        <td class="text-right font-weight-bold" id="disp-net" style="font-size:16px;color:#00695c">৳ 0</td>
                    </tr>

                    {{-- Row 14: Advance (min 25% of Total) --}}
                    <tr class="bill-row" style="background:#fff8e1">
                        <td class="bill-sl">13</td>
                        <td class="bill-name-cell">
                            <strong>Advance</strong>
                            <div id="adv-warning" class="bill-warn" style="display:none">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Advance must be at least 25% of Total
                            </div>
                        </td>
                        <td>
                            <input type="number" class="bill-amount-input" id="amt-advance"
                                   min="0" placeholder="Min 25% of Total" oninput="recalc()"
                                   style="border-color:#f9a825">
                        </td>
                    </tr>

                    {{-- Row 15: Due --}}
                    <tr class="bill-total-row" style="background:#ffebee">
                        <td class="bill-sl">14</td>
                        <td style="font-size:14px;font-weight:700;color:#c62828">Due</td>
                        <td class="text-right font-weight-bold" id="disp-due" style="font-size:15px;color:#c62828">৳ 0</td>
                    </tr>

                </tbody>
            </table>

            {{-- Collected by & Notes --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="bill-field">
                        <label>Collected By</label>
                        <input type="text" class="bill-input" id="f-received-by" placeholder="Staff name…">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bill-field">
                        <label>Notes</label>
                        <input type="text" class="bill-input" id="f-notes" placeholder="Optional notes…">
                    </div>
                </div>
            </div>

            <div class="bill-reminder mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                পরবর্তী যোগাযোগের জন্য Registration no অনুগ্রহপূর্বক মনে রাখবেন বা এই রশিদ সাথে আনবেন।
            </div>

            {{-- Footer actions --}}
            <div class="bill-form-footer mt-4">
                <button class="pc-back-btn" onclick="backToStep1()">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button class="pc-save-btn" id="btn-save" onclick="savePayment()">
                    <i class="fas fa-save mr-1"></i> Save &amp; Print Bill
                </button>
            </div>
        </div>
    </div>

    {{-- ══ RECEIPT / PRINT VIEW ══ --}}
    <div id="receipt-view" style="display:none">
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="pc-rx-card" style="border-color:#00796B">
                    <div class="pc-rx-icon" style="background:#00796B"><i class="fas fa-user"></i></div>
                    <div><div class="pc-rx-lbl">Patient</div><div class="pc-rx-val" id="ib-name">—</div></div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="pc-rx-card" style="border-color:#00796B">
                    <div class="pc-rx-icon" style="background:#00796B"><i class="fas fa-receipt"></i></div>
                    <div><div class="pc-rx-lbl">Receipt No</div><div class="pc-rx-val" id="ib-receipt">—</div></div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="pc-rx-card" style="border-color:#2e7d32">
                    <div class="pc-rx-icon" style="background:#2e7d32"><i class="fas fa-money-bill-wave"></i></div>
                    <div><div class="pc-rx-lbl">Advance Paid</div><div class="pc-rx-val" id="ib-paid">৳ 0</div></div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="pc-rx-card" style="border-color:#e65100">
                    <div class="pc-rx-icon" style="background:#e65100"><i class="fas fa-exclamation-circle"></i></div>
                    <div><div class="pc-rx-lbl">Due</div><div class="pc-rx-val" id="ib-due">৳ 0</div></div>
                </div>
            </div>
        </div>

        <div class="pc-card">
            <div class="pc-card-header">
                <div class="pc-card-title">
                    <span class="pc-icon-wrap"><i class="fas fa-receipt"></i></span>
                    <div>
                        <h5 class="mb-0">Investigation Bill Receipt</h5>
                        <small class="text-muted">Ready to print</small>
                    </div>
                </div>
                <span class="pc-saved-badge"><i class="fas fa-check-circle mr-1"></i> Saved</span>
            </div>

            {{-- ══ PRINTABLE AREA ══ --}}
            <div class="pc-card-body p-0">
                <div id="prescription-print-area">
                    <div class="print-bill-wrapper">

                        {{-- Letterhead --}}
                        <div class="print-letterhead">
                            <div class="print-lh-left">
                                <div class="print-reg-label">Registration no:</div>
                                <div class="print-reg-val" id="pr-reg">—</div>
                            </div>
                            <div class="print-lh-center">
                                <div class="print-clinic-name">Professor Clinic</div>
                                <div class="print-clinic-addr">Majhira, Shajahanpur, Bogura</div>
                                <div class="print-clinic-cell">Cell: 01720-039006</div>
                            </div>
                            <div class="print-lh-right">
                                <div>01713-740680</div>
                                <div>01720-039005</div>
                                <div>01720-039006</div>
                                <div>01720-039007</div>
                                <div>01720-039008</div>
                            </div>
                        </div>

                        {{-- Patient info --}}
                        <div class="print-patient-row">
                            <div class="print-pf">
                                <span class="print-pf-label">Patient's name:</span>
                                <span class="print-pf-val" id="pr-name">—</span>
                            </div>
                            <div class="print-pf">
                                <span class="print-pf-label">Refd. by Dr:</span>
                                <span class="print-pf-val" id="pr-ref">—</span>
                            </div>
                            <div class="print-pf">
                                <span class="print-pf-label">Age:</span>
                                <span class="print-pf-val" id="pr-age">—</span>
                            </div>
                            <div class="print-pf">
                                <span class="print-pf-label">Date:</span>
                                <span class="print-pf-val" id="pr-date">—</span>
                            </div>
                            <div class="print-pf">
                                <span class="print-pf-label">Receipt:</span>
                                <span class="print-pf-val font-weight-bold" id="pr-receipt">—</span>
                            </div>
                        </div>

                        <div class="print-bill-title">INVESTIGATION BILL</div>

                        {{-- Bill rows --}}
                        <table class="print-bill-table">
                            <thead>
                                <tr>
                                    <th style="width:42px">Sl.</th>
                                    <th>Investigation</th>
                                    <th style="width:110px;text-align:right">Amount (৳)</th>
                                </tr>
                            </thead>
                            <tbody id="pr-bill-tbody">
                                {{-- filled by JS --}}
                            </tbody>
                        </table>

                        {{-- Footer note --}}
                        <div class="print-footer-note">
                            পরবর্তী যোগাযোগের জন্য উপরের Registration no অনুগ্রহপূর্বক মনে রাখবেন বা এই রশিদ সাথে আনবেন।
                        </div>
                        <div class="print-sig-row">
                            <div class="print-sig-left">
                                <span>Collected by: </span>
                                <span id="pr-collected-by" style="font-weight:600">—</span>
                            </div>
                            <div class="print-sig-right">
                                <div class="print-sig-line"></div>
                                <div class="print-sig-label">Authorized Signature</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="pc-card-footer">
                <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i> Generated: <span id="gen-time">—</span>
                </small>
                <div style="display:flex;gap:8px">
                    <button onclick="printRx()" class="pc-action-btn pc-print-btn">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button onclick="editReceipt()" class="pc-action-btn pc-edit-btn">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button onclick="backToStep1()" class="pc-action-btn pc-new-btn">
                        <i class="fas fa-plus mr-1"></i> New Bill
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /#panel-step2 --}}

{{-- ══ MODAL: View past receipt ══ --}}
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#00796B,#00695C);color:#fff;border:none;padding:18px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:18px;margin-right:12px">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold">Investigation Bill</h5>
                        <small style="opacity:.9" id="modal-subtitle">Loading…</small>
                    </div>
                </div>
                <div style="display:flex;gap:8px">
                    <button class="pc-modal-print-btn" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;opacity:.8" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="modal-loading" style="text-align:center;padding:60px;color:#888">
                    <i class="fas fa-spinner fa-spin" style="font-size:30px;color:#00796B"></i>
                    <p class="mt-3">Loading receipt…</p>
                </div>
                <div id="modal-error" class="d-none" style="text-align:center;padding:60px;color:#e53935">
                    <i class="fas fa-exclamation-triangle" style="font-size:32px"></i>
                    <p class="mt-3" id="modal-error-msg">Failed to load.</p>
                </div>
                <div id="modal-rx-area" class="d-none">
                    <div id="modal-prescription-print-area" style="padding:20px 24px"></div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #eee;padding:14px 24px">
                <small class="text-muted"><i class="fas fa-clock mr-1"></i> Saved: <span id="m-saved-time">—</span></small>
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<iframe id="print-iframe" style="position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none" title="Print Frame"></iframe>

@stop

{{-- ══════════════════════════════════════════════════
     CSS
══════════════════════════════════════════════════ --}}
@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --pc:#00796B; --pc-deep:#00695C; --pc-light:#E0F2F1; --pc-soft:#B2DFDB;
    --text:#1a2332; --muted:#6b7a90; --border:#e4e9f0; --bg:#f0f4f0;
    --red:#c62828; --amber:#f9a825; --green:#2e7d32;
    --radius:10px; --shadow:0 4px 16px rgba(0,0,0,.08);
    --font:'DM Sans','Hind Siliguri',Arial,sans-serif;
}
*,*::before,*::after{box-sizing:border-box}
body,.content-wrapper{background:var(--bg)!important;font-family:var(--font)}
.text-diag{color:var(--pc)!important}

/* Page title */
.page-main-title{font-size:22px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:10px}
.page-title-icon{width:38px;height:38px;border-radius:10px;background:var(--pc-light);display:inline-flex;align-items:center;justify-content:center;color:var(--pc);font-size:17px}
.btn-back-modern{background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:6px;font-weight:500;padding:7px 16px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;transition:all .2s}
.btn-back-modern:hover{background:var(--pc-light);border-color:var(--pc);color:var(--pc-deep);text-decoration:none}

/* Step tracker */
.step-track-card{background:#fff;border-radius:var(--radius);box-shadow:0 1px 4px rgba(0,0,0,.06);border:1px solid var(--border);padding:16px 24px}
.step-track-inner{display:flex;align-items:center}
.step-item{display:flex;align-items:center}
.step-text{margin-left:10px}
.step-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;transition:all .35s;border:2.5px solid transparent}
.step-active{background:var(--pc);color:#fff;border-color:var(--pc);box-shadow:0 0 0 4px rgba(0,121,107,.15)}
.step-done{background:var(--pc-deep);color:#fff;border-color:var(--pc-deep)}
.step-inactive{background:#fff;color:#ccc;border-color:#ddd}
.step-label-main{font-size:13px;font-weight:700;line-height:1.2}
.step-label-sub{font-size:11px;color:var(--muted)}
.step-label-active{color:var(--pc)}
.step-label-inactive{color:#bbb}
.step-connector-line{flex:1;max-width:140px;height:3px;background:#e8ecf0;margin:0 18px;border-radius:2px;transition:background .4s}
.step-connector-line.done{background:var(--pc-deep)}

/* Cards */
.pc-card{background:#fff;border-radius:14px;box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden;margin-bottom:24px}
.pc-card-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd;flex-wrap:wrap;gap:10px}
.pc-card-title{display:flex;align-items:center;gap:12px}
.pc-card-title h5,.pc-card-title h6{font-size:15px}
.pc-icon-wrap{width:40px;height:40px;border-radius:10px;background:var(--pc-light);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--pc);flex-shrink:0}
.pc-card-body{padding:24px}
.pc-card-footer{padding:14px 24px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between}
.pc-badge{background:var(--pc-light);color:var(--pc-deep);border-radius:20px;padding:5px 14px;font-size:12.5px;font-weight:600}
.pc-saved-badge{background:#43A047;color:#fff;border-radius:20px;padding:6px 14px;font-size:12px;font-weight:600}

/* Search */
.pc-search-bar{padding:12px 24px;background:#fafbff;border-bottom:2px solid var(--pc-soft)}
.pc-search-group{display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s}
.pc-search-group:focus-within{border-color:var(--pc);box-shadow:0 0 0 3px rgba(0,121,107,.1)}
.pc-search-icon{padding:0 12px;color:#aab;font-size:15px}
.pc-search-input{flex:1;border:none;outline:none;padding:10px 6px;font-size:14px;background:transparent;color:var(--text);font-family:var(--font)}
.pc-search-btn{border:none;padding:10px 22px;font-size:13px;font-weight:600;cursor:pointer;background:var(--pc);color:#fff;transition:background .2s}
.pc-search-btn:hover{background:var(--pc-deep)}

/* Tables */
.pc-table-wrap{overflow-x:auto;overflow-y:auto;max-height:calc(100vh - 380px)}
.pc-table{width:100%;border-collapse:separate;border-spacing:0}
.pc-table thead th{background:#f0faf8;color:var(--text);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:10px 13px;border-bottom:2px solid var(--pc-soft);white-space:nowrap;position:sticky;top:0;z-index:10}
.pc-table tbody tr{transition:background .15s}
.pc-table tbody tr:hover{background:#f0faf8}
.pc-table tbody td{padding:10px 13px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);vertical-align:middle}
.pc-table tbody tr:last-child td{border-bottom:none}
.pc-adm-badge{background:#e0f2f1;color:var(--pc-deep);border-radius:5px;padding:2px 8px;font-size:11.5px;font-weight:700;font-family:monospace}
.pc-blood{background:#ffebee;color:var(--red);border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700}
.pc-gender-m{background:#e3f2fd;color:#1565c0;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700}
.pc-gender-f{background:#fce4ec;color:#c2185b;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700}
.pc-status-paid{background:#e8f5e9;color:#2e7d32;border-radius:5px;padding:3px 9px;font-size:11px;font-weight:600}
.pc-status-partial{background:#fff3e0;color:#f57c00;border-radius:5px;padding:3px 9px;font-size:11px;font-weight:600}
.pc-status-due{background:#ffebee;color:var(--red);border-radius:5px;padding:3px 9px;font-size:11px;font-weight:600}
.pc-name-cell{display:flex;align-items:center;gap:10px}
.pc-avatar{width:32px;height:32px;border-radius:50%;background:var(--pc);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}
.pc-empty{text-align:center;padding:40px 20px;color:var(--muted)}
.pc-empty i{font-size:48px;margin-bottom:16px;opacity:.6;display:block}
.pc-empty p{margin:0;font-size:15px}
.pc-pagination{padding:16px 24px;background:#fafbff;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.pc-pagination .pagination{margin:0}
.pc-select-btn{background:var(--pc);color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-select-btn:hover{background:var(--pc-deep)}
.pc-view-btn{background:#1976D2;color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-view-btn:hover{background:#1565C0}

/* Patient bar */
.pc-patient-bar{background:linear-gradient(135deg,var(--pc),var(--pc-deep));border-radius:14px;padding:20px 24px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.pc-bar-avatar{width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0}
.pc-bar-name{font-size:18px;font-weight:700;margin-bottom:2px}
.pc-bar-meta{font-size:13px;opacity:.9}
.pc-bar-dot{width:8px;height:8px;border-radius:50%;background:#4caf50;animation:pulse 2s infinite;flex-shrink:0}
.pc-change-btn{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-change-btn:hover{background:rgba(255,255,255,.3)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}

/* ── BILL LETTERHEAD ── */
.bill-letterhead{
    display:flex;align-items:flex-start;justify-content:space-between;
    padding:20px 24px 16px;
    background:linear-gradient(135deg,var(--pc),var(--pc-deep));
    color:#fff;flex-wrap:wrap;gap:12px
}
.bill-lh-left{font-size:12px;opacity:.9;min-width:140px}
.bill-reg-no{font-size:12px}
.bill-clinic-name{font-size:22px;font-weight:700;letter-spacing:.5px;text-align:center}
.bill-clinic-addr{font-size:13px;opacity:.9;text-align:center}
.bill-lh-center{flex:1;text-align:center}
.bill-lh-right{font-size:12px;opacity:.85;text-align:right;line-height:1.8;min-width:130px}

/* Bill info row */
.bill-info-row{display:flex;flex-wrap:wrap;gap:14px;padding:16px 24px;background:#f8f9fa;border-bottom:1px solid var(--border)}
.bill-info-field{flex:1;min-width:150px;display:flex;flex-direction:column;gap:4px}
.bill-info-field label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.bill-input{width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;color:var(--text);font-family:var(--font);background:#fff;transition:border-color .2s;outline:none}
.bill-input:focus{border-color:var(--pc)}
.bill-input[readonly]{background:#f0f0f0;color:var(--muted)}
select.bill-input{cursor:pointer}

/* Bill title bar */
.bill-title-bar{
    text-align:center;font-size:15px;font-weight:700;letter-spacing:2px;
    padding:10px;background:#f0faf8;border-bottom:2px solid var(--pc-soft);
    color:var(--pc-deep);text-transform:uppercase
}

/* Bill table */
.bill-table{width:100%;border-collapse:collapse;margin:0}
.bill-table thead th{
    background:#f0faf8;color:var(--text);font-size:11px;font-weight:700;
    text-transform:uppercase;letter-spacing:.5px;padding:9px 16px;
    border-bottom:2px solid var(--pc-soft)
}
.bill-row td{
    padding:9px 16px;border-bottom:1px solid var(--border);
    font-size:13px;vertical-align:middle;color:var(--text)
}
.bill-row:hover td{background:#f9fdfc}
.bill-sl{font-weight:700;color:var(--muted);width:42px;text-align:center}
.bill-total-row td{padding:10px 16px;font-size:13px;vertical-align:middle}
.bill-amount-input{
    width:100%;max-width:120px;padding:7px 10px;border:1.5px solid var(--border);
    border-radius:6px;font-size:13px;font-weight:600;color:var(--text);
    font-family:var(--font);text-align:right;background:#fff;outline:none;
    transition:border-color .2s;display:block;margin-left:auto
}
.bill-amount-input:focus{border-color:var(--pc)}

/* USG sub-rows */
.bill-sub-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.bill-sub-label{font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap;min-width:36px}
.bill-sub-select{flex:1;min-width:180px;padding:5px 8px;border:1.5px solid var(--border);border-radius:5px;font-size:12px;font-family:var(--font);outline:none;transition:border-color .2s;cursor:pointer}
.bill-sub-select:focus{border-color:var(--pc)}
.bill-sub-amt{width:90px;padding:5px 8px;border:1.5px solid var(--border);border-radius:5px;font-size:12px;font-weight:600;text-align:right;font-family:var(--font);outline:none;transition:border-color .2s}
.bill-sub-amt:focus{border-color:var(--pc)}
.bill-sub-total-cell{font-size:13px;vertical-align:middle}

/* Dropdown rows (X-Ray, Pathology) */
.bill-dropdown-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.bill-dropdown-select{flex:1;min-width:220px;padding:5px 8px;border:1.5px solid var(--border);border-radius:5px;font-size:12px;font-family:var(--font);outline:none;transition:border-color .2s;cursor:pointer}
.bill-dropdown-select:focus{border-color:var(--pc)}
.bill-dropdown-custom{padding:5px 8px;border:1.5px solid var(--pc-soft);border-radius:5px;font-size:12px;font-family:var(--font);outline:none;flex:1;min-width:140px}

/* Others desc */
.bill-other-desc{width:100%;padding:5px 8px;border:1.5px solid var(--border);border-radius:5px;font-size:12px;font-family:var(--font);outline:none}
.bill-other-desc:focus{border-color:var(--pc)}

/* Advance warning */
.bill-warn{background:#fff3e0;border-left:3px solid var(--amber);color:#e65100;font-size:12px;padding:5px 10px;border-radius:4px;margin-top:6px}

/* Reminder */
.bill-reminder{background:var(--pc-light);border-left:4px solid var(--pc);color:var(--pc-deep);padding:10px 14px;border-radius:6px;font-size:12px}

/* Bill field */
.bill-field{margin-bottom:16px}
.bill-field label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px}

/* Form footer */
.bill-form-footer{padding-top:20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.pc-back-btn{background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:6px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-back-btn:hover{background:var(--bg);border-color:var(--pc)}
.pc-save-btn{background:var(--pc);color:#fff;border:none;border-radius:6px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s}
.pc-save-btn:hover{background:var(--pc-deep)}

/* Receipt summary cards */
.pc-rx-card{background:#fff;border-radius:10px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,.06);border-left:4px solid;display:flex;align-items:center;gap:14px}
.pc-rx-icon{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:19px;color:#fff;flex-shrink:0}
.pc-rx-lbl{font-size:12px;color:var(--muted);margin-bottom:3px}
.pc-rx-val{font-size:16px;font-weight:700;color:var(--text)}
.pc-action-btn{background:#fff;border:1px solid var(--border);color:var(--text);border-radius:6px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-print-btn:hover{background:var(--pc-light);color:var(--pc-deep);border-color:var(--pc)}
.pc-edit-btn:hover{background:#fff3e0;color:#f57c00}
.pc-new-btn:hover{background:#e8f5e9;color:var(--green)}

/* ── PRINTABLE BILL ── */
.print-bill-wrapper{background:#fff;max-width:560px;margin:0 auto;border:1px solid #ccc;font-family:'Hind Siliguri',Arial,sans-serif}
.print-letterhead{display:flex;justify-content:space-between;align-items:flex-start;padding:18px;border-bottom:2px solid #00796B}
.print-lh-left{font-size:11px;color:#444;min-width:110px}
.print-reg-label{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.5px}
.print-reg-val{font-size:13px;font-weight:700;color:#00695C}
.print-lh-center{flex:1;text-align:center;padding:0 12px}
.print-clinic-name{font-size:19px;font-weight:700;color:#00695C;letter-spacing:.5px}
.print-clinic-addr{font-size:12px;color:#555}
.print-clinic-cell{font-size:11px;color:#777}
.print-lh-right{font-size:11px;color:#555;text-align:right;line-height:1.7;min-width:110px}
.print-patient-row{display:flex;flex-wrap:wrap;gap:10px;padding:12px 18px;background:#f8f9fa;border-bottom:1px solid #ddd}
.print-pf{flex:1;min-width:110px}
.print-pf-label{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.4px;display:block}
.print-pf-val{font-size:12px;font-weight:600;color:#333}
.print-bill-title{text-align:center;font-size:13px;font-weight:700;letter-spacing:2px;padding:8px;background:#f0faf8;border-bottom:2px solid #B2DFDB;color:#00695C;text-transform:uppercase}
.print-bill-table{width:100%;border-collapse:collapse;font-size:12px}
.print-bill-table thead th{background:#f0faf8;color:#1a2332;font-size:10.5px;font-weight:700;text-transform:uppercase;padding:7px 14px;border-bottom:2px solid #B2DFDB;text-align:left}
.print-bill-table tbody td{padding:7px 14px;border-bottom:1px solid #eee;vertical-align:middle}
.print-bill-table tfoot td{padding:8px 14px;font-weight:700}
.print-footer-note{font-size:11px;color:#666;padding:10px 18px;border-top:1px dashed #ccc;background:#fafafa}
.print-sig-row{display:flex;justify-content:space-between;align-items:flex-end;padding:14px 18px}
.print-sig-left{font-size:12px;color:#555}
.print-sig-right{text-align:center}
.print-sig-line{border-bottom:1px solid #999;width:120px;margin-bottom:4px}
.print-sig-label{font-size:11px;color:#777}

/* Modal */
.pc-modal-print-btn{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:6px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-modal-print-btn:hover{background:rgba(255,255,255,.3)}

/* Previous payments */
#prev-payments-list .mini-item{background:#f0faf8;border-radius:6px;padding:10px 14px;margin-bottom:8px;border-left:3px solid var(--pc)}
#prev-payments-list .mini-item:last-child{margin-bottom:0}
.mini-item-row{display:flex;justify-content:space-between;align-items:center}

/* Utilities */
.d-none{display:none!important}
.text-right{text-align:right}

/* ── PRINT MEDIA ── */
@media print{
    *{visibility:hidden!important}
    #prescription-print-area,#prescription-print-area *{visibility:visible!important}
    #prescription-print-area{position:fixed!important;top:0!important;left:0!important;width:100%!important;margin:0!important;padding:0!important;z-index:99999!important}
    .print-bill-wrapper{box-shadow:none!important;border:1px solid #000!important;max-width:100%!important}
    @page{margin:10mm;size:A5}
}
</style>
@stop

{{-- ══════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════ --}}
@section('js')
<script>
// ── Globals ──────────────────────────────────────
var selectedPatient = null;

function getCsrfToken(){
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
}
function esc(s){
    if(!s) return '';
    return s.toString().replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]});
}
function taka(n){ return '৳ ' + Number(n||0).toLocaleString('en-BD'); }
function fmtDate(d){ if(!d) return '—'; return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}); }

// ── Notification ──────────────────────────────────
function showAlert(msg, type){
    var el = document.getElementById('save-alert');
    el.className = 'alert alert-'+(type||'success')+' mb-3';
    el.textContent = msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){ el.classList.add('d-none'); }, 5000);
}

// ── Select patient ────────────────────────────────
function selectPatient(btn){
    selectedPatient = {
        admission_id:   btn.dataset.admissionId,
        patient_id:     btn.dataset.patientId,
        name:           btn.dataset.name,
        age:            btn.dataset.age,
        code:           btn.dataset.code,
        mobile:         btn.dataset.mobile,
        blood:          btn.dataset.blood,
        admission_date: btn.dataset.admissionDate
    };
    document.getElementById('f-patient-id').value   = selectedPatient.patient_id;
    document.getElementById('f-admission-id').value = selectedPatient.admission_id;
    document.getElementById('f-patient-code').value = selectedPatient.code;
    document.getElementById('f-patient-name').value = selectedPatient.name;
    document.getElementById('f-patient-age').value  = selectedPatient.age;
    document.getElementById('f-reg-no').value        = selectedPatient.code;
    document.getElementById('lh-reg-no').textContent = selectedPatient.code;
    document.getElementById('f-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('spb-avatar').textContent = (selectedPatient.name||'P').charAt(0).toUpperCase();
    document.getElementById('spb-name').textContent   = selectedPatient.name;
    document.getElementById('spb-meta').textContent   =
        'Code: '+selectedPatient.code+' | Age: '+selectedPatient.age+' | Adm. #'+selectedPatient.admission_id;

    // Reset all amounts
    ['admission','ecg','xray','path','stitch','nebulize','oxygen','injection','others',
     'less','advance'].forEach(function(k){
        var el = document.getElementById('amt-'+k);
        if(el) el.value = '';
    });
    ['usg-la-amt','usg-wa-amt','usg-pp-amt'].forEach(function(k){
        var el = document.getElementById(k);
        if(el) el.value = '';
    });
    document.getElementById('f-received-by').value = '';
    document.getElementById('f-notes').value = '';
    document.getElementById('bill-form-card').style.display = 'block';
    document.getElementById('receipt-view').style.display   = 'none';
    recalc();
    moveToStep2();
    loadPreviousPayments();
}

// ── Step navigation ───────────────────────────────
function moveToStep2(){
    var s1c = document.getElementById('step1-circle');
    s1c.className = 'step-circle step-done';
    s1c.innerHTML = '<i class="fas fa-check"></i>';
    document.getElementById('step-connector').classList.add('done');
    document.getElementById('step2-circle').className = 'step-circle step-active';
    document.getElementById('step2-label').className  = 'step-label-main step-label-active';
    document.getElementById('breadcrumb-current').textContent = 'Investigation Bill';
    document.getElementById('panel-step1').style.display = 'none';
    document.getElementById('panel-step2').style.display = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}
function backToStep1(){
    var s1c = document.getElementById('step1-circle');
    s1c.className = 'step-circle step-active';
    s1c.textContent = '1';
    document.getElementById('step-connector').classList.remove('done');
    document.getElementById('step2-circle').className = 'step-circle step-inactive';
    document.getElementById('step2-label').className  = 'step-label-main step-label-inactive';
    document.getElementById('breadcrumb-current').textContent = 'Select Patient';
    document.getElementById('panel-step1').style.display = 'block';
    document.getElementById('panel-step2').style.display = 'none';
    window.scrollTo({top:0,behavior:'smooth'});
}

// ── Dropdown handlers ─────────────────────────────
function onUsgChange(part){
    var sel = document.getElementById('usg-'+part+'-name');
    var amt = document.getElementById('usg-'+part+'-amt');
    if(!sel||!amt) return;
    if(sel.value === 'custom'){
        amt.value = '';
        amt.readOnly = false;
        amt.focus();
    } else if(sel.value){
        amt.value = sel.value;
        amt.readOnly = false;
    } else {
        amt.value = '';
    }
    recalc();
}

function onXrayChange(){
    var sel = document.getElementById('xray-select');
    var amt = document.getElementById('amt-xray');
    var cust = document.getElementById('xray-custom-name');
    if(sel.value === 'custom'){
        cust.style.display = '';
        amt.value = '';
        amt.readOnly = false;
        cust.focus();
    } else if(sel.value){
        cust.style.display = 'none';
        amt.value = sel.value;
        amt.readOnly = false;
    } else {
        cust.style.display = 'none';
        amt.value = '';
    }
    recalc();
}

function onPathChange(){
    var sel = document.getElementById('path-select');
    var amt = document.getElementById('amt-path');
    var cust = document.getElementById('path-custom-name');
    if(sel.value === 'custom'){
        cust.style.display = '';
        amt.value = '';
        amt.readOnly = false;
        cust.focus();
    } else if(sel.value){
        cust.style.display = 'none';
        amt.value = sel.value;
        amt.readOnly = false;
    } else {
        cust.style.display = 'none';
        amt.value = '';
    }
    recalc();
}

function onLessChange(){
    var sel = document.getElementById('less-type');
    var amt = document.getElementById('amt-less');
    var total = calcGross();
    if(sel.value === '0'){
        amt.value = '';
        amt.readOnly = true;
    } else if(sel.value === 'custom'){
        amt.value = '';
        amt.readOnly = false;
        amt.focus();
    } else if(sel.value.startsWith('pct')){
        var pct = parseInt(sel.value.replace('pct',''));
        amt.value = Math.round(total * pct / 100);
        amt.readOnly = true;
    }
    recalc();
}

// ── Recalculate totals ────────────────────────────
function v(id){ return parseFloat(document.getElementById(id)&&document.getElementById(id).value) || 0; }

function calcGross(){
    var usgTotal = v('usg-la-amt') + v('usg-wa-amt') + v('usg-pp-amt');
    document.getElementById('usg-subtotal').textContent = taka(usgTotal);
    return v('amt-admission') + usgTotal + v('amt-ecg') + v('amt-xray') +
           v('amt-path') + v('amt-stitch') + v('amt-nebulize') + v('amt-oxygen') +
           v('amt-injection') + v('amt-others');
}

function recalc(){
    var gross    = calcGross();
    var less     = v('amt-less');
    var net      = Math.max(0, gross - less);
    var advance  = v('amt-advance');
    var due      = Math.max(0, net - advance);
    var minAdv   = gross * 0.25;

    document.getElementById('disp-total').textContent = taka(gross);
    document.getElementById('disp-net').textContent   = taka(net);
    document.getElementById('disp-due').textContent   = taka(due);

    // Advance 25% warning
    var warn = document.getElementById('adv-warning');
    var advInput = document.getElementById('amt-advance');
    if(advance > 0 && advance < minAdv){
        warn.style.display = '';
        advInput.style.borderColor = '#e53935';
    } else {
        warn.style.display = 'none';
        advInput.style.borderColor = '#f9a825';
    }
}

// ── Previous payments ─────────────────────────────
function loadPreviousPayments(){
    if(!selectedPatient) return;
    var box  = document.getElementById('prev-payments-box');
    var list = document.getElementById('prev-payments-list');
    box.style.display = 'none';

    fetch('/nursing/InvestigationPayment/by-admission/'+selectedPatient.admission_id,{
        headers:{'Accept':'application/json'}
    })
    .then(function(r){
        if(!r.ok) return [];
        var ct = r.headers.get('content-type')||'';
        if(!ct.includes('application/json')) return [];
        return r.json();
    })
    .then(function(data){
        if(!Array.isArray(data)||data.length===0){ box.style.display='none'; return; }
        box.style.display = 'block';
        var totalPaid = data.reduce(function(s,p){ return s+parseFloat(p.paid_amount||0); },0);
        document.getElementById('prev-total-paid').textContent = taka(totalPaid);
        list.innerHTML = data.map(function(p){
            var sc = p.status==='paid'?'pc-status-paid':(p.status==='partial'?'pc-status-partial':'pc-status-due');
            return '<div class="mini-item">' +
                '<div class="mini-item-row">' +
                '<div><strong>'+esc(p.receipt_no)+'</strong> <small class="text-muted">'+fmtDate(p.payment_date)+'</small></div>' +
                '<div style="display:flex;align-items:center;gap:8px">' +
                '<span class="'+sc+'">'+esc(p.status)+'</span>' +
                '<strong>'+taka(p.paid_amount)+'</strong></div>' +
                '</div>' +
                (parseFloat(p.due_amount)>0
                    ? '<div class="mini-item-row mt-1"><small class="text-muted">Due:</small>&nbsp;<small class="text-danger font-weight-bold">'+taka(p.due_amount)+'</small></div>'
                    : '') +
                '</div>';
        }).join('');
    })
    .catch(function(e){ console.warn(e); box.style.display='none'; });
}

// ── Build items for payload ───────────────────────
function buildItems(){
    var items = [];
    var usgLa = v('usg-la-amt'), usgWa = v('usg-wa-amt'), usgPp = v('usg-pp-amt');
    if(v('amt-admission')>0) items.push({category:'Admission',service_name:'Admission Fee',unit_price:v('amt-admission'),quantity:1,item_discount:0,amount:v('amt-admission'),remarks:''});
    if(usgLa>0) items.push({category:'USG',service_name:'USG L/A ('+( document.getElementById('usg-la-name').options[document.getElementById('usg-la-name').selectedIndex].text )+')',unit_price:usgLa,quantity:1,item_discount:0,amount:usgLa,remarks:''});
    if(usgWa>0) items.push({category:'USG',service_name:'USG W/A ('+( document.getElementById('usg-wa-name').options[document.getElementById('usg-wa-name').selectedIndex].text )+')',unit_price:usgWa,quantity:1,item_discount:0,amount:usgWa,remarks:''});
    if(usgPp>0) items.push({category:'USG',service_name:'USG P/P ('+( document.getElementById('usg-pp-name').options[document.getElementById('usg-pp-name').selectedIndex].text )+')',unit_price:usgPp,quantity:1,item_discount:0,amount:usgPp,remarks:''});
    if(v('amt-ecg')>0) items.push({category:'ECG',service_name:'E.C.G',unit_price:v('amt-ecg'),quantity:1,item_discount:0,amount:v('amt-ecg'),remarks:''});
    if(v('amt-xray')>0){
        var xn = document.getElementById('xray-custom-name').value ||
                 document.getElementById('xray-select').options[document.getElementById('xray-select').selectedIndex].text;
        items.push({category:'X-Ray',service_name:'X-Ray: '+xn,unit_price:v('amt-xray'),quantity:1,item_discount:0,amount:v('amt-xray'),remarks:''});
    }
    if(v('amt-path')>0){
        var pn = document.getElementById('path-custom-name').value ||
                 document.getElementById('path-select').options[document.getElementById('path-select').selectedIndex].text;
        items.push({category:'Pathology',service_name:'Pathology: '+pn,unit_price:v('amt-path'),quantity:1,item_discount:0,amount:v('amt-path'),remarks:''});
    }
    if(v('amt-stitch')>0) items.push({category:'Other',service_name:'Stitch Cutting / Dressing',unit_price:v('amt-stitch'),quantity:1,item_discount:0,amount:v('amt-stitch'),remarks:''});
    if(v('amt-nebulize')>0) items.push({category:'Other',service_name:'Nebulize',unit_price:v('amt-nebulize'),quantity:1,item_discount:0,amount:v('amt-nebulize'),remarks:''});
    if(v('amt-oxygen')>0) items.push({category:'Other',service_name:'Oxygen',unit_price:v('amt-oxygen'),quantity:1,item_discount:0,amount:v('amt-oxygen'),remarks:''});
    if(v('amt-injection')>0) items.push({category:'Other',service_name:'Injection',unit_price:v('amt-injection'),quantity:1,item_discount:0,amount:v('amt-injection'),remarks:''});
    if(v('amt-others')>0){
        var od = document.getElementById('others-desc').value || 'Others';
        items.push({category:'Other',service_name:od,unit_price:v('amt-others'),quantity:1,item_discount:0,amount:v('amt-others'),remarks:''});
    }
    return items;
}

// ── Save Payment ──────────────────────────────────
function savePayment(){
    if(!selectedPatient){ showAlert('Please select a patient.','danger'); return; }
    var items = buildItems();
    if(items.length===0){ showAlert('Please enter at least one amount.','danger'); return; }

    var gross   = calcGross();
    var less    = v('amt-less');
    var net     = Math.max(0, gross - less);
    var advance = v('amt-advance');
    var minAdv  = gross * 0.25;

    if(advance < minAdv && advance > 0){
        showAlert('Advance must be at least 25% of Total (৳'+Math.ceil(minAdv)+').','warning');
        return;
    }

    var payload = {
        patient_id:    selectedPatient.patient_id,
        admission_id:  selectedPatient.admission_id,
        patient_name:  selectedPatient.name,
        patient_age:   selectedPatient.age,
        patient_code:  selectedPatient.code,
        mobile_no:     selectedPatient.mobile,
        payment_date:  document.getElementById('f-date').value,
        payment_method:'cash',
        discount:      less,
        paid_amount:   advance,
        collected_by:  document.getElementById('f-received-by').value,
        notes:         document.getElementById('f-notes').value,
        items:         items
    };

    var btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving…';

    fetch('/nursing/InvestigationPayment/store',{
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':getCsrfToken()},
        body:JSON.stringify(payload)
    })
    .then(function(r){
        var ct = r.headers.get('content-type')||'';
        if(!ct.includes('application/json')){
            return r.text().then(function(t){ throw new Error('Server error '+r.status+': '+t.substring(0,200)); });
        }
        return r.json().then(function(d){
            if(!r.ok) throw new Error(d.message||'Server error '+r.status);
            return d;
        });
    })
    .then(function(data){
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save &amp; Print Bill';
        if(data.success){
            renderReceipt(data.data);
            showAlert('Bill saved successfully!','success');
            loadPreviousPayments();
        } else {
            showAlert(data.message||'Failed to save.','danger');
        }
    })
    .catch(function(err){
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save &amp; Print Bill';
        showAlert('Error: '+err.message,'danger');
        console.error(err);
    });
}

// ── Render receipt ────────────────────────────────
function renderReceipt(d){
    document.getElementById('ib-name').textContent    = d.patient_name||'—';
    document.getElementById('ib-receipt').textContent = d.receipt_no  ||'—';
    document.getElementById('ib-paid').textContent    = taka(d.paid_amount);
    document.getElementById('ib-due').textContent     = taka(d.due_amount);

    document.getElementById('pr-reg').textContent         = d.patient_code  ||'—';
    document.getElementById('pr-name').textContent        = d.patient_name  ||'—';
    document.getElementById('pr-age').textContent         = d.patient_age   ||'—';
    document.getElementById('pr-date').textContent        = fmtDate(d.payment_date);
    document.getElementById('pr-receipt').textContent     = d.receipt_no    ||'—';
    document.getElementById('pr-ref').textContent         = document.getElementById('f-ref-dr').value||'—';
    document.getElementById('pr-collected-by').textContent = d.received_by  ||'—';
    document.getElementById('gen-time').textContent       = new Date().toLocaleString('en-GB');

    var rows = (d.tests||[]).map(function(it,i){
        return '<tr>' +
            '<td style="text-align:center;font-weight:600;color:#888">'+(i+1).toString().padStart(2,'0')+'</td>' +
            '<td>'+esc(it.test_name)+'</td>' +
            '<td style="text-align:right;font-weight:600">'+Number(it.subtotal||0).toLocaleString()+'</td>' +
        '</tr>';
    });

    var gross    = parseFloat(d.gross_amount||0);
    var less     = parseFloat(d.discount_amount||0);
    var net      = parseFloat(d.total_amount||0);
    var advance  = parseFloat(d.paid_amount||0);
    var due      = parseFloat(d.due_amount||0);

    rows.push('<tr style="background:#f0faf8;font-weight:700">' +
        '<td colspan="2" style="text-align:right;padding:8px 14px">Total</td>' +
        '<td style="text-align:right;padding:8px 14px">'+Number(gross).toLocaleString()+'</td></tr>');
    if(less>0){
        rows.push('<tr style="color:#c62828">' +
            '<td colspan="2" style="text-align:right;padding:6px 14px">Less</td>' +
            '<td style="text-align:right;padding:6px 14px">– '+Number(less).toLocaleString()+'</td></tr>');
    }
    rows.push('<tr style="background:#e0f2f1;font-weight:700;color:#00695c">' +
        '<td colspan="2" style="text-align:right;padding:8px 14px;font-size:13px">Net Total</td>' +
        '<td style="text-align:right;padding:8px 14px;font-size:14px">'+Number(net).toLocaleString()+'</td></tr>');
    rows.push('<tr style="background:#fff8e1">' +
        '<td colspan="2" style="text-align:right;padding:7px 14px">Advance</td>' +
        '<td style="text-align:right;padding:7px 14px;font-weight:700">'+Number(advance).toLocaleString()+'</td></tr>');
    if(due>0){
        rows.push('<tr style="background:#ffebee;font-weight:700;color:#c62828">' +
            '<td colspan="2" style="text-align:right;padding:8px 14px">Due</td>' +
            '<td style="text-align:right;padding:8px 14px">'+Number(due).toLocaleString()+'</td></tr>');
    }

    document.getElementById('pr-bill-tbody').innerHTML = rows.join('');
    document.getElementById('bill-form-card').style.display = 'none';
    document.getElementById('receipt-view').style.display   = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

// ── View past receipt (modal) ─────────────────────
function viewReceipt(id){
    $('#receiptModal').modal('show');
    document.getElementById('modal-loading').style.display = '';
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');

    fetch('/nursing/InvestigationPayment/detail/'+id,{headers:{'Accept':'application/json'}})
    .then(function(r){
        if(!r.ok) throw new Error('HTTP '+r.status);
        return r.json();
    })
    .then(function(resp){
        if(resp.success) renderModal(resp.data);
        else showModalError(resp.message||'Not found');
    })
    .catch(function(e){ showModalError(e.message); });
}

function renderModal(d){
    var rows = (d.tests||[]).map(function(it,i){
        return '<tr>' +
            '<td>'+(i+1)+'</td><td>'+esc(it.test_name)+'</td>' +
            '<td style="text-align:right">'+Number(it.subtotal||0).toLocaleString()+'</td>' +
        '</tr>';
    }).join('');

    var gross = parseFloat(d.gross_amount||d.total_amount||0);
    var less  = parseFloat(d.discount_amount||0);
    var net   = parseFloat(d.total_amount||0);
    var adv   = parseFloat(d.paid_amount||0);
    var due   = parseFloat(d.due_amount||0);

    document.getElementById('modal-prescription-print-area').innerHTML =
        '<div class="print-bill-wrapper">' +
        '<div class="print-letterhead">' +
        '<div class="print-lh-left"><div class="print-reg-label">Registration no</div><div class="print-reg-val">'+esc(d.patient_code||'—')+'</div></div>' +
        '<div class="print-lh-center"><div class="print-clinic-name">Professor Clinic</div><div class="print-clinic-addr">Majhira, Shajahanpur, Bogura</div><div class="print-clinic-cell">Cell: 01720-039006</div></div>' +
        '<div class="print-lh-right"><div>01713-740680</div><div>01720-039005</div><div>01720-039006</div><div>01720-039007</div><div>01720-039008</div></div>' +
        '</div>' +
        '<div class="print-patient-row">' +
        '<div class="print-pf"><span class="print-pf-label">Patient\'s name</span><span class="print-pf-val">'+esc(d.patient_name||'—')+'</span></div>' +
        '<div class="print-pf"><span class="print-pf-label">Age</span><span class="print-pf-val">'+esc(d.patient_age||'—')+'</span></div>' +
        '<div class="print-pf"><span class="print-pf-label">Date</span><span class="print-pf-val">'+fmtDate(d.payment_date)+'</span></div>' +
        '<div class="print-pf"><span class="print-pf-label">Receipt</span><span class="print-pf-val font-weight-bold">'+esc(d.receipt_no||'—')+'</span></div>' +
        '</div>' +
        '<div class="print-bill-title">INVESTIGATION BILL</div>' +
        '<table class="print-bill-table"><thead><tr><th style="width:42px;text-align:center">Sl.</th><th>Investigation</th><th style="text-align:right">Amount (৳)</th></tr></thead>' +
        '<tbody>'+rows+'</tbody>' +
        '<tfoot>' +
        '<tr style="background:#f0faf8"><td colspan="2" style="text-align:right;font-weight:700">Total</td><td style="text-align:right;font-weight:700">'+Number(gross).toLocaleString()+'</td></tr>' +
        (less>0 ? '<tr style="color:#c62828"><td colspan="2" style="text-align:right">Less</td><td style="text-align:right">– '+Number(less).toLocaleString()+'</td></tr>' : '') +
        '<tr style="background:#e0f2f1"><td colspan="2" style="text-align:right;font-weight:700;color:#00695c">Net Total</td><td style="text-align:right;font-weight:700;color:#00695c">'+Number(net).toLocaleString()+'</td></tr>' +
        '<tr style="background:#fff8e1"><td colspan="2" style="text-align:right">Advance</td><td style="text-align:right;font-weight:700">'+Number(adv).toLocaleString()+'</td></tr>' +
        (due>0 ? '<tr style="background:#ffebee"><td colspan="2" style="text-align:right;font-weight:700;color:#c62828">Due</td><td style="text-align:right;font-weight:700;color:#c62828">'+Number(due).toLocaleString()+'</td></tr>' : '') +
        '</tfoot></table>' +
        '<div class="print-footer-note">পরবর্তী যোগাযোগের জন্য উপরের Registration no অনুগ্রহপূর্বক মনে রাখবেন বা এই রশিদ সাথে আনবেন।</div>' +
        '<div class="print-sig-row"><div class="print-sig-left">Collected by: <strong>'+esc(d.received_by||'—')+'</strong></div>' +
        '<div class="print-sig-right"><div class="print-sig-line"></div><div class="print-sig-label">Authorized Signature</div></div></div>' +
        '</div>';

    document.getElementById('modal-subtitle').textContent = (d.patient_name||'')+' — '+(d.receipt_no||'');
    document.getElementById('m-saved-time').textContent = d.created_at
        ? new Date(d.created_at).toLocaleString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—';
    document.getElementById('modal-loading').style.display = 'none';
    document.getElementById('modal-rx-area').classList.remove('d-none');
}

function showModalError(msg){
    document.getElementById('modal-error-msg').textContent = msg;
    document.getElementById('modal-loading').style.display = 'none';
    document.getElementById('modal-error').classList.remove('d-none');
}

// ── Print ─────────────────────────────────────────
function printRx(){ window.print(); }

function printModal(){
    var area = document.getElementById('modal-prescription-print-area');
    if(!area||!area.innerHTML.trim()){ alert('No receipt to print.'); return; }
    var html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Investigation Bill</title>' +
        '<style>' +
        'body{margin:0;padding:10mm;font-family:"Hind Siliguri",Arial,sans-serif}' +
        ':root{--pc:#00796B;--pc-deep:#00695C;--pc-light:#E0F2F1;--pc-soft:#B2DFDB}' +
        '.print-bill-wrapper{max-width:560px;margin:0 auto;border:1px solid #ccc}' +
        '.print-letterhead{display:flex;justify-content:space-between;padding:18px;border-bottom:2px solid #00796B}' +
        '.print-lh-center{flex:1;text-align:center;padding:0 12px}' +
        '.print-clinic-name{font-size:19px;font-weight:700;color:#00695C}' +
        '.print-clinic-addr,.print-clinic-cell{font-size:12px;color:#555}' +
        '.print-lh-left,.print-lh-right{font-size:11px;color:#555;min-width:110px}' +
        '.print-lh-right{text-align:right;line-height:1.7}' +
        '.print-reg-label{font-size:10px;color:#888;text-transform:uppercase}' +
        '.print-reg-val{font-size:13px;font-weight:700;color:#00695C}' +
        '.print-patient-row{display:flex;flex-wrap:wrap;gap:10px;padding:12px 18px;background:#f8f9fa;border-bottom:1px solid #ddd}' +
        '.print-pf{flex:1;min-width:110px}' +
        '.print-pf-label{font-size:10px;color:#888;text-transform:uppercase;display:block}' +
        '.print-pf-val{font-size:12px;font-weight:600;color:#333}' +
        '.print-bill-title{text-align:center;font-size:13px;font-weight:700;letter-spacing:2px;padding:8px;background:#f0faf8;border-bottom:2px solid #B2DFDB;color:#00695C;text-transform:uppercase}' +
        '.print-bill-table{width:100%;border-collapse:collapse;font-size:12px}' +
        '.print-bill-table thead th{background:#f0faf8;font-size:10.5px;font-weight:700;text-transform:uppercase;padding:7px 14px;border-bottom:2px solid #B2DFDB;text-align:left}' +
        '.print-bill-table tbody td,.print-bill-table tfoot td{padding:7px 14px;border-bottom:1px solid #eee}' +
        '.print-footer-note{font-size:11px;color:#666;padding:10px 18px;border-top:1px dashed #ccc}' +
        '.print-sig-row{display:flex;justify-content:space-between;padding:14px 18px}' +
        '.print-sig-line{border-bottom:1px solid #999;width:120px;margin-bottom:4px}' +
        '.print-sig-label{font-size:11px;color:#777;text-align:center}' +
        '.font-weight-bold{font-weight:700}.text-right{text-align:right}' +
        '@page{margin:10mm;size:A5}' +
        '</style></head><body>' + area.innerHTML +
        '<script>window.onload=function(){window.print();}<\/script></body></html>';
    var iframe = document.getElementById('print-iframe');
    iframe.onload = function(){
        setTimeout(function(){ try{ iframe.contentWindow.print(); }catch(e){} }, 300);
        iframe.onload = null;
    };
    iframe.srcdoc = html;
}

// ── Table filters ─────────────────────────────────
function filterAdmitted(){
    var q = (document.getElementById('patientSearch').value||'').toLowerCase();
    document.querySelectorAll('#admittedTable tbody tr.admitted-row').forEach(function(r){
        r.style.display = r.textContent.toLowerCase().includes(q)?'':'none';
    });
}
function filterPayTable(){
    var q = (document.getElementById('paySearch').value||'').toLowerCase();
    document.querySelectorAll('#payTable tbody tr.pay-row').forEach(function(r){
        r.style.display = r.textContent.toLowerCase().includes(q)?'':'none';
    });
}

function editReceipt(){
    document.getElementById('receipt-view').style.display  = 'none';
    document.getElementById('bill-form-card').style.display = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
}

// Init
document.addEventListener('DOMContentLoaded', function(){
    var el = document.getElementById('amt-less');
    if(el) el.readOnly = true; // start locked until dropdown chosen
    recalc();
});
</script>
@stop