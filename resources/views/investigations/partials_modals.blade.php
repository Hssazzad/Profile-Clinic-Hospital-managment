{{-- ===== ADD TEST MODAL ===== --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content inv-modal">
            <div class="inv-modal-header">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="modal-icon"><i class="fas fa-plus"></i></div>
                    <div>
                        <h5 class="mb-0 font-weight-bold text-white">Add New Test</h5>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Test Name <span class="text-danger">*</span></label>
                    <input type="text" id="add-name" class="form-control" placeholder="e.g. CBC, Serum Creatinine">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Category <span class="text-danger">*</span></label>
                    <select id="add-category" class="form-control" onchange="toggleCustomCategory('add')">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->Name }}">{{ $cat->Name }}</option>
                        @endforeach
                        <option value="custom">+ Add New Category</option>
                    </select>
                </div>
                <div class="form-group d-none" id="add-custom-category-wrap">
                    <label class="font-weight-bold small text-muted text-uppercase">New Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="add-custom-category" class="form-control" placeholder="Enter new category name">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Price (?) <span class="text-danger">*</span></label>
                    <input type="number" id="add-price" class="form-control" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                    <select id="add-status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e4e9f0;padding:16px 24px">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" onclick="submitAdd()">
                    <i class="fas fa-save mr-1"></i> Save Test
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== EDIT TEST MODAL ===== --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content inv-modal">
            <div class="inv-modal-header" style="background:linear-gradient(135deg,#1565c0,#0d47a1)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="modal-icon"><i class="fas fa-edit"></i></div>
                    <div>
                        <h5 class="mb-0 font-weight-bold text-white">Edit Test</h5>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Test Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit-name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Category <span class="text-danger">*</span></label>
                    <select id="edit-category" class="form-control" onchange="toggleCustomCategory('edit')">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->Name }}">{{ $cat->Name }}</option>
                        @endforeach
                        <option value="custom">+ Add New Category</option>
                    </select>
                </div>
                <div class="form-group d-none" id="edit-custom-category-wrap">
                    <label class="font-weight-bold small text-muted text-uppercase">New Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit-custom-category" class="form-control" placeholder="Enter new category name">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Price (?) <span class="text-danger">*</span></label>
                    <input type="number" id="edit-price" class="form-control" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                    <select id="edit-status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e4e9f0;padding:16px 24px">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" onclick="submitEdit()">
                    <i class="fas fa-save mr-1"></i> Update Test
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== ADD CATEGORY MODAL ===== --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content inv-modal">
            <div class="inv-modal-header" style="background:linear-gradient(135deg,#f57c00,#e65100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="modal-icon"><i class="fas fa-folder-plus"></i></div>
                    <div>
                        <h5 class="mb-0 font-weight-bold text-white">Add Category</h5>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-0">
                    <label class="font-weight-bold small text-muted text-uppercase">Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="new-category-name" class="form-control" placeholder="e.g. Pathology">
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e4e9f0;padding:16px 24px">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn px-4 text-white" style="background:#f57c00" onclick="submitAddCategory()">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== JAVASCRIPT ===== --}}
<script>
var ADD_URL    = '{{ route("investigations.store") }}';
var UPDATE_URL = '{{ route("investigations.update") }}';
var DELETE_URL = '{{ route("investigations.destroy", ":id") }}';
var CAT_STORE_URL = '{{ route("investigations.categories.store") }}';

// ---- OPEN MODALS ----
function openAddModal() {
    document.getElementById('add-name').value = '';
    document.getElementById('add-category').value = '';
    document.getElementById('add-price').value = '';
    document.getElementById('add-status').value = 'active';
    document.getElementById('add-custom-category').value = '';
    document.getElementById('add-custom-category-wrap').classList.add('d-none');
    $('#addModal').modal('show');
}

function openEditModal(btn) {
    var row = btn.closest('tr');
    document.getElementById('edit-id').value       = row.dataset.id;
    document.getElementById('edit-name').value     = row.dataset.name;
    document.getElementById('edit-price').value    = row.dataset.price;
    document.getElementById('edit-status').value   = 'active';

    // Category dropdown-? match ??? select ???
    var catSelect = document.getElementById('edit-category');
    var matched = false;
    for (var i = 0; i < catSelect.options.length; i++) {
        if (catSelect.options[i].value === row.dataset.category) {
            catSelect.selectedIndex = i;
            matched = true;
            break;
        }
    }
    if (!matched) catSelect.value = '';
    document.getElementById('edit-custom-category-wrap').classList.add('d-none');

    $('#editModal').modal('show');
}

function openAddCategoryModal() {
    document.getElementById('new-category-name').value = '';
    $('#addCategoryModal').modal('show');
}

// ---- CUSTOM CATEGORY TOGGLE ----
function toggleCustomCategory(prefix) {
    var val  = document.getElementById(prefix + '-category').value;
    var wrap = document.getElementById(prefix + '-custom-category-wrap');
    if (val === 'custom') {
        wrap.classList.remove('d-none');
    } else {
        wrap.classList.add('d-none');
    }
}

// ---- SHOW ALERT ----
function showAlert(msg, type) {
    var box = document.getElementById('alert-box');
    box.className = 'alert alert-' + type + ' mb-3';
    box.textContent = msg;
    box.classList.remove('d-none');
    setTimeout(function () { box.classList.add('d-none'); }, 4000);
}

// ---- ADD TEST ----
function submitAdd() {
    var name     = document.getElementById('add-name').value.trim();
    var category = document.getElementById('add-category').value;
    var custom   = document.getElementById('add-custom-category').value.trim();
    var price    = document.getElementById('add-price').value;
    var status   = document.getElementById('add-status').value;

    if (!name || !category || !price) {
        return showAlert('Name, Category and Price are required.', 'danger');
    }

    fetch(ADD_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({
            name: name,
            category: category,
            custom_category: custom,
            price: price,
            status: status
        })
    })
    .then(r => r.json())
    .then(function (data) {
        if (data.success) {
            $('#addModal').modal('hide');
            showAlert(data.message, 'success');
            appendRow(data.data);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(function () { showAlert('Something went wrong.', 'danger'); });
}

// ---- EDIT TEST ----
function submitEdit() {
    var id       = document.getElementById('edit-id').value;
    var name     = document.getElementById('edit-name').value.trim();
    var category = document.getElementById('edit-category').value;
    var custom   = document.getElementById('edit-custom-category').value.trim();
    var price    = document.getElementById('edit-price').value;
    var status   = document.getElementById('edit-status').value;

    if (!name || !category || !price) {
        return showAlert('Name, Category and Price are required.', 'danger');
    }

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({
            id: id,
            name: name,
            category: category,
            custom_category: custom,
            price: price,
            status: status
        })
    })
    .then(r => r.json())
    .then(function (data) {
        if (data.success) {
            $('#editModal').modal('hide');
            showAlert(data.message, 'success');
            updateRow(data.data);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(function () { showAlert('Something went wrong.', 'danger'); });
}

// ---- DELETE TEST ----
function deleteRecord(id, btn) {
    if (!confirm('Are you sure you want to delete this test?')) return;

    var url = DELETE_URL.replace(':id', id);
    fetch(url, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(function (data) {
        if (data.success) {
            showAlert(data.message, 'success');
            var row = btn.closest('tr');
            row.remove();
            renumberRows();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(function () { showAlert('Something went wrong.', 'danger'); });
}

// ---- ADD CATEGORY ----
function submitAddCategory() {
    var name = document.getElementById('new-category-name').value.trim();
    if (!name) return showAlert('Category name is required.', 'danger');

    fetch(CAT_STORE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ name: name })
    })
    .then(r => r.json())
    .then(function (data) {
        if (data.success) {
            $('#addCategoryModal').modal('hide');
            showAlert(data.message, 'success');
            // ?? dropdown-? ???? option ??? ???
            addOptionToDropdowns(data.data.Name);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(function () { showAlert('Something went wrong.', 'danger'); });
}

// ---- DOM HELPERS ----
function appendRow(inv) {
    var tbody = document.getElementById('inv-tbody');
    var empty = document.getElementById('empty-row');
    if (empty) empty.remove();

    var count = tbody.querySelectorAll('tr.inv-row').length + 1;
    var tr = document.createElement('tr');
    tr.className = 'inv-row';
    tr.dataset.id       = inv.ID;
    tr.dataset.name     = inv.Name;
    tr.dataset.category = inv.category || '';
    tr.dataset.price    = inv.Amount;
    tr.innerHTML =
        '<td class="text-muted small">' + count + '</td>' +
        '<td><strong>' + inv.Name + '</strong></td>' +
        '<td><span class="cat-badge">' + (inv.category || 'Other') + '</span></td>' +
        '<td class="text-muted">' + inv.Code + '</td>' +
        '<td class="font-weight-bold">? ' + Number(inv.Amount).toLocaleString() + '</td>' +
        '<td class="text-center">' +
            '<button class="btn-edit" onclick="openEditModal(this)"><i class="fas fa-edit"></i></button> ' +
            '<button class="btn-delete" onclick="deleteRecord(' + inv.ID + ', this)"><i class="fas fa-trash"></i></button>' +
        '</td>';
    tbody.appendChild(tr);
}

function updateRow(inv) {
    var row = document.querySelector('tr.inv-row[data-id="' + inv.ID + '"]');
    if (!row) return;
    row.dataset.name     = inv.Name;
    row.dataset.category = inv.category || '';
    row.dataset.price    = inv.Amount;
    row.cells[1].innerHTML = '<strong>' + inv.Name + '</strong>';
    row.cells[2].innerHTML = '<span class="cat-badge">' + (inv.category || 'Other') + '</span>';
    row.cells[3].textContent = inv.Code;
    row.cells[4].textContent = '? ' + Number(inv.Amount).toLocaleString();
}

function renumberRows() {
    var rows = document.querySelectorAll('tr.inv-row');
    rows.forEach(function (r, i) {
        r.cells[0].textContent = i + 1;
    });
    if (rows.length === 0) {
        var tbody = document.getElementById('inv-tbody');
        tbody.innerHTML = '<tr id="empty-row"><td colspan="6" class="text-center p-4">No tests found.</td></tr>';
    }
}

function addOptionToDropdowns(name) {
    ['add-category', 'edit-category'].forEach(function (selId) {
        var sel = document.getElementById(selId);
        // "custom" option ?? ??? insert ???
        var customOpt = sel.querySelector('option[value="custom"]');
        var newOpt = document.createElement('option');
        newOpt.value = name;
        newOpt.textContent = name;
        sel.insertBefore(newOpt, customOpt);
    });

    // filter dropdown-?? ??? ???
    var filterSel = document.getElementById('filter-category');
    if (filterSel) {
        var opt = document.createElement('option');
        opt.value = name;
        opt.textContent = name;
        filterSel.appendChild(opt);
    }
}
</script>