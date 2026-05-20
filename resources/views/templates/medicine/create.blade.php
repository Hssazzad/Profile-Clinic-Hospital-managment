@extends('adminlte::page')

@section('title', 'Template Medicine')

@section('content_header')
<div class="container-fluid">
    <div class="row align-items-center py-1">
        <div class="col-sm-6">
            <h1 class="m-0" style="font-size:1.25rem;font-weight:600;color:#1a2940;letter-spacing:0.01em;">
                <i class="fas fa-pills mr-2" style="color:#2563a8;font-size:1rem;"></i>Template Medicine
            </h1>
            <p class="m-0" style="font-size:0.78rem;color:#6b7280;margin-top:2px!important;">Manage medicine templates for patient care orders</p>
        </div>
    </div>
</div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid" style="max-width:1200px;">

        {{-- Template Selection --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="tm-card">
                    <div class="tm-card-header">
                        <i class="fas fa-file-medical-alt mr-2"></i>Select Template
                    </div>
                    <div class="tm-card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="tm-label">Template <span class="text-danger">*</span></label>
                                <select id="templateid" class="form-control select2">
                                    <option value="">-- Select Template --</option>
                                    @foreach($templates as $temp)
                                        <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        <div id="alertMessage"></div>

        {{-- Step Navigation --}}
        <div class="row mb-3" id="stepNavigation" style="display:none;">
            <div class="col-md-12">
                <div class="tm-tab-bar">
                    <button type="button" class="tm-tab-btn stepBtn" id="stepBtn_admit">
                        <i class="fas fa-user-check mr-1"></i> AT Admission
                        <span class="tm-badge" id="admitCount">0</span>
                    </button>
                    <button type="button" class="tm-tab-btn stepBtn" id="stepBtn_preorder">
                        <i class="fas fa-syringe mr-1"></i> Pre-Operation
                        <span class="tm-badge" id="preorderCount">0</span>
                    </button>
                    <button type="button" class="tm-tab-btn stepBtn" id="stepBtn_postorder">
                        <i class="fas fa-heartbeat mr-1"></i> Post-Operation
                        <span class="tm-badge" id="postorderCount">0</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content tm-modal">
                    <div class="modal-header tm-modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Medicine</h5>
                        <button type="button" class="close tm-modal-close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-3">
                        <input type="hidden" id="edit_medicine_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">Medicine Name <span class="text-danger">*</span></label>
                                    <div class="med-autocomplete-wrap" id="edit_med_wrap">
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="edit_medicine" class="form-control tm-input med-ac-input" placeholder="Type medicine name..." autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary btn-sm med-ac-clear" onclick="clearMedInput('edit_medicine','edit_med_wrap')" tabindex="-1"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="med-ac-dropdown" id="edit_med_dropdown"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">Company Name</label>
                                    <div class="med-autocomplete-wrap" id="edit_company_wrap">
                                        <input type="text" id="edit_company" class="form-control tm-input med-ac-input" placeholder="Enter company name..." autocomplete="off">
                                        <div class="med-ac-dropdown" id="edit_company_dropdown"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="tm-label">সকাল (Morning)</label>
                                    <select id="edit_morning" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option><option value="26u">26u</option><option value="30u">30u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="tm-label">দুপুর (Noon)</label>
                                    <select id="edit_noon" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="tm-label">রাত (Night)</label>
                                    <select id="edit_night" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option><option value="26u">26u</option><option value="30u">30u</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">আহারের আগে/পরে</label>
                                    <select id="edit_meal_timing" class="form-control tm-select form-control-sm">
                                        <option value="">-- Select --</option><option value="before">আগে (Before)</option><option value="after">পরে (After)</option><option value="with">সাথে (With)</option><option value="empty">খালি পেটে (Empty Stomach)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">কতদিন?</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="edit_duration_num" class="form-control tm-input" placeholder="0" min="0" style="max-width:70px;">
                                        <select id="edit_duration_type" class="form-control tm-select">
                                            <option value="">--</option><option value="দিন">দিন (Days)</option><option value="মাস">মাস (Month)</option><option value="চলবে">চলবে (Ongoing)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">Route</label>
                                    <select id="edit_route" class="form-control tm-select form-control-sm">
                                        <option value="Oral">Oral (মুখে)</option><option value="IV">IV (শিরায়)</option><option value="IM">IM (মাংসে)</option><option value="SC">SC (চামড়ার নিচে)</option><option value="Topical">Topical</option><option value="Inhalation">Inhalation</option><option value="Tablet">Tablet</option><option value="Gel">Gel</option><option value="Injection">Injection</option><option value="Eye Drop">Eye Drop</option><option value="Ear Drop">Ear Drop</option><option value="Nasal Spray">Nasal Spray</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="tm-label">Special Instructions / Notes</label>
                                    <select id="edit_instruction" class="form-control tm-select form-control-sm">
                                        <option value="">-- None --</option><option value="Before Food">Before Food</option><option value="After Food">After Food</option><option value="Empty Stomach">Empty Stomach</option><option value="With Food">With Food</option><option value="At Bed Time">At Bed Time</option><option value="With Water">With Water</option><option value="With Milk">With Milk</option><option value="As Directed">As Directed</option><option value="Chew Before Swallow">Chew Before Swallow</option><option value="Do Not Crush">Do Not Crush</option><option value="Swallow Whole">Swallow Whole</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2" style="border-top:1px solid #e5e7eb;">
                        <button type="button" class="btn tm-btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
                        <button type="button" class="btn tm-btn-warning btn-sm" id="btnUpdateMedicine"><i class="fas fa-save mr-1"></i>Update Medicine</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- /EDIT MODAL --}}

        {{-- ১. AT ADMISSION --}}
        <div class="row" id="admitSection" style="display:none;">
            <div class="col-lg-5">
                <div class="tm-card tm-card-accent-blue">
                    <div class="tm-card-header tm-header-blue">
                        <i class="fas fa-user-check mr-2"></i>AT Admission — Add Medicine
                    </div>
                    <form id="admitForm">
                        @csrf
                        <div class="tm-card-body">
                            <div class="form-group mb-2">
                                <label class="tm-label">Medicine Name <span class="text-danger">*</span></label>
                                <div class="med-autocomplete-wrap" id="admit_med_wrap">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="admit_medicine" class="form-control tm-input med-ac-input" placeholder="Type or select medicine..." autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-sm med-ac-clear" onclick="clearMedInput('admit_medicine','admit_med_wrap')" tabindex="-1"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="med-ac-dropdown" id="admit_med_dropdown"></div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="tm-label">Company Name</label>
                                <div class="med-autocomplete-wrap" id="admit_company_wrap">
                                    <input type="text" id="admit_company" class="form-control tm-input med-ac-input" placeholder="Enter company name..." autocomplete="off">
                                    <div class="med-ac-dropdown" id="admit_company_dropdown"></div>
                                </div>
                            </div>
                            <div class="tm-dosage-row">
                                <div>
                                    <label class="tm-label">সকাল</label>
                                    <select id="admit_morning" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option><option value="26u">26u</option><option value="30u">30u</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="tm-label">দুপুর</label>
                                    <select id="admit_noon" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="tm-label">রাত</label>
                                    <select id="admit_night" class="form-control tm-select form-control-sm">
                                        <option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option><option value="26u">26u</option><option value="30u">30u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="tm-label">আহারের আগে/পরে</label>
                                        <select id="admit_meal_timing" class="form-control tm-select form-control-sm">
                                            <option value="">-- Select --</option><option value="before">আগে (Before)</option><option value="after">পরে (After)</option><option value="with">সাথে (With)</option><option value="empty">খালি পেটে (Empty Stomach)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="tm-label">কতদিন?</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="admit_duration_num" class="form-control tm-input" placeholder="0" min="0" style="max-width:60px;">
                                            <select id="admit_duration_type" class="form-control tm-select">
                                                <option value="">--</option><option value="দিন">দিন (Days)</option><option value="মাস">মাস (Month)</option><option value="চলবে">চলবে (Ongoing)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="tm-label">Route</label>
                                        <select id="admit_route" class="form-control tm-select form-control-sm">
                                            <option value="Oral">Oral (মুখে)</option><option value="IV">IV (শিরায়)</option><option value="IM">IM (মাংসে)</option><option value="SC">SC (চামড়ার নিচে)</option><option value="Topical">Topical</option><option value="Inhalation">Inhalation</option><option value="Tablet">Tablet</option><option value="Gel">Gel</option><option value="Injection">Injection</option><option value="Eye Drop">Eye Drop</option><option value="Ear Drop">Ear Drop</option><option value="Nasal Spray">Nasal Spray</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="tm-label">Special Instructions</label>
                                        <select id="admit_instruction" class="form-control tm-select form-control-sm">
                                            <option value="">-- None --</option><option value="Before Food">Before Food</option><option value="After Food">After Food</option><option value="Empty Stomach">Empty Stomach</option><option value="With Food">With Food</option><option value="At Bed Time">At Bed Time</option><option value="With Water">With Water</option><option value="With Milk">With Milk</option><option value="As Directed">As Directed</option><option value="Chew Before Swallow">Chew Before Swallow</option><option value="Do Not Crush">Do Not Crush</option><option value="Swallow Whole">Swallow Whole</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tm-card-footer">
                            <button type="button" class="btn tm-btn-primary btn-sm btn-block btnAddMedicine" data-type="admit">
                                <i class="fas fa-plus mr-1"></i> Add Medicine
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="tm-card">
                    <div class="tm-card-header tm-header-blue">
                        <i class="fas fa-list mr-2"></i>Added Medicines — AT Admission
                    </div>
                    <div class="tm-card-body p-0">
                        <div class="table-responsive">
                            <table class="table tm-table m-0">
                                <thead><tr><th>#</th><th>Medicine</th><th>সকাল</th><th>দুপুর</th><th>রাত</th><th>আগে/পরে</th><th>কতদিন</th><th>Action</th></tr></thead>
                                <tbody id="admitTableBody"><tr><td colspan="8" class="tm-empty">No medicines added</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ২. PRE-OPERATION --}}
        <div class="row" id="preorderSection" style="display:none;">
            <div class="col-lg-5">
                <div class="tm-card tm-card-accent-amber">
                    <div class="tm-card-header tm-header-amber">
                        <i class="fas fa-syringe mr-2"></i>Pre-Operation — Add Medicine
                    </div>
                    <form id="preorderForm">
                        @csrf
                        <div class="tm-card-body">
                            <div class="form-group mb-2">
                                <label class="tm-label">Medicine Name <span class="text-danger">*</span></label>
                                <div class="med-autocomplete-wrap" id="preorder_med_wrap">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="preorder_medicine" class="form-control tm-input med-ac-input" placeholder="Type or select medicine..." autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-sm med-ac-clear" onclick="clearMedInput('preorder_medicine','preorder_med_wrap')" tabindex="-1"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="med-ac-dropdown" id="preorder_med_dropdown"></div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="tm-label">Company Name</label>
                                <div class="med-autocomplete-wrap" id="preorder_company_wrap">
                                    <input type="text" id="preorder_company" class="form-control tm-input med-ac-input" placeholder="Enter company name..." autocomplete="off">
                                    <div class="med-ac-dropdown" id="preorder_company_dropdown"></div>
                                </div>
                            </div>
                            <div class="tm-dosage-row">
                                <div><label class="tm-label">সকাল</label><select id="preorder_morning" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                                <div><label class="tm-label">দুপুর</label><select id="preorder_noon" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                                <div><label class="tm-label">রাত</label><select id="preorder_night" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">আহারের আগে/পরে</label><select id="preorder_meal_timing" class="form-control tm-select form-control-sm"><option value="">-- Select --</option><option value="before">আগে (Before)</option><option value="after">পরে (After)</option><option value="with">সাথে (With)</option><option value="empty">খালি পেটে (Empty Stomach)</option></select></div></div>
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">কতদিন?</label><div class="input-group input-group-sm"><input type="number" id="preorder_duration_num" class="form-control tm-input" placeholder="0" min="0" style="max-width:60px;"><select id="preorder_duration_type" class="form-control tm-select"><option value="">--</option><option value="দিন">দিন (Days)</option><option value="মাস">মাস (Month)</option><option value="চলবে">চলবে (Ongoing)</option></select></div></div></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">Route</label><select id="preorder_route" class="form-control tm-select form-control-sm"><option value="Oral">Oral (মুখে)</option><option value="IV">IV (শিরায়)</option><option value="IM">IM (মাংসে)</option><option value="SC">SC (চামড়ার নিচে)</option><option value="Topical">Topical</option><option value="Inhalation">Inhalation</option><option value="Tablet">Tablet</option><option value="Injection">Injection</option></select></div></div>
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">Special Instructions</label><select id="preorder_instruction" class="form-control tm-select form-control-sm"><option value="">-- None --</option><option value="Before Food">Before Food</option><option value="After Food">After Food</option><option value="Empty Stomach">Empty Stomach</option><option value="With Food">With Food</option><option value="At Bed Time">At Bed Time</option><option value="With Water">With Water</option><option value="With Milk">With Milk</option><option value="As Directed">As Directed</option></select></div></div>
                            </div>
                        </div>
                        <div class="tm-card-footer">
                            <button type="button" class="btn tm-btn-amber btn-sm btn-block btnAddMedicine" data-type="preorder"><i class="fas fa-plus mr-1"></i> Add Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="tm-card">
                    <div class="tm-card-header tm-header-amber"><i class="fas fa-list mr-2"></i>Added Medicines — Pre-Operation</div>
                    <div class="tm-card-body p-0">
                        <div class="table-responsive">
                            <table class="table tm-table m-0">
                                <thead><tr><th>#</th><th>Medicine</th><th>সকাল</th><th>দুপুর</th><th>রাত</th><th>আগে/পরে</th><th>কতদিন</th><th>Action</th></tr></thead>
                                <tbody id="preorderTableBody"><tr><td colspan="8" class="tm-empty">No medicines added</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ৩. POST-OPERATION --}}
        <div class="row" id="postorderSection" style="display:none;">
            <div class="col-lg-5">
                <div class="tm-card tm-card-accent-green">
                    <div class="tm-card-header tm-header-green"><i class="fas fa-heartbeat mr-2"></i>Post-Operation — Add Medicine</div>
                    <form id="postorderForm">
                        @csrf
                        <div class="tm-card-body">
                            <div class="form-group mb-2">
                                <label class="tm-label">Medicine Name <span class="text-danger">*</span></label>
                                <div class="med-autocomplete-wrap" id="postorder_med_wrap">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="postorder_medicine" class="form-control tm-input med-ac-input" placeholder="Type or select medicine..." autocomplete="off">
                                        <div class="input-group-append"><button type="button" class="btn btn-outline-secondary btn-sm med-ac-clear" onclick="clearMedInput('postorder_medicine','postorder_med_wrap')" tabindex="-1"><i class="fas fa-times"></i></button></div>
                                    </div>
                                    <div class="med-ac-dropdown" id="postorder_med_dropdown"></div>
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="tm-label">Company Name</label>
                                <div class="med-autocomplete-wrap" id="postorder_company_wrap">
                                    <input type="text" id="postorder_company" class="form-control tm-input med-ac-input" placeholder="Enter company name..." autocomplete="off">
                                    <div class="med-ac-dropdown" id="postorder_company_dropdown"></div>
                                </div>
                            </div>
                            <div class="tm-dosage-row">
                                <div><label class="tm-label">সকাল</label><select id="postorder_morning" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                                <div><label class="tm-label">দুপুর</label><select id="postorder_noon" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                                <div><label class="tm-label">রাত</label><select id="postorder_night" class="form-control tm-select form-control-sm"><option value="">-</option><option value="0">0</option><option value="1/2">½</option><option value="1">1</option><option value="1+1/2">1½</option><option value="2">2</option></select></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">আহারের আগে/পরে</label><select id="postorder_meal_timing" class="form-control tm-select form-control-sm"><option value="">-- Select --</option><option value="before">আগে (Before)</option><option value="after">পরে (After)</option><option value="with">সাথে (With)</option><option value="empty">খালি পেটে (Empty Stomach)</option></select></div></div>
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">কতদিন?</label><div class="input-group input-group-sm"><input type="number" id="postorder_duration_num" class="form-control tm-input" placeholder="0" min="0" style="max-width:60px;"><select id="postorder_duration_type" class="form-control tm-select"><option value="">--</option><option value="দিন">দিন (Days)</option><option value="মাস">মাস (Month)</option><option value="চলবে">চলবে (Ongoing)</option></select></div></div></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">Route</label><select id="postorder_route" class="form-control tm-select form-control-sm"><option value="Oral">Oral (মুখে)</option><option value="IV">IV (শিরায়)</option><option value="IM">IM (মাংসে)</option><option value="SC">SC (চামড়ার নিচে)</option><option value="Topical">Topical</option><option value="Inhalation">Inhalation</option><option value="Tablet">Tablet</option><option value="Injection">Injection</option></select></div></div>
                                <div class="col-md-6"><div class="form-group mb-2"><label class="tm-label">Special Instructions</label><select id="postorder_instruction" class="form-control tm-select form-control-sm"><option value="">-- None --</option><option value="Before Food">Before Food</option><option value="After Food">After Food</option><option value="Empty Stomach">Empty Stomach</option><option value="With Food">With Food</option><option value="At Bed Time">At Bed Time</option><option value="With Water">With Water</option><option value="With Milk">With Milk</option><option value="As Directed">As Directed</option></select></div></div>
                            </div>
                        </div>
                        <div class="tm-card-footer">
                            <button type="button" class="btn tm-btn-green btn-sm btn-block btnAddMedicine" data-type="postorder"><i class="fas fa-plus mr-1"></i> Add Medicine</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="tm-card">
                    <div class="tm-card-header tm-header-green"><i class="fas fa-list mr-2"></i>Added Medicines — Post-Operation</div>
                    <div class="tm-card-body p-0">
                        <div class="table-responsive">
                            <table class="table tm-table m-0">
                                <thead><tr><th>#</th><th>Medicine</th><th>সকাল</th><th>দুপুর</th><th>রাত</th><th>আগে/পরে</th><th>কতদিন</th><th>Action</th></tr></thead>
                                <tbody id="postorderTableBody"><tr><td colspan="8" class="tm-empty">No medicines added</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
/* ── Base Card ── */
.tm-card {
    background: #fff;
    border: 1px solid #dde3ec;
    border-radius: 6px;
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.tm-card-accent-blue  { border-top: 3px solid #2563a8; }
.tm-card-accent-amber { border-top: 3px solid #b45309; }
.tm-card-accent-green { border-top: 3px solid #166534; }

.tm-card-header {
    padding: 10px 16px;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border-bottom: 1px solid #dde3ec;
    display: flex;
    align-items: center;
}
.tm-header-blue  { background: #eef4fb; color: #1e4d8c; }
.tm-header-amber { background: #fef9ee; color: #92400e; }
.tm-header-green { background: #f0faf4; color: #14532d; }

.tm-card-body   { padding: 14px 16px; }
.tm-card-footer { padding: 10px 16px; background: #f8fafc; border-top: 1px solid #dde3ec; }

/* ── Labels & Inputs ── */
.tm-label {
    display: block;
    font-size: 0.76rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.tm-input, .tm-select {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 0.84rem;
    color: #1f2937;
    background: #fff;
    transition: border-color 0.15s;
}
.tm-input:focus, .tm-select:focus {
    border-color: #2563a8;
    outline: none;
    box-shadow: 0 0 0 2px rgba(37,99,168,0.12);
}

/* ── Dosage 3-col row ── */
.tm-dosage-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
}

/* ── Tab Bar ── */
.tm-tab-bar {
    display: flex;
    gap: 0;
    background: #fff;
    border: 1px solid #dde3ec;
    border-radius: 6px;
    overflow: hidden;
}
.tm-tab-btn {
    flex: 1;
    padding: 10px 8px;
    background: #f8fafc;
    border: none;
    border-right: 1px solid #dde3ec;
    font-size: 0.83rem;
    font-weight: 600;
    color: #4b5563;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.tm-tab-btn:last-child { border-right: none; }
.tm-tab-btn:hover { background: #eef4fb; color: #1e4d8c; }
.tm-tab-btn.active { background: #2563a8; color: #fff; }
.tm-tab-btn.active_other { background: #e8f5e9; color: #166534; }

.tm-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 700;
}
.tm-tab-btn.active .tm-badge { background: rgba(255,255,255,0.25); color: #fff; }
.tm-tab-btn:not(.active) .tm-badge { background: #dde3ec; color: #374151; }

/* ── Table ── */
.tm-table { font-size: 0.82rem; border-collapse: collapse; width: 100%; }
.tm-table thead tr { background: #f1f5fb; }
.tm-table thead th {
    padding: 8px 10px;
    font-size: 0.74rem;
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #dde3ec;
    white-space: nowrap;
    text-align: center;
}
.tm-table thead th:nth-child(2) { text-align: left; }
.tm-table tbody td {
    padding: 7px 10px;
    border-bottom: 1px solid #f1f5fb;
    color: #1f2937;
    vertical-align: middle;
    text-align: center;
}
.tm-table tbody td:nth-child(2) { text-align: left; }
.tm-table tbody tr:last-child td { border-bottom: none; }
.tm-table tbody tr:hover { background: #f8fafc; }
.tm-empty { padding: 20px; text-align: center; color: #9ca3af; font-style: italic; font-size: 0.83rem; }

/* ── Meal badge pills ── */
.meal-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
}
.meal-before { background: #fef3c7; color: #92400e; }
.meal-after  { background: #dbeafe; color: #1e40af; }
.meal-with   { background: #f3f4f6; color: #374151; }
.meal-empty  { background: #f0fdf4; color: #166534; }

/* ── Action buttons ── */
.btn-action-group { display: flex; gap: 4px; justify-content: center; }
.btn-xs { padding: 3px 8px; font-size: 0.72rem; border-radius: 3px; line-height: 1.4; }
.btn-edit   { background:#fef3c7; border:1px solid #d97706; color:#92400e; }
.btn-edit:hover { background:#fde68a; }
.btn-del    { background:#fff; border:1px solid #e5e7eb; color:#9ca3af; }
.btn-del:hover  { background:#fef2f2; border-color:#fca5a5; color:#dc2626; }

/* ── Buttons ── */
.tm-btn-primary { background:#2563a8; border-color:#2563a8; color:#fff; }
.tm-btn-primary:hover { background:#1e4d8c; border-color:#1e4d8c; color:#fff; }
.tm-btn-amber   { background:#b45309; border-color:#b45309; color:#fff; }
.tm-btn-amber:hover { background:#92400e; color:#fff; }
.tm-btn-green   { background:#166534; border-color:#166534; color:#fff; }
.tm-btn-green:hover { background:#14532d; color:#fff; }
.tm-btn-warning { background:#d97706; border-color:#d97706; color:#fff; }
.tm-btn-warning:hover { background:#b45309; color:#fff; }
.tm-btn-secondary { background:#f9fafb; border-color:#d1d5db; color:#374151; }
.tm-btn-secondary:hover { background:#f3f4f6; }

/* ── Modal ── */
.tm-modal { border: none; border-radius: 8px; overflow: hidden; }
.tm-modal-header { background:#1e4d8c; color:#fff; padding: 12px 16px; border-bottom: none; }
.tm-modal-header .modal-title { font-size:0.92rem; font-weight:600; }
.tm-modal-close { color:#fff; opacity:0.85; font-size:1.3rem; }
.tm-modal-close:hover { opacity:1; color:#fff; }

/* ── Alerts ── */
.alert { border-radius: 5px; font-size: 0.86rem; padding: 10px 16px; border: none; }
.alert-success { background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981; }
.alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

/* ── Autocomplete ── */
.med-autocomplete-wrap { position: relative; }
.med-ac-dropdown {
    display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;
    background: #fff; border: 1px solid #cbd5e1; border-top: none;
    border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-height: 210px; overflow-y: auto;
}
.med-ac-dropdown.show { display: block; }
.med-ac-item {
    padding: 7px 12px; font-size: 0.82rem; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    border-bottom: 1px solid #f1f5f9; transition: background 0.1s;
}
.med-ac-item:last-child { border-bottom: none; }
.med-ac-item:hover, .med-ac-item.focused { background: #eef4fb; color: #1e4d8c; }
.med-ac-cat { font-size: 0.68rem; font-weight: 700; padding: 1px 6px; border-radius: 3px; text-transform: uppercase; flex-shrink: 0; }
.cat-db      { background: #dbeafe; color: #1e40af; }
.cat-company { background: #fce7f3; color: #9d174d; }
.med-ac-item mark { background: #fef08a; color: inherit; padding: 0; font-weight: 700; }
.med-ac-no-result, .med-ac-loading { padding: 10px 14px; font-size: 0.8rem; color: #9ca3af; text-align: center; }
.med-ac-clear { border-left: 0 !important; color: #9ca3af; padding: 0 8px !important; }
.med-ac-clear:hover { color: #dc2626; background: #fef2f2 !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ════════════════════════════════════════════════════════════
   MEDICINE LIST — loaded from common_medicine table via Ajax
════════════════════════════════════════════════════════════ */
var MEDICINE_LIST = [];
var MEDICINE_LIST_LOADED = false;
var COMPANY_LIST = [
    'Square','Beximco','Incepta','Opsonin','ACI','Eskayef','Renata',
    'General Pharma','Drug International','Novo Nordisk','Sanofi',
    'Aristopharma','Acme','Healthcare Pharma','Orion Pharma',
];

function loadCommonMedicines() {
    $.get("{{ route('common.medicine.list') }}", function(res) {
        if (res && res.medicines) {
            MEDICINE_LIST = res.medicines.map(function(m) {
                return { id: m.id, name: m.name, group: m.GroupName || '', strength: m.strength || '' };
            });
        }
        MEDICINE_LIST_LOADED = true;
    }).fail(function() {
        MEDICINE_LIST_LOADED = true;
        console.warn('Could not load common medicines from server.');
    });
}

/* ════════════════════════════════════════════════════════════
   CUSTOM AUTOCOMPLETE ENGINE
════════════════════════════════════════════════════════════ */
function highlightMatch(text, query) {
    if (!query) return escHtml(text);
    var re = new RegExp('(' + escRe(query) + ')', 'gi');
    return escHtml(text).replace(re, '<mark>$1</mark>');
}
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escRe(s)   { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }

function showMedDropdown(inputId, dropId, query) {
    var drop = document.getElementById(dropId);
    if (!drop) return;
    if (!MEDICINE_LIST_LOADED) {
        drop.innerHTML = '<div class="med-ac-loading"><i class="fas fa-spinner fa-spin mr-1"></i>Loading medicines...</div>';
        drop.classList.add('show'); return;
    }
    query = (query || '').trim();
    var lower = query.toLowerCase();
    var results = query.length === 0
        ? MEDICINE_LIST.slice(0, 30)
        : MEDICINE_LIST.filter(function(m) {
            return m.name.toLowerCase().indexOf(lower) !== -1 || (m.group && m.group.toLowerCase().indexOf(lower) !== -1);
          });
    if (results.length === 0) {
        drop.innerHTML = '<div class="med-ac-no-result"><i class="fas fa-search mr-1"></i>No match — you can still type freely</div>';
        drop.classList.add('show'); return;
    }
    drop.innerHTML = results.map(function(m, idx) {
        var groupBadge = m.group ? '<span class="med-ac-cat cat-db">' + escHtml(m.group) + '</span>' : '<span class="med-ac-cat cat-db">Medicine</span>';
        return '<div class="med-ac-item" data-val="' + escHtml(m.name) + '" data-idx="' + idx + '">' + groupBadge +
            '<span>' + highlightMatch(m.name, query) + (m.strength ? ' <small style="color:#9ca3af;">(' + escHtml(m.strength) + ')</small>' : '') + '</span></div>';
    }).join('');
    drop.querySelectorAll('.med-ac-item').forEach(function(item) {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            document.getElementById(inputId).value = this.dataset.val;
            hideDrop(dropId);
        });
    });
    drop.classList.add('show');
}

function showCompanyDropdown(inputId, dropId, query) {
    var drop = document.getElementById(dropId);
    if (!drop) return;
    query = (query || '').trim();
    var lower = query.toLowerCase();
    var results = query.length === 0 ? COMPANY_LIST : COMPANY_LIST.filter(function(c) { return c.toLowerCase().indexOf(lower) !== -1; });
    if (results.length === 0) {
        drop.innerHTML = '<div class="med-ac-no-result">No match — type freely</div>';
        drop.classList.add('show'); return;
    }
    drop.innerHTML = results.map(function(c) {
        return '<div class="med-ac-item" data-val="' + escHtml(c) + '"><span class="med-ac-cat cat-company">Co.</span><span>' + highlightMatch(c, query) + '</span></div>';
    }).join('');
    drop.querySelectorAll('.med-ac-item').forEach(function(item) {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            document.getElementById(inputId).value = this.dataset.val;
            hideDrop(dropId);
        });
    });
    drop.classList.add('show');
}

function hideDrop(dropId)  { var d = document.getElementById(dropId); if (d) d.classList.remove('show'); }
function hideAllDrops()    { document.querySelectorAll('.med-ac-dropdown').forEach(function(d) { d.classList.remove('show'); }); }

function initMedAC(inputId, dropId) {
    var input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('focus',  function() { showMedDropdown(inputId, dropId, this.value); });
    input.addEventListener('input',  function() { showMedDropdown(inputId, dropId, this.value); });
    input.addEventListener('blur',   function() { setTimeout(function() { hideDrop(dropId); }, 180); });
    input.addEventListener('keydown', function(e) {
        var drop = document.getElementById(dropId);
        if (!drop || !drop.classList.contains('show')) return;
        var items = drop.querySelectorAll('.med-ac-item');
        var focused = drop.querySelector('.med-ac-item.focused');
        var idx = -1;
        items.forEach(function(it, i) { if (it === focused) idx = i; });
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (idx < items.length - 1) { if (focused) focused.classList.remove('focused'); items[idx+1].classList.add('focused'); items[idx+1].scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (idx > 0) { if (focused) focused.classList.remove('focused'); items[idx-1].classList.add('focused'); items[idx-1].scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'Enter') {
            if (focused) { e.preventDefault(); input.value = focused.dataset.val; hideDrop(dropId); }
        } else if (e.key === 'Escape') { hideDrop(dropId); }
    });
}

function initCompanyAC(inputId, dropId) {
    var input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('focus', function() { showCompanyDropdown(inputId, dropId, this.value); });
    input.addEventListener('input', function() { showCompanyDropdown(inputId, dropId, this.value); });
    input.addEventListener('blur',  function() { setTimeout(function() { hideDrop(dropId); }, 180); });
}

window.clearMedInput = function(inputId) { var inp = document.getElementById(inputId); if (inp) { inp.value = ''; inp.focus(); } };

document.addEventListener('click', function(e) { if (!e.target.closest('.med-autocomplete-wrap')) hideAllDrops(); });

/* ════════════════════════════════════════════════════════════
   MAIN APP LOGIC (all logic unchanged)
════════════════════════════════════════════════════════════ */
if (typeof jQuery !== 'undefined') {
    jQuery(function($) {

        loadCommonMedicines();

        let currentStep = 'admit';
        let medicines = { admit: [], preorder: [], postorder: [] };

        $('#templateid').select2({ width: '100%' });

        initMedAC('admit_medicine',     'admit_med_dropdown');
        initMedAC('preorder_medicine',  'preorder_med_dropdown');
        initMedAC('postorder_medicine', 'postorder_med_dropdown');
        initMedAC('edit_medicine',      'edit_med_dropdown');

        initCompanyAC('admit_company',     'admit_company_dropdown');
        initCompanyAC('preorder_company',  'preorder_company_dropdown');
        initCompanyAC('postorder_company', 'postorder_company_dropdown');
        initCompanyAC('edit_company',      'edit_company_dropdown');

        $('#templateid').on('change', function() {
            let tid = $(this).val();
            if (tid) {
                currentStep = 'admit';
                medicines = { admit: [], preorder: [], postorder: [] };
                $('#stepNavigation').show();
                loadAllMedicines();
                showStep('admit');
            } else { hideAllSteps(); }
        });

        $('#stepBtn_admit').on('click',     function() { currentStep = 'admit';     showStep('admit');     });
        $('#stepBtn_preorder').on('click',  function() { currentStep = 'preorder';  showStep('preorder');  });
        $('#stepBtn_postorder').on('click', function() { currentStep = 'postorder'; showStep('postorder'); });

        $('.btnAddMedicine').on('click',  function() { addMedicine($(this).data('type')); });
        $('#btnUpdateMedicine').on('click', function() { updateMedicine(); });

        function showStep(step) {
            $('#admitSection, #preorderSection, #postorderSection').hide();
            $('.stepBtn').removeClass('active');
            if      (step === 'admit')     { $('#admitSection').show();     $('#stepBtn_admit').addClass('active');     }
            else if (step === 'preorder')  { $('#preorderSection').show();  $('#stepBtn_preorder').addClass('active');  }
            else if (step === 'postorder') { $('#postorderSection').show(); $('#stepBtn_postorder').addClass('active'); }
            updateButtonStates();
        }

        function updateButtonStates() {
            $('.stepBtn').removeClass('active_other');
            if (medicines.admit.length > 0    && currentStep !== 'admit')     $('#stepBtn_admit').addClass('active_other');
            if (medicines.preorder.length > 0  && currentStep !== 'preorder')  $('#stepBtn_preorder').addClass('active_other');
            if (medicines.postorder.length > 0 && currentStep !== 'postorder') $('#stepBtn_postorder').addClass('active_other');
        }

        function hideAllSteps() {
            $('#admitSection, #preorderSection, #postorderSection').hide();
            $('#stepNavigation').hide();
        }

        function buildDosage(type) {
            let m  = $('#' + type + '_morning').val() || '-';
            let n  = $('#' + type + '_noon').val()    || '-';
            let ni = $('#' + type + '_night').val()   || '-';
            return m + '+' + n + '+' + ni;
        }

        function buildDuration(type) {
            let num   = $('#' + type + '_duration_num').val();
            let dtype = $('#' + type + '_duration_type').val();
            if (dtype === 'চলবে') return 'চলবে';
            if (num && dtype) return num + ' ' + dtype;
            if (dtype) return dtype;
            return '';
        }

        function addMedicine(type) {
            let tid      = $('#templateid').val();
            let medicine = $('#' + type + '_medicine').val();
            if (!tid)                          { showAlert('Please select a template!', 'error'); return; }
            if (!medicine || !medicine.trim()) { showAlert('Please enter medicine name!', 'error'); return; }

            let dosage      = buildDosage(type);
            let duration    = buildDuration(type);
            let meal_timing = $('#' + type + '_meal_timing').val();
            let instruction = $('#' + type + '_instruction').val();
            let route       = $('#' + type + '_route').val();
            let company     = $('#' + type + '_company').val();
            let morning     = $('#' + type + '_morning').val();
            let noon        = $('#' + type + '_noon').val();
            let night       = $('#' + type + '_night').val();
            let order_type  = type;

            let btn = $('.btnAddMedicine[data-type="' + type + '"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "{{ route('templates.medicine.ajax.add') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", templateid: tid, name: medicine, dosage, morning, noon, night, meal_timing, duration, instruction, route, order_type, company },
                success: function(res) {
                    if (res.ok) { showAlert('Medicine added successfully.', 'success'); clearForm(type); loadAllMedicines(); }
                    else { showAlert('Error: ' + res.message, 'error'); }
                },
                error: function() { showAlert('Server error!', 'error'); },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-plus mr-1"></i> Add Medicine'); }
            });
        }

        function clearForm(type) {
            $('#' + type + '_medicine').val('');
            $('#' + type + '_company').val('');
            $('#' + type + '_morning').val('');
            $('#' + type + '_noon').val('');
            $('#' + type + '_night').val('');
            $('#' + type + '_meal_timing').val('');
            $('#' + type + '_duration_num').val('');
            $('#' + type + '_duration_type').val('');
            $('#' + type + '_instruction').val('');
            $('#' + type + '_route').val('Oral');
            $('#' + type + '_medicine').focus();
        }

        window.editMedicine = function(id) {
            let row = null;
            ['admit','preorder','postorder'].forEach(function(t) {
                let found = medicines[t].find(function(m) { return m.id == id; });
                if (found) row = found;
            });
            if (!row) { showAlert('Medicine data not found!', 'error'); return; }

            $('#edit_medicine_id').val(row.id);
            $('#edit_medicine').val(row.name   || '');
            $('#edit_company').val(row.company  || '');
            $('#edit_route').val(row.route      || 'Oral');
            $('#edit_meal_timing').val(row.meal_timing || '');
            $('#edit_instruction').val(row.instruction || '');

            let morning = row.morning || (row.dose ? (row.dose.split('+')[0] || '') : '');
            let noon    = row.noon    || (row.dose ? (row.dose.split('+')[1] || '') : '');
            let night   = row.night   || (row.dose ? (row.dose.split('+')[2] || '') : '');
            $('#edit_morning').val(morning);
            $('#edit_noon').val(noon);
            $('#edit_night').val(night);

            let dur = row.duration || '';
            if (dur === 'চলবে' || dur.toLowerCase().includes('চলবে')) {
                $('#edit_duration_num').val(''); $('#edit_duration_type').val('চলবে');
            } else {
                let parts = dur.split(' ');
                if (parts.length >= 2) { $('#edit_duration_num').val(parts[0]); $('#edit_duration_type').val(parts[1]); }
                else { $('#edit_duration_num').val(''); $('#edit_duration_type').val(''); }
            }
            $('#editMedicineModal').modal('show');
        };

        function updateMedicine() {
            let id = $('#edit_medicine_id').val();
            if (!id) return;

            let name        = $('#edit_medicine').val().trim();
            let company     = $('#edit_company').val().trim();
            let morning     = $('#edit_morning').val();
            let noon        = $('#edit_noon').val();
            let night       = $('#edit_night').val();
            let dosage      = (morning||'-') + '+' + (noon||'-') + '+' + (night||'-');
            let meal_timing = $('#edit_meal_timing').val();
            let instruction = $('#edit_instruction').val();
            let route       = $('#edit_route').val();

            let order_type = '';
            ['admit','preorder','postorder'].forEach(function(t) {
                let found = medicines[t].find(function(m) { return m.id == id; });
                if (found) order_type = found.order_type || t;
            });

            let dnum  = $('#edit_duration_num').val();
            let dtype = $('#edit_duration_type').val();
            let duration = '';
            if      (dtype === 'চলবে') duration = 'চলবে';
            else if (dnum && dtype)    duration = dnum + ' ' + dtype;
            else if (dtype)            duration = dtype;

            if (!name) { showAlert('Medicine name is required!', 'error'); return; }

            let btn = $('#btnUpdateMedicine');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

            $.ajax({
                url: `/templates/medicine/ajax/${id}`,
                type: 'POST',
                data: { _token: "{{ csrf_token() }}", _method: 'PUT', name, company, dosage, morning, noon, night, meal_timing, duration, instruction, route, order_type },
                success: function(res) {
                    if (res.ok || res.success) {
                        showAlert('Medicine updated successfully.', 'success');
                        $('#editMedicineModal').modal('hide');
                        loadAllMedicines();
                    } else { showAlert('Update failed: ' + (res.message || 'Unknown error'), 'error'); }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error (HTTP ' + xhr.status + ')';
                    showAlert('Error: ' + msg, 'error');
                },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Medicine'); }
            });
        }

        function loadAllMedicines() {
            let tid = $('#templateid').val();
            if (!tid) return;
            $.get("{{ route('templates.medicine.ajax.list') }}", { templateid: tid }, function(res) {
                medicines = { admit: [], preorder: [], postorder: [] };
                let admitHtml = '', preorderHtml = '', postorderHtml = '';
                let cntA = 1, cntPre = 1, cntPost = 1;

                if (res.rows && res.rows.length > 0) {
                    res.rows.forEach(function(row) {
                        let type = row.order_type || 'admit';
                        if (!medicines[type]) medicines[type] = [];
                        medicines[type].push(row);

                        let morning = row.morning || (row.dose ? row.dose.split('+')[0] : '-');
                        let noon    = row.noon    || (row.dose ? row.dose.split('+')[1] : '-');
                        let night   = row.night   || (row.dose ? row.dose.split('+')[2] : '-');
                        let meal    = row.meal_timing || '';
                        let dur     = row.duration || '';
                        let cnt     = type === 'admit' ? cntA++ : type === 'preorder' ? cntPre++ : cntPost++;

                        let mealLabel = '';
                        if      (meal === 'before') mealLabel = '<span class="meal-pill meal-before">আগে</span>';
                        else if (meal === 'after')  mealLabel = '<span class="meal-pill meal-after">পরে</span>';
                        else if (meal === 'with')   mealLabel = '<span class="meal-pill meal-with">সাথে</span>';
                        else if (meal === 'empty')  mealLabel = '<span class="meal-pill meal-empty">খালি</span>';
                        else                        mealLabel = '<span style="color:#9ca3af;">—</span>';

                        let rowHtml = `<tr>
                            <td style="font-weight:600;color:#6b7280;">${cnt}</td>
                            <td>
                                <span style="font-weight:600;color:#1f2937;">${row.name}</span>
                                ${row.company ? '<br><small style="color:#9ca3af;font-size:0.72rem;">' + row.company + '</small>' : ''}
                            </td>
                            <td>${morning || '—'}</td>
                            <td>${noon    || '—'}</td>
                            <td>${night   || '—'}</td>
                            <td>${mealLabel}</td>
                            <td style="font-size:0.8rem;color:#374151;">${dur || '—'}</td>
                            <td>
                                <div class="btn-action-group">
                                    <button onclick="editMedicine(${row.id})" class="btn btn-xs btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteMedicine(${row.id})" class="btn btn-xs btn-del" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>`;

                        if      (type === 'admit')    admitHtml     += rowHtml;
                        else if (type === 'preorder') preorderHtml  += rowHtml;
                        else                          postorderHtml += rowHtml;
                    });
                }

                let empty = '<tr><td colspan="8" class="tm-empty">No medicines added</td></tr>';
                $('#admitTableBody').html(admitHtml     || empty);
                $('#preorderTableBody').html(preorderHtml  || empty);
                $('#postorderTableBody').html(postorderHtml || empty);

                $('#admitCount').text(medicines.admit.length);
                $('#preorderCount').text(medicines.preorder.length);
                $('#postorderCount').text(medicines.postorder.length);
                updateButtonStates();
            });
        }

        function showAlert(message, type) {
            let cls  = type === 'success' ? 'alert-success' : 'alert-danger';
            let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            $('#alertMessage').html(`
                <div class="alert ${cls} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} mr-2"></i>${message}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            `);
            setTimeout(function() { $('.alert').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
        }

        window.deleteMedicine = function(id) {
            if (confirm('Are you sure you want to delete this medicine?')) {
                $.ajax({
                    url: `/templates/medicine/ajax/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.ok || res.success) { showAlert('Medicine deleted.', 'success'); loadAllMedicines(); }
                    },
                    error: function() { showAlert('Delete failed!', 'error'); }
                });
            }
        };

    }); // end jQuery ready
}
</script>
@stop