<script>
// ==== Investigations -> copy from modal textarea to main textarea ====
function applyInvestigations(){
  const from = document.getElementById('investigations_modal')?.value || '';
  const to   = document.getElementById('investigations');
  if (to) to.value = from;
}

// ==== Modal medicines (staging) ====
function addModalRow(){
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input class="form-control modal-medicine-name" required></td>
    <td><input class="form-control modal-strength"></td>
    <td><input class="form-control modal-dose"></td>
    <td><input class="form-control modal-route"></td>
    <td><input class="form-control modal-frequency"></td>
    <td><input class="form-control modal-duration"></td>
    <td><input class="form-control modal-timing"></td>
    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeModalRow(this)">X</button></td>
  `;
  document.querySelector('#modalMedTable tbody').appendChild(tr);
}
function removeModalRow(btn){
  const row = btn.closest('tr');
  const body = row.parentNode;
  if (body.children.length > 1) body.removeChild(row);
}

function commitModalMedicines(){
  const rows = document.querySelectorAll('#modalMedTable tbody tr');
  const tbody = document.querySelector('#medTable tbody');
  if (!tbody) return;

  rows.forEach(r => {
    const name = r.querySelector('.modal-medicine-name')?.value?.trim();
    if (!name) return;

    const strength  = r.querySelector('.modal-strength')?.value || '';
    const dose      = r.querySelector('.modal-dose')?.value || '';
    const route     = r.querySelector('.modal-route')?.value || '';
    const frequency = r.querySelector('.modal-frequency')?.value || '';
    const duration  = r.querySelector('.modal-duration')?.value || '';
    const timing    = r.querySelector('.modal-timing')?.value || '';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input name="medicine_name[]" class="form-control" required value="${escapeHtml(name)}"></td>
      <td><input name="strength[]" class="form-control" value="${escapeHtml(strength)}"></td>
      <td><input name="dose[]" class="form-control" value="${escapeHtml(dose)}"></td>
      <td><input name="route[]" class="form-control" value="${escapeHtml(route)}"></td>
      <td><input name="frequency[]" class="form-control" value="${escapeHtml(frequency)}"></td>
      <td><input name="duration[]" class="form-control" value="${escapeHtml(duration)}"></td>
      <td><input name="timing[]" class="form-control" value="${escapeHtml(timing)}"></td>
      <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)">X</button></td>
    `;
    tbody.appendChild(tr);
  });

  // reset modal to a single empty row
  const body = document.querySelector('#modalMedTable tbody');
  if (body) {
    body.innerHTML = `
      <tr>
        <td><input class="form-control modal-medicine-name" required></td>
        <td><input class="form-control modal-strength"></td>
        <td><input class="form-control modal-dose"></td>
        <td><input class="form-control modal-route"></td>
        <td><input class="form-control modal-frequency"></td>
        <td><input class="form-control modal-duration"></td>
        <td><input class="form-control modal-timing"></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeModalRow(this)">X</button></td>
      </tr>`;
  }
}

// ==== Next appointment -> hidden input on main form ====
function setNextAppointment(){
  const pick = document.getElementById('next_appointment_picker')?.value || '';
  const hidden = document.getElementById('hidden_next_appointment');
  if (hidden) hidden.value = pick;
}

// ==== Helper ====
function escapeHtml(str){
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// (Optional) Autofocus + prevent Enter submit inside modals (BS4)
$('#medicineModal').on('shown.bs.modal', function () {
  $(this).find('.modal-medicine-name').first().trigger('focus');
});
$('#investigationModal').on('shown.bs.modal', function () {
  $('#investigations_modal').trigger('focus');
});
$('#nextAppointmentModal').on('shown.bs.modal', function () {
  $('#next_appointment_picker').trigger('focus');
});
['medicineModal','investigationModal'].forEach(id => {
  document.getElementById(id)?.addEventListener('keydown', function(e){
    if (e.key === 'Enter') e.preventDefault();
  });
});
</script>
@push('js')
<script>
// ---------- Elements ----------
const $query  = document.getElementById('patientQuery');
const $select = document.getElementById('patientSelect');
const $pid    = document.getElementById('patient_id');

// optional: show a one-line summary under the select
let $info = document.getElementById('patientInfo');
if (!$info) {
  $info = document.createElement('small');
  $info.id = 'patientInfo';
  $info.className = 'text-muted d-block mt-1';
  $select.parentNode.appendChild($info);
}

// ---------- Debounced fetch ----------
let timer = null;
$query.addEventListener('input', function () {
  const term = this.value.trim();
  clearTimeout(timer);

  if (!term) {
    $select.innerHTML = '<option value="">-- Select patient --</option>';
    $pid.value = '';
    $info.textContent = '';
    return;
  }

  timer = setTimeout(() => {
    fetch(`{{ route(app()->bound('router') && Route::has('patients.search.ajax') ? 'patients.search.ajax' : (Route::has('admin.patients.search.ajax') ? 'admin.patients.search.ajax' : 'patients.search.ajax')) }}?term=` + encodeURIComponent(term), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(list => {
      $select.innerHTML = '<option value="">-- Select patient --</option>';
      list.forEach(it => {
        const opt = document.createElement('option');
        opt.value = it.id;
        opt.textContent = it.text;
        opt.dataset.code   = it.code;
        opt.dataset.name   = it.name;
        opt.dataset.mobile = it.mobile || '';
        opt.dataset.age    = it.age    || '';
        opt.dataset.gender = it.gender || '';
        $select.appendChild(opt);
      });
    })
    .catch(() => {
      $select.innerHTML = '<option value="">(search error)</option>';
    });
  }, 300);
});

// ---------- On select -> set hidden patient_id + info ----------
$select.addEventListener('change', function(){
  const opt = this.options[this.selectedIndex];
  $pid.value = this.value || '';
  if (this.value) {
    const summary = `${opt.dataset.name} (${opt.dataset.code}) — ${opt.dataset.mobile} | Age: ${opt.dataset.age} | ${opt.dataset.gender}`;
    $info.textContent = summary.replace(/\s+\|\s+\|/g, ' | '); // tiny cleanup
  } else {
    $info.textContent = '';
  }
});

// ---------- Guard: require a patient before submit ----------
document.getElementById('rxForm').addEventListener('submit', function(e){
  if (!($pid.value || '').trim()) {
    e.preventDefault();
    alert('Please select a patient first.');
    $query.focus();
  }
});
</script>
@endpush
@push('js')
<script>
// ---- Delegated remove for MAIN table ----
(function(){
  const table = document.getElementById('medTable');
  if (!table) return;

  table.addEventListener('click', function(e){
    const btn = e.target.closest('.js-remove-row');
    if (!btn) return;

    const tr = btn.closest('tr');
    if (!tr) return;

    const tbody = tr.parentNode;
    // if you want to keep at least one row, uncomment next line:
    // if (tbody.children.length <= 1) return;

    tr.remove();
  });
})();

// ---- Delegated remove for MODAL table (optional but recommended) ----
(function(){
  const modalTable = document.getElementById('modalMedTable');
  if (!modalTable) return;

  modalTable.addEventListener('click', function(e){
    const btn = e.target.closest('.js-remove-modal-row');
    if (!btn) return;

    const tr = btn.closest('tr');
    if (!tr) return;

    const tbody = tr.parentNode;
    // keep at least one blank row in modal
    if (tbody.children.length > 1) tr.remove();
  });
})();
</script>
@endpush
