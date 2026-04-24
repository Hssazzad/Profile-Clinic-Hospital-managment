@extends('adminlte::page')

@section('title', 'Investigation Management')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0" style="font-size:22px;font-weight:700;color:#1a2332">
            <span style="width:38px;height:38px;border-radius:10px;background:#e0f2f1;display:inline-flex;align-items:center;justify-content:center;color:#00796b;margin-right:10px">
                <i class="fas fa-flask"></i>
            </span>
            Investigation Management
        </h1>
    </div>
    <div style="display:flex;gap:10px">
        <button class="btn-add-new" style="background:#f57c00" onclick="openManageCategoriesModal()">
            <i class="fas fa-list-ul mr-1"></i> Manage Categories
        </button>
        <button class="btn-add-new" onclick="openAddCategoryModal()">
            <i class="fas fa-folder-plus mr-1"></i> Add Category
        </button>
        <button class="btn-add-new" onclick="openAddModal()">
            <i class="fas fa-plus mr-1"></i> Add Test
        </button>
    </div>
</div>
@stop

@section('content')

<div id="alert-box" class="alert d-none mb-3" role="alert" style="border-radius:10px;font-weight:500"></div>

{{-- Main Card --}}
<div class="main-card">
    <div class="main-card-header">
        <div style="display:flex;align-items:center;gap:12px">
            <span style="width:40px;height:40px;border-radius:10px;background:#e0f2f1;display:flex;align-items:center;justify-content:center;color:#00796b;font-size:18px">
                <i class="fas fa-list"></i>
            </span>
            <div>
                <h5 class="mb-0 font-weight-bold">All Investigations</h5>
            </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <select id="filter-category" class="filter-select" onchange="filterTable()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->Name }}">{{ $cat->Name }}</option>
                @endforeach
            </select>
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Search test name..." onkeyup="filterTable()">
            </div>
        </div>
    </div>

    <div style="overflow-x:auto">
        <table class="inv-table" id="inv-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Test Name</th>
                    <th style="width:130px">Category</th>
                    <th style="width:90px">Code</th>
                    <th style="width:110px">Price (?)</th>
                    <th style="width:110px;text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody id="inv-tbody">
                @forelse($investigations as $inv)
                <tr class="inv-row" data-id="{{ $inv->ID }}" data-name="{{ $inv->Name }}" data-category="{{ $inv->category }}" data-price="{{ $inv->Amount }}">
                    <td class="text-muted small">{{ $loop->iteration }}</td>
                    <td><strong>{{ $inv->Name }}</strong></td>
                    <td><span class="cat-badge">{{ $inv->category ?? 'Other' }}</span></td>
                    <td class="text-muted">{{ $inv->Code }}</td>
                    <td class="font-weight-bold">? {{ number_format($inv->Amount, 0) }}</td>
                    <td class="text-center">
                        <button class="btn-edit" onclick="openEditModal(this)"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete" onclick="deleteRecord({{ $inv->ID }}, this)"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr id="empty-row"><td colspan="6" class="text-center p-4">No tests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MANAGE CATEGORIES MODAL ===== --}}
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content inv-modal">
            <div class="inv-modal-header" style="background:linear-gradient(135deg, #f57c00, #e65100)">
                <div style="display:flex;align-items:center;gap:12px">
                    <div class="modal-icon"><i class="fas fa-list-ul"></i></div>
                    <div>
                        <h5 class="mb-0 font-weight-bold text-white">Manage Categories</h5>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th>Code</th>
                            <th>Category Name</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr id="cat-row-{{ $cat->ID }}">
                            <td>{{ $cat->Code }}</td>
                            <td>
                                <span id="cat-name-display-{{ $cat->ID }}"><strong>{{ $cat->Name }}</strong></span>
                                <input type="text" id="cat-name-input-{{ $cat->ID }}" class="form-control form-control-sm d-none" value="{{ $cat->Name }}">
                            </td>
                            <td class="text-right">
                                <button class="btn btn-sm btn-info" id="btn-edit-cat-{{ $cat->ID }}" onclick="toggleEditCategory({{ $cat->ID }})"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-success d-none" id="btn-save-cat-{{ $cat->ID }}" onclick="saveEditCategory({{ $cat->ID }})"><i class="fas fa-save"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory({{ $cat->ID }})"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ADD/EDIT MODALS (???? ????, ???? Dropdown ? $cat->Name ???) --}}
@include('investigations.partials_modals') {{-- Modal ?? ??????? ?? ?? ???? ???? ???? ???? Add/Edit Test Modal ??????? ????? ?????? --}}
{{-- Note: Dropdown ?? ??? ???: @foreach($categories as $cat) <option value="{{ $cat->Name }}">{{ $cat->Name }}</option> @endforeach --}}

@stop

@section('css')
<style>
/* ????? ???? CSS ?? ?????... */
:root { --teal: #00796B; --teal-d: #00695C; --border: #e4e9f0; --text: #1a2332; --bg: #f0f4f0; --radius: 12px; }
.btn-add-new { background:var(--teal); color:#fff; border:none; border-radius:8px; padding:10px 20px; font-weight:600; }
.main-card { background:#fff; border-radius:var(--radius); border:1px solid var(--border); overflow:hidden; }
.main-card-header { padding:18px 24px; background:#fafbfd; display:flex; justify-content:space-between; }
.inv-table { width:100%; border-collapse:collapse; }
.inv-table th { background:#f0faf8; padding:11px 14px; text-transform:uppercase; font-size:11.5px; }
.inv-table td { padding:11px 14px; border-bottom:1px solid var(--border); font-size:13px; }
.cat-badge { background:#e0f2f1; color:var(--teal-d); border-radius:5px; padding:3px 10px; font-weight:600; }
.btn-edit { background:#e3f2fd; color:#1565c0; border:none; padding:5px 10px; border-radius:6px; }
.btn-delete { background:#ffebee; color:#c62828; border:none; padding:5px 10px; border-radius:6px; }
.inv-modal-header { background:linear-gradient(135deg, var(--teal), var(--teal-d)); padding:20px 24px; display:flex; justify-content:space-between;}
.modal-icon { width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px;}
.modal-close-btn { background:none; border:none; color:#fff; font-size:18px;}
.d-none { display:none !important; }
</style>
@stop

@section('js')
<script>
var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var CAT_UPDATE_URL = '{{ route("investigations.categories.update") }}';
var CAT_DELETE_URL = '{{ route("investigations.categories.destroy", ":id") }}';

// --- CATEGORY MANAGEMENT JS ---
function openManageCategoriesModal() {
    $('#manageCategoriesModal').modal('show');
}

function toggleEditCategory(id) {
    document.getElementById('cat-name-display-' + id).classList.add('d-none');
    document.getElementById('btn-edit-cat-' + id).classList.add('d-none');
    document.getElementById('cat-name-input-' + id).classList.remove('d-none');
    document.getElementById('btn-save-cat-' + id).classList.remove('d-none');
}

function saveEditCategory(id) {
    var newName = document.getElementById('cat-name-input-' + id).value.trim();
    if (!newName) return alert("Name is required!");

    fetch(CAT_UPDATE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ id: id, name: newName })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload(); // Reload to update all dropdowns & tables easily
        } else {
            alert(data.message);
        }
    });
}

function deleteCategory(id) {
    if(!confirm("Are you sure you want to delete this category?")) return;

    var url = CAT_DELETE_URL.replace(':id', id);
    fetch(url, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload(); // Reload to update all dropdowns seamlessly
        } else {
            alert(data.message);
        }
    });
}

// (????? ???? Test Add/Edit/Delete JavaScript ????????? ????? ??????)
function filterTable() {
    var q = (document.getElementById('search-input').value || '').toLowerCase();
    var cat = document.getElementById('filter-category').value;

    document.querySelectorAll('tr.inv-row').forEach(function (r) {
        var matchQ = !q || r.dataset.name.toLowerCase().includes(q);
        var matchC = !cat || r.dataset.category === cat;
        r.style.display = (matchQ && matchC) ? '' : 'none';
    });
}
</script>
@stop