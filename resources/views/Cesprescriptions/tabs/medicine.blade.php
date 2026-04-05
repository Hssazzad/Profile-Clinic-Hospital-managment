@php
  // Expected: $pid, $patientId (like your other tabs)
  $pid       = (int)($pid ?? request('id'));
  $patientId = (int)($patientId ?? request('patient'));
@endphp

@if (!$pid)
  <div class="alert alert-danger">No prescription selected. Save Patient first.</div>
  @php return; @endphp
@endif

<style>
  .card{background:#fff;border:1px solid #e5eaf3;border-radius:.75rem;padding:1rem;margin-bottom:1rem}
  .btn{padding:.5rem .9rem;border-radius:.5rem;border:1px solid #dbe3f0;background:#0ea5e9;color:#fff;cursor:pointer}
  .btn.secondary{background:#f8fafc;color:#0e1a2a}
  .btn.danger{background:#ef4444}
  .btn[disabled]{opacity:.6;cursor:not-allowed}
  .form-control{width:100%;padding:.45rem .55rem;border:1px solid #d9e2ef;border-radius:.5rem}
  .table{width:100%;border-collapse:collapse}
  .table th,.table td{border:1px solid #e5eaf3;padding:.5rem .6rem;vertical-align:top}
  .table th{background:#f4f7fb}
  label{display:block;margin-bottom:.35rem;font-weight:600}
  .nowrap{white-space:nowrap}

  /* Grid for add-new row */
  .grid7{
      display:grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
      gap:.5rem;
      align-items:flex-end;
  }

  /* Mobile: stack add row vertically */
  @media (max-width: 768px){
      .grid7{
          grid-template-columns: 1fr;
      }
  }

  .grid7 label{
      font-size:.85rem;
      font-weight:500;
  }

  #btnAddMed{
      width:100%;
  }

  /* ===== MOBILE: TABLE -> VERTICAL CARDS FOR MED LIST ===== */
  @media (max-width: 768px){
      .med-table-wrap{
          margin:0 -0.5rem;
      }
      .med-table-wrap .table{
          border:0;
      }
      .med-table-wrap thead{
          display:none;
      }
      .med-table-wrap tbody tr{
          display:block;
          border:1px solid #e5eaf3;
          border-radius:.75rem;
          margin-bottom:.75rem;
          padding:.35rem .5rem;
      }
      .med-table-wrap tbody td{
          display:flex;
          align-items:center;
          border:0;
          border-bottom:1px dashed #e5eaf3;
          padding:.25rem .2rem;
      }
      .med-table-wrap tbody td:last-child{
          border-bottom:0;
          justify-content:flex-end;
      }
      .med-table-wrap tbody td::before{
          content:attr(data-label);
          flex:0 0 35%;
          font-weight:600;
          color:#4b5563;
          margin-right:.5rem;
          font-size:.85rem;
      }
      .med-table-wrap tbody td .form-control{
          width:100%;
      }
      .med-table-wrap tbody td.nowrap{
          display:flex;
          gap:.25rem;
      }
      .med-table-wrap tbody td.nowrap::before{
          content:"Actions";
          flex:0 0 auto;
          font-weight:600;
          color:#4b5563;
          margin-right:.5rem;
      }
      .med-table-wrap .btn{
          padding:.3rem .6rem;
          font-size:.8rem;
      }
  }

  /* Select2 styling */
  .select2-container--bootstrap4 .select2-selection {
      border: 1px solid #6b7280 !important;
      border-radius: 6px !important;
      min-height: 38px !important;
      padding: 4px !important;
  }

  .select2-container--bootstrap4.select2-container--focus .select2-selection {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 1px #2563eb33 !important;
  }

  .select2-container--bootstrap4 .select2-dropdown {
      border: 1px solid #6b7280 !important;
      border-radius: 6px !important;
  }

  .select2-container--bootstrap4 .select2-results__option--highlighted {
      background: #0ea5e9 !important;
      color: #fff !important;
  }

  .select2-container--bootstrap4 .select2-results__option[aria-selected="true"] {
      background: #e0f2fe !important;
      color: #0e7490 !important;
  }
</style>

{{-- ADD NEW (AJAX) --}}
<div class="card">
  <div class="grid7">
    <div>
      <label>Medicine *</label>
      <select id="med_name" class="form-control select2" style="width:100%" required>
        <option value="">-- Select Medicine --</option>
        @foreach($commonMedicines as $m)
          <option
              value="{{ $m->name }}"
              data-strength="{{ $m->strength }}"
              data-group="{{ $m->GroupName }}"
          >
              {{ $m->name }} {{ $m->strength }} ({{ $m->GroupName }})
          </option>
        @endforeach
      </select>
      <button type="button" id="getmedicine" class="btn btn-info mt-1 w-100">
        Load Medicine
      </button>
    </div>

    <div>
      <label>Strength</label>
      <input id="med_strength" class="form-control" placeholder="20 mg">
    </div>

    <div>
      <label>Dose</label>
      <select id="med_dose" class="form-control select2" style="width:100%" required>
        <option value="">-- Select Dose --</option>
        <option value="½ Tablet">1/2 Tablet</option>
        <option value="1 Tablet">1 Tablet</option>
        <option value="2 Tablet">2 Tablet</option>
        <option value="3 Tablet">3 Tablet</option>
        <option value="1 Capsule">1 Capsule</option>
        <option value="2 Capsule">2 Capsule</option>
      </select>
    </div>

    <div>
      <label>When take</label>
      <select id="med_frequency" class="form-control select2" style="width:100%" required>
        <option value="">-- Select Dose --</option>
        <option value="1+0+0">1+0+0</option>
        <option value="1+1+0">1+1+0</option>
        <option value="1+1+1">1+1+1</option>
        <option value="1+0+1">1+0+1</option>
        <option value="0+0+1">0+0+1</option>
        <option value="0+1+1">0+1+1</option>
      </select>
    </div>

    <div>
      <label>Timing</label>
      <select id="med_timing" class="form-control select2" style="width:100%" required>
        <option value="After Meal">After Meal</option>
        <option value="Before Meal">Before Meal</option>
      </select>
    </div>

    <div>
      <label>Duration/Till</label>
      <select id="med_duration" class="form-control select2" style="width:100%" required>
        <option value="5 days">5 days</option>
        <option value="7 days">7 days</option>
        <option value="15 days">15 days</option>
        <option value="30 days">30 days</option>
        <option value="Continue">Continue</option>
      </select>
    </div>

    <div>
      <label>&nbsp;</label>
      <button id="btnAddMed" class="btn btn-primary">
        Add
      </button>
    </div>
  </div>

  <div style="margin-top:.5rem">
    <label>Note</label>
    <input id="med_note" class="form-control" placeholder="optional general note (per medicine)">
  </div>

  <div id="medMsg" style="margin-top:.5rem;font-size:.9rem;display:none"></div>
</div>

{{-- LIST (AJAX) --}}
<div class="card med-table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th style="width:18%">Name</th>
        <th style="width:10%">Strength</th>
        <th style="width:10%">Dose</th>
        <th style="width:12%">Frequency</th>
        <th style="width:10%">Duration</th>
        <th style="width:10%">Timing</th>
        <th>Note</th>
        <th style="width:160px">Actions</th>
      </tr>
    </thead>
    <tbody id="medRows">
      <tr><td colspan="8" class="text-muted">Loading…</td></tr>
    </tbody>
  </table>
</div>

<div style="display:flex;justify-content:space-between;gap:.75rem">
  <a class="btn secondary" href="{{ route('rx.wizard',['id'=>$pid,'patient'=>$patientId,'tab'=>'diagnosis']) }}">← Back</a>
  <form method="get" action="{{ route('rx.wizard') }}" style="margin:0">
    <input type="hidden" name="id" value="{{ $pid }}">
    <input type="hidden" name="patient" value="{{ $patientId }}">
    <input type="hidden" name="tab" value="preview">
    <button class="btn" type="submit">Next → Preview</button>
  </form>
</div>

{{-- =============== MAIN AJAX SCRIPT (NO jQuery HERE) =============== --}}
<script>
(function(){
  const $ = (s,ctx=document)=>ctx.querySelector(s);
  const pid  = {{ $pid }};
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const rowsTbody = $('#medRows');
  const msg = $('#medMsg');

  function esc(s){
    return String(s||'')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;');
  }

  function showMsg(text, ok=true){
    if (!msg) return;
    msg.style.display='block';
    msg.style.color = ok ? '#065f46' : '#b91c1c';
    msg.textContent = text;
    setTimeout(()=>{ msg.style.display='none'; }, 1600);
  }

  function renderList(rows){
    if (!rows || rows.length===0) {
      rowsTbody.innerHTML = `<tr><td colspan="8" class="text-muted">No medicines added.</td></tr>`;
      return;
    }
    rowsTbody.innerHTML = rows.map(r=>`
      <tr data-id="${r.id}">
        <td data-label="Name">
          <input class="form-control med-name" value="${esc(r.name||'')}" required>
        </td>
        <td data-label="Strength">
          <input class="form-control med-strength" value="${esc(r.strength||'')}">
        </td>
        <td data-label="Dose">
          <input class="form-control med-dose" value="${esc(r.dose||'')}">
        </td>
        <td data-label="Frequency">
          <input class="form-control med-frequency" value="${esc(r.frequency||'')}">
        </td>
        <td data-label="Duration">
          <input class="form-control med-duration" value="${esc(r.duration||'')}">
        </td>
        <td data-label="Timing">
          <input class="form-control med-timing" value="${esc(r.timing||'')}">
        </td>
        <td data-label="Note">
          <input class="form-control med-note" value="${esc(r.note||'')}">
        </td>
        <td class="nowrap">
          <button type="button" class="btn btn-save">Save</button>
          <button type="button" class="btn danger btn-del">Delete</button>
        </td>
      </tr>
    `).join('');
  }

  async function call(action, payload={}){
    const fd = new FormData();
    fd.append('action', action);
    fd.append('prescription_id', pid);
    fd.append('_token', csrf);
    for (const [k,v] of Object.entries(payload)) fd.append(k, v ?? '');

    const res = await fetch(`{{ route('rx.med.ajax') }}`, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' },
      body: fd
    });

    if (res.status === 422) {
      const j = await res.json();
      throw new Error(j?.message || 'Validation error');
    }
    const j = await res.json().catch(()=>({ok:false,error:'Bad JSON'}));
    if (j.ok === false && j.error) throw new Error(j.error);
    return j;
  }

  async function loadList(){
    try{
      const j = await call('list');
      renderList(j.rows||[]);
    }catch(e){
      rowsTbody.innerHTML = `<tr><td colspan="8" class="text-danger">Load failed: ${esc(e.message)}</td></tr>`;
    }
  }

  // Add
  document.getElementById('btnAddMed')?.addEventListener('click', async ()=>{
    const payload = {
      name:      document.getElementById('med_name')?.value || '',
      strength:  document.getElementById('med_strength')?.value || '',
      dose:      document.getElementById('med_dose')?.value || '',
      route:     document.getElementById('med_route')?.value || '',
      frequency: document.getElementById('med_frequency')?.value || '',
      duration:  document.getElementById('med_duration')?.value || '',
      timing:    document.getElementById('med_timing')?.value || '',
      note:      document.getElementById('med_note')?.value || '',
    };
    if (!payload.name) { showMsg('Medicine name required', false); return; }

    const btn = document.getElementById('btnAddMed');
    btn.disabled = true; btn.textContent = 'Saving...';

    try{
      await call('add', payload);
      ['med_name','med_strength','med_dose','med_route','med_frequency','med_duration','med_timing','med_note']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value=''; });
      await loadList();
      showMsg('Added.');
    }catch(e){
      showMsg(e.message || 'Save error', false);
    }finally{
      btn.disabled = false; btn.textContent = 'Add';
    }
  });

  // Inline Save/Delete
  rowsTbody?.addEventListener('click', async (e)=>{
    const tr = e.target.closest('tr[data-id]');
    if (!tr) return;
    const id = tr.getAttribute('data-id');

    if (e.target.classList.contains('btn-save')) {
      const payload = {
        id,
        name:      tr.querySelector('.med-name')?.value || '',
        strength:  tr.querySelector('.med-strength')?.value || '',
        dose:      tr.querySelector('.med-dose')?.value || '',
        route:     tr.querySelector('.med-route')?.value || '',
        frequency: tr.querySelector('.med-frequency')?.value || '',
        duration:  tr.querySelector('.med-duration')?.value || '',
        timing:    tr.querySelector('.med-timing')?.value || '',
        note:      tr.querySelector('.med-note')?.value || '',
      };
      if (!payload.name) { showMsg('Medicine name required', false); return; }
      const btn = e.target; btn.disabled = true; btn.textContent = 'Saving...';
      try{
        await call('update', payload);
        showMsg('Updated.');
      }catch(err){
        showMsg(err.message || 'Update failed', false);
      }finally{
        btn.disabled = false; btn.textContent = 'Save';
      }
    }

    if (e.target.classList.contains('btn-del')) {
      if (!confirm('Delete this item?')) return;
      const btn = e.target; btn.disabled = true; btn.textContent = 'Deleting...';
      try{
        await call('delete', { id });
        tr.remove();
        if (!rowsTbody.children.length)
          rowsTbody.innerHTML = `<tr><td colspan="8" class="text-muted">No medicines added.</td></tr>`;
        showMsg('Deleted.');
      }catch(err){
        showMsg(err.message || 'Delete failed', false);
        btn.disabled = false; btn.textContent = 'Delete';
      }
    }
  });

  // initial load
  loadList();
})();
</script>

{{-- =============== SELECT2 + AUTOFOCUS + LOAD MEDICINES (jQuery) =============== --}}
@push('js')
<script>
jQuery(function($){

    if (!$.fn.select2) {
        console.warn('Select2 plugin not loaded.');
        return;
    }

    const $medName      = $('#med_name');
    const $medDose      = $('#med_dose');
    const $med_frequency = $('#med_frequency');
    const $btn          = $('#getmedicine');

    function bindAutoFocus($el){
        $el.on('select2:open', function(e){
            const id = e.target.id;
            const selector = ".select2-search__field[aria-controls='select2-" + id + "-results']";
            setTimeout(function(){
                const el = document.querySelector(selector);
                if (el) el.focus();
            }, 0);
        });
    }

    // Init Select2 for dropdowns
    $medName.select2({
        theme: 'bootstrap4',
        placeholder: '-- Select Medicine --',
        width: '100%'
    });
    $medDose.select2({
        theme: 'bootstrap4',
        placeholder: '-- Select Dose --',
        width: '100%'
    });
    $med_frequency.select2({
        theme: 'bootstrap4',
        placeholder: '-- Select Dose --',
        width: '100%'
    });

    // Auto-focus when opened
    bindAutoFocus($medName);
    bindAutoFocus($medDose);
    bindAutoFocus($med_frequency);

    // Load medicines via AJAX
    $btn.on('click', async function () {
        $btn.text("Loading...");
        $btn.prop('disabled', true);

        try {
            const res = await fetch("{{ route('ajax.load.medicines') }}");
            const data = await res.json();

            // Reset options
            $medName.empty().append(
                $('<option>', {value: '', text: '-- Select Medicine --'})
            );

            data.forEach(row => {
                const $opt = $('<option>', {
                    value: row.name,
                    text: `${row.name} ${row.strength} (${row.GroupName})`
                })
                .attr('data-strength', row.strength || '')
                .attr('data-group', row.GroupName || '');

                $medName.append($opt);
            });

            $medName.trigger('change.select2');
            alert("Medicines loaded successfully!");

        } catch (e) {
            console.error(e);
            alert("Failed to load medicines.");
        } finally {
            $btn.text("Load Medicine");
            $btn.prop('disabled', false);
        }
    });

    // Auto-fill strength when medicine changes
    $medName.on('change', function () {
        const opt   = this.options[this.selectedIndex];
        const power = opt ? (opt.getAttribute('data-strength') || '') : '';
        document.getElementById('med_strength').value = power;
    });

});
</script>
@endpush
