@extends('adminlte::page')

@section('title', 'Create Surgery Template')

@section('content_header')
    <h1 class="mb-0">Create Surgery Template</h1>
@stop

@section('content')
<div class="container-fluid">
    <form id="templateForm" method="POST" action="{{ route('surgery-templates.store') }}">
        @csrf
        
        <!-- Basic Information -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Template Information</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="template_name">Template Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="template_name" name="template_name" required>
                </div>
            </div>
        </div>

        <!-- Rx Admission -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Rx On Admission</h3>
                <button type="button" class="btn btn-sm btn-success float-right" onclick="addMedicineRow('rx_admission')">
                    <i class="fas fa-plus"></i> Add Medicine
                </button>
            </div>
            <div class="card-body">
                <div id="rx_admission_container">
                    <!-- Dynamic medicine rows will be added here -->
                </div>
            </div>
        </div>

        <!-- Pre-Operative Orders -->
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Pre-Operative Orders</h3>
                <button type="button" class="btn btn-sm btn-success float-right" onclick="addOrderRow('pre_op_orders')">
                    <i class="fas fa-plus"></i> Add Order
                </button>
            </div>
            <div class="card-body">
                <div id="pre_op_orders_container">
                    <!-- Dynamic order rows will be added here -->
                </div>
            </div>
        </div>

        <!-- Post-Operative Orders -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Post-Operative Orders</h3>
                <button type="button" class="btn btn-sm btn-success float-right" onclick="addOrderRow('post_op_orders')">
                    <i class="fas fa-plus"></i> Add Order
                </button>
            </div>
            <div class="card-body">
                <div id="post_op_orders_container">
                    <!-- Dynamic order rows will be added here -->
                </div>
            </div>
        </div>

        <!-- Investigations -->
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Investigations</h3>
                <button type="button" class="btn btn-sm btn-success float-right" onclick="addInvestigationRow()">
                    <i class="fas fa-plus"></i> Add Investigation
                </button>
            </div>
            <div class="card-body">
                <div id="investigations_container">
                    <!-- Dynamic investigation rows will be added here -->
                </div>
            </div>
        </div>

        <!-- Advices -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">General Advices</h3>
                <button type="button" class="btn btn-sm btn-success float-right" onclick="addAdviceRow()">
                    <i class="fas fa-plus"></i> Add Advice
                </button>
            </div>
            <div class="card-body">
                <div id="advices_container">
                    <!-- Dynamic advice rows will be added here -->
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Additional Notes</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Template
                </button>
                <a href="{{ route('surgery-templates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Medicine Row Template (hidden) -->
<template id="medicineRowTemplate">
    <div class="row mb-2 medicine-row">
        <div class="col-md-3">
            <select class="form-control medicine-type" onchange="updateMedicines(this)">
                <option value="">Select Type</option>
                @foreach ($medicineTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control medicine-name" onchange="updateCompany(this)">
                <option value="">Select Medicine</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control company-name" readonly placeholder="Company">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<!-- Order Row Template (hidden) -->
<template id="orderRowTemplate">
    <div class="row mb-2 order-row">
        <div class="col-md-10">
            <input type="text" class="form-control" placeholder="Enter order details">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<!-- Investigation Row Template (hidden) -->
<template id="investigationRowTemplate">
    <div class="row mb-2 investigation-row">
        <div class="col-md-10">
            <input type="text" class="form-control" placeholder="Enter investigation">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<!-- Advice Row Template (hidden) -->
<template id="adviceRowTemplate">
    <div class="row mb-2 advice-row">
        <div class="col-md-10">
            <input type="text" class="form-control" placeholder="Enter advice">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>
@stop

@push('scripts')
<script>
// Medicine data from server
const medicines = @json($medicines);

// Add medicine row
function addMedicineRow(containerId) {
    const template = document.getElementById('medicineRowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById(containerId + '_container').appendChild(clone);
}

// Add order row
function addOrderRow(containerId) {
    const template = document.getElementById('orderRowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById(containerId + '_container').appendChild(clone);
}

// Add investigation row
function addInvestigationRow() {
    const template = document.getElementById('investigationRowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('investigations_container').appendChild(clone);
}

// Add advice row
function addAdviceRow() {
    const template = document.getElementById('adviceRowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('advices_container').appendChild(clone);
}

// Remove row
function removeRow(button) {
    button.closest('.row').remove();
}

// Update medicines dropdown based on type
function updateMedicines(select) {
    const type = select.value;
    const medicineSelect = select.closest('.medicine-row').querySelector('.medicine-name');
    const companyInput = select.closest('.medicine-row').querySelector('.company-name');
    
    // Clear current selections
    medicineSelect.innerHTML = '<option value="">Select Medicine</option>';
    companyInput.value = '';
    
    if (type) {
        const filteredMedicines = medicines.filter(m => m.type === type);
        filteredMedicines.forEach(medicine => {
            const option = document.createElement('option');
            option.value = medicine.id;
            option.textContent = medicine.display_name || medicine.name;
            option.dataset.company = medicine.company_name;
            medicineSelect.appendChild(option);
        });
    }
}

// Update company name based on medicine selection
function updateCompany(select) {
    const companyInput = select.closest('.medicine-row').querySelector('.company-name');
    const selectedOption = select.options[select.selectedIndex];
    companyInput.value = selectedOption.dataset.company || '';
}

// Form submission
$('#templateForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        template_name: formData.get('template_name'),
        notes: formData.get('notes'),
        rx_admission: [],
        pre_op_orders: [],
        post_op_orders: [],
        investigations: [],
        advices: []
    };
    
    // Collect Rx Admission medicines
    document.querySelectorAll('#rx_admission_container .medicine-row').forEach(row => {
        const type = row.querySelector('.medicine-type').value;
        const medicineId = row.querySelector('.medicine-name').value;
        const company = row.querySelector('.company-name').value;
        
        if (type && medicineId) {
            data.rx_admission.push({
                type: type,
                medicine_id: medicineId,
                company_name: company
            });
        }
    });
    
    // Collect Pre-Op Orders
    document.querySelectorAll('#pre_op_orders_container .order-row input').forEach(input => {
        if (input.value.trim()) {
            data.pre_op_orders.push(input.value.trim());
        }
    });
    
    // Collect Post-Op Orders
    document.querySelectorAll('#post_op_orders_container .order-row input').forEach(input => {
        if (input.value.trim()) {
            data.post_op_orders.push(input.value.trim());
        }
    });
    
    // Collect Investigations
    document.querySelectorAll('#investigations_container .investigation-row input').forEach(input => {
        if (input.value.trim()) {
            data.investigations.push(input.value.trim());
        }
    });
    
    // Collect Advices
    document.querySelectorAll('#advices_container .advice-row input').forEach(input => {
        if (input.value.trim()) {
            data.advices.push(input.value.trim());
        }
    });
    
    // Submit via AJAX
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert('Template created successfully!');
                window.location.href = '/surgery-templates';
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                let errorMessage = 'Please fix the following errors:\n';
                Object.keys(errors).forEach(key => {
                    errorMessage += '- ' + errors[key][0] + '\n';
                });
                alert(errorMessage);
            } else {
                alert('An error occurred while saving the template.');
            }
        }
    });
});
</script>
@endpush
