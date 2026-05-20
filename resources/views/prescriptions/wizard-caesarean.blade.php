@extends('adminlte::page')

@section('content')
<div class="container" style="max-width: 900px;">

    {{-- Header Card --}}
    <div class="card mb-3">
        <div class="card-header">
            <strong>Pre-Assessment / Vitals (Pre-Consultation)</strong>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SEARCH (AJAX using SAME route prescriptions.preconassessment) --}}
            <div class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <input type="text" id="q" name="q" class="form-control"
                               placeholder="Search by Name / Patient Code / Mobile"
                               value="{{ $q ?? request('q') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="btnSearch" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
                <div class="small text-muted mt-1" id="searchHint" style="display:none;">Searching...</div>
            </div>

            {{-- RESULTS (scrollable + sticky header) --}}
            <div class="table-responsive mb-3" id="resultWrap"
                 style="{{ (isset($patients) && $patients->count() > 0) ? '' : 'display:none;' }}">
                <div style="max-height:150px; overflow:auto; border:1px solid #dee2e6;">
                    <table class="table table-sm table-bordered" style="margin-bottom:0;">
                        <thead class="table-light">
                            <tr>
                                <th style="position:sticky; top:0; background:#f8f9fa; z-index:2;">Patient Code</th>
                                <th style="position:sticky; top:0; background:#f8f9fa; z-index:2;">Name</th>
                                <th style="position:sticky; top:0; background:#f8f9fa; z-index:2;">Mobile</th>
                                <th style="width:90px; position:sticky; top:0; background:#f8f9fa; z-index:2;">Select</th>
                            </tr>
                        </thead>

                        <tbody id="resultBody">
                            {{-- Initial page load data (optional) --}}
                            @if(isset($patients) && $patients->count() > 0)
                                @foreach($patients as $p)
                                    <tr>
                                        <td>{{ $p->patientcode }}</td>
                                        <td>{{ $p->patientname }}</td>
                                        <td>{{ $p->mobile_no }}</td>
                                        <td>
                                            <a href="{{ route('prescriptions.preconassessment', ['patientcode' => $p->patientcode, 'q' => ($q ?? request('q'))]) }}"
                                               class="btn btn-sm btn-success">
                                                Select
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="alert alert-warning py-2" id="emptyMsg" style="display:none;">
                No patient found.
            </div>

            {{-- SAVE (POST) --}}
            <form method="POST" action="{{ route('prescriptions.preconassessment.save') }}">
                @csrf

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Patient Code <span style="color:red;font-size:20px;font-weight:bold">*</span></label>
                        <input type="number" id="patientcode" name="patientcode" class="form-control"
                               value="{{ old('patientcode', request('patientcode')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Weight (kg)</label>
                        <input type="text" name="weight" class="form-control" value="{{ old('weight') }}" placeholder="e.g. 62.5">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Height (cm)</label>
                        <input type="text" name="height" class="form-control" value="{{ old('height') }}" placeholder="e.g. 170">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Temperature (°C)</label>
                        <input type="text" name="temp" class="form-control" value="{{ old('temp') }}" placeholder="e.g. 36.8">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">BP (Systolic)</label>
                        <input type="number" name="bp_sys" class="form-control" value="{{ old('bp_sys') }}" placeholder="e.g. 120">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">BP (Diastolic)</label>
                        <input type="number" name="bp_dia" class="form-control" value="{{ old('bp_dia') }}" placeholder="e.g. 80">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pulse (bpm)</label>
                        <input type="number" name="pulse" class="form-control" value="{{ old('pulse') }}" placeholder="e.g. 78">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">SpO2 (%)</label>
                        <input type="number" name="spo2" class="form-control" value="{{ old('spo2') }}" placeholder="e.g. 98">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Respiration (rpm)</label>
                        <input type="number" name="rr" class="form-control" value="{{ old('rr') }}" placeholder="e.g. 16">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any remarks...">{{ old('notes') }}</textarea>
                    </div>

                </div>

                <hr>

                <button type="submit" class="btn btn-success">Save Pre-Assessment</button>
            </form>

        </div>
    </div>

</div>

{{-- AJAX script (no jQuery needed) --}}
<script>
(function () {
    function debounce(fn, delay) {
        let t = null;
        return function () {
            clearTimeout(t);
            t = setTimeout(fn, delay);
        };
    }

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (m) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
        });
    }

    const $q        = document.getElementById('q');
    const $btn      = document.getElementById('btnSearch');
    const $hint     = document.getElementById('searchHint');
    const $wrap     = document.getElementById('resultWrap');
    const $body     = document.getElementById('resultBody');
    const $emptyMsg = document.getElementById('emptyMsg');

    function show(el) { el.style.display = ''; }
    function hide(el) { el.style.display = 'none'; }

    async function doSearch() {
        const q = ($q.value || '').trim();

        if (!q) {
            hide($wrap);
            hide($emptyMsg);
            $body.innerHTML = '';
            return;
        }

        show($hint);
        hide($emptyMsg);

        try {
            const url = "{{ route('prescriptions.preconassessment') }}" + "?q=" + encodeURIComponent(q);

            const res = await fetch(url, {
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();
            const rows = Array.isArray(data.patients) ? data.patients : [];

            if (!data.ok || rows.length === 0) {
                $body.innerHTML = '';
                hide($wrap);
                show($emptyMsg);
                return;
            }

            let html = '';
            rows.forEach(function (p) {
                const patientcode = esc(p.patientcode);
                const patientname = esc(p.patientname);
                const mobile_no   = esc(p.mobile_no);

                // Keep your old behavior: clicking Select reloads page with patientcode
                const selectUrl = "{{ route('prescriptions.preconassessment') }}"
                    + "?patientcode=" + encodeURIComponent(patientcode)
                    + "&q=" + encodeURIComponent(q);

                html += `
                    <tr>
                        <td>${patientcode}</td>
                        <td>${patientname}</td>
                        <td>${mobile_no}</td>
                        <td>
                            <a href="${selectUrl}" class="btn btn-sm btn-success">Select</a>
                        </td>
                    </tr>
                `;
            });

            $body.innerHTML = html;
            show($wrap);
        } catch (e) {
            $body.innerHTML = '';
            hide($wrap);
            show($emptyMsg);
            $emptyMsg.innerText = "Search error. Please try again.";
        } finally {
            hide($hint);
        }
    }

    const auto = debounce(doSearch, 400);

    $q.addEventListener('input', auto);
    $btn.addEventListener('click', doSearch);

    // optional: if page loaded with q value, auto show results via ajax
    if (($q.value || '').trim()) {
        doSearch();
    }
})();
</script>
@endsection
