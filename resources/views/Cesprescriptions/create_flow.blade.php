@extends('adminlte::page')

@section('title', 'New Prescription')

@section('content_header')
    <h1 class="text-primary">Create Prescription</h1>
@stop

@section('content')
<div class="container mt-3">

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Fix the following:</strong>
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('prescriptions.store') }}" method="post" id="rxForm">
                @csrf

                {{-- ====== Patient search ====== --}}
                <div class="form-row-3 mb-3">
                    <div class="label-col">Search Patient</div>
                    <div class="colon-col d-none d-md-block">:</div>
                    <div class="input-col">
                        <div class="form-row">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <input type="text" id="patientQuery" class="form-control" placeholder="Type name / code / mobile …">
                            </div>
                            <div class="col-12 col-md-6">
                                <select id="patientSelect" class="form-control">
                                    <option value="">-- Select patient from results --</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                        <small class="text-muted d-block mt-1" id="patientInfo"></small>
                    </div>
                </div>

                {{-- ========================= 4 REGIONS (guided) ========================= --}}
                <div class="row">
                    {{-- 1) Investigations (append rows) --}}
                    <div class="col-12 col-lg-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                  <h5 class="mb-0 d-inline">Investigations</h5>
                                  <span class="badge badge-secondary ml-2" id="step_investigation_badge">Pending</span>
                                </div>
                                <button type="button" id="btnInvestigationOpen" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#investigationModal" disabled>
                                  Add Investigation(s)
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="investTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Investigation</th>
                                            <th>Notes</th>
                                            <th style="width:60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 2) Diagnosis (merge selections) --}}
                    <div class="col-12 col-lg-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                  <h5 class="mb-0 d-inline">Diagnosis</h5>
                                  <span class="badge badge-secondary ml-2" id="step_diagnosis_badge">Pending</span>
                                </div>
                                <button type="button" id="btnDiagnosisOpen" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#diagnosisModal" disabled>
                                  Select / Add
                                </button>
                            </div>
                            <div id="diagnosis_display" class="p-2 bg-light rounded">— Not selected —</div>
                            <div id="diagnosis_hidden_box"></div>
                        </div>
                    </div>

                    {{-- 3) Medicines (append rows) --}}
                    <div class="col-12 col-lg-12 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                  <h5 class="mb-0 d-inline">Medicines</h5>
                                  <span class="badge badge-secondary ml-2" id="step_medicine_badge">Pending</span>
                                </div>
                                <button type="button" id="btnMedicineOpen" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#medicineModal" disabled>
                                  Add Medicine(s)
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="medTable">
                                    <thead class="thead-light">
                                        <tr class="text-muted">
                                            <th>Medicine</th>
                                            <th>Strength</th>
                                            <th>Dose</th>
                                            <th>Route</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th>Timing</th>
                                            <th style="width:60px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- (Optional) Chief Complaint (kept available any time) --}}
                    <div class="col-12 col-lg-12 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="mb-0">Complain</h5>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#complainModal">Select / Add</button>
                            </div>
                            <div id="chief_complaint_display" class="p-2 bg-light rounded">— Not selected —</div>
                            <div id="chief_complaint_hidden_box"></div>
                        </div>
                    </div>
                </div>
                {{-- ======================= /4 REGIONS ======================= --}}

                {{-- Footer fields --}}
                <div class="form-row-3 mb-3 mt-3">
                    <div class="label-col">Prescribed On</div>
                    <div class="colon-col d-none d-md-block">:</div>
                    <div class="input-col">
                        <input type="date" name="prescribed_on" class="form-control" value="{{ old('prescribed_on', now()->toDateString()) }}">
                    </div>
                </div>
                <div class="form-row-3 mb-3">
                    <div class="label-col">Doctor Name</div>
                    <div class="colon-col d-none d-md-block">:</div>
                    <div class="input-col">
                        <input type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
                    </div>
                </div>
                <div class="form-row-3 mb-3">
                    <div class="label-col">Doctor Reg. No</div>
                    <div class="colon-col d-none d-md-block">:</div>
                    <div class="input-col">
                        <input type="text" name="doctor_reg_no" class="form-control" value="{{ old('doctor_reg_no') }}">
                    </div>
                </div>

                <div class="mt-2 d-flex flex-wrap">
                    <button id="btnSaveRx" class="btn btn-primary mr-2 mb-2" disabled>Save Prescription</button>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== MODALS ==================== --}}

{{-- Complain Modal --}}
<div class="modal fade" id="complainModal" tabindex="-1" role="dialog" aria-labelledby="complainModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="complainModalLabel">Select / Add Complain(s)</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <label>Chief Complaint(s)</label>
            <select id="complain_select" class="form-control" multiple size="6" data-index-route="{{ route('complaints.index') }}"></select>
            <div class="text-muted my-2 text-center">or</div>
            <label>New Complain</label>
            <div class="input-group">
                <input type="text" id="complain_new" class="form-control" placeholder="Type new complain">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="complain_add_btn" data-store-route="{{ route('complaints.store') }}">+ Add</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="complain_use_btn" class="btn btn-primary">Use Selected</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Diagnosis Modal --}}
<div class="modal fade" id="diagnosisModal" tabindex="-1" role="dialog" aria-labelledby="diagnosisModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="diagnosisModalLabel">Select / Add Diagnosis</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <label>Diagnosis</label>
            <select id="diagnosis_select" class="form-control" multiple size="6" data-index-route="{{ route('diagnoses.index') }}"></select>
            <div class="text-muted my-2 text-center">or</div>
            <label>New Diagnosis</label>
            <div class="input-group">
                <input type="text" id="diagnosis_new" class="form-control" placeholder="Type new diagnosis">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="diagnosis_add_btn" data-store-route="{{ route('diagnoses.store') }}">+ Add</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="diagnosis_use_btn" class="btn btn-primary">Use Selected</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Investigation Modal --}}
<div class="modal fade" id="investigationModal" tabindex="-1" role="dialog" aria-labelledby="investigationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="investigationModalLabel">Add Investigation(s)</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <label>Investigation</label>
            <select id="investigation_select" class="form-control" multiple size="6" data-index-route="{{ route('investigations.index') }}"></select>
            <div class="text-muted my-2 text-center">or</div>
            <label>New Investigation</label>
            <div class="input-group mb-3">
                <input type="text" id="investigation_new" class="form-control" placeholder="Type new investigation">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="investigation_add_btn" data-store-route="{{ route('investigations.store') }}">+ Add</button>
                </div>
            </div>
            <label>Notes (optional for all selected)</label>
            <input type="text" id="investigation_note" class="form-control" placeholder="Notes (optional)">
        </div>
        <div class="modal-footer">
          <button type="button" id="investigation_use_btn" class="btn btn-primary">Add to List</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Medicine Modal (MEDICINE OPTIONS PRE-RENDERED FROM SERVER) --}}
<div class="modal fade" id="medicineModal" tabindex="-1" role="dialog" aria-labelledby="medicineModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="medicineModalLabel">Add Medicine(s)</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body">
            <label for="medicine_select">Medicine</label>
            <select id="medicine_select" class="form-control">
                <option value="">-- Select Medicine --</option>
                @foreach($medicines as $m)
                  <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>

            <div class="text-muted my-2 text-center">or</div>

            <label for="medicine_new">New Medicine</label>
            <div class="input-group mb-3">
                <input type="text" id="medicine_new" class="form-control" placeholder="Type new medicine">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="medicine_add_btn" data-store-route="{{ route('medicines.store') }}">+ Add</button>
                </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-2"><label>Strength</label><input type="text" class="form-control" id="med_strength"></div>
              <div class="col-md-4 mb-2"><label>Dose</label><input type="text" class="form-control" id="med_dose"></div>
              <div class="col-md-4 mb-2"><label>Route</label><input type="text" class="form-control" id="med_route"></div>
              <div class="col-md-4 mb-2"><label>Frequency</label><input type="text" class="form-control" id="med_frequency"></div>
              <div class="col-md-4 mb-2"><label>Duration</label><input type="text" class="form-control" id="med_duration"></div>
              <div class="col-md-4 mb-2"><label>Timing</label><input type="text" class="form-control" id="med_timing"></div>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="medicine_use_btn" class="btn btn-primary">Add to List</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('css')
<style>
.form-row-3{display:flex;flex-wrap:wrap;align-items:center}
.form-row-3 .label-col{flex:0 0 100%;max-width:100%;font-weight:600;margin-bottom:.25rem}
.form-row-3 .colon-col{flex:0 0 10px;max-width:10px;text-align:center}
.form-row-3 .input-col{flex:0 0 100%;max-width:100%}
@media(min-width:768px){.form-row-3 .label-col{flex-basis:220px;max-width:220px;margin-bottom:0}.form-row-3 .input-col{flex:1 1 auto;max-width:none}}
@media(min-width:992px){.form-row-3 .label-col{flex-basis:200px;max-width:200px}}
.table .thead-light th{background:#f8f9fa}
.badge{font-size:90%}
</style>
@endsection

@push('css')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ---------- tiny helpers ----------
const $qs  = (sel, ctx=document) => ctx.querySelector(sel);
const $$qs = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));
const csrf = "{{ csrf_token() }}";

function selectedValues(selectEl){ return Array.from(selectEl.selectedOptions).map(o=>o.value).filter(Boolean); }
function getHiddenArrayValues(box, name){ return $$qs( `input[name="${name}[]"]`, box ).map(i=>i.value); }
function setHiddenArray(box, name, values){
  box.innerHTML = '';
  values.forEach(v => {
    const i = document.createElement('input');
    i.type = 'hidden'; i.name = name+'[]'; i.value = v;
    box.appendChild(i);
  });
}
function chips(values){ return values.length ? values.map(v=>`<span class="badge badge-pill badge-secondary mr-1 mb-1">${v}</span>`).join(' ') : '— Not selected —'; }
function setBadge(id, ok) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('badge-secondary','badge-success');
  el.classList.add(ok ? 'badge-success' : 'badge-secondary');
  el.textContent = ok ? 'Done' : 'Pending';
}

// ---------- Select2 for medicine (pre-rendered options) ----------
$(function(){
  $('#medicine_select').select2({
    width: '100%',
    placeholder: '-- Select Medicine --',
    allowClear: true,
    dropdownParent: $('#medicineModal')
  });
});

// ---------- Patient search (typeahead) ----------
(function(){
  const q = $qs('#patientQuery'), sel = $qs('#patientSelect'), pid = $qs('#patient_id'), info = $qs('#patientInfo');
  if(!q) return;
  let t=null;
  q.addEventListener('input', ()=>{
    const term=q.value.trim(); clearTimeout(t);
    if(!term){ sel.innerHTML='<option value="">-- Select patient from results --</option>'; return; }
    t=setTimeout(()=>{
      fetch(`{{ route('patients.search.ajax') }}?term=${encodeURIComponent(term)}`)
        .then(r=>r.json()).then(list=>{
          sel.innerHTML='<option value="">-- Select patient from results --</option>';
          list.forEach(it=>{
            const o=document.createElement('option');
            o.value=it.id; o.textContent=it.text;
            o.dataset.name=it.name; o.dataset.mobile=it.mobile;
            sel.appendChild(o);
          });
        });
    },300);
  });
  sel.addEventListener('change', ()=>{
    const o=sel.options[sel.selectedIndex];
    pid.value = sel.value || '';
    info.textContent = sel.value ? `${o.dataset.name} — ${o.dataset.mobile}` : '';

    // guided flow: enable Investigation and open it
    State.patientSelected = !!sel.value;
    refreshFlowUI();
    if (State.patientSelected) $('#investigationModal').modal('show');
  });
})();

// ---------- Guided flow state ----------
const State = {
  patientSelected: false,
  hasInvestigation: false,
  hasDiagnosis: false,
  hasMedicine: false,
};

function refreshFlowUI() {
  $('#btnInvestigationOpen').prop('disabled', !State.patientSelected);
  $('#btnDiagnosisOpen').prop('disabled', !(State.patientSelected && State.hasInvestigation));
  $('#btnMedicineOpen').prop('disabled', !(State.patientSelected && State.hasInvestigation && State.hasDiagnosis));

  setBadge('step_investigation_badge', State.hasInvestigation);
  setBadge('step_diagnosis_badge', State.hasDiagnosis);
  setBadge('step_medicine_badge', State.hasMedicine);

  const canSave = State.patientSelected && State.hasInvestigation && State.hasDiagnosis && State.hasMedicine;
  $('#btnSaveRx').prop('disabled', !canSave);
}
$(refreshFlowUI);

// ---------- Add New in modal (complain/diagnosis/investigation/medicine) ----------
async function postJSON(url, data){
  const r = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': csrf },
    body: JSON.stringify(data)
  });
  if (!r.ok) throw new Error('HTTP '+r.status);
  return r.json();
}

[
  { input:'#complain_new',      btn:'#complain_add_btn',      select:'#complain_select',      store:"{{ route('complaints.store') }}" },
  { input:'#diagnosis_new',     btn:'#diagnosis_add_btn',     select:'#diagnosis_select',     store:"{{ route('diagnoses.store') }}" },
  { input:'#investigation_new', btn:'#investigation_add_btn', select:'#investigation_select', store:"{{ route('investigations.store') }}" },
  { input:'#medicine_new',      btn:'#medicine_add_btn',      select:'#medicine_select',      store:"{{ route('medicines.store') }}" },
].forEach(cfg=>{
  const btn = $qs(cfg.btn);
  if(!btn) return;
  btn.addEventListener('click', async ()=>{
    const val = $qs(cfg.input).value.trim();
    if(!val) { alert('Enter a name first'); return; }
    try{
      const res = await postJSON(cfg.store, { name: val });
      const select = $(cfg.select);
      // If Select2 (medicine), append as option with a fake id if you only return {id,name}
      if (cfg.select === '#medicine_select') {
        // Expecting {id, name} back
        const id = (res.id ?? res.item?.id ?? val);
        const name = (res.name ?? res.item?.name ?? val);
        if (select.find('option[value="'+id+'"]').length === 0) {
          select.append(new Option(name, id, true, true)).trigger('change');
        } else {
          select.val(id).trigger('change');
        }
        $qs(cfg.input).value = '';
        return;
      }
      // For simple HTML selects
      const sel = $qs(cfg.select);
      if(!Array.from(sel.options).some(o=>o.value===val)){
        const o=document.createElement('option'); o.value=val; o.textContent=val; sel.appendChild(o);
      }
      $qs(cfg.input).value = '';
    }catch(e){
      alert('Error saving. Check console.'); console.error(e);
    }
  });
});

// ---------- Use Selected: DIAGNOSIS (merge) ----------
$('#diagnosis_use_btn').on('click', ()=>{
  const sel = document.getElementById('diagnosis_select');
  const vals = selectedValues(sel);
  if(!vals.length){ alert('Please select one or more diagnoses'); return; }
  const box = document.getElementById('diagnosis_hidden_box');
  const existing = getHiddenArrayValues(box, 'diagnosis');
  const merged = Array.from(new Set([...existing, ...vals]));
  setHiddenArray(box, 'diagnosis', merged);
  document.getElementById('diagnosis_display').innerHTML = chips(merged);

  State.hasDiagnosis = merged.length > 0;
  refreshFlowUI();
  if (State.hasDiagnosis) $('#medicineModal').modal('show');

  $('#diagnosisModal').modal('hide');
});

// ---------- Add to List: INVESTIGATIONS (append; avoid exact duplicate name+note) ----------
$('#investigation_use_btn').on('click', ()=>{
  const sel = document.getElementById('investigation_select');
  const names = selectedValues(sel);
  if(!names.length){ alert('Select one or more investigations'); return; }
  const note = document.getElementById('investigation_note').value.trim();
  const tbody = document.querySelector('#investTable tbody');
  const existingKeys = new Set($$qs('input[name="investigation_name[]"]').map(i=>{
    const row = i.closest('tr');
    const noteVal = row.querySelector('input[name="investigation_note[]"]')?.value || '';
    return i.value + '||' + noteVal;
  }));
  names.forEach(name=>{
    const key = name + '||' + (note||'');
    if (existingKeys.has(key)) return;
    existingKeys.add(key);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${name}<input type="hidden" name="investigation_name[]" value="${name}"></td>
      <td>${note||''}<input type="hidden" name="investigation_note[]" value="${note||''}"></td>
      <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove-invest">X</button></td>`;
    tbody.appendChild(tr);
  });
  document.getElementById('investigation_note').value = '';

  State.hasInvestigation = document.querySelectorAll('#investTable tbody tr').length > 0;
  refreshFlowUI();

  $('#investigationModal').modal('hide');
});

// ---------- Add to List: MEDICINES (append; avoid exact duplicate row) ----------
$('#medicine_use_btn').on('click', ()=>{
  const select = document.getElementById('medicine_select');
  const chosen = Array.from(select.selectedOptions).map(o => ({ id:o.value, name:o.textContent }));
  if(!chosen.length){ alert('Select one or more medicines'); return; }
  const f = {
    strength:  $('#med_strength').val().trim(),
    dose:      $('#med_dose').val().trim(),
    route:     $('#med_route').val().trim(),
    frequency: $('#med_frequency').val().trim(),
    duration:  $('#med_duration').val().trim(),
    timing:    $('#med_timing').val().trim(),
  };
  const $tbody = $('#medTable tbody');
  const existingKeys = new Set($('#medTable tbody tr').map(function(){
    return $(this).find('input').map(function(){ return this.value; }).get().join('||');
  }).get());

  chosen.forEach(function (m) {
    const key = [m.id, f.strength, f.dose, f.route, f.frequency, f.duration, f.timing].join('||');
    if (existingKeys.has(key)) return;
    existingKeys.add(key);

    const row = `
      <tr>
        <td>${m.name}
          <input type="hidden" name="medicine_id[]" value="${m.id}">
          <input type="hidden" name="medicine_name[]" value="${m.name}">
        </td>
        <td>${f.strength||''}<input type="hidden" name="strength[]" value="${f.strength||''}"></td>
        <td>${f.dose||''}<input type="hidden" name="dose[]" value="${f.dose||''}"></td>
        <td>${f.route||''}<input type="hidden" name="route[]" value="${f.route||''}"></td>
        <td>${f.frequency||''}<input type="hidden" name="frequency[]" value="${f.frequency||''}"></td>
        <td>${f.duration||''}<input type="hidden" name="duration[]" value="${f.duration||''}"></td>
        <td>${f.timing||''}<input type="hidden" name="timing[]" value="${f.timing||''}"></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-remove-med">X</button></td>
      </tr>`;
    $tbody.append(row);
  });

  State.hasMedicine = $('#medTable tbody tr').length > 0;
  refreshFlowUI();

  $('#medicineModal').modal('hide');
});

// ---------- Row deletion keeps state in sync ----------
$(document).on('click', '.js-remove-invest', function(){ $(this).closest('tr').remove(); State.hasInvestigation = $('#investTable tbody tr').length > 0; refreshFlowUI(); });
$(document).on('click', '.js-remove-med',    function(){ $(this).closest('tr').remove(); State.hasMedicine      = $('#medTable tbody tr').length > 0; refreshFlowUI(); });

// ---------- Guard: prevent submit if flow incomplete ----------
$('#rxForm').on('submit', function(e){
  const ok = State.patientSelected && State.hasInvestigation && State.hasDiagnosis && State.hasMedicine;
  if (!ok) {
    e.preventDefault();
    alert('Please complete: Investigation, Diagnosis and Medicine after selecting a patient.');
  }
});
</script>
@endpush
