@extends('adminlte::page')

@section('title', 'Reference Person')

@section('content')

<style>
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

.pc-layout { display: grid; grid-template-columns: 380px 1fr; gap: 10px; }
@media (max-width: 900px) { .pc-layout { grid-template-columns: 1fr; } }

.pc-fg { margin-bottom: 8px; }
.pc-fg label { display: block; font-size: 10px; font-weight: 700; color: #444; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 0.3px; }
.pc-fg input, .pc-fg select { width: 100%; border: 1px solid #999; padding: 5px 7px; font-size: 13px; border-radius: 2px; background: #fff; }
.pc-fg input:focus, .pc-fg select:focus { outline: none; border-color: #1a4f8a; background: #f5f9ff; }
.pc-fg .field-note { font-size: 10px; color: #888; margin-top: 2px; }

.btn-pc-submit { background: #558b2f; color: #fff; border: none; padding: 7px 24px; font-size: 12px; font-weight: 700; cursor: pointer; border-radius: 2px; }
.btn-pc-submit:hover { background: #3d6b21; }
.btn-pc-reset  { background: #78909c; color: #fff; border: none; padding: 7px 16px; font-size: 12px; font-weight: 700; cursor: pointer; border-radius: 2px; margin-left: 6px; }
.btn-pc-reset:hover { background: #546e7a; }

hr.pc-hr { border: none; border-top: 1px solid #d0d8e0; margin: 10px 0; }

.pc-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pc-table th { background: #1a4f8a; color: #fff; padding: 5px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
.pc-table td { padding: 5px 8px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
.pc-table tbody tr:nth-child(even) td { background: #f2f7ff; }
.pc-table tbody tr:hover td { background: #e8f0fb; }

.badge-active   { background: #c8e6c9; color: #1b5e20; padding: 2px 8px; font-size: 10px; font-weight: 700; border-radius: 2px; }
.badge-inactive { background: #ffcdd2; color: #b71c1c; padding: 2px 8px; font-size: 10px; font-weight: 700; border-radius: 2px; }

.btn-edit  { background: #1565c0; color: #fff; border: none; padding: 2px 10px; font-size: 11px; border-radius: 2px; cursor: pointer; }
.btn-edit:hover { background: #0d47a1; }
.btn-del   { background: #c62828; color: #fff; border: none; padding: 2px 10px; font-size: 11px; border-radius: 2px; cursor: pointer; margin-left: 4px; }
.btn-del:hover  { background: #b71c1c; }

.pc-footer { background: #1a4f8a; color: #aac6e8; text-align: center; padding: 6px; font-size: 10px; margin-top: 12px; }

.pc-modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1050; align-items:center; justify-content:center; }
.pc-modal-backdrop.show { display:flex; }
.pc-modal { background:#fff; border-top:3px solid #1a4f8a; width:440px; max-width:95vw; border-radius:2px; }
.pc-modal-head { background:#dce8f5; border-bottom:1px solid #b0bec5; padding:6px 12px; font-size:11px; font-weight:700; color:#1a4f8a; text-transform:uppercase; display:flex; justify-content:space-between; align-items:center; }
.pc-modal-body { padding:14px; }
.pc-modal-close { background:none; border:none; font-size:18px; cursor:pointer; color:#555; line-height:1; }
</style>

<div class="pc-wrap">

    {{-- TOP BAR --}}
    <div class="pc-topbar">
        <div style="display:flex;align-items:center;gap:10px">
            <div class="pc-topbar-logo">PROF<br>CLINIC</div>
            <div>
                <h1>Professor Clinic — Settings</h1>
                <small>Reference Person Management</small>
            </div>
        </div>
        <div class="pc-date-badge">{{ date('d M, Y') }}</div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="pc-alert-success">&#10003; &nbsp;{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="pc-alert-danger">&#9888; &nbsp;{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="pc-alert-danger">
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="pc-layout">

        {{-- LEFT: ADD FORM --}}
        <div>
            <div class="pc-card">
                <div class="pc-card-head">&#9312; Add Reference Person</div>
                <div class="pc-card-body">
                    <form method="POST" action="{{ route('settings.referenceperson.store') }}">
                        @csrf

                        <div class="pc-fg">
                            <label>Code <span style="color:#b71c1c">*</span></label>
                            <input type="text" name="Code" maxlength="25"
                                   value="{{ old('Code') }}"
                                   placeholder="e.g. RP-001" required>
                            <div class="field-note">Unique code, max 25 characters</div>
                        </div>

                        <div class="pc-fg">
                            <label>Reference Type <span style="color:#b71c1c">*</span></label>
                            <select name="ref_type" required>
                                <option value="">— Select Type —</option>
                                <option value="OfficeEmployee" {{ old('ref_type') == 'OfficeEmployee' ? 'selected' : '' }}>Office Employee</option>
                                <option value="PCNurse"        {{ old('ref_type') == 'PCNurse'        ? 'selected' : '' }}>PC Nurse</option>
                                <option value="MidWife"        {{ old('ref_type') == 'MidWife'        ? 'selected' : '' }}>Mid Wife</option>
                                <option value="Other"          {{ old('ref_type') == 'Other'          ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="pc-fg">
                            <label>Full Name <span style="color:#b71c1c">*</span></label>
                            <input type="text" name="Name" maxlength="25"
                                   value="{{ old('Name') }}"
                                   placeholder="Enter full name" required>
                        </div>

                        <div class="pc-fg">
                            <label>Mobile Number <span style="color:#b71c1c">*</span></label>
                            <input type="text" name="Mobile" maxlength="15"
                                   value="{{ old('Mobile') }}"
                                   placeholder="01XXXXXXXXX" required>
                        </div>

                        <hr class="pc-hr">
                        <button type="submit" class="btn-pc-submit">&#43; Save Record</button>
                        <button type="reset"  class="btn-pc-reset">Reset</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: LIST TABLE --}}
        <div>
            <div class="pc-card">
                <div class="pc-card-head">
                    &#9313; Reference Person List
                    <span style="float:right;background:#1a4f8a;color:#fff;padding:1px 8px;border-radius:2px;font-size:11px">
                        {{ $refs->count() }} records
                    </span>
                </div>
                <div class="pc-card-body" style="padding:0">
                    <div style="overflow-x:auto;max-height:520px;overflow-y:auto">
                        <table class="pc-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refs as $i => $r)
                                <tr>
                                    <td style="color:#888">{{ $i + 1 }}</td>
                                    <td style="font-weight:700;color:#1a4f8a">{{ $r->Code }}</td>
                                    <td>{{ $r->ref_type }}</td>
                                    <td style="font-weight:600">{{ $r->Name }}</td>
                                    <td>{{ $r->Mobile }}</td>
                                    <td>
                                        @if($r->active)
                                            <span class="badge-active">Active</span>
                                        @else
                                            <span class="badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap">
                                        <button class="btn-edit"
                                            onclick="openEdit({{ $r->ID }},'{{ addslashes($r->Code) }}','{{ $r->ref_type }}','{{ addslashes($r->Name) }}','{{ addslashes($r->Mobile) }}',{{ $r->active }})">
                                            Edit
                                        </button>
                                        <form method="POST"
                                              action="{{ route('settings.referenceperson.destroy', $r->ID) }}"
                                              style="display:inline"
                                              onsubmit="return confirm('Delete {{ addslashes($r->Name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-del">Del</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:28px;color:#888;font-size:12px">
                                        No reference persons found. Add one from the form.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pc-footer">Professor Clinic — Settings &nbsp;|&nbsp; Reference Person Module</div>
</div>

{{-- EDIT MODAL --}}
<div class="pc-modal-backdrop" id="editModal">
    <div class="pc-modal">
        <div class="pc-modal-head">
            <span>&#9998; Edit Reference Person</span>
            <button class="pc-modal-close" onclick="closeEdit()">&times;</button>
        </div>
        <div class="pc-modal-body">
            <form method="POST" id="editForm">
                @csrf @method('PUT')

                <div class="pc-fg">
                    <label>Code <span style="color:#b71c1c">*</span></label>
                    <input type="text" name="Code" id="edit_code" maxlength="25" required>
                    <div class="field-note">Unique code, max 25 characters</div>
                </div>

                <div class="pc-fg">
                    <label>Reference Type <span style="color:#b71c1c">*</span></label>
                    <select name="ref_type" id="edit_ref_type" required>
                        <option value="OfficeEmployee">Office Employee</option>
                        <option value="PCNurse">PC Nurse</option>
                        <option value="MidWife">Mid Wife</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="pc-fg">
                    <label>Full Name <span style="color:#b71c1c">*</span></label>
                    <input type="text" name="Name" id="edit_name" maxlength="25" required>
                </div>

                <div class="pc-fg">
                    <label>Mobile Number <span style="color:#b71c1c">*</span></label>
                    <input type="text" name="Mobile" id="edit_mobile" maxlength="15" required>
                </div>

                <div class="pc-fg" style="display:flex;align-items:center;gap:8px;margin-top:6px">
                    <input type="checkbox" name="active" id="edit_active" value="1" style="width:auto;margin:0">
                    <label for="edit_active" style="margin:0;font-size:12px;font-weight:700;text-transform:uppercase;color:#444;cursor:pointer">
                        Active
                    </label>
                </div>

                <hr class="pc-hr">
                <button type="submit" class="btn-pc-submit">&#10003; Update Record</button>
                <button type="button" class="btn-pc-reset" onclick="closeEdit()">Cancel</button>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
function openEdit(id, code, type, name, mobile, active) {
    document.getElementById('editForm').action =
        "{{ url('settings/referenceperson/update') }}/" + id;
    document.getElementById('edit_code').value     = code;
    document.getElementById('edit_ref_type').value = type;
    document.getElementById('edit_name').value     = name;
    document.getElementById('edit_mobile').value   = mobile;
    document.getElementById('edit_active').checked = (active == 1);
    document.getElementById('editModal').classList.add('show');
}

function closeEdit() {
    document.getElementById('editModal').classList.remove('show');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
@endpush

@endsection