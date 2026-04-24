@extends('adminlte::page')

@section('title', 'Pre-Con Assessment')

@section('content')

@if ($errors->any())
    <div style="background:#fdecea;border:1px solid #ef9a9a;border-left:4px solid #c62828;padding:8px 12px;margin-bottom:10px;font-size:12px;color:#b71c1c;">
        <ul style="margin:0;padding-left:16px">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
/* ===== GOVT HOSPITAL STYLE — Professor Clinic ===== */
.pc-wrap { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
.pc-topbar { background: #1a4f8a; color: #fff; padding: 7px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #c8a000; margin-bottom: 10px; }
.pc-topbar-logo { width: 40px; height: 40px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #1a4f8a; font-size: 10px; text-align: center; line-height: 1.2; flex-shrink: 0; }
.pc-topbar h1 { font-size: 14px; font-weight: 700; margin: 0 0 2px; }
.pc-topbar small { font-size: 10px; color: #aac6e8; }
.pc-date-badge { background: #c8a000; color: #1a1a00; padding: 3px 12px; font-size: 11px; font-weight: 700; border-radius: 2px; white-space: nowrap; }

.pc-card { background: #fff; border: 1px solid #b0bec5; border-top: 3px solid #1a4f8a; margin-bottom: 10px; }
.pc-card-head { background: #dce8f5; border-bottom: 1px solid #b0bec5; padding: 5px 10px; font-size: 11px; font-weight: 700; color: #1a4f8a; text-transform: uppercase; letter-spacing: 0.5px; }
.pc-card-body { padding: 10px 12px; }

.pc-alert-success { background: #e8f5e9; border: 1px solid #a5d6a7; border-left: 4px solid #388e3c; padding: 7px 12px; font-size: 12px; color: #1b5e20; margin-bottom: 10px; }
.pc-alert-danger  { background: #fdecea; border: 1px solid #ef9a9a; border-left: 4px solid #c62828; padding: 7px 12px; font-size: 12px; color: #b71c1c; margin-bottom: 10px; }

/* Search */
.pc-search-row { display: flex; gap: 8px; align-items: stretch; }
.pc-search-row input { flex: 1; border: 1px solid #999; padding: 6px 9px; font-size: 13px; border-radius: 2px; }
.pc-search-row button { background: #1a4f8a; color: #fff; border: none; padding: 0 20px; font-size: 12px; font-weight: 700; cursor: pointer; border-radius: 2px; white-space: nowrap; }
.pc-search-row button:hover { background: #153d70; }

/* Table */
.pc-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pc-table th { background: #1a4f8a; color: #fff; padding: 5px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
.pc-table td { padding: 4px 8px; border-bottom: 1px solid #e0e0e0; }
.pc-table tbody tr:nth-child(even) td { background: #f2f7ff; }
.pc-table tbody tr.pc-row-selected td { background: #dce8f5 !important; }
.btn-pc-select { background: #2c6aad; color: #fff; border: none; padding: 2px 12px; font-size: 11px; border-radius: 2px; cursor: pointer; }
.btn-pc-select:hover { background: #1a4f8a; }

/* Two-col layout */
.pc-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 10px; }
@media (max-width: 900px) { .pc-layout { grid-template-columns: 1fr; } }

/* Selected patient */
.pc-selected-box { background: #f5f9e8; border: 1px solid #8bc34a; border-left: 4px solid #558b2f; padding: 6px 10px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
.pc-selected-box .pc-pid-label { font-size: 10px; font-weight: 700; color: #558b2f; text-transform: uppercase; margin-bottom: 1px; }
.pc-check { color: #558b2f; font-size: 20px; margin-left: auto; }

/* Form groups */
.pc-fg { margin-bottom: 8px; }
.pc-fg label { display: block; font-size: 10px; font-weight: 700; color: #444; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 0.3px; }
.pc-fg input, .pc-fg textarea, .pc-fg select { width: 100%; border: 1px solid #999; padding: 5px 7px; font-size: 13px; border-radius: 2px; background: #fff; }
.pc-fg input:focus, .pc-fg textarea:focus { outline: none; border-color: #1a4f8a; background: #f5f9ff; }
.pc-fg textarea { resize: vertical; }
.pc-unit-input { display: flex; border: 1px solid #999; border-radius: 2px; overflow: hidden; }
.pc-unit-input input { flex: 1; border: none; padding: 5px 7px; font-size: 13px; background: #fff; outline: none; }
.pc-unit-input input:focus { background: #f5f9ff; }
.pc-unit-input span { background: #dce8f5; border-left: 1px solid #999; padding: 5px 9px; font-size: 11px; font-weight: 700; color: #1a4f8a; white-space: nowrap; }

.pc-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.pc-3col { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
.pc-label-red { color: #b71c1c !important; }
.pc-label-blue { color: #0d47a1 !important; }

.btn-pc-submit { background: #558b2f; color: #fff; border: none; padding: 8px 28px; font-size: 13px; font-weight: 700; cursor: pointer; border-radius: 2px; margin-top: 6px; }
.btn-pc-submit:hover { background: #3d6b21; }

hr.pc-hr { border: none; border-top: 1px solid #d0d8e0; margin: 10px 0; }

/* History */
.pc-hist-head { background: #dce8f5; border: 1px solid #b0bec5; border-bottom: none; padding: 5px 10px; font-size: 11px; font-weight: 700; color: #1a4f8a; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center; }
.pc-hist-head .pc-count { background: #1a4f8a; color: #fff; border-radius: 2px; padding: 1px 8px; font-size: 11px; }
.pc-hist-wrap { border: 1px solid #b0bec5; overflow-x: auto; max-height: 420px; overflow-y: auto; }
.pc-hist-tbl { width: 100%; border-collapse: collapse; font-size: 11px; }
.pc-hist-tbl th { background: #1a4f8a; color: #fff; padding: 4px 6px; text-align: left; font-size: 10px; text-transform: uppercase; position: sticky; top: 0; z-index: 10; }
.pc-hist-tbl td { padding: 3px 6px; border-bottom: 1px solid #e0e0e0; color: #333; }
.pc-hist-tbl tbody tr:nth-child(even) td { background: #f2f7ff; }

/* Patient mini profile */
.pc-patient-profile { background: #1a3a5c; color: #fff; padding: 8px 12px; margin-bottom: 0; display: flex; align-items: center; gap: 10px; border-radius: 2px; }
.pc-patient-profile .pc-avatar { width: 42px; height: 42px; border-radius: 50%; border: 2px solid #aac6e8; object-fit: cover; flex-shrink: 0; }
.pc-patient-profile p { margin: 0; font-weight: 700; font-size: 13px; }
.pc-patient-profile small { font-size: 10px; color: #aac6e8; }

.pc-empty-state { text-align: center; padding: 28px 16px; color: #888; font-size: 12px; }

/* Footer */
.pc-footer { background: #1a4f8a; color: #aac6e8; text-align: center; padding: 6px; font-size: 10px; margin-top: 12px; }
</style>

<div class="pc-wrap">

    {{-- TOP BAR --}}
    <div class="pc-topbar">
        <div style="display:flex;align-items:center;gap:10px">
            <div class="pc-topbar-logo">PROF<br>CLINIC</div>
            <div>
                <h1>Professor Clinic — Nursing Management System</h1>
                <small>Pre-Consultation Assessment &amp; Vitals Entry</small>
            </div>
        </div>
        <div class="pc-date-badge">{{ date('d M, Y') }}</div>
    </div>

    {{-- SESSION ALERTS --}}
    @if(session('success'))
        <div class="pc-alert-success">&#10003; &nbsp;{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="pc-alert-danger">&#9888; &nbsp;{{ session('error') }}</div>
    @endif

    {{-- SEARCH SECTION --}}
    <div class="pc-card">
        <div class="pc-card-head">&#9675; Patient Quick Search</div>
        <div class="pc-card-body">
            <div class="pc-search-row">
                <input type="text" id="q" name="q"
                       placeholder="Enter Name, Patient ID or Mobile Number..."
                       value="{{ $q ?? request('q') }}">
                <button type="button" id="btnSearch">Search Records</button>
            </div>

            {{-- RESULTS TABLE --}}
            <div id="resultWrap" style="{{ (isset($patients) && count($patients) > 0) ? '' : 'display:none;' }} margin-top:10px; overflow-x:auto;">
                <table class="pc-table" style="margin-top:8px">
                    <thead>
                        <tr>
                            <th>ID Code</th>
                            <th>Patient Name</th>
                            <th>Contact No.</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="resultBody">
                        @if(isset($patients))
                            @foreach($patients as $p)
                                <tr class="{{ request('patientcode') == $p->patientcode ? 'pc-row-selected' : '' }}">
                                    <td style="font-weight:700;color:#1a4f8a">{{ $p->patientcode }}</td>
                                    <td>{{ $p->patientname }}</td>
                                    <td>{{ $p->mobile_no }}</td>
                                    <td>
                                        <a href="?patientcode={{ $p->patientcode }}&q={{ $q ?? request('q') }}"
                                           class="btn-pc-select">Select &rarr;</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="pc-layout">

        {{-- LEFT: VITALS ENTRY FORM --}}
        <div>
            <div class="pc-card">
                <div class="pc-card-head">&#9312; Clinical Vitals Entry</div>
                <div class="pc-card-body">
                    <form method="POST" action="{{ route('prescriptions.preconassessment.save') }}" id="vitalsForm">
                        @csrf

                        {{-- Selected Patient --}}
                        <div class="pc-selected-box">
                            <div>
                                <div class="pc-pid-label">Selected Patient</div>
                                <input type="text" id="patientcode" name="patientcode"
                                    style="border:none;background:transparent;font-size:15px;font-weight:700;color:#1a4f8a;padding:0;width:100%"
                                    value="{{ old('patientcode', request('patientcode')) }}"
                                    required readonly
                                    placeholder="— No patient selected —">
                            </div>
                            @if(request('patientcode'))
                                <span class="pc-check">&#10004;</span>
                            @endif
                        </div>

                        {{-- Weight & Height --}}
                        <div class="pc-2col">
                            <div class="pc-fg">
                                <label>Body Weight</label>
                                <div class="pc-unit-input">
                                    <input type="number" step="0.1" min="0" name="weight" id="weight"
                                           class="vital-input" value="{{ old('weight') }}" placeholder="0.0">
                                    <span>kg</span>
                                </div>
                            </div>
                            <div class="pc-fg">
                                <label>Height</label>
                                <div class="pc-unit-input">
                                    <input type="number" step="0.1" min="0" name="height" id="height"
                                           class="vital-input" value="{{ old('height') }}" placeholder="0.0">
                                    <span>cm</span>
                                </div>
                            </div>
                        </div>

                        {{-- BP + SpO2 --}}
                        <div class="pc-3col">
                            <div class="pc-fg">
                                <label class="pc-label-red">BP (Systolic)</label>
                                <input type="number" min="0" name="bp_sys" id="bp_sys"
                                       class="vital-input" value="{{ old('bp_sys') }}" placeholder="120">
                            </div>
                            <div class="pc-fg">
                                <label class="pc-label-red">BP (Diastolic)</label>
                                <input type="number" min="0" name="bp_dia" id="bp_dia"
                                       class="vital-input" value="{{ old('bp_dia') }}" placeholder="80">
                            </div>
                            <div class="pc-fg">
                                <label class="pc-label-blue">SpO2 (%)</label>
                                <input type="number" min="0" max="100" name="spo2" id="spo2"
                                       class="vital-input" value="{{ old('spo2') }}" placeholder="98">
                            </div>
                        </div>

                        {{-- Temp / Pulse / RR --}}
                        <div class="pc-3col">
                            <div class="pc-fg">
                                <label>Temp (°F)</label>
                                <div class="pc-unit-input">
                                    <input type="number" step="0.1" min="0" name="temp" id="temp"
                                           class="vital-input" value="{{ old('temp') }}" placeholder="98.6">
                                    <span>°F</span>
                                </div>
                            </div>
                            <div class="pc-fg">
                                <label>Pulse (BPM)</label>
                                <input type="number" min="0" name="pulse" id="pulse"
                                       class="vital-input" value="{{ old('pulse') }}" placeholder="72">
                            </div>
                            <div class="pc-fg">
                                <label>Resp (RPM)</label>
                                <input type="number" min="0" name="rr" id="rr"
                                       class="vital-input" value="{{ old('rr') }}" placeholder="16">
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="pc-fg">
                            <label>Clinical Observations &amp; Complaints</label>
                            <textarea name="notes" rows="3"
                                      placeholder="Describe any visible symptoms or patient complaints...">{{ old('notes') }}</textarea>
                        </div>

                        <hr class="pc-hr">

                        <button type="submit" class="btn-pc-submit">
                            &#128190; Confirm &amp; Save Record
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: PATIENT PROFILE + HISTORY --}}
        <div>
            @if(isset($selectedPatient))
            <div class="pc-card" style="margin-bottom:10px">
                <div class="pc-card-head">&#9313; Selected Patient</div>
                <div class="pc-card-body" style="padding:8px 12px">
                    <div class="pc-patient-profile">
                        <img class="pc-avatar"
                             src="https://ui-avatars.com/api/?name={{ urlencode($selectedPatient->patientname) }}&size=80"
                             alt="avatar">
                        <div>
                            <p>{{ $selectedPatient->patientname }}</p>
                            <small>PID: {{ $selectedPatient->patientcode }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- HISTORY --}}
            <div class="pc-hist-head">
                <span>&#9719; Recent Assessment Logs</span>
                <span id="recordCount" class="pc-count">0</span>
            </div>
            <div class="pc-hist-wrap">
                <div id="historyList">
                    <div class="pc-empty-state">
                        Select a patient to view assessment history
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="pc-footer">Professor Clinic — Nursing Management System &nbsp;|&nbsp; Pre-Consultation Assessment Module</div>
</div>

@push('js')
<script>
$(document).ready(function() {
    let currentPatientCode = "{{ request('patientcode') }}";

    // ----------------------------------------------------------------
    // SEARCH LOGIC
    // ----------------------------------------------------------------
    $('#btnSearch').on('click', function() {
        let q = $('#q').val();
        if(q.length < 2) return alert('Please enter at least 2 characters');
        window.location.href = "?q=" + encodeURIComponent(q);
    });

    $('#q').keypress(function(e) {
        if(e.which == 13) $('#btnSearch').click();
    });

    // ----------------------------------------------------------------
    // BLOCK NEGATIVE INPUT
    // keydown: block '-' and 'e/E' (scientific notation) at source
    // input:   clamp if somehow a negative value slips through (paste/scroll)
    // ----------------------------------------------------------------
    $('.vital-input').on('keydown', function(e) {
        if (e.key === '-' || e.key === 'e' || e.key === 'E') {
            e.preventDefault();
        }
    });

    $('.vital-input').on('input', function() {
        var val = parseFloat($(this).val());
        if (!isNaN(val) && val < 0) {
            $(this).val('');
        }
    });

    // ----------------------------------------------------------------
    // LOAD HISTORY VIA AJAX WHEN PATIENT IS SELECTED
    // ----------------------------------------------------------------
    if(currentPatientCode) {
        loadAssessmentHistory(currentPatientCode);
    }

    function loadAssessmentHistory(patientcode) {
        $.ajax({
            url: `/preconassessment/history/${patientcode}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success && response.data.length > 0) {
                    renderHistoryTable(response.data);
                    $('#recordCount').text(response.total);
                } else {
                    $('#historyList').html(`
                        <div class="pc-empty-state">
                            No assessment records found for this patient
                        </div>
                    `);
                    $('#recordCount').text('0');
                }
            },
            error: function(error) {
                console.error('Error loading history:', error);
                $('#historyList').html(`
                    <div style="padding:10px;font-size:12px;color:#b71c1c;background:#fdecea;border-left:4px solid #c62828;">
                        &#9888; Error loading assessment history
                    </div>
                `);
            }
        });
    }

    function renderHistoryTable(records) {
        let html = `
            <table class="pc-hist-tbl">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>Weight</th>
                        <th>Height</th>
                        <th>BMI</th>
                        <th>BP</th>
                        <th>Pulse</th>
                        <th>SpO2</th>
                        <th>Temp</th>
                        <th>RR</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
        `;

        records.forEach(function(record, index) {
            html += `
                <tr>
                    <td style="font-weight:700;color:#1a4f8a;white-space:nowrap">${record.datetime}</td>
                    <td>${record.weight} <span style="color:#999;font-size:10px">kg</span></td>
                    <td>${record.height} <span style="color:#999;font-size:10px">cm</span></td>
                    <td style="font-weight:700;color:#0d47a1">${record.bmi}</td>
                    <td style="font-weight:700;color:#b71c1c">${record.bp}</td>
                    <td>${record.pulse}</td>
                    <td style="font-weight:700;color:#0d47a1">${record.spo2}%</td>
                    <td>${record.temp}&deg;F</td>
                    <td>${record.rr}</td>
                    <td style="color:#888">${record.notes === 'No notes' ? '&mdash;' : record.notes.substring(0, 20) + '...'}</td>
                </tr>
            `;
        });

        html += `</tbody></table>`;
        $('#historyList').html(html);
    }

    // ----------------------------------------------------------------
    // REFRESH HISTORY AFTER FORM SUBMISSION
    // ----------------------------------------------------------------
    $('#vitalsForm').on('submit', function(e) {
        // submitted hoile reload hobe, tarpor history automatically load hobe
    });
});
</script>
@endpush

@endsection