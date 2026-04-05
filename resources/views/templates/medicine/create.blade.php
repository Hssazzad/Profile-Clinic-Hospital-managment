@extends('adminlte::page')

@section('title', 'Template Medicine')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-pills mr-2"></i>Template Medicine</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">

        {{-- Template Selection --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label>Select Template <span class="text-danger">*</span></label>
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

        {{-- Alert Messages --}}
        <div id="alertMessage"></div>

        {{-- Step Navigation (Top) --}}
        <div class="row mb-3" id="stepNavigation" style="display: none;">
            <div class="col-md-12">
                <div class="card card-outline mb-0">
                    <div class="card-body p-2">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm w-100 stepBtn" id="stepBtn_admit">
                                    <i class="fas fa-user-check"></i> AT Admission
                                    <span id="admitCount" class="badge badge-primary ml-2">0</span>
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm w-100 stepBtn" id="stepBtn_preorder">
                                    <i class="fas fa-syringe"></i> Pre-Operation
                                    <span id="preorderCount" class="badge badge-warning ml-2">0</span>
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm w-100 stepBtn" id="stepBtn_postorder">
                                    <i class="fas fa-heartbeat"></i> Post-Operation
                                    <span id="postorderCount" class="badge badge-success ml-2">0</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Prescription Table View (like image) --}}
        <div id="prescriptionTableSection" style="display:none;" class="mb-3">
            <div class="card card-outline card-info">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-file-medical mr-1"></i> Prescription View</h3>
                    <button class="btn btn-xs btn-info" id="togglePrescriptionView">
                        <i class="fas fa-eye-slash"></i> Hide Preview
                    </button>
                </div>
                <div class="card-body p-0" id="prescriptionTableBody">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm prescription-table m-0">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th rowspan="2" style="width:35%">ঔষধের নাম</th>
                                    <th colspan="3">কখন খাবেন?</th>
                                    <th colspan="2">আহারের</th>
                                    <th colspan="3">কতদিন?</th>
                                </tr>
                                <tr class="text-center">
                                    <th>সকাল</th>
                                    <th>দুপুর</th>
                                    <th>রাত</th>
                                    <th>আগে</th>
                                    <th>পরে</th>
                                    <th>দিন</th>
                                    <th>মাস</th>
                                    <th>চলবে</th>
                                </tr>
                            </thead>
                            <tbody id="prescriptionRows">
                                <tr><td colspan="9" class="text-center text-muted p-3">No medicines added yet</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ১. AT ADMISSION --}}
        <div class="row" id="admitSection" style="display: none;">
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-user-check mr-1"></i> AT Admission</h3>
                    </div>
                    <form id="admitForm">
                        @csrf
                        <div class="card-body p-3">

                            {{-- Medicine Name (searchable dropdown + free text) --}}
                            <div class="form-group mb-2">
                                <label class="small">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" id="admit_medicine" list="admit_medicine_list" class="form-control form-control-sm" placeholder="Type or select medicine...">
                                <datalist id="admit_medicine_list">
                                    <option value="Almivas 40">
                                    <option value="Rosu 5mg">
                                    <option value="Lopimol 75mg">
                                    <option value="Sevel 800">
                                    <option value="Syndopa 110">
                                    <option value="Vulex CR 200">
                                    <option value="Gensulin 30/70 100">
                                    <option value="Oxut 20">
                                    <option value="Quiot XR 200">
                                    <option value="Rusagyl 500mg">
                                    <option value="Metformin 500mg">
                                    <option value="Amlodipine 5mg">
                                    <option value="Atorvastatin 10mg">
                                    <option value="Omeprazole 20mg">
                                    <option value="Pantoprazole 40mg">
                                    <option value="Clopidogrel 75mg">
                                    <option value="Aspirin 75mg">
                                    <option value="Lisinopril 5mg">
                                    <option value="Enalapril 5mg">
                                    <option value="Losartan 50mg">
                                    <option value="Valsartan 80mg">
                                    <option value="Bisoprolol 5mg">
                                    <option value="Carvedilol 6.25mg">
                                    <option value="Furosemide 40mg">
                                    <option value="Spironolactone 25mg">
                                    <option value="Digoxin 0.25mg">
                                    <option value="Warfarin 5mg">
                                    <option value="Insulin Regular">
                                    <option value="Insulin Glargine">
                                    <option value="Glibenclamide 5mg">
                                    <option value="Glimepiride 2mg">
                                    <option value="Sitagliptin 50mg">
                                    <option value="Levodopa/Carbidopa">
                                    <option value="Pramipexole 0.5mg">
                                    <option value="Amantadine 100mg">
                                    <option value="Trihexyphenidyl 2mg">
                                    <option value="Donepezil 5mg">
                                    <option value="Memantine 10mg">
                                    <option value="Rivastigmine 1.5mg">
                                    <option value="Quetiapine 25mg">
                                    <option value="Clonazepam 0.5mg">
                                    <option value="Diazepam 5mg">
                                    <option value="Pregabalin 75mg">
                                    <option value="Gabapentin 300mg">
                                    <option value="Carbamazepine 200mg">
                                    <option value="Valproate 200mg">
                                    <option value="Phenytoin 100mg">
                                    <option value="Levetiracetam 500mg">
                                    <option value="Amitriptyline 10mg">
                                    <option value value="Escitalopram 10mg">
                                    <option value="Sertraline 50mg">
                                    <option value="Fluoxetine 20mg">
                                    <option value="Methotrexate 2.5mg">
                                    <option value="Hydroxychloroquine 200mg">
                                    <option value="Prednisolone 5mg">
                                    <option value="Dexamethasone 4mg">
                                    <option value="Methylprednisolone 4mg">
                                    <option value="Ciprofloxacin 500mg">
                                    <option value="Amoxicillin 500mg">
                                    <option value="Azithromycin 500mg">
                                    <option value="Cefixime 200mg">
                                    <option value="Metronidazole 400mg">
                                    <option value="Nystatin 100000IU">
                                    <option value="Fluconazole 150mg">
                                    <option value="Calcium + Vit D3">
                                    <option value="Folic Acid 5mg">
                                    <option value="Ferrous Sulphate 200mg">
                                    <option value="Vitamin B Complex">
                                    <option value="Vitamin C 500mg">
                                    <option value="Zinc 20mg">
                                </datalist>
                            </div>

                            <div class="form-group mb-2">
                                <label class="small">Company Name</label>
                                <input type="text" id="admit_company" list="company_list" class="form-control form-control-sm" placeholder="Enter company name...">
                                <datalist id="company_list">
                                    <option value="Square">
                                    <option value="Beximco">
                                    <option value="Incepta">
                                    <option value="Opsonin">
                                    <option value="ACI">
                                    <option value="Eskayef">
                                    <option value="Renata">
                                    <option value="General Pharma">
                                    <option value="Drug International">
                                    <option value="Novo Nordisk">
                                    <option value="Sanofi">
                                    <option value="Aristopharma">
                                    <option value="Acme">
                                    <option value="Healthcare Pharma">
                                    <option value="Orion Pharma">
                                </datalist>
                            </div>

                            <div class="row">
                                {{-- Morning / Shokal --}}
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">সকাল (Morning)</label>
                                        <select id="admit_morning" class="form-control form-control-sm">
                                            <option value="">-</option>
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
                                {{-- Noon / Dupur --}}
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">দুপুর (Noon)</label>
                                        <select id="admit_noon" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- Night / Raat --}}
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">রাত (Night)</label>
                                        <select id="admit_night" class="form-control form-control-sm">
                                            <option value="">-</option>
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
                            </div>

                            <div class="row">
                                {{-- Before / After Food --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">আহারের আগে/পরে</label>
                                        <select id="admit_meal_timing" class="form-control form-control-sm">
                                            <option value="">-- Select --</option>
                                            <option value="before">আগে (Before)</option>
                                            <option value="after">পরে (After)</option>
                                            <option value="with">সাথে (With)</option>
                                            <option value="empty">খালি পেটে (Empty Stomach)</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- Duration Type --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">কতদিন?</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="admit_duration_num" class="form-control form-control-sm" placeholder="0" min="0" style="max-width:60px;">
                                            <select id="admit_duration_type" class="form-control form-control-sm">
                                                <option value="">--</option>
                                                <option value="দিন">দিন (Days)</option>
                                                <option value="মাস">মাস (Month)</option>
                                                <option value="চলবে">চলবে (Ongoing)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Route</label>
                                        <select id="admit_route" class="form-control form-control-sm">
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
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Order Type</label>
                                        <select id="admit_order_type" class="form-control form-control-sm">
                                            <option value="admit">🏥 Admission</option>
                                            <option value="preorder">⚕️ Pre-Operation</option>
                                            <option value="postorder">🔧 Post-Operation</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small">Special Instructions / Notes</label>
                                <select id="admit_instruction" class="form-control form-control-sm">
                                    <option value="">-- None --</option>
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
                        <div class="card-footer p-2">
                            <button type="button" class="btn btn-primary btn-sm btn-block btnAddMedicine" data-type="admit">
                                <i class="fas fa-plus"></i> Add Medicine
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-outline card-primary">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Added Medicines</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered table-striped table-hover m-0">
                            <thead class="bg-primary text-white text-center" style="font-size: 0.82em;">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>সকাল</th>
                                    <th>দুপুর</th>
                                    <th>রাত</th>
                                    <th>আগে/পরে</th>
                                    <th>কতদিন</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="admitTableBody" style="font-size: 0.88em;">
                                <tr><td colspan="8" class="text-center p-3">No medicines added</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ২. PRE-OPERATION --}}
        <div class="row" id="preorderSection" style="display: none;">
            <div class="col-lg-6">
                <div class="card card-warning card-outline">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-syringe mr-1"></i> Pre-Operation</h3>
                    </div>
                    <form id="preorderForm">
                        @csrf
                        <div class="card-body p-3">
                            <div class="form-group mb-2">
                                <label class="small">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" id="preorder_medicine" list="preorder_medicine_list" class="form-control form-control-sm" placeholder="Type or select medicine...">
                                <datalist id="preorder_medicine_list">
                                    <option value="Midazolam 5mg">
                                    <option value="Atropine 0.6mg">
                                    <option value="Metoclopramide 10mg">
                                    <option value="Ranitidine 150mg">
                                    <option value="Diazepam 10mg">
                                    <option value="Ondansetron 4mg">
                                    <option value="Cefazolin 1g">
                                    <option value="Glycopyrrolate 0.2mg">
                                    <option value="Fentanyl 100mcg">
                                    <option value="Morphine 10mg">
                                    <option value="Dexamethasone 8mg">
                                    <option value="Pantoprazole 40mg">
                                </datalist>
                            </div>

                            <div class="form-group mb-2">
                                <label class="small">Company Name</label>
                                <input type="text" id="preorder_company" list="company_list" class="form-control form-control-sm" placeholder="Enter company name...">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">সকাল</label>
                                        <select id="preorder_morning" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">দুপুর</label>
                                        <select id="preorder_noon" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">রাত</label>
                                        <select id="preorder_night" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">আহারের আগে/পরে</label>
                                        <select id="preorder_meal_timing" class="form-control form-control-sm">
                                            <option value="">-- Select --</option>
                                            <option value="before">আগে (Before)</option>
                                            <option value="after">পরে (After)</option>
                                            <option value="with">সাথে (With)</option>
                                            <option value="empty">খালি পেটে (Empty Stomach)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">কতদিন?</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="preorder_duration_num" class="form-control form-control-sm" placeholder="0" min="0" style="max-width:60px;">
                                            <select id="preorder_duration_type" class="form-control form-control-sm">
                                                <option value="">--</option>
                                                <option value="দিন">দিন (Days)</option>
                                                <option value="মাস">মাস (Month)</option>
                                                <option value="চলবে">চলবে (Ongoing)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Route</label>
                                        <select id="preorder_route" class="form-control form-control-sm">
                                            <option value="Oral">Oral (মুখে)</option>
                                            <option value="IV">IV (শিরায়)</option>
                                            <option value="IM">IM (মাংসে)</option>
                                            <option value="SC">SC (চামড়ার নিচে)</option>
                                            <option value="Topical">Topical</option>
                                            <option value="Inhalation">Inhalation</option>
                                            <option value="Tablet">Tablet</option>
                                            <option value="Injection">Injection</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Order Type</label>
                                        <select id="preorder_order_type" class="form-control form-control-sm">
                                            <option value="admit">🏥 Admission</option>
                                            <option value="preorder" selected>⚕️ Pre-Operation</option>
                                            <option value="postorder">🔧 Post-Operation</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small">Special Instructions</label>
                                <select id="preorder_instruction" class="form-control form-control-sm">
                                    <option value="">-- None --</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="After Food">After Food</option>
                                    <option value="Empty Stomach">Empty Stomach</option>
                                    <option value="With Food">With Food</option>
                                    <option value="At Bed Time">At Bed Time</option>
                                    <option value="With Water">With Water</option>
                                    <option value="With Milk">With Milk</option>
                                    <option value="As Directed">As Directed</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer p-2">
                            <button type="button" class="btn btn-warning btn-sm btn-block btnAddMedicine" data-type="preorder">
                                <i class="fas fa-plus"></i> Add Medicine
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-outline card-warning">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Added Medicines</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered table-striped table-hover m-0">
                            <thead class="bg-warning text-dark text-center" style="font-size: 0.82em;">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>সকাল</th>
                                    <th>দুপুর</th>
                                    <th>রাত</th>
                                    <th>আগে/পরে</th>
                                    <th>কতদিন</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="preorderTableBody" style="font-size: 0.88em;">
                                <tr><td colspan="8" class="text-center p-3">No medicines added</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ৩. POST-OPERATION --}}
        <div class="row" id="postorderSection" style="display: none;">
            <div class="col-lg-6">
                <div class="card card-success card-outline">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-heartbeat mr-1"></i> Post-Operation</h3>
                    </div>
                    <form id="postorderForm">
                        @csrf
                        <div class="card-body p-3">
                            <div class="form-group mb-2">
                                <label class="small">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" id="postorder_medicine" list="postorder_medicine_list" class="form-control form-control-sm" placeholder="Type or select medicine...">
                                <datalist id="postorder_medicine_list">
                                    <option value="Tramadol 50mg">
                                    <option value="Ketorolac 30mg">
                                    <option value="Paracetamol 500mg">
                                    <option value="Ondansetron 4mg">
                                    <option value="Metoclopramide 10mg">
                                    <option value="Heparin 5000IU">
                                    <option value="Enoxaparin 40mg">
                                    <option value="Cefuroxime 750mg">
                                    <option value="Dexamethasone 4mg">
                                    <option value="Omeprazole 40mg">
                                    <option value="Metronidazole 500mg">
                                    <option value="Diclofenac 75mg">
                                </datalist>
                            </div>

                            <div class="form-group mb-2">
                                <label class="small">Company Name</label>
                                <input type="text" id="postorder_company" list="company_list" class="form-control form-control-sm" placeholder="Enter company name...">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">সকাল</label>
                                        <select id="postorder_morning" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">দুপুর</label>
                                        <select id="postorder_noon" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small">রাত</label>
                                        <select id="postorder_night" class="form-control form-control-sm">
                                            <option value="">-</option>
                                            <option value="0">0</option>
                                            <option value="1/2">½</option>
                                            <option value="1">1</option>
                                            <option value="1+1/2">1½</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">আহারের আগে/পরে</label>
                                        <select id="postorder_meal_timing" class="form-control form-control-sm">
                                            <option value="">-- Select --</option>
                                            <option value="before">আগে (Before)</option>
                                            <option value="after">পরে (After)</option>
                                            <option value="with">সাথে (With)</option>
                                            <option value="empty">খালি পেটে (Empty Stomach)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">কতদিন?</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="postorder_duration_num" class="form-control form-control-sm" placeholder="0" min="0" style="max-width:60px;">
                                            <select id="postorder_duration_type" class="form-control form-control-sm">
                                                <option value="">--</option>
                                                <option value="দিন">দিন (Days)</option>
                                                <option value="মাস">মাস (Month)</option>
                                                <option value="চলবে">চলবে (Ongoing)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Route</label>
                                        <select id="postorder_route" class="form-control form-control-sm">
                                            <option value="Oral">Oral (মুখে)</option>
                                            <option value="IV">IV (শিরায়)</option>
                                            <option value="IM">IM (মাংসে)</option>
                                            <option value="SC">SC (চামড়ার নিচে)</option>
                                            <option value="Topical">Topical</option>
                                            <option value="Inhalation">Inhalation</option>
                                            <option value="Tablet">Tablet</option>
                                            <option value="Injection">Injection</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="small">Order Type</label>
                                        <select id="postorder_order_type" class="form-control form-control-sm">
                                            <option value="admit">🏥 Admission</option>
                                            <option value="preorder">⚕️ Pre-Operation</option>
                                            <option value="postorder" selected>🔧 Post-Operation</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small">Special Instructions</label>
                                <select id="postorder_instruction" class="form-control form-control-sm">
                                    <option value="">-- None --</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="After Food">After Food</option>
                                    <option value="Empty Stomach">Empty Stomach</option>
                                    <option value="With Food">With Food</option>
                                    <option value="At Bed Time">At Bed Time</option>
                                    <option value="With Water">With Water</option>
                                    <option value="With Milk">With Milk</option>
                                    <option value="As Directed">As Directed</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer p-2">
                            <button type="button" class="btn btn-success btn-sm btn-block btnAddMedicine" data-type="postorder">
                                <i class="fas fa-plus"></i> Add Medicine
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-outline card-success">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Added Medicines</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered table-striped table-hover m-0">
                            <thead class="bg-success text-white text-center" style="font-size: 0.82em;">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>সকাল</th>
                                    <th>দুপুর</th>
                                    <th>রাত</th>
                                    <th>আগে/পরে</th>
                                    <th>কতদিন</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="postorderTableBody" style="font-size: 0.88em;">
                                <tr><td colspan="8" class="text-center p-3">No medicines added</td></tr>
                            </tbody>
                        </table>
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
    .stepBtn {
        border: 2px solid #ddd;
        background: white;
        color: #333;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s;
        font-size: 0.95em;
        font-weight: 600;
    }
    .stepBtn:hover {
        border-color: #007bff;
        background: #f0f8ff;
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .stepBtn.active {
        border-color: #007bff;
        background: #007bff;
        color: white;
        box-shadow: 0 2px 8px rgba(0,123,255,0.3);
    }
    .stepBtn.active_other {
        border-color: #28a745;
        background: #e8f5e9;
    }
    .badge { font-size: 0.75em; padding: 4px 8px; }

    /* Prescription table styling to look like the real prescription */
    .prescription-table th, .prescription-table td {
        text-align: center;
        vertical-align: middle;
        font-size: 0.88em;
        padding: 5px 6px !important;
    }
    .prescription-table .med-name-cell {
        text-align: left;
        font-weight: 600;
    }
    .prescription-table .tick { color: #28a745; font-weight: bold; }
    .input-group-sm .form-control { font-size: 0.85em; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
if (typeof jQuery !== 'undefined') {
    jQuery(function($) {

        let currentStep = 'admit';
        let medicines = { admit: [], preorder: [], postorder: [] };

        // Initialize Select2
        $('#templateid').select2({ width: '100%' });

        // Template Selection
        $('#templateid').on('change', function() {
            let tid = $(this).val();
            if (tid) {
                currentStep = 'admit';
                medicines = { admit: [], preorder: [], postorder: [] };
                $('#stepNavigation').show();
                $('#prescriptionTableSection').show();
                loadAllMedicines();
                showStep('admit');
            } else {
                hideAllSteps();
            }
        });

        // Step Navigation
        $('#stepBtn_admit').on('click', function() { currentStep = 'admit'; showStep('admit'); });
        $('#stepBtn_preorder').on('click', function() { currentStep = 'preorder'; showStep('preorder'); });
        $('#stepBtn_postorder').on('click', function() { currentStep = 'postorder'; showStep('postorder'); });

        // Toggle prescription preview
        $('#togglePrescriptionView').on('click', function() {
            let body = $('#prescriptionTableBody');
            if (body.is(':visible')) {
                body.hide();
                $(this).html('<i class="fas fa-eye"></i> Show Preview');
            } else {
                body.show();
                $(this).html('<i class="fas fa-eye-slash"></i> Hide Preview');
            }
        });

        // Add Medicine
        $('.btnAddMedicine').on('click', function() {
            addMedicine($(this).data('type'));
        });

        function showStep(step) {
            $('#admitSection, #preorderSection, #postorderSection').hide();
            $('.stepBtn').removeClass('active');
            if (step === 'admit') { $('#admitSection').show(); $('#stepBtn_admit').addClass('active'); }
            else if (step === 'preorder') { $('#preorderSection').show(); $('#stepBtn_preorder').addClass('active'); }
            else if (step === 'postorder') { $('#postorderSection').show(); $('#stepBtn_postorder').addClass('active'); }
            updateButtonStates();
        }

        function updateButtonStates() {
            $('.stepBtn').removeClass('active_other');
            if (medicines.admit.length > 0 && currentStep !== 'admit') $('#stepBtn_admit').addClass('active_other');
            if (medicines.preorder.length > 0 && currentStep !== 'preorder') $('#stepBtn_preorder').addClass('active_other');
            if (medicines.postorder.length > 0 && currentStep !== 'postorder') $('#stepBtn_postorder').addClass('active_other');
        }

        function hideAllSteps() {
            $('#admitSection, #preorderSection, #postorderSection').hide();
            $('#stepNavigation').hide();
            $('#prescriptionTableSection').hide();
        }

        function buildDosage(type) {
            let m = $('#' + type + '_morning').val() || '-';
            let n = $('#' + type + '_noon').val() || '-';
            let ni = $('#' + type + '_night').val() || '-';
            return m + '+' + n + '+' + ni;
        }

        function buildDuration(type) {
            let num = $('#' + type + '_duration_num').val();
            let dtype = $('#' + type + '_duration_type').val();
            if (dtype === 'চলবে') return 'চলবে';
            if (num && dtype) return num + ' ' + dtype;
            if (dtype) return dtype;
            return '';
        }

        function addMedicine(type) {
            let tid = $('#templateid').val();
            let medicine = $('#' + type + '_medicine').val();

            if (!tid) { showAlert('Please select a template!', 'error'); return; }
            if (!medicine || medicine.trim() === '') { showAlert('Please enter medicine name!', 'error'); return; }

            let dosage = buildDosage(type);
            let duration = buildDuration(type);
            let meal_timing = $('#' + type + '_meal_timing').val();
            let instruction = $('#' + type + '_instruction').val();
            let route = $('#' + type + '_route').val();
            let order_type = $('#' + type + '_order_type').val();
            let company = $('#' + type + '_company').val();

            // Build morning/noon/night individually for saving
            let morning = $('#' + type + '_morning').val();
            let noon = $('#' + type + '_noon').val();
            let night = $('#' + type + '_night').val();

            let btn = $('.btnAddMedicine[data-type="' + type + '"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "{{ route('templates.medicine.ajax.add') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    templateid: tid,
                    name: medicine,
                    dosage: dosage,
                    morning: morning,
                    noon: noon,
                    night: night,
                    meal_timing: meal_timing,
                    duration: duration,
                    instruction: instruction,
                    route: route,
                    order_type: order_type,
                    company: company
                },
                success: function(res) {
                    if (res.ok) {
                        showAlert('✓ Medicine added!', 'success');
                        clearForm(type);
                        loadAllMedicines();
                    } else {
                        showAlert('Error: ' + res.message, 'error');
                    }
                },
                error: function() { showAlert('Server error!', 'error'); },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Add Medicine');
                }
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

        function loadAllMedicines() {
            let tid = $('#templateid').val();
            if (!tid) return;

            $.get("{{ route('templates.medicine.ajax.list') }}", { templateid: tid }, function(res) {
                medicines = { admit: [], preorder: [], postorder: [] };

                let admitHtml = '', preorderHtml = '', postorderHtml = '';
                let prescriptionHtml = '';
                let cntA = 1, cntPre = 1, cntPost = 1, cntAll = 1;

                if (res.rows && res.rows.length > 0) {
                    res.rows.forEach(function(row) {
                        let type = row.order_type || 'admit';
                        medicines[type].push(row);

                        let morning = row.morning || (row.dose ? row.dose.split('+')[0] : '-');
                        let noon    = row.noon    || (row.dose ? row.dose.split('+')[1] : '-');
                        let night   = row.night   || (row.dose ? row.dose.split('+')[2] : '-');
                        let meal    = row.meal_timing || '';
                        let dur     = row.duration || '';

                        // Meal timing display
                        let mealBefore = (meal === 'before') ? '✓' : '';
                        let mealAfter  = (meal === 'after')  ? '✓' : '';

                        // Duration parse
                        let durDin = '', durMas = '', durCholbe = '';
                        if (dur === 'চলবে' || dur.toLowerCase().includes('চলবে') || dur.toLowerCase().includes('continue') || dur.toLowerCase().includes('ongoing')) {
                            durCholbe = '✓';
                        } else {
                            let parts = dur.split(' ');
                            if (parts[1] && parts[1].includes('মাস')) { durMas = parts[0]; }
                            else if (parts[0]) { durDin = parts[0]; }
                        }

                        let cnt = type === 'admit' ? cntA++ : type === 'preorder' ? cntPre++ : cntPost++;
                        let rowHtml = `<tr class="text-center">
                            <td>${cnt}</td>
                            <td class="text-left"><strong>${row.name}</strong>${row.company ? '<br><small class="text-muted">' + row.company + '</small>' : ''}</td>
                            <td>${morning || '-'}</td>
                            <td>${noon || '-'}</td>
                            <td>${night || '-'}</td>
                            <td>${meal === 'before' ? '<span class="badge badge-warning">আগে</span>' : meal === 'after' ? '<span class="badge badge-info">পরে</span>' : meal === 'with' ? '<span class="badge badge-secondary">সাথে</span>' : meal === 'empty' ? '<span class="badge badge-light">খালি</span>' : '-'}</td>
                            <td>${dur || '-'}</td>
                            <td>
                                <button onclick="deleteMedicine(${row.id})" class="btn btn-xs btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;

                        if (type === 'admit') admitHtml += rowHtml;
                        else if (type === 'preorder') preorderHtml += rowHtml;
                        else postorderHtml += rowHtml;

                        // Prescription preview row (Bengali layout like image)
                        let typeLabel = type === 'admit' ? '🏥' : type === 'preorder' ? '⚕️' : '🔧';
                        prescriptionHtml += `<tr>
                            <td class="med-name-cell">${typeLabel} <strong>${row.name}</strong>${row.company ? ' <small class="text-muted">(${row.company})</small>' : ''}</td>
                            <td>${morning || ''}</td>
                            <td>${noon || ''}</td>
                            <td>${night || ''}</td>
                            <td class="tick">${mealBefore}</td>
                            <td class="tick">${mealAfter}</td>
                            <td>${durDin}</td>
                            <td>${durMas}</td>
                            <td class="tick">${durCholbe}</td>
                        </tr>`;
                        cntAll++;
                    });
                }

                let empty8 = '<tr><td colspan="8" class="text-center p-3 text-muted">No medicines added</td></tr>';
                $('#admitTableBody').html(admitHtml || empty8);
                $('#preorderTableBody').html(preorderHtml || empty8);
                $('#postorderTableBody').html(postorderHtml || empty8);
                $('#prescriptionRows').html(prescriptionHtml || '<tr><td colspan="9" class="text-center text-muted p-3">No medicines added yet</td></tr>');

                $('#admitCount').text(medicines.admit.length);
                $('#preorderCount').text(medicines.preorder.length);
                $('#postorderCount').text(medicines.postorder.length);

                updateButtonStates();
            });
        }

        function showAlert(message, type) {
            let cls = type === 'success' ? 'alert-success' : 'alert-danger';
            let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            $('#alertMessage').html(`
                <div class="alert ${cls} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon}"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            `);
            setTimeout(() => { $('.alert').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
        }

        window.deleteMedicine = function(id) {
            if (confirm('Delete this medicine?')) {
                $.ajax({
                    url: `/templates/medicine/ajax/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.ok) { showAlert('Deleted!', 'success'); loadAllMedicines(); }
                    }
                });
            }
        };

    });
}
</script>
@stop