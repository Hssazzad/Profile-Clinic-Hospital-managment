@extends('adminlte::page')

@section('title', 'Add Medicine')

@section('content_header')   
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-pills mr-2"></i>Add Medicine</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                    <li class="breadcrumb-item active">Add Medicine</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border-bottom: none;
    }
    
    .card-header i {
        margin-right: 8px;
    }
    
    .form-group {
        margin-bottom: 1.2rem;
    }
    
    .form-group label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 5px;
        font-size: 13px;
    }
    
    .form-control, .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }
    
    .form-control:focus, .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }
    
    .medicine-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .medicine-table th {
        background: #f8f9fa;
        padding: 12px 10px;
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .medicine-table td {
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
        font-size: 13px;
        vertical-align: middle;
    }
    
    .btn-add {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-add:hover {
        background: #218838;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .btn-delete:hover {
        background: #c82333;
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .badge-admit {
        background: #cce5ff;
        color: #004085;
    }
    
    .badge-pre {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-post {
        background: #d4edda;
        color: #155724;
    }
    
    .text-muted {
        color: #6c757d;
    }
    
    .small-text {
        font-size: 12px;
        color: #6c757d;
    }
    
    .empty-state {
        padding: 30px;
        text-align: center;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    /* Prescription table styling */
    .prescription-table {
        font-size: 0.9em;
    }
    
    .prescription-table th {
        background: #f8f9fa;
        font-weight: 600;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .prescription-table td {
        border: 1px solid #dee2e6;
        padding: 6px;
        vertical-align: middle;
    }
    
    .stepBtn {
        transition: all 0.3s;
    }
    
    .stepBtn.active {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
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
                            <label><i class="fas fa-file-medical"></i> Select Template <span class="text-danger">*</span></label>
                            <select id="templateid" class="form-control select2">
                                <option value="">-- Choose a template --</option>
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
                            <div class="col-md-12">
                                <button type="button" class="btn btn-sm w-100 stepBtn active btn-primary" id="stepBtn_admit">
                                    <i class="fas fa-file-medical"></i> Fresh Prescription
                                    <span id="admitCount" class="badge badge-light ml-2">0</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Fresh Prescription --}}
        <div class="row" id="medicineSection" style="display: none;">
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-file-medical mr-1"></i> Fresh Prescription</h3>
                    </div>
                    <form id="admitForm">
                        @csrf
                        <div class="card-body p-3">

                            {{-- Medicine Name (searchable dropdown + free text) --}}
                            <div class="form-group mb-2">
                                <label class="small"><i class="fas fa-capsules"></i> Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" id="medicine_name" list="medicine_list" class="form-control form-control-sm" placeholder="Type or select medicine...">
                                <datalist id="medicine_list">
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
                                    <option value="Escitalopram 10mg">
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
                                <input type="text" id="medicine_company" list="company_list" class="form-control form-control-sm" placeholder="Enter company name...">
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
                                        <select id="medicine_morning" class="form-control form-control-sm">
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
                                        <select id="medicine_noon" class="form-control form-control-sm">
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
                                        <select id="medicine_night" class="form-control form-control-sm">
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
                                        <select id="medicine_meal_timing" class="form-control form-control-sm">
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
                                            <input type="number" id="medicine_duration_num" class="form-control form-control-sm" placeholder="0" min="0" style="max-width:60px;">
                                            <select id="medicine_duration_type" class="form-control form-control-sm">
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
                                        <select id="medicine_route" class="form-control form-control-sm">
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
                                        <select id="medicine_order_type" class="form-control form-control-sm">
                                            <option value="admit" selected>📋 Fresh Prescription</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small">Special Instructions / Notes</label>
                                <select id="medicine_instruction" class="form-control form-control-sm">
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
                            <tbody id="medicineBody" style="font-size: 0.88em;">
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

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Check jQuery
if (typeof jQuery === 'undefined') {
    console.error('jQuery not loaded');
} else {
    console.log('jQuery loaded');
}

let medicines = [];

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select an option'
    });
    
    // Template change
    $('#templateid').on('change', function() {
        let templateid = $(this).val();
        if(templateid) {
            $('#medicineSection, #stepNavigation').fadeIn();
            loadMedicinesFromTemplate(templateid);
            loadExistingMedicines(templateid);
        } else {
            $('#medicineSection, #stepNavigation').fadeOut();
        }
    });
    
    
    // Add medicine button
    $('.btnAddMedicine').on('click', function() {
        addMedicine();
    });
    
});


// Load medicines
function loadMedicinesFromTemplate(templateid) {
    $.ajax({
        url: "{{ route('templates.medicine.ajax.list') }}",
        type: "GET",
        data: {templateid: templateid},
        success: function(res) {
            console.log('Server Response:', res);
            
            if(res.ok && res.rows && res.rows.length > 0) {
                // Update medicine datalist
                let datalist = $('#medicine_list').empty();
                res.rows.forEach(med => {
                    let brand = med.brand || med.name || 'Unknown';
                    let text = brand;
                    if(med.strength) text += ' ' + med.strength;
                    if(med.generic) text += ' (' + med.generic + ')';
                    
                    let option = $('<option>', {
                        value: text,
                        'data-medicine': JSON.stringify(med)
                    });
                    datalist.append(option);
                });
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            alert('Error loading medicines');
        }
    });
}

// Load existing medicines
function loadExistingMedicines(templateid) {
    $.ajax({
        url: "{{ route('templates.medicine.ajax.list') }}",
        type: "GET",
        data: {templateid: templateid},
        success: function(res) {
            if(res.ok && res.rows) {
                medicines = res.rows.map(med => ({
                    id: med.id,
                    name: med.brand || med.name || 'Unknown',
                    company: med.company || '',
                    morning: med.morning || '',
                    noon: med.noon || '',
                    night: med.night || '',
                    meal_timing: med.meal_timing || '',
                    duration_num: med.duration_num || '',
                    duration_type: med.duration_type || '',
                    route: med.route || 'Oral',
                    order_type: med.order_type || 'admit',
                    instruction: med.instruction || ''
                }));
                updateMedicineTable();
                updateCount();
            }
        }
    });
}

// Add medicine
function addMedicine() {
    let medicineName = $('#medicine_name').val();
    if(!medicineName) {
        alert('Please enter medicine name');
        return;
    }
    
    let medicine = {
        id: Date.now(),
        name: medicineName,
        company: $('#medicine_company').val() || '',
        morning: $('#medicine_morning').val() || '-',
        noon: $('#medicine_noon').val() || '-',
        night: $('#medicine_night').val() || '-',
        meal_timing: $('#medicine_meal_timing').val() || '-',
        duration_num: $('#medicine_duration_num').val() || '0',
        duration_type: $('#medicine_duration_type').val() || '',
        route: $('#medicine_route').val() || 'Oral',
        order_type: 'admit',
        instruction: $('#medicine_instruction').val() || ''
    };
    
    medicines.push(medicine);
    updateMedicineTable();
    updateCount();
    clearForm();
    alert('Medicine added successfully');
}

// Update table
function updateMedicineTable() {
    if(medicines.length === 0) {
        $('#medicineBody').html('<tr><td colspan="8" class="text-center p-3">No medicines added</td></tr>');
        return;
    }
    
    let rows = '';
    medicines.forEach((med, index) => {
        let duration = med.duration_num + ' ' + med.duration_type;
        if(med.duration_num === '0' || !med.duration_type) duration = '-';
        
        rows += `<tr>
            <td>${index + 1}</td>
            <td>
                <strong>${med.name}</strong><br>
                <small class="text-muted">${med.company}</small>
            </td>
            <td class="text-center">${med.morning}</td>
            <td class="text-center">${med.noon}</td>
            <td class="text-center">${med.night}</td>
            <td class="text-center">${med.meal_timing}</td>
            <td class="text-center">${duration}</td>
            <td class="text-center">
                <button class="btn btn-danger btn-xs" onclick="removeMedicine(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });
    
    $('#medicineBody').html(rows);
}


// Update count
function updateCount() {
    let admitCount = medicines.length;
    $('#admitCount').text(admitCount);
}

// Clear form
function clearForm() {
    $('#medicine_name, #medicine_company').val('');
    $('#medicine_morning, #medicine_noon, #medicine_night').val('');
    $('#medicine_meal_timing, #medicine_duration_type, #medicine_instruction').val('');
    $('#medicine_duration_num').val('0');
    $('#medicine_route').val('Oral');
    $('#medicine_order_type').val('admit');
}

// Remove medicine
function removeMedicine(index) {
    if(confirm('Remove this medicine?')) {
        medicines.splice(index, 1);
        updateMedicineTable();
        updateCount();
    }
}
</script>
@stop