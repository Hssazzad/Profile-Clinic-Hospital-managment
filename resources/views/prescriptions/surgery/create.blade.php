@extends('adminlte::page')

@section('title', 'Add Medicine')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-pills"></i> Add Medicine</h1>
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
</style>
@stop

@section('content')
<div class="container-fluid">
    <!-- Template Selection -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
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

    <!-- Medicine Section -->
    <div class="row" id="medicineSection" style="display: none;">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i> Add New Medicine
                </div>
                <div class="card-body">
                    <!-- Medicine Selection -->
                    <div class="form-group">
                        <label><i class="fas fa-capsules"></i> Medicine Name <span class="text-danger">*</span></label>
                        <select id="medicine_name" class="form-control select2">
                            <option value="">-- Select Medicine --</option>
                        </select>
                    </div>

                    <!-- Medicine Details (Readonly) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Brand</label>
                                <input type="text" id="medicine_brand" class="form-control" readonly placeholder="Auto-filled">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Generic</label>
                                <input type="text" id="medicine_generic" class="form-control" readonly placeholder="Auto-filled">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Strength</label>
                                <input type="text" id="medicine_strength" class="form-control" readonly placeholder="Auto-filled">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <input type="text" id="medicine_type" class="form-control" readonly placeholder="Auto-filled">
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dosage</label>
                                <select id="medicine_dosage" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1+0+1">1+0+1 (Morning+Noon+Night)</option>
                                    <option value="1+1+1">1+1+1 (Three Times)</option>
                                    <option value="0+0+1">0+0+1 (Only Night)</option>
                                    <option value="1+0+0">1+0+0 (Only Morning)</option>
                                    <option value="1+1+1+1">1+1+1+1 (Four Times)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duration</label>
                                <select id="medicine_duration" class="form-control">
                                    <option value="">Select</option>
                                    <option value="3 Days">3 Days</option>
                                    <option value="5 Days">5 Days</option>
                                    <option value="7 Days">7 Days</option>
                                    <option value="10 Days">10 Days</option>
                                    <option value="14 Days">14 Days</option>
                                    <option value="1 Month">1 Month</option>
                                    <option value="Continue">Continue</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Route</label>
                                <select id="medicine_route" class="form-control">
                                    <option value="Oral">Oral</option>
                                    <option value="IV">IV</option>
                                    <option value="IM">IM</option>
                                    <option value="SC">SC</option>
                                    <option value="Topical">Topical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order Type</label>
                                <select id="medicine_order_type" class="form-control">
                                    <option value="admit">🏥 Admission</option>
                                    <option value="preorder">⚕️ Pre-Operation</option>
                                    <option value="postorder">🔧 Post-Operation</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Instruction</label>
                        <select id="medicine_instruction" class="form-control">
                            <option value="">Select</option>
                            <option value="Before Food">Before Food</option>
                            <option value="After Food">After Food</option>
                            <option value="Empty Stomach">Empty Stomach</option>
                            <option value="With Food">With Food</option>
                            <option value="At Bed Time">At Bed Time</option>
                        </select>
                    </div>

                    <button class="btn-add" onclick="addMedicine()">
                        <i class="fas fa-plus"></i> Add to List
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list"></i> Added Medicines
                </div>
                <div class="card-body">
                    <table class="medicine-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Medicine</th>
                                <th width="10%">Type</th>
                                <th width="10%">Dosage</th>
                                <th width="10%">Duration</th>
                                <th width="15%">Order</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="medicineBody">
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-pills fa-3x mb-3"></i>
                                        <p>No medicines added yet</p>
                                        <small class="small-text">Select medicine from left panel</small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
            $('#medicineSection').fadeIn();
            loadMedicinesFromTemplate(templateid);
            loadExistingMedicines(templateid);
        } else {
            $('#medicineSection').fadeOut();
        }
    });
    
    // Medicine selection
    $('#medicine_name').on('change', function() {
        let selected = $(this).find(':selected');
        let medData = selected.data('medicine');
        if(medData) {
            fillMedicineDetails(medData);
        } else {
            // Clear fields if no selection
            clearMedicineFields();
        }
    });
});

// Clear medicine fields
function clearMedicineFields() {
    $('#medicine_brand, #medicine_generic, #medicine_strength, #medicine_type').val('');
}

// Load medicines
function loadMedicinesFromTemplate(templateid) {
    $.ajax({
        url: "{{ route('templates.medicine.ajax.list') }}",
        type: "GET",
        data: {templateid: templateid},
        success: function(res) {
            console.log('Server Response:', res); // Debug
            
            if(res.ok && res.rows && res.rows.length > 0) {
                let dropdown = $('#medicine_name').empty().append('<option value="">-- Select Medicine --</option>');
                
                res.rows.forEach(med => {
                    // Safe access with fallback
                    let brand = med.brand || med.name || 'Unknown';
                    let generic = med.generic || '';
                    let strength = med.strength || '';
                    
                    let text = brand;
                    if(strength) text += ' ' + strength;
                    if(generic) text += ' (' + generic + ')';
                    
                    let option = $('<option>', {
                        value: med.id,
                        text: text,
                        'data-medicine': JSON.stringify(med)
                    });
                    dropdown.append(option);
                });
                
                dropdown.trigger('change');
            } else {
                $('#medicine_name').html('<option value="">No medicines available</option>');
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
                    brand: med.brand || med.name || 'Unknown',
                    generic: med.generic || '',
                    strength: med.strength || '',
                    type: med.type || 'Tablet',
                    dosage: med.dosage || '-',
                    duration: med.duration || '-',
                    route: med.route || 'Oral',
                    order_type: med.order_type || 'admit',
                    instruction: med.instruction || ''
                }));
                updateMedicineTable();
            }
        }
    });
}

// Fill details
function fillMedicineDetails(med) {
    $('#medicine_brand').val(med.brand || med.name || '');
    $('#medicine_generic').val(med.generic || '');
    $('#medicine_strength').val(med.strength || '');
    $('#medicine_type').val(med.type || 'Tablet');
}

// Add medicine
function addMedicine() {
    if(!$('#medicine_name').val()) {
        alert('Please select a medicine');
        return;
    }
    
    let medicine = {
        id: Date.now(),
        brand: $('#medicine_brand').val() || 'Unknown',
        generic: $('#medicine_generic').val() || '',
        strength: $('#medicine_strength').val() || '',
        type: $('#medicine_type').val() || 'Tablet',
        dosage: $('#medicine_dosage').val() || '-',
        duration: $('#medicine_duration').val() || '-',
        route: $('#medicine_route').val() || 'Oral',
        order_type: $('#medicine_order_type').val() || 'admit',
        instruction: $('#medicine_instruction').val() || ''
    };
    
    medicines.push(medicine);
    updateMedicineTable();
    clearForm();
    alert('Medicine added successfully');
}

// Update table
function updateMedicineTable() {
    if(medicines.length === 0) {
        $('#medicineBody').html(`
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-pills fa-3x mb-3"></i>
                        <p>No medicines added yet</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    let rows = '';
    medicines.forEach((med, index) => {
        let orderClass = med.order_type === 'preorder' ? 'badge-pre' : 
                        med.order_type === 'postorder' ? 'badge-post' : 'badge-admit';
        
        let displayBrand = med.brand || 'Unknown';
        let displayGeneric = med.generic || '';
        let displayStrength = med.strength || '';
        
        rows += `<tr>
            <td>${index + 1}</td>
            <td>
                <strong>${displayBrand}</strong><br>
                <small class="small-text">${displayGeneric} ${displayStrength}</small>
            </td>
            <td>${med.type || 'Tablet'}</td>
            <td>${med.dosage || '-'}</td>
            <td>${med.duration || '-'}</td>
            <td><span class="badge ${orderClass}">${med.order_type}</span></td>
            <td>
                <button class="btn-delete" onclick="removeMedicine(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });
    
    $('#medicineBody').html(rows);
}

// Clear form
function clearForm() {
    $('#medicine_name').val('').trigger('change');
    $('#medicine_brand, #medicine_generic, #medicine_strength, #medicine_type').val('');
    $('#medicine_dosage, #medicine_duration, #medicine_instruction').val('');
    $('#medicine_route').val('Oral');
    $('#medicine_order_type').val('admit');
}

// Remove medicine
function removeMedicine(index) {
    if(confirm('Remove this medicine?')) {
        medicines.splice(index, 1);
        updateMedicineTable();
    }
}
</script>
@stop