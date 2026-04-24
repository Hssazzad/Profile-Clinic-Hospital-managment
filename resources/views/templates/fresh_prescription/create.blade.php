@extends('adminlte::page')

@section('title', 'Add Medicine')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 page-main-title">
                <span class="page-title-icon"><i class="fas fa-pills"></i></span>
                Add Medicine
            </h1>
            <ol class="breadcrumb mt-1 p-0" style="background:transparent;font-size:12px;">
                <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                <li class="breadcrumb-item active">Add Medicine</li>
            </ol>
        </div>
    </div>
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   ROOT
══════════════════════════════════════════ */
:root {
    --primary:      #5c6bc0;
    --primary-dark: #3949ab;
    --primary-light:#e8eaf6;
    --success:      #43a047;
    --success-light:#e8f5e9;
    --danger:       #e53935;
    --danger-light: #ffebee;
    --warning:      #fb8c00;
    --warning-light:#fff3e0;
    --text:         #1a2332;
    --muted:        #6b7a90;
    --border:       #e4e9f0;
    --bg:           #f4f6fb;
    --white:        #ffffff;
    --radius-sm:    6px;
    --radius-md:    10px;
    --radius-lg:    14px;
    --shadow-sm:    0 1px 4px rgba(0,0,0,.06);
    --shadow-md:    0 4px 16px rgba(0,0,0,.09);
    --font:         'DM Sans','Hind Siliguri',Arial,sans-serif;
}
*,*::before,*::after { box-sizing:border-box; }
body,.content-wrapper { background:var(--bg) !important; font-family:var(--font); }

/* ══════════════════════════════════════════
   PAGE HEADER
══════════════════════════════════════════ */
.page-main-title { font-size:21px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:10px; }
.page-title-icon { width:36px;height:36px;border-radius:9px;background:var(--primary-light);display:inline-flex;align-items:center;justify-content:center;color:var(--primary);font-size:16px; }

/* ══════════════════════════════════════════
   MODERN CARD
══════════════════════════════════════════ */
.mod-card { background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-md);border:1px solid var(--border);overflow:hidden;margin-bottom:22px; }
.mod-card-header { padding:14px 20px;border-bottom:1px solid var(--border);background:#fafbff;display:flex;align-items:center;justify-content:space-between; }
.mod-card-title { font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px; }
.mod-card-title i { color:var(--primary); }
.mod-card-body { padding:20px; }

/* ══════════════════════════════════════════
   TEMPLATE SELECT
══════════════════════════════════════════ */
.template-select-wrap { position:relative; }
.template-select-wrap .select2-container { width:100% !important; }
.select2-container--default .select2-selection--single {
    height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);
    display:flex;align-items:center;
}
.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--primary);box-shadow:0 0 0 3px rgba(92,107,192,.12); }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:40px;padding-left:12px;color:var(--text);font-size:14px; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:38px; }
.select2-dropdown { border:1.5px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-md); }
.select2-container--default .select2-results__option--highlighted { background:var(--primary-light);color:var(--primary-dark); }

/* ══════════════════════════════════════════
   STEP NAV
══════════════════════════════════════════ */
.step-nav-pill {
    display:inline-flex;align-items:center;gap:8px;
    background:var(--primary);color:#fff;
    border:none;border-radius:20px;padding:7px 18px;
    font-size:13px;font-weight:600;cursor:default;
    box-shadow:0 2px 8px rgba(92,107,192,.25);
}
.step-nav-pill .count-dot {
    background:rgba(255,255,255,.25);border-radius:12px;
    padding:2px 9px;font-size:12px;font-weight:700;min-width:28px;text-align:center;
}

/* ══════════════════════════════════════════
   FORM FIELDS
══════════════════════════════════════════ */
.field-group { margin-bottom:15px; }
.field-label { display:block;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px; }
.field-input {
    width:100%;padding:9px 12px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);font-size:13.5px;color:var(--text);
    background:var(--white);transition:border-color .2s,box-shadow .2s;
    outline:none;font-family:var(--font);
}
.field-input:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(92,107,192,.1); }
.field-input:read-only { background:#f8f9fb;color:var(--muted); }
select.field-input { cursor:pointer; }

/* ══════════════════════════════════════════
   CUSTOM MEDICINE AUTOCOMPLETE
══════════════════════════════════════════ */
.med-search-wrap { position:relative; }
.med-search-input {
    width:100%;padding:9px 36px 9px 38px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    font-size:13.5px;color:var(--text);background:var(--white);
    outline:none;font-family:var(--font);transition:border-color .2s,box-shadow .2s;
}
.med-search-input:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(92,107,192,.1); }
.med-search-icon { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#aab;font-size:14px;pointer-events:none; }
.med-search-clear { position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#aab;font-size:13px;cursor:pointer;display:none;background:none;border:none;padding:2px 4px;line-height:1; }
.med-search-clear:hover { color:var(--danger); }

.med-dropdown {
    position:absolute;top:calc(100% + 4px);left:0;right:0;
    background:var(--white);border:1.5px solid var(--border);
    border-radius:var(--radius-md);box-shadow:var(--shadow-md);
    z-index:9999;max-height:260px;overflow-y:auto;display:none;
}
.med-dropdown.open { display:block; }
.med-dropdown-item {
    padding:10px 14px;cursor:pointer;font-size:13px;
    border-bottom:1px solid var(--border);transition:background .12s;
    display:flex;align-items:center;justify-content:space-between;
}
.med-dropdown-item:last-child { border-bottom:none; }
.med-dropdown-item:hover,.med-dropdown-item.active { background:var(--primary-light); }
.med-dropdown-item .med-item-name { font-weight:600;color:var(--text); }
.med-dropdown-item .med-item-name mark { background:var(--primary-light);color:var(--primary-dark);border-radius:3px;padding:0 2px; }
.med-dropdown-item .med-item-cat { font-size:11px;color:var(--muted);background:var(--bg);border-radius:4px;padding:1px 7px;white-space:nowrap; }
.med-dropdown-empty { padding:18px;text-align:center;color:var(--muted);font-size:13px; }
.med-dropdown-empty i { font-size:20px;display:block;margin-bottom:6px;opacity:.5; }

/* ══════════════════════════════════════════
   DOSE PILLS
══════════════════════════════════════════ */
.dose-row { display:flex;gap:10px;align-items:center; }
.dose-col { flex:1;text-align:center; }
.dose-label { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:5px;display:block; }
.dose-select-wrap { position:relative; }
.dose-select {
    width:100%;padding:7px 10px;border:1.5px solid var(--border);
    border-radius:var(--radius-sm);font-size:13px;color:var(--text);
    background:var(--white);outline:none;cursor:pointer;text-align:center;
    transition:border-color .18s;font-family:var(--font);appearance:none;
}
.dose-select:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(92,107,192,.1); }
.dose-select.has-value { border-color:var(--primary);background:var(--primary-light);color:var(--primary-dark);font-weight:600; }
.dose-divider { font-size:18px;font-weight:300;color:var(--border);line-height:1;padding-top:20px; }

/* ══════════════════════════════════════════
   BUTTONS
══════════════════════════════════════════ */
.btn-add-med {
    width:100%;padding:10px 16px;border:none;border-radius:var(--radius-sm);
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff;font-size:14px;font-weight:600;cursor:pointer;
    transition:all .2s;display:flex;align-items:center;justify-content:center;gap:7px;
    box-shadow:0 3px 10px rgba(92,107,192,.28);font-family:var(--font);
}
.btn-add-med:hover { transform:translateY(-1px);box-shadow:0 5px 16px rgba(92,107,192,.36); }
.btn-add-med:disabled { opacity:.6;cursor:not-allowed;transform:none; }

.btn-sm-action {
    border:none;border-radius:5px;padding:5px 10px;font-size:12px;cursor:pointer;
    font-weight:600;display:inline-flex;align-items:center;gap:4px;transition:all .18s;
}
.btn-edit-med  { background:var(--warning-light);color:var(--warning); }
.btn-edit-med:hover  { background:var(--warning);color:#fff; }
.btn-del-med   { background:var(--danger-light);color:var(--danger); }
.btn-del-med:hover   { background:var(--danger);color:#fff; }

/* ══════════════════════════════════════════
   MEDICINE TABLE (right side)
══════════════════════════════════════════ */
.med-result-table { width:100%;border-collapse:collapse; }
.med-result-table thead th {
    background:#f4f6fb;color:var(--text);font-size:11px;
    font-weight:700;text-transform:uppercase;letter-spacing:.5px;
    padding:9px 12px;border-bottom:2px solid var(--border);white-space:nowrap;
    text-align:center;
}
.med-result-table thead th:first-child,.med-result-table thead th:nth-child(2) { text-align:left; }
.med-result-table tbody tr { transition:background .12s; }
.med-result-table tbody tr:hover { background:#f8f9ff; }
.med-result-table tbody td {
    padding:9px 12px;border-bottom:1px solid var(--border);
    font-size:13px;color:var(--text);vertical-align:middle;text-align:center;
}
.med-result-table tbody td:first-child,.med-result-table tbody td:nth-child(2) { text-align:left; }
.med-result-table tbody tr:last-child td { border-bottom:none; }

.dose-cell {
    display:inline-flex;align-items:center;gap:3px;
    background:var(--primary-light);color:var(--primary-dark);
    border-radius:12px;padding:2px 10px;font-size:12px;font-weight:600;
}
.meal-badge { border-radius:12px;padding:2px 10px;font-size:11.5px;font-weight:600; }
.meal-before { background:#fff3e0;color:#e65100; }
.meal-after  { background:#e8f5e9;color:#2e7d32; }
.meal-with   { background:#e3f2fd;color:#1565c0; }
.meal-empty  { background:#f3e5f5;color:#6a1b9a; }

.dur-badge { background:var(--bg);color:var(--muted);border-radius:12px;padding:2px 9px;font-size:11.5px;font-weight:600;border:1px solid var(--border); }

.empty-table-state { text-align:center;padding:32px;color:var(--muted); }
.empty-table-state i { font-size:32px;opacity:.4;display:block;margin-bottom:8px; }
.empty-table-state span { font-size:13px; }

/* ══════════════════════════════════════════
   ALERT
══════════════════════════════════════════ */
#alertMessage .alert {
    border-radius:var(--radius-md);border:none;box-shadow:var(--shadow-sm);
    font-size:13.5px;font-weight:500;display:flex;align-items:center;gap:8px;
}

/* ══════════════════════════════════════════
   EDIT MODAL
══════════════════════════════════════════ */
.modal-content { border:none;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.16); }
.modal-header-edit {
    background:linear-gradient(135deg,var(--warning),#ef6c00);
    padding:16px 20px;display:flex;align-items:center;justify-content:space-between;
}
.modal-header-edit .modal-title-text { color:#fff;font-weight:700;font-size:15px;display:flex;align-items:center;gap:8px; }
.modal-header-edit .btn-close-modal { background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s; }
.modal-header-edit .btn-close-modal:hover { background:rgba(255,255,255,.35); }
.modal-body-edit { padding:22px; }
.modal-footer-edit { padding:14px 20px;background:#fafbff;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px; }
.btn-modal-cancel { background:#fff;border:1.5px solid var(--border);color:var(--text);border-radius:var(--radius-sm);padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .18s; }
.btn-modal-cancel:hover { background:var(--bg); }
.btn-modal-update { background:linear-gradient(135deg,var(--warning),#ef6c00);color:#fff;border:none;border-radius:var(--radius-sm);padding:8px 20px;font-size:13px;font-weight:600;cursor:pointer;transition:all .18s;display:flex;align-items:center;gap:6px; }
.btn-modal-update:hover { opacity:.9; }
.btn-modal-update:disabled { opacity:.6;cursor:not-allowed; }

/* ══════════════════════════════════════════
   SECTION DIVIDER
══════════════════════════════════════════ */
.sec-divider { border:none;border-top:1.5px solid var(--border);margin:16px 0; }
.sec-sub-title { font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
.sec-sub-title i { font-size:11px; }
</style>
@stop

@section('content')
<section class="content">
<div class="container-fluid">

    {{-- Alert --}}
    <div id="alertMessage" class="mb-3"></div>

    {{-- Template Selection --}}
    <div class="mod-card mb-3">
        <div class="mod-card-body" style="padding:16px 20px;">
            <div class="field-group mb-0">
                <label class="field-label"><i class="fas fa-file-medical mr-1"></i> Select Template <span class="text-danger">*</span></label>
                <div class="template-select-wrap">
                    <select id="templateid" class="select2" style="width:100%">
                        <option value="">— Choose a template —</option>
                        @foreach($templates as $temp)
                            <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Step nav --}}
    <div id="stepNavigation" style="display:none;" class="mb-3">
        <button type="button" class="step-nav-pill">
            <i class="fas fa-file-medical"></i> Fresh Prescription
            <span class="count-dot" id="admitCount">0</span>
        </button>
    </div>

    {{-- Main Two-Column --}}
    <div class="row" id="medicineSection" style="display:none;">

        {{-- LEFT: Form --}}
        <div class="col-lg-5 mb-3">
            <div class="mod-card" style="height:100%">
                <div class="mod-card-header">
                    <div class="mod-card-title">
                        <i class="fas fa-plus-circle"></i> Add Medicine
                    </div>
                </div>
                <div class="mod-card-body">

                    {{-- Medicine Name — custom autocomplete --}}
                    <div class="field-group">
                        <label class="field-label"><i class="fas fa-capsules mr-1"></i> Medicine Name <span class="text-danger">*</span></label>
                        <div class="med-search-wrap">
                            <i class="fas fa-search med-search-icon"></i>
                            <input type="text" id="medicine_name" class="med-search-input"
                                   placeholder="Type to search medicine..." autocomplete="off">
                            <button class="med-search-clear" id="medSearchClear" title="Clear">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="med-dropdown" id="medDropdown"></div>
                        </div>
                    </div>

                    {{-- Company --}}
                    <div class="field-group">
                        <label class="field-label">Company / Brand</label>
                        <div class="med-search-wrap">
                            <i class="fas fa-building med-search-icon" style="font-size:12px;"></i>
                            <input type="text" id="medicine_company" class="med-search-input"
                                   placeholder="e.g. Square, Beximco..." autocomplete="off">
                            <button class="med-search-clear" id="compSearchClear" title="Clear">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="med-dropdown" id="compDropdown"></div>
                        </div>
                    </div>

                    <hr class="sec-divider">
                    <div class="sec-sub-title"><i class="fas fa-clock"></i> Dosage Schedule</div>

                    {{-- Dose row --}}
                    <div class="dose-row mb-3">
                        <div class="dose-col">
                            <span class="dose-label">☀ সকাল</span>
                            <select id="medicine_morning" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                                <option value="26u">26u</option>
                                <option value="30u">30u</option>
                            </select>
                        </div>
                        <div class="dose-divider">+</div>
                        <div class="dose-col">
                            <span class="dose-label">🌤 দুপুর</span>
                            <select id="medicine_noon" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                        <div class="dose-divider">+</div>
                        <div class="dose-col">
                            <span class="dose-label">🌙 রাত</span>
                            <select id="medicine_night" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                                <option value="26u">26u</option>
                                <option value="30u">30u</option>
                            </select>
                        </div>
                    </div>

                    <hr class="sec-divider">
                    <div class="sec-sub-title"><i class="fas fa-utensils"></i> Meal & Duration</div>

                    <div class="row">
                        <div class="col-6">
                            <div class="field-group">
                                <label class="field-label">আহারের</label>
                                <select id="medicine_meal_timing" class="field-input">
                                    <option value="">— Select —</option>
                                    <option value="before">আগে (Before)</option>
                                    <option value="after">পরে (After)</option>
                                    <option value="with">সাথে (With)</option>
                                    <option value="empty">খালি পেটে</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field-group">
                                <label class="field-label">কতদিন?</label>
                                <div class="d-flex gap-2" style="gap:6px;">
                                    <input type="number" id="medicine_duration_num" class="field-input" placeholder="0" min="0" style="width:65px;flex-shrink:0;">
                                    <select id="medicine_duration_type" class="field-input" style="flex:1;">
                                        <option value="">—</option>
                                        <option value="দিন">দিন</option>
                                        <option value="মাস">মাস</option>
                                        <option value="চলবে">চলবে</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="sec-divider">
                    <div class="sec-sub-title"><i class="fas fa-route"></i> Route & Instructions</div>

                    <div class="row">
                        <div class="col-6">
                            <div class="field-group">
                                <label class="field-label">Route</label>
                                <select id="medicine_route" class="field-input">
                                    <option value="Oral">Oral (মুখে)</option>
                                    <option value="IV">IV (শিরায়)</option>
                                    <option value="IM">IM (মাংসে)</option>
                                    <option value="SC">SC (চামড়ার নিচে)</option>
                                    <option value="Topical">Topical</option>
                                    <option value="Inhalation">Inhalation</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Gel">Gel</option>
                                    <option value="Injection">Injection</option>
                                    <option value="Eye Drop">Eye Drop</option>
                                    <option value="Ear Drop">Ear Drop</option>
                                    <option value="Nasal Spray">Nasal Spray</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field-group">
                                <label class="field-label">Instructions</label>
                                <select id="medicine_instruction" class="field-input">
                                    <option value="">— None —</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="After Food">After Food</option>
                                    <option value="Empty Stomach">Empty Stomach</option>
                                    <option value="With Food">With Food</option>
                                    <option value="At Bed Time">At Bed Time</option>
                                    <option value="With Water">With Water</option>
                                    <option value="With Milk">With Milk</option>
                                    <option value="As Directed">As Directed</option>
                                    <option value="Chew Before Swallow">Chew Before Swallow</option>
                                    <option value="Do Not Crush">Do Not Crush</option>
                                    <option value="Swallow Whole">Swallow Whole</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-2">
                        <button type="button" class="btn-add-med" id="btnAddMedicine">
                            <i class="fas fa-plus"></i> Add Medicine
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- RIGHT: Table --}}
        <div class="col-lg-7 mb-3">
            <div class="mod-card" style="height:100%">
                <div class="mod-card-header">
                    <div class="mod-card-title">
                        <i class="fas fa-list-ul"></i> Added Medicines
                    </div>
                    <span style="background:var(--primary-light);color:var(--primary-dark);border-radius:12px;padding:3px 12px;font-size:12px;font-weight:700;" id="medCountPill">0 items</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="med-result-table" id="medicineTableEl">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Medicine</th>
                                <th>সকাল</th>
                                <th>দুপুর</th>
                                <th>রাত</th>
                                <th>আহার</th>
                                <th>কতদিন</th>
                                <th style="width:90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="medicineBody">
                            <tr>
                                <td colspan="8">
                                    <div class="empty-table-state">
                                        <i class="fas fa-pills"></i>
                                        <span>No medicines added yet</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /.row --}}

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header-edit">
                    <div class="modal-title-text">
                        <i class="fas fa-edit"></i> Edit Medicine
                    </div>
                    <button type="button" class="btn-close-modal" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body-edit">
                    <input type="hidden" id="edit_medicine_id">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="field-group">
                                <label class="field-label">Medicine Name <span class="text-danger">*</span></label>
                                <div class="med-search-wrap">
                                    <i class="fas fa-search med-search-icon"></i>
                                    <input type="text" id="edit_medicine" class="med-search-input"
                                           placeholder="Type to search..." autocomplete="off">
                                    <div class="med-dropdown" id="editMedDropdown"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label">Route</label>
                                <select id="edit_route" class="field-input">
                                    <option value="Oral">Oral</option>
                                    <option value="IV">IV</option>
                                    <option value="IM">IM</option>
                                    <option value="SC">SC</option>
                                    <option value="Topical">Topical</option>
                                    <option value="Inhalation">Inhalation</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Injection">Injection</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="dose-row mb-3">
                        <div class="dose-col">
                            <span class="dose-label">☀ সকাল</span>
                            <select id="edit_morning" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                        <div class="dose-divider">+</div>
                        <div class="dose-col">
                            <span class="dose-label">🌤 দুপুর</span>
                            <select id="edit_noon" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                        <div class="dose-divider">+</div>
                        <div class="dose-col">
                            <span class="dose-label">🌙 রাত</span>
                            <select id="edit_night" class="dose-select" onchange="markDoseChanged(this)">
                                <option value="">—</option>
                                <option value="0">0</option>
                                <option value="1/2">½</option>
                                <option value="1">1</option>
                                <option value="1+1/2">1½</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label">আহারের</label>
                                <select id="edit_meal_timing" class="field-input">
                                    <option value="">— Select —</option>
                                    <option value="before">Before</option>
                                    <option value="after">After</option>
                                    <option value="with">With</option>
                                    <option value="empty">Empty Stomach</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label">Duration</label>
                                <div class="d-flex" style="gap:6px;">
                                    <input type="number" id="edit_duration_num" class="field-input" placeholder="0" min="0" style="width:65px;flex-shrink:0;">
                                    <select id="edit_duration_type" class="field-input">
                                        <option value="">—</option>
                                        <option value="দিন">দিন</option>
                                        <option value="মাস">মাস</option>
                                        <option value="চলবে">চলবে</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label">Instructions</label>
                                <select id="edit_instruction" class="field-input">
                                    <option value="">— None —</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="After Food">After Food</option>
                                    <option value="With Food">With Food</option>
                                    <option value="At Bed Time">At Bed Time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-edit">
                    <button type="button" class="btn-modal-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn-modal-update" id="btnUpdateMedicine">
                        <i class="fas fa-save"></i> Update Medicine
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
</section>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ══════════════════════════════════════════════════════
   STATIC DATA
══════════════════════════════════════════════════════ */
var MEDICINE_LIST = [
    'Almivas 40','Rosu 5mg','Lopimol 75mg','Sevel 800','Syndopa 110',
    'Vulex CR 200','Gensulin 30/70 100','Oxut 20','Quiot XR 200','Rusagyl 500mg',
    'Metformin 500mg','Amlodipine 5mg','Atorvastatin 10mg','Omeprazole 20mg',
    'Pantoprazole 40mg','Clopidogrel 75mg','Aspirin 75mg','Lisinopril 5mg',
    'Enalapril 5mg','Losartan 50mg','Valsartan 80mg','Bisoprolol 5mg',
    'Carvedilol 6.25mg','Furosemide 40mg','Spironolactone 25mg','Digoxin 0.25mg',
    'Warfarin 5mg','Insulin Regular','Insulin Glargine','Glibenclamide 5mg',
    'Glimepiride 2mg','Sitagliptin 50mg','Levodopa/Carbidopa','Pramipexole 0.5mg',
    'Amantadine 100mg','Donepezil 5mg','Memantine 10mg','Quetiapine 25mg',
    'Clonazepam 0.5mg','Pregabalin 75mg','Gabapentin 300mg','Carbamazepine 200mg',
    'Valproate 200mg','Phenytoin 100mg','Levetiracetam 500mg','Amitriptyline 10mg',
    'Escitalopram 10mg','Sertraline 50mg','Fluoxetine 20mg','Methotrexate 2.5mg',
    'Hydroxychloroquine 200mg','Prednisolone 5mg','Dexamethasone 4mg',
    'Methylprednisolone 4mg','Ciprofloxacin 500mg','Amoxicillin 500mg',
    'Azithromycin 500mg','Cefixime 200mg','Metronidazole 400mg',
    'Fluconazole 150mg','Calcium + Vit D3','Folic Acid 5mg',
    'Ferrous Sulphate 200mg','Vitamin B Complex','Vitamin C 500mg','Zinc 20mg',
    'Paracetamol 500mg','Ibuprofen 400mg','Diclofenac 50mg','Tramadol 50mg',
    'Ranitidine 150mg','Domperidone 10mg','Ondansetron 4mg','Promethazine 25mg'
];

var COMPANY_LIST = [
    'Square','Beximco','Incepta','Opsonin','ACI','Eskayef','Renata',
    'General Pharma','Drug International','Novo Nordisk','Sanofi',
    'Aristopharma','Acme','Healthcare Pharma','Orion Pharma'
];

var medicines = [];
var CSRF_TOKEN = '{{ csrf_token() }}';

/* ══════════════════════════════════════════════════════
   CUSTOM AUTOCOMPLETE ENGINE
══════════════════════════════════════════════════════ */
function buildAutocomplete(inputId, dropdownId, dataList, clearBtnId) {
    var input    = document.getElementById(inputId);
    var dropdown = document.getElementById(dropdownId);
    var clearBtn = clearBtnId ? document.getElementById(clearBtnId) : null;
    var activeIdx = -1;

    if (!input || !dropdown) return;

    function highlight(text, query) {
        if (!query) return '<span class="med-item-name">' + esc(text) + '</span>';
        var re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return '<span class="med-item-name">' + esc(text).replace(re, '<mark>$1</mark>') + '</span>';
    }

    function showDropdown(q) {
        var filtered = dataList.filter(function(item) {
            return item.toLowerCase().includes(q.toLowerCase());
        }).slice(0, 12);

        if (!filtered.length) {
            dropdown.innerHTML = '<div class="med-dropdown-empty"><i class="fas fa-search"></i>No results for "' + esc(q) + '"</div>';
        } else {
            dropdown.innerHTML = filtered.map(function(item, i) {
                return '<div class="med-dropdown-item" data-val="' + esc(item) + '" data-i="' + i + '">' +
                    highlight(item, q) +
                '</div>';
            }).join('');
        }
        dropdown.classList.add('open');
        activeIdx = -1;

        // Click on item
        dropdown.querySelectorAll('.med-dropdown-item').forEach(function(el) {
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                input.value = el.dataset.val;
                hideDropdown();
                if (clearBtn) clearBtn.style.display = 'block';
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    }

    function hideDropdown() {
        dropdown.classList.remove('open');
        dropdown.innerHTML = '';
        activeIdx = -1;
    }

    input.addEventListener('input', function() {
        var q = this.value.trim();
        if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
        if (q.length >= 1) showDropdown(q);
        else hideDropdown();
    });

    input.addEventListener('keydown', function(e) {
        var items = dropdown.querySelectorAll('.med-dropdown-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, items.length - 1);
            items.forEach(function(el, i) { el.classList.toggle('active', i === activeIdx); });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
            items.forEach(function(el, i) { el.classList.toggle('active', i === activeIdx); });
        } else if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            input.value = items[activeIdx].dataset.val;
            if (clearBtn) clearBtn.style.display = 'block';
            hideDropdown();
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    input.addEventListener('focus', function() {
        var q = this.value.trim();
        if (q.length >= 1) showDropdown(q);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) hideDropdown();
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            this.style.display = 'none';
            hideDropdown();
            input.focus();
        });
    }
}

function esc(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m];
    });
}

/* ══════════════════════════════════════════════════════
   DOSE SELECT HIGHLIGHT
══════════════════════════════════════════════════════ */
function markDoseChanged(sel) {
    if (sel.value) sel.classList.add('has-value');
    else sel.classList.remove('has-value');
}

/* ══════════════════════════════════════════════════════
   MEDICINE TABLE RENDER
══════════════════════════════════════════════════════ */
function updateMedicineTable() {
    var tbody = document.getElementById('medicineBody');
    var pill  = document.getElementById('medCountPill');
    document.getElementById('admitCount').textContent = medicines.length;
    if (pill) pill.textContent = medicines.length + ' item' + (medicines.length !== 1 ? 's' : '');

    if (!medicines.length) {
        tbody.innerHTML = '<tr><td colspan="8"><div class="empty-table-state"><i class="fas fa-pills"></i><span>No medicines added yet</span></div></td></tr>';
        return;
    }

    var mealMap = { before:'Before', after:'After', with:'With', empty:'Empty Stomach' };
    var mealClass = { before:'meal-before', after:'meal-after', with:'meal-with', empty:'meal-empty' };

    tbody.innerHTML = medicines.map(function(med, i) {
        var duration = med.duration || '';
        if (!duration && med.duration_num && med.duration_type) duration = med.duration_num + ' ' + med.duration_type;

        var mealLabel = mealMap[med.meal_timing] || (med.meal_timing || '—');
        var mealCls   = mealClass[med.meal_timing] || '';

        var doseCell = function(val) {
            return val ? '<span class="dose-cell">' + esc(val) + '</span>' : '<span style="color:#ccc;">—</span>';
        };

        return '<tr>' +
            '<td><span style="color:var(--muted);font-size:12px;">' + (i + 1) + '</span></td>' +
            '<td>' +
                '<strong style="font-size:13.5px;">' + esc(med.name) + '</strong>' +
                (med.company ? '<br><small style="color:var(--muted);font-size:11px;">' + esc(med.company) + '</small>' : '') +
            '</td>' +
            '<td>' + doseCell(med.morning) + '</td>' +
            '<td>' + doseCell(med.noon) + '</td>' +
            '<td>' + doseCell(med.night) + '</td>' +
            '<td>' + (med.meal_timing ? '<span class="meal-badge ' + mealCls + '">' + esc(mealLabel) + '</span>' : '<span style="color:#ccc;">—</span>') + '</td>' +
            '<td>' + (duration ? '<span class="dur-badge">' + esc(duration) + '</span>' : '<span style="color:#ccc;">—</span>') + '</td>' +
            '<td>' +
                '<div style="display:flex;gap:5px;justify-content:center;">' +
                '<button class="btn-sm-action btn-edit-med" onclick="editMedicine(' + i + ')" title="Edit"><i class="fas fa-edit"></i></button>' +
                '<button class="btn-sm-action btn-del-med" onclick="removeMedicine(' + i + ')" title="Delete"><i class="fas fa-trash"></i></button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }).join('');
}

/* ══════════════════════════════════════════════════════
   ADD MEDICINE
══════════════════════════════════════════════════════ */
function addMedicine() {
    var templateid = $('#templateid').val();
    var medicineName = document.getElementById('medicine_name').value.trim();

    if (!templateid) { showAlert('Please select a template first.', 'error'); return; }
    if (!medicineName) { showAlert('Please enter medicine name.', 'error'); return; }

    var morning = document.getElementById('medicine_morning').value;
    var noon    = document.getElementById('medicine_noon').value;
    var night   = document.getElementById('medicine_night').value;
    var dosage  = (morning || '-') + '+' + (noon || '-') + '+' + (night || '-');

    var dnum  = document.getElementById('medicine_duration_num').value;
    var dtype = document.getElementById('medicine_duration_type').value;
    var duration = '';
    if (dtype === 'চলবে') duration = 'চলবে';
    else if (dnum && dtype) duration = dnum + ' ' + dtype;
    else if (dtype) duration = dtype;

    var btn = document.getElementById('btnAddMedicine');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    $.ajax({
        url : "{{ route('templates.fresh.ajax.store') }}",
        type: 'POST',
        data: {
            _token          : CSRF_TOKEN,
            templateid      : templateid,
            name            : medicineName,
            dosage          : dosage,
            morning         : morning,
            noon            : noon,
            night           : night,
            meal_timing     : document.getElementById('medicine_meal_timing').value,
            duration        : duration,
            duration_num    : dnum,
            duration_type   : dtype,
            route           : document.getElementById('medicine_route').value,
            order_type      : 'fresh prescription',
            instruction     : document.getElementById('medicine_instruction').value,
            company         : document.getElementById('medicine_company').value.trim()
        },
        success: function(res) {
            if (res.ok) {
                showAlert('Medicine added successfully!', 'success');
                clearForm();
                loadExistingMedicines(templateid);
            } else {
                showAlert('Error: ' + res.message, 'error');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error (HTTP ' + xhr.status + ')';
            showAlert('Error: ' + msg, 'error');
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Add Medicine';
        }
    });
}

/* ══════════════════════════════════════════════════════
   LOAD FROM DB
══════════════════════════════════════════════════════ */
function loadMedicinesFromTemplate(templateid) {
    $.ajax({
        url    : "{{ route('templates.medicine.ajax.list') }}",
        type   : 'GET',
        data   : { templateid: templateid },
        success: function(res) {
            if (res.ok && res.rows && res.rows.length) {
                var extra = res.rows.map(function(m) { return m.brand || m.name || ''; }).filter(Boolean);
                extra.forEach(function(n) { if (!MEDICINE_LIST.includes(n)) MEDICINE_LIST.push(n); });
            }
        }
    });
}

function loadExistingMedicines(templateid) {
    $.ajax({
        url    : "{{ route('templates.medicine.ajax.list') }}",
        type   : 'GET',
        data   : { templateid: templateid },
        success: function(res) {
            if (res.ok && res.rows) {
                medicines = res.rows.map(function(med) {
                    return {
                        id           : med.id,
                        name         : med.brand || med.name || 'Unknown',
                        company      : med.company || '',
                        morning      : med.morning || '',
                        noon         : med.noon || '',
                        night        : med.night || '',
                        meal_timing  : med.meal_timing || '',
                        duration     : med.duration || '',
                        duration_num : med.duration_num || '',
                        duration_type: med.duration_type || '',
                        route        : med.route || 'Oral',
                        order_type   : med.order_type || 'fresh prescription',
                        instruction  : med.instruction || ''
                    };
                });
                updateMedicineTable();
            }
        }
    });
}

/* ══════════════════════════════════════════════════════
   EDIT MEDICINE
══════════════════════════════════════════════════════ */
window.editMedicine = function(index) {
    var med = medicines[index];
    if (!med) { showAlert('Medicine not found!', 'error'); return; }

    document.getElementById('edit_medicine_id').value = index;
    document.getElementById('edit_medicine').value    = med.name || '';
    document.getElementById('edit_route').value       = med.route || 'Oral';

    var setVal = function(id, val) {
        var el = document.getElementById(id);
        if (el) { el.value = val || ''; markDoseChanged(el); }
    };
    setVal('edit_morning', med.morning);
    setVal('edit_noon',    med.noon);
    setVal('edit_night',   med.night);

    document.getElementById('edit_meal_timing').value  = med.meal_timing || '';
    document.getElementById('edit_duration_num').value = med.duration_num || '';
    document.getElementById('edit_duration_type').value= med.duration_type || '';
    document.getElementById('edit_instruction').value  = med.instruction || '';

    $('#editMedicineModal').modal('show');
};

window.updateMedicine = function() {
    var index = document.getElementById('edit_medicine_id').value;
    var med   = medicines[index];
    if (!med) { showAlert('Medicine not found!', 'error'); return; }

    var name = document.getElementById('edit_medicine').value.trim();
    if (!name) { showAlert('Medicine name is required!', 'error'); return; }

    var morning = document.getElementById('edit_morning').value;
    var noon    = document.getElementById('edit_noon').value;
    var night   = document.getElementById('edit_night').value;
    var dosage  = (morning || '-') + '+' + (noon || '-') + '+' + (night || '-');
    var dnum    = document.getElementById('edit_duration_num').value;
    var dtype   = document.getElementById('edit_duration_type').value;
    var duration= '';
    if (dtype === 'চলবে') duration = 'চলবে';
    else if (dnum && dtype) duration = dnum + ' ' + dtype;
    else if (dtype) duration = dtype;

    var btn = document.getElementById('btnUpdateMedicine');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    if (med.id) {
        $.ajax({
            url : '/templates/medicine/ajax/' + med.id,
            type: 'POST',
            data: {
                _token      : CSRF_TOKEN,
                _method     : 'PUT',
                name        : name,
                dosage      : dosage,
                morning     : morning,
                noon        : noon,
                night       : night,
                meal_timing : document.getElementById('edit_meal_timing').value,
                duration    : duration,
                duration_num: dnum,
                duration_type:dtype,
                route       : document.getElementById('edit_route').value,
                instruction : document.getElementById('edit_instruction').value,
                order_type  : 'fresh prescription'
            },
            success: function(res) {
                if (res.ok || res.success) {
                    showAlert('Medicine updated!', 'success');
                    $('#editMedicineModal').modal('hide');
                    loadExistingMedicines($('#templateid').val());
                } else {
                    showAlert('Update failed: ' + (res.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error';
                showAlert('Error: ' + msg, 'error');
            },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Update Medicine';
            }
        });
    } else {
        medicines[index] = Object.assign({}, medicines[index], {
            name         : name,
            morning      : morning,
            noon         : noon,
            night        : night,
            meal_timing  : document.getElementById('edit_meal_timing').value,
            duration     : duration,
            duration_num : dnum,
            duration_type: dtype,
            route        : document.getElementById('edit_route').value,
            instruction  : document.getElementById('edit_instruction').value
        });
        updateMedicineTable();
        $('#editMedicineModal').modal('hide');
        showAlert('Medicine updated!', 'success');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Update Medicine';
    }
};

/* ══════════════════════════════════════════════════════
   REMOVE MEDICINE
══════════════════════════════════════════════════════ */
window.removeMedicine = function(index) {
    if (!confirm('Delete this medicine?')) return;
    var med = medicines[index];
    if (med && med.id) {
        $.ajax({
            url : '/templates/medicine/ajax/' + med.id,
            type: 'DELETE',
            data: { _token: CSRF_TOKEN },
            success: function(res) {
                if (res.ok || res.success) {
                    showAlert('Deleted!', 'success');
                    loadExistingMedicines($('#templateid').val());
                } else { showAlert('Delete failed!', 'error'); }
            },
            error: function() { showAlert('Delete failed!', 'error'); }
        });
    } else {
        medicines.splice(index, 1);
        updateMedicineTable();
        showAlert('Deleted!', 'success');
    }
};

/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
function showAlert(message, type) {
    var cls  = type === 'success' ? 'alert-success' : 'alert-danger';
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    document.getElementById('alertMessage').innerHTML =
        '<div class="alert ' + cls + ' alert-dismissible fade show" role="alert">' +
        '<i class="fas ' + icon + '"></i> ' + message +
        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
        '</div>';
    setTimeout(function() { $('.alert').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
}

function clearForm() {
    document.getElementById('medicine_name').value    = '';
    document.getElementById('medicine_company').value = '';
    document.getElementById('medSearchClear').style.display  = 'none';
    document.getElementById('compSearchClear').style.display = 'none';
    ['medicine_morning','medicine_noon','medicine_night'].forEach(function(id) {
        var el = document.getElementById(id);
        el.value = '';
        el.classList.remove('has-value');
    });
    document.getElementById('medicine_meal_timing').value  = '';
    document.getElementById('medicine_duration_type').value= '';
    document.getElementById('medicine_duration_num').value = '';
    document.getElementById('medicine_route').value        = 'Oral';
    document.getElementById('medicine_instruction').value  = '';
}

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
$(document).ready(function() {
    // Select2
    $('.select2').select2({ width: '100%', placeholder: '— Choose a template —' });

    // Template change
    $('#templateid').on('change', function() {
        var tid = $(this).val();
        if (tid) {
            document.getElementById('medicineSection').style.display = '';
            document.getElementById('stepNavigation').style.display  = '';
            loadMedicinesFromTemplate(tid);
            loadExistingMedicines(tid);
        } else {
            document.getElementById('medicineSection').style.display = 'none';
            document.getElementById('stepNavigation').style.display  = 'none';
        }
    });

    // Add medicine
    document.getElementById('btnAddMedicine').addEventListener('click', addMedicine);

    // Update medicine
    document.getElementById('btnUpdateMedicine').addEventListener('click', function() { updateMedicine(); });

    // Build autocompletes
    buildAutocomplete('medicine_name', 'medDropdown',    MEDICINE_LIST, 'medSearchClear');
    buildAutocomplete('medicine_company', 'compDropdown', COMPANY_LIST, 'compSearchClear');
    buildAutocomplete('edit_medicine', 'editMedDropdown', MEDICINE_LIST, null);

    // Keyboard shortcut: Enter on medicine_name triggers add
    document.getElementById('medicine_name').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !document.getElementById('medDropdown').classList.contains('open')) {
            addMedicine();
        }
    });
});
</script>
@stop