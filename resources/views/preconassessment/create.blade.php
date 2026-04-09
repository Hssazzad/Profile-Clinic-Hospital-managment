@extends('adminlte::page')

@section('title', 'Pre-Con Assessment')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-12">
            
            <div class="card card-outline card-primary shadow-lg border-0 mb-5">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-primary">
                            <i class="fas fa-stethoscope mr-2"></i>
                            Patient Vitals & Pre-Assessment
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-primary px-3 py-2 mr-2"><i class="far fa-calendar-alt mr-1"></i> {{ date('d M, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-light-gray">
                    {{-- Status Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                            <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                            <i class="icon fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- SEARCH SECTION --}}
                    <div class="search-section bg-white p-4 rounded shadow-sm border mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-9">
                                <label class="text-muted small font-weight-bold text-uppercase mb-2">
                                    <i class="fas fa-search mr-1"></i> Quick Patient Lookup
                                </label>
                                <div class="input-group input-group-lg border rounded-pill overflow-hidden bg-light">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent border-0 text-muted ml-2">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="q" name="q" class="form-control bg-transparent border-0 shadow-none"
                                           placeholder="Enter Name, Patient ID, or Mobile Number..."
                                           value="{{ $q ?? request('q') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="btnSearch" class="btn btn-primary btn-lg btn-block font-weight-bold shadow-sm rounded-pill">
                                    Search Records
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- SEARCH RESULTS TABLE --}}
                    <div class="table-responsive mb-4 shadow-sm rounded border bg-white" id="resultWrap"
                         style="{{ (isset($patients) && count($patients) > 0) ? '' : 'display:none;' }}">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 pl-4">ID Code</th>
                                    <th class="py-3">Patient Name</th>
                                    <th class="py-3">Contact No.</th>
                                    <th class="py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="resultBody">
                                @if(isset($patients))
                                    @foreach($patients as $p)
                                        <tr class="{{ request('patientcode') == $p->patientcode ? 'table-primary' : '' }}">
                                            <td class="pl-4 font-weight-bold text-primary">{{ $p->patientcode }}</td>
                                            <td class="font-weight-600">{{ $p->patientname }}</td>
                                            <td><i class="fas fa-phone-alt mr-2 text-muted small"></i>{{ $p->mobile_no }}</td>
                                            <td class="text-center">
                                                <a href="?patientcode={{ $p->patientcode }}&q={{ $q ?? request('q') }}"
                                                   class="btn btn-sm btn-outline-success px-4 rounded-pill">
                                                    Select <i class="fas fa-arrow-right ml-1 small"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        {{-- LEFT: DATA ENTRY FORM --}}
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                                        <span class="badge badge-pill badge-primary mr-2">1</span> Clinical Vitals Entry
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('prescriptions.preconassessment.save') }}" id="vitalsForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <div class="p-3 bg-light rounded border-left-primary d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <label class="text-muted small font-weight-bold text-uppercase d-block mb-0">Selected Patient</label>
                                                        <input type="text" id="patientcode" name="patientcode" 
                                                            class="form-control form-control-plaintext font-weight-bold text-primary h5 mb-0 p-0"
                                                            value="{{ old('patientcode', request('patientcode')) }}" required readonly 
                                                            placeholder="?? No patient selected">
                                                    </div>
                                                    @if(request('patientcode'))
                                                        <i class="fas fa-user-check text-success fa-2x opacity-50"></i>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold small text-muted text-uppercase">Body Weight</label>
                                                <div class="input-group input-group-custom">
                                                    <input type="number" step="0.1" name="weight" id="weight" class="form-control vital-input" value="{{ old('weight') }}" placeholder="0.0">
                                                    <div class="input-group-append"><span class="input-group-text bg-white border-left-0">kg</span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="font-weight-bold small text-muted text-uppercase">Height</label>
                                                <div class="input-group input-group-custom">
                                                    <input type="number" step="0.1" name="height" id="height" class="form-control vital-input" value="{{ old('height') }}" placeholder="0.0">
                                                    <div class="input-group-append"><span class="input-group-text bg-white border-left-0">cm</span></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-danger text-uppercase">BP (Systolic)</label>
                                                <input type="number" name="bp_sys" id="bp_sys" class="form-control vital-input border-danger-soft font-weight-bold" value="{{ old('bp_sys') }}" placeholder="120">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-danger text-uppercase">BP (Diastolic)</label>
                                                <input type="number" name="bp_dia" id="bp_dia" class="form-control vital-input border-danger-soft font-weight-bold" value="{{ old('bp_dia') }}" placeholder="80">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-info text-uppercase">SpO2 (%)</label>
                                                <input type="number" name="spo2" id="spo2" class="form-control vital-input border-info-soft font-weight-bold" value="{{ old('spo2') }}" placeholder="98">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-muted text-uppercase">Temp (°C)</label>
                                                <input type="number" step="0.1" name="temp" id="temp" class="form-control vital-input" value="{{ old('temp') }}" placeholder="37.0">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-muted text-uppercase">Pulse (BPM)</label>
                                                <input type="number" name="pulse" id="pulse" class="form-control vital-input" value="{{ old('pulse') }}" placeholder="72">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="font-weight-bold small text-muted text-uppercase">Resp (RPM)</label>
                                                <input type="number" name="rr" id="rr" class="form-control vital-input" value="{{ old('rr') }}" placeholder="16">
                                            </div>

                                            <div class="col-12 form-group mt-2">
                                                <label class="font-weight-bold small text-muted text-uppercase">Clinical Observations & Complaints</label>
                                                <textarea name="notes" class="form-control border-light shadow-sm" rows="3" placeholder="Describe any visible symptoms or patient complaints...">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>
                                        <hr class="my-4">
                                        <button type="submit" class="btn btn-success btn-lg px-5 shadow rounded-pill">
                                            <i class="fas fa-save mr-2"></i> Confirm & Save Record
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: HISTORY & INSIGHTS --}}
                        <div class="col-lg-5 mt-4 mt-lg-0">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                                        <span class="badge badge-pill badge-info mr-2">2</span> Assessment Insight
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($selectedPatient))
                                    <div class="patient-mini-profile d-flex align-items-center p-3 mb-4 rounded bg-dark shadow">
                                        <img class="rounded-circle border border-2 border-white" width="55" height="55" src="https://ui-avatars.com/api/?name={{ urlencode($selectedPatient->patientname) }}">
                                        <div class="ml-3">
                                            <p class="mb-0 text-white font-weight-bold h6">{{ $selectedPatient->patientname }}</p>
                                            <span class="badge badge-info small">PID: {{ $selectedPatient->patientcode }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <div id="insightPanel" class="rounded border p-3 mb-3 bg-white border-left-info shadow-sm">
                                        <div id="bmiSection" class="text-center mb-4">
                                            <div id="bmiBadge" class="p-4 rounded-lg bg-secondary text-white transition-all shadow-sm">
                                                <span class="text-uppercase small font-weight-bold opacity-75">Calculated BMI</span>
                                                <h1 id="bmiVal" class="font-weight-bold mb-0">--</h1>
                                                <span id="bmiStat" class="small font-italic">Enter Height & Weight</span>
                                            </div>
                                        </div>

                                        <div id="summaryReport" class="p-3 rounded bg-light border-dashed">
                                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                                <span class="text-muted small font-weight-bold">BLOOD PRESSURE:</span>
                                                <span id="repBP" class="font-weight-bold text-dark">- / -</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                                <span class="text-muted small font-weight-bold">OXYGEN SATURATION:</span>
                                                <span id="repSpO2" class="font-weight-bold text-dark">- %</span>
                                            </div>
                                            <div id="repRisk" class="badge badge-secondary w-100 py-2 text-uppercase" style="letter-spacing: 1px;">Awaiting Data</div>
                                        </div>
                                    </div>

                                    {{-- HISTORY BOX --}}
                                    <div class="history-card mt-4 border rounded overflow-hidden shadow-sm bg-white">
                                        <div class="bg-light p-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                            <span class="small font-weight-bold text-uppercase text-muted">
                                                <i class="fas fa-history mr-1"></i> Recent Assessment Logs
                                                <span id="recordCount" class="badge badge-secondary ml-2">0</span>
                                            </span>
                                        </div>
                                        <div class="history-content" style="max-height: 600px; overflow-y: auto;">
                                            <div id="historyList">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-folder-open text-muted opacity-25 fa-3x mb-3"></i>
                                                    <p class="small text-muted mt-2">Select a patient to view assessment history</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-gray { background-color: #f8fafc; }
    .bg-info-light { background-color: #eef9fd; }
    .border-left-primary { border-left: 5px solid #007bff !important; }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
    .vital-input { border-radius: 8px; border: 1px solid #dae1e7; padding: 1.2rem 0.75rem; transition: all 0.2s; font-size: 1.1rem; }
    .vital-input:focus { border-color: #007bff; box-shadow: 0 4px 6px rgba(0,123,255,.1); background-color: #fff; }
    .border-danger-soft { border-color: #f5c6cb; background-color: #fffafa; }
    .border-info-soft { border-color: #bee5eb; background-color: #faffff; }
    .border-dashed { border: 2px dashed #e2e8f0 !important; }
    .history-item:hover { background-color: #f8fafc; cursor: default; }
    .transition-all { transition: all 0.3s ease; }
    .font-weight-600 { font-weight: 600; }
    
    .bg-risk-high { background-color: #e53e3e !important; }
    .bg-risk-mod { background-color: #dd6b20 !important; }
    .bg-risk-normal { background-color: #38a169 !important; }

    .input-group-custom .input-group-text { border-radius: 0 8px 8px 0; border: 1px solid #dae1e7; color: #94a3b8; font-weight: bold; }
    .table thead th { border-top: 0; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; color: #64748b; background: #f8fafc; }
</style>

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
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open text-muted opacity-25 fa-3x mb-3"></i>
                            <p class="small text-muted mt-2">No assessment records found for this patient</p>
                        </div>
                    `);
                    $('#recordCount').text('0');
                }
            },
            error: function(error) {
                console.error('Error loading history:', error);
                $('#historyList').html(`
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle mr-2"></i> Error loading history
                    </div>
                `);
            }
        });
    }

    function renderHistoryTable(records) {
        let html = `
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="font-size: 11px;">Date & Time</th>
                            <th style="font-size: 11px;">Weight</th>
                            <th style="font-size: 11px;">Height</th>
                            <th style="font-size: 11px;">BMI</th>
                            <th style="font-size: 11px;">BP</th>
                            <th style="font-size: 11px;">Pulse</th>
                            <th style="font-size: 11px;">SpO2</th>
                            <th style="font-size: 11px;">Temp</th>
                            <th style="font-size: 11px;">RR</th>
                            <th style="font-size: 11px;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        records.forEach(function(record, index) {
            // Alternate row colors
            let rowClass = index % 2 === 0 ? 'bg-white' : 'bg-light';
            
            html += `
                <tr class="${rowClass}">
                    <td style="font-size: 12px;" class="font-weight-bold">
                        <i class="fas fa-calendar-check mr-1 text-primary"></i> ${record.datetime}
                    </td>
                    <td style="font-size: 12px;">${record.weight} <span class="text-muted">kg</span></td>
                    <td style="font-size: 12px;">${record.height} <span class="text-muted">cm</span></td>
                    <td style="font-size: 12px;" class="font-weight-bold text-info">${record.bmi}</td>
                    <td style="font-size: 12px;" class="font-weight-bold text-danger">${record.bp} <span class="text-muted">mmHg</span></td>
                    <td style="font-size: 12px;">${record.pulse} <span class="text-muted">bpm</span></td>
                    <td style="font-size: 12px;" class="font-weight-bold text-info">${record.spo2}%</td>
                    <td style="font-size: 12px;">${record.temp}°C</td>
                    <td style="font-size: 12px;">${record.rr}</td>
                    <td style="font-size: 11px;" class="text-muted">
                        <small>${record.notes === 'No notes' ? '—' : record.notes.substring(0, 20) + '...'}</small>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        $('#historyList').html(html);
    }

    // ----------------------------------------------------??-----------
    // LIVE INSIGHT CALCULATION
    // ----------------------------------------------------------------
    function updateInsights() {
        let weight = parseFloat($('#weight').val());
        let height = parseFloat($('#height').val()) / 100;
        let sys = parseInt($('#bp_sys').val());
        let dia = parseInt($('#bp_dia').val());
        let spo2 = parseInt($('#spo2').val());

        // BMI Calculation
        if (weight > 0 && height > 0) {
            let bmi = (weight / (height * height)).toFixed(1);
            $('#bmiVal').text(bmi);
            
            let status = "Normal";
            let colorClass = "bg-risk-normal";

            if (bmi < 18.5) { status = "Underweight"; colorClass = "bg-info"; }
            else if (bmi >= 25 && bmi < 30) { status = "Overweight"; colorClass = "bg-risk-mod"; }
            else if (bmi >= 30) { status = "Obese"; colorClass = "bg-risk-high"; }

            $('#bmiStat').text(status);
            $('#bmiBadge').removeClass('bg-secondary bg-info bg-risk-normal bg-risk-mod bg-risk-high').addClass(colorClass);
        }

        // BP Update
        if (sys || dia) {
            $('#repBP').text(`${sys || '--'} / ${dia || '--'} mmHg`);
        }

        // SpO2 Update
        if (spo2) {
            $('#repSpO2').text(`${spo2}%`);
            if (spo2 < 94) {
                $('#repRisk').text('Review Required').removeClass('badge-secondary').addClass('badge-danger');
            } else {
                $('#repRisk').text('Stable').removeClass('badge-secondary badge-danger').addClass('badge-success');
            }
        }
    }

    $('.vital-input').on('input', updateInsights);

    // ----------------------------------------------------------------
    // REFRESH HISTORY AFTER FORM SUBMISSION
    // ----------------------------------------------------------------
    $('#vitalsForm').on('submit', function(e) {
        // ???? submitted ???, ??? reload ???, ????? history automatically load ???
    });
});
</script>
@endpush

@endsection