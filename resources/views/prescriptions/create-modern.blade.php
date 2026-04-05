@extends('adminlte::page')

@section('title', 'Create Modern Prescription')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-file-medical"></i> Create Prescription</h1>
@endsection

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Fix the following:</strong>
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-md"></i> New Prescription</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('prescriptions.store') }}" method="post" id="prescriptionForm">
                @csrf

                <!-- Surgery Template Selection -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-purple">
                            <div class="card-header bg-purple text-white" style="background-color: #6f42c1 !important;">
                                <h6 class="mb-0"><i class="fas fa-file-medical-alt"></i> Surgery Template (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="surgery_template" class="font-weight-bold">Select Surgery Template</label>
                                            <select name="surgery_template" id="surgery_template" class="form-control">
                                                <option value="">-- Select template (optional) --</option>
                                            </select>
                                            <small class="text-muted">Select a template to auto-populate prescription fields</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-info btn-sm" id="loadTemplateBtn" disabled>
                                                    <i class="fas fa-download"></i> Load Template
                                                </button>
                                                <button type="button" class="btn btn-warning btn-sm" id="clearTemplateBtn">
                                                    <i class="fas fa-times"></i> Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Selection & Vitals -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Patient Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="patient_id" class="font-weight-bold">Select Patient <span class="text-danger">*</span></label>
                                    <select name="patient_id" id="patient_id" class="form-control" required>
                                        <option value="">-- Select patient --</option>
                                        @foreach($patients as $p)
                                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->patientname ?? 'Patient' }} {{ $p->patientcode ? '('.$p->patientcode.')' : '' }} {{ $p->mobile_no ? '- '.$p->mobile_no : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success" id="vitalsCard" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-heartbeat"></i> Latest Vitals</h6>
                            </div>
                            <div class="card-body" id="vitalsContent">
                                <!-- Vitals will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis with ICD-10 -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-stethoscope"></i> Diagnosis (ICD-10)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="diagnosis" class="font-weight-bold">Primary Diagnosis</label>
                                    <div class="input-group">
                                        <input type="text" id="diagnosis" name="diagnosis" class="form-control icd10-search"
                                               placeholder="Search ICD-10 codes or diagnosis..." value="{{ old('diagnosis') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Start typing to search ICD-10 codes and descriptions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Medication Rows -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-pills"></i> Medications</h6>
                                <button type="button" class="btn btn-sm btn-light" id="addMedicineBtn">
                                    <i class="fas fa-plus"></i> Add Medicine
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="medicinesContainer">
                                    <!-- Initial medicine row -->
                                    <div class="medicine-row border rounded p-3 mb-3 bg-light">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Medicine <span class="text-danger">*</span></label>
                                                <select name="medicines[0][medicine_name]" class="form-control medicine-select" required>
                                                    <option value="">Select medicine</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Dosage</label>
                                                <input type="text" name="medicines[0][dosage]" class="form-control" placeholder="e.g., 500mg">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Frequency</label>
                                                <select name="medicines[0][frequency]" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="Once daily">Once daily</option>
                                                    <option value="Twice daily">Twice daily</option>
                                                    <option value="Three times daily">Three times daily</option>
                                                    <option value="Four times daily">Four times daily</option>
                                                    <option value="SOS">SOS</option>
                                                    <option value="PRN">PRN</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Duration</label>
                                                <input type="text" name="medicines[0][duration]" class="form-control" placeholder="e.g., 5 days">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Instructions</label>
                                                <input type="text" name="medicines[0][instructions]" class="form-control" placeholder="e.g., after meal">
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label><br>
                                                <button type="button" class="btn btn-sm btn-danger remove-medicine" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-notes-medical"></i> Additional Notes</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="notes">Doctor's Advice / Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any additional advice or notes for the patient...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Save Prescription
                                </button>
                                <button type="button" class="btn btn-primary btn-lg ml-2" id="saveAndPrintBtn">
                                    <i class="fas fa-print"></i> Save & Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.medicine-row {
    position: relative;
    transition: all 0.3s ease;
}
.medicine-row:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.select2-container--default .select2-selection--single {
    height: 38px;
    padding-top: 3px;
}
.icd10-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}
.icd10-suggestion {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}
.icd10-suggestion:hover {
    background-color: #f8f9fa;
}
.icd10-suggestion strong {
    color: #007bff;
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let medicineRowCount = 1;

    // Initialize Select2 for medicine search
    function initializeMedicineSelect(selectElement) {
        selectElement.select2({
            ajax: {
                url: '{{ route("medicines.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Search medicine...',
            allowClear: true
        });
    }

    // Initialize first medicine select
    initializeMedicineSelect($('.medicine-select').first());

    // Add new medicine row
    $('#addMedicineBtn').click(function() {
        const newRow = `
            <div class="medicine-row border rounded p-3 mb-3 bg-light">
                <div class="row">
                    <div class="col-md-3">
                        <label>Medicine <span class="text-danger">*</span></label>
                        <select name="medicines[${medicineRowCount}][medicine_name]" class="form-control medicine-select" required>
                            <option value="">Select medicine</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Dosage</label>
                        <input type="text" name="medicines[${medicineRowCount}][dosage]" class="form-control" placeholder="e.g., 500mg">
                    </div>
                    <div class="col-md-2">
                        <label>Frequency</label>
                        <select name="medicines[${medicineRowCount}][frequency]" class="form-control">
                            <option value="">Select</option>
                            <option value="Once daily">Once daily</option>
                            <option value="Twice daily">Twice daily</option>
                            <option value="Three times daily">Three times daily</option>
                            <option value="Four times daily">Four times daily</option>
                            <option value="SOS">SOS</option>
                            <option value="PRN">PRN</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Duration</label>
                        <input type="text" name="medicines[${medicineRowCount}][duration]" class="form-control" placeholder="e.g., 5 days">
                    </div>
                    <div class="col-md-2">
                        <label>Instructions</label>
                        <input type="text" name="medicines[${medicineRowCount}][instructions]" class="form-control" placeholder="e.g., after meal">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-danger remove-medicine">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#medicinesContainer').append(newRow);
        initializeMedicineSelect($('#medicinesContainer .medicine-select').last());
        medicineRowCount++;
        updateRemoveButtons();
    });

    // Remove medicine row
    $(document).on('click', '.remove-medicine', function() {
        $(this).closest('.medicine-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const rows = $('.medicine-row');
        rows.find('.remove-medicine').toggle(rows.length > 1);
    }

    // ICD-10 Search
    let icd10Timeout;
    $('.icd10-search').on('input', function() {
        const query = $(this).val();
        const $suggestions = $('#icd10Suggestions');

        clearTimeout(icd10Timeout);

        if (query.length < 2) {
            $suggestions.hide();
            return;
        }

        icd10Timeout = setTimeout(function() {
            $.get('{{ route("icd10.search") }}', { q: query }, function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(item) {
                        html += `<div class="icd10-suggestion" data-code="${item.code}" data-description="${item.description}">
                            <strong>${item.code}</strong> - ${item.description}
                        </div>`;
                    });
                    $suggestions.html(html).show();
                } else {
                    $suggestions.hide();
                }
            });
        }, 300);
    });

    // ICD-10 suggestion click
    $(document).on('click', '.icd10-suggestion', function() {
        const code = $(this).data('code');
        const description = $(this).data('description');
        $('.icd10-search').val(`${code} - ${description}`);
        $('#icd10Suggestions').hide();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.icd10-search, #icd10Suggestions').length) {
            $('#icd10Suggestions').hide();
        }
    });

    // Load patient vitals
    $('#patient_id').change(function() {
        const patientId = $(this).val();

        if (patientId) {
            $.get('{{ route("patients.vitals", ":patientId") }}'.replace(':patientId', patientId), function(data) {
                if (data.vitals) {
                    let vitalsHtml = `
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Weight</small>
                                <strong>${data.vitals.weight || 'N/A'} kg</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">BP</small>
                                <strong>${data.vitals.bp_systolic || 'N/A'}/${data.vitals.bp_diastolic || 'N/A'} mmHg</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Heart Rate</small>
                                <strong>${data.vitals.heart_rate || 'N/A'} bpm</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Temperature</small>
                                <strong>${data.vitals.temperature || 'N/A'}°F</strong>
                            </div>
                        </div>
                    `;
                    $('#vitalsContent').html(vitalsHtml);
                    $('#vitalsCard').show();
                } else {
                    $('#vitalsCard').hide();
                }
            }).fail(function() {
                $('#vitalsCard').hide();
            });
        } else {
            $('#vitalsCard').hide();
        }
    });

    // Save and Print functionality
    $('#saveAndPrintBtn').click(function() {
        const form = $('#prescriptionForm');
        const originalAction = form.attr('action');

        // Submit form first
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // If successful, redirect to show page with print parameter
                window.location.href = response.redirect_url + '?print=1';
            },
            error: function(xhr) {
                // Handle errors
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    for (let field in errors) {
                        errors[field].forEach(function(error) {
                            errorHtml += '<li>' + error + '</li>';
                        });
                    }
                    errorHtml += '</ul></div>';
                    $('.container-fluid').prepend(errorHtml);
                }
            }
        });
    });

    // Initialize
    updateRemoveButtons();

    // Surgery Template Functionality
    // Load surgery templates on page load
    $.get('{{ route("api.surgery-templates.list") }}', function(templates) {
        const select = $('#surgery_template');
        templates.forEach(template => {
            select.append(`<option value="${template.id}">${template.template_name}</option>`);
        });
    });

    // Enable/disable load button based on template selection
    $('#surgery_template').change(function() {
        const templateId = $(this).val();
        $('#loadTemplateBtn').prop('disabled', !templateId);
    });

    // Load template data
    $('#loadTemplateBtn').click(function() {
        const templateId = $('#surgery_template').val();

        if (!templateId) return;

        $.get('{{ route("api.surgery-templates.data", ":templateId") }}'.replace(':templateId', templateId), function(response) {
            if (response.success) {
                const data = response.data;

                // Clear existing form data first
                clearFormData();

                // Auto-populate Rx Admission medicines
                if (data.rx_admission && data.rx_admission.length > 0) {
                    data.rx_admission.forEach((medicine, index) => {
                        if (index > 0) {
                            addMedicineRow();
                        }
                        const row = $('.medicine-row').eq(index);
                        row.find('.medicine-select').val(medicine.medicine_id);
                        row.find('input[name*="dosage"]').val(medicine.dosage || '');
                        row.find('select[name*="frequency"]').val(medicine.frequency || 'Three times daily');
                        row.find('input[name*="duration"]').val(medicine.duration || '5 days');
                        row.find('textarea[name*="instructions"]').val(medicine.instructions || '');
                    });
                }

                // Auto-populate Pre-Operative Orders
                if (data.pre_op_orders && data.pre_op_orders.length > 0) {
                    const preOpContainer = $('#preOpOrdersContainer');
                    if (preOpContainer.length === 0) {
                        // Create pre-op section if it doesn't exist
                        $('.card-body').append(`
                            <div class="row mb-4" id="preOpSection">
                                <div class="col-12">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-procedures"></i> Pre-Operative Orders</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="preOpOrdersContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    data.pre_op_orders.forEach(order => {
                        $('#preOpOrdersContainer').append(`
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                ${order}
                            </div>
                        `);
                    });
                }

                // Auto-populate Post-Operative Orders
                if (data.post_op_orders && data.post_op_orders.length > 0) {
                    const postOpContainer = $('#postOpOrdersContainer');
                    if (postOpContainer.length === 0) {
                        // Create post-op section if it doesn't exist
                        $('#preOpSection').after(`
                            <div class="row mb-4" id="postOpSection">
                                <div class="col-12">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Post-Operative Orders</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="postOpOrdersContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    data.post_op_orders.forEach(order => {
                        $('#postOpOrdersContainer').append(`
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                ${order}
                            </div>
                        `);
                    });
                }

                // Auto-populate Investigations
                if (data.investigations && data.investigations.length > 0) {
                    const investigationsContainer = $('#investigationsContainer');
                    if (investigationsContainer.length === 0) {
                        // Create investigations section if it doesn't exist
                        $('#postOpSection').after(`
                            <div class="row mb-4" id="investigationsSection">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-microscope"></i> Investigations</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="investigationsContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    data.investigations.forEach(investigation => {
                        $('#investigationsContainer').append(`
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                ${investigation}
                            </div>
                        `);
                    });
                }

                // Auto-populate Advices
                if (data.advices && data.advices.length > 0) {
                    const advicesContainer = $('#advicesContainer');
                    if (advicesContainer.length === 0) {
                        // Create advices section if it doesn't exist
                        $('#investigationsSection').after(`
                            <div class="row mb-4" id="advicesSection">
                                <div class="col-12">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0"><i class="fas fa-lightbulb"></i> General Advices</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="advicesContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    data.advices.forEach(advice => {
                        $('#advicesContainer').append(`
                            <div class="alert alert-secondary alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                ${advice}
                            </div>
                        `);
                    });
                }

                // Add notes if present
                if (data.notes) {
                    const notesTextarea = $('textarea[name="notes"]');
                    if (notesTextarea.length === 0) {
                        // Create notes section if it doesn't exist
                        $('#advicesSection').after(`
                            <div class="row mb-4" id="notesSection">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Additional Notes</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea name="notes" class="form-control" rows="3">${data.notes}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        notesTextarea.val(data.notes);
                    }
                }

                // Show success message
                showAlert('Template loaded successfully! You can edit any field before saving.', 'success');
            }
        }).fail(function() {
            showAlert('Failed to load template data. Please try again.', 'error');
        });
    });

    // Clear template data
    $('#clearTemplateBtn').click(function() {
        clearFormData();
        $('#surgery_template').val('');
        $('#loadTemplateBtn').prop('disabled', true);
        showAlert('Form data cleared.', 'info');
    });

    // Function to clear form data
    function clearFormData() {
        // Clear medicines
        $('#medicinesContainer .medicine-row:not(:first)').remove();
        $('#medicinesContainer .medicine-row:first').find('input, select, textarea').val('');

        // Remove dynamic sections
        $('#preOpSection, #postOpSection, #investigationsSection, #advicesSection, #notesSection').remove();

        // Clear diagnosis
        $('#diagnosis').val('');
    }

    // Function to show alerts
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' :
                          type === 'error' ? 'alert-danger' : 'alert-info';

        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                ${message}
            </div>
        `);

        $('.container-fluid').prepend(alert);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
});
</script>

<!-- ICD-10 Suggestions Container -->
<div id="icd10Suggestions" class="icd10-suggestions"></div>
@endsection
