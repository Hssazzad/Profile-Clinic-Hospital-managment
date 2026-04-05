{{-- resources/views/surgery-prescriptions/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Surgery Prescription')

@section('content_header')
    <div class="container-fluid">      
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-prescription"></i> Surgery Prescription</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('surgery-prescriptions.index') }}">Surgery Prescriptions</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .progress { height: 20px; margin-bottom: 20px; }
    .progress-bar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: width 0.6s ease; }
    .step-indicator { margin-bottom: 30px; }
    .step-item { text-align: center; position: relative; }
    .step-number { width: 40px; height: 40px; line-height: 40px; border-radius: 50%; display: inline-block; font-weight: bold; margin-bottom: 10px; transition: all 0.3s; }
    .step-number.bg-success { background: #28a745 !important; }
    .step-label { font-size: 14px; font-weight: 500; }
    .step.active .step-number { transform: scale(1.1); box-shadow: 0 0 15px rgba(40, 167, 69, 0.5); }
    .card { border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .card-header { background: #ADD8E6; color: white; font-weight: 600; border-radius: 8px 8px 0 0 !important; }
    .card-header h5 { margin: 0; font-size: 16px; }
    .form-group label { font-weight: 500; color: #495057; font-size: 13px; margin-bottom: 4px; }
    .required:after { content: " *"; color: red; }
    .btn-primary, .btn-success { padding: 12px 25px; font-weight: 600; border-radius: 8px; transition: all 0.3s; font-size: 15px; min-height: 48px; }
    .btn-primary:hover, .btn-success:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
    .patient-info-card { background: #f8f9fa; border-left: 4px solid #28a745; border-radius: 5px; padding: 15px; margin-top: 15px; }
    .medicine-table th { background: #f4f6f9; font-size: 13px; font-weight: 600; }
    .medicine-table td { font-size: 13px; vertical-align: middle; }
    .select2-container--bootstrap4 .select2-selection { border-radius: 5px; border-color: #ced4da; }
    .btn-delete { background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s; font-weight: 600; }
    .btn-delete:hover { background: #c82333; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); }
    .btn-add { background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s; width: 100%; font-size: 15px; }
    .btn-add:hover { background: #218838; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4); }
    .footer-buttons { display: flex; justify-content: space-between; align-items: center; }
    .footer-buttons .btn { padding: 12px 30px; font-size: 16px; font-weight: 600; min-width: 150px; border-radius: 8px; }
    .footer-buttons .btn-success { background: #28a745; border-color: #28a745; }
    .footer-buttons .btn-success:hover { background: #218838; border-color: #218838; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3); }
    .footer-buttons .btn-primary { background: #007bff; border-color: #007bff; }
    .footer-buttons .btn-primary:hover { background: #0056b3; border-color: #0056b3; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3); }
    .patient-table-wrapper { max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 20px; }
    .patient-table { width: 100%; border-collapse: collapse; }
    .patient-table th { background: #f4f6f9; position: sticky; top: 0; z-index: 10; padding: 12px 10px; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    .patient-table td { padding: 10px; font-size: 13px; border-bottom: 1px solid #e9ecef; cursor: pointer; transition: background 0.2s; }
    .patient-table tbody tr:hover { background: #e8f4fd; }
    .patient-table tbody tr.selected { background: #cce5ff; border-left: 3px solid #007bff; }
    .search-box { margin-bottom: 15px; }
    .search-box input { border-radius: 20px; padding: 10px 20px; border: 1px solid #ced4da; width: 100%; font-size: 14px; }
    .search-box input:focus { outline: none; border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
    .patient-count { font-size: 13px; color: #6c757d; margin-top: 10px; }
    .select-patient-btn { background: #28a745; color: white; border: none; padding: 3px 10px; border-radius: 3px; font-size: 12px; cursor: pointer; }
    .patient-id-badge { background: #6c757d; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    .template-section { background: #f8f9fc; border: 1px solid #d1d9e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .nav-tabs .nav-link { font-weight: 500; color: #495057; position: relative; }
    .nav-tabs .nav-link.active { color: #007bff; border-bottom: 3px solid #007bff; }
    .nav-tabs .nav-link.disabled { color: #6c757d; cursor: not-allowed; opacity: 0.6; }
    .step-badge { margin-left: 5px; font-size: 10px; padding: 2px 6px; border-radius: 10px; }
    .step-navigation { margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
    .step-completed { position: absolute; top: 5px; right: 5px; color: #28a745; font-size: 12px; }
    .tab-content { background: #fff; border: 1px solid #dee2e6; border-top: none; padding: 20px; }

    /* ========== EXISTING DATA SEARCH PANEL ========== */
    .existing-data-panel {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
        border: 2px solid #007bff;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }
    .existing-data-panel .panel-title {
        font-size: 13px;
        font-weight: 700;
        color: #007bff;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .existing-data-panel .panel-title .badge {
        font-size: 10px;
        background: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
    }
    .existing-search-input {
        border: 1px solid #007bff !important;
        border-radius: 20px !important;
        padding: 6px 14px !important;
        font-size: 13px !important;
        width: 100%;
        transition: all 0.2s;
    }
    .existing-search-input:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.2) !important;
    }
    .existing-results-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: white;
        margin-top: 8px;
        display: none;
    }
    .existing-result-item {
        padding: 8px 12px;
        cursor: pointer;
        font-size: 13px;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.15s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .existing-result-item:hover { background: #e8f4fd; }
    .existing-result-item:last-child { border-bottom: none; }
    .existing-result-item .result-main { font-weight: 500; color: #333; }
    .existing-result-item .result-sub { font-size: 11px; color: #6c757d; }
    .btn-use-existing {
        background: #007bff;
        color: white;
        border: none;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-use-existing:hover { background: #0056b3; }
    .or-divider {
        text-align: center;
        margin: 12px 0;
        position: relative;
        color: #adb5bd;
        font-size: 12px;
        font-weight: 600;
    }
    .or-divider::before, .or-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 42%;
        height: 1px;
        background: #dee2e6;
    }
    .or-divider::before { left: 0; }
    .or-divider::after { right: 0; }

    .allergy-warning { color: #dc3545; font-weight: bold; background: #f8d7da; padding: 2px 6px; border-radius: 4px; }
    .success-toast { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .shortcut-hint { position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; font-size: 12px; z-index: 9999; opacity: 0; transition: opacity 0.3s; }
    .shortcut-hint.show { opacity: 1; }

    /* ========== PRINT STYLES - PRESCRIPTION PAD EXACT MATCH ========== */
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .toast, .toast-container, #toast-container { display: none !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { font-size: 12px; line-height: 1.4; margin: 0; padding: 0; background: none !important; }
        html { background: none !important; }
        .card { border: none !important; box-shadow: none !important; margin: 0 !important; background: none !important; }
        .card-body { padding: 0 !important; }
        .container-fluid { background: none !important; padding: 0 !important; }
        /* Prescription pad layout */
        #rx-header-table { width: 100%; }
        #rx-name-row { background: #fce4ec !important; }
        #rx-body-left { border-right: 2px solid #005F02 !important; }
        #rx-footer-table { width: 100%; border-top: 2px solid #005F02 !important; }
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @elseif(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @elseif(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Progress Bar -->
    <div class="step-indicator no-print">
        <div class="row">
            <div class="col-4 step-item" id="step1-indicator">
                <div class="step-number bg-primary text-white rounded-circle">1</div>
                <div class="step-label">Select Patient</div>
            </div>
            <div class="col-4 step-item" id="step2-indicator">
                <div class="step-number bg-secondary text-white rounded-circle">2</div>
                <div class="step-label">Load Template & Add All Items</div>
            </div>
            <div class="col-4 step-item" id="step3-indicator">
                <div class="step-number bg-secondary text-white rounded-circle">3</div>
                <div class="step-label">Review & Print</div>
            </div>
        </div>
        <div class="progress mt-3">
            <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 33%;">Step 1</div>
        </div>
    </div>

    <form id="prescriptionForm">
        @csrf

        <!-- ======= STEP 1: Patient Selection ======= -->
        <div class="card step-content no-print" id="step1-content">
            <div class="card-header">
                <h5><i class="fas fa-user mr-2"></i>Step 1: Select Patient</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <input type="text" id="patient-search-input" class="form-control" placeholder="🔍 Search by name, phone, patient ID...">
                </div>
                <div class="patient-table-wrapper">
                    <table class="patient-table" id="patients-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Age/Gender</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body">
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading patients...</p></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="patient-count" id="patient-count">Loading...</div>
                <div id="patient-details" style="display: none;" class="patient-info-card">
                    <h6><i class="fas fa-check-circle text-success mr-2"></i>Selected Patient:</h6>
                    <div class="row">
                        <div class="col-md-3"><strong>Name:</strong> <span id="patient-name"></span></div>
                        <div class="col-md-3"><strong>ID:</strong> <span id="patient-id"></span></div>
                        <div class="col-md-3"><strong>Age/Gender:</strong> <span id="patient-age-gender"></span></div>
                        <div class="col-md-3"><strong>Phone:</strong> <span id="patient-phone"></span></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary float-right" onclick="nextStep(1)">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- ======= STEP 2: Template & All Items ======= -->
        <div class="card step-content no-print" id="step2-content" style="display: none;">
            <div class="card-header">
                <h5><i class="fas fa-pills mr-2"></i>Step 2: Load Template & Add All Items</h5>
            </div>
            <div class="card-body">

                <!-- Template Dropdown -->
                <div class="template-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required"><i class="fas fa-user-md text-success mr-2"></i>Select Doctor *</label>
                                <select class="form-control select2" id="doctor-select" style="width: 100%;">
                                    <option value="">-- Choose a doctor --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required"><i class="fas fa-file-medical text-info mr-2"></i>Select Template *</label>
                                <select class="form-control select2" id="template-select" style="width: 100%;">
                                    <option value="">-- Choose a template --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-block" onclick="loadSelectedTemplate()">
                                    <i class="fas fa-download mr-2"></i>Load Template
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="template-loading" style="display: none;" class="alert alert-info py-2">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading template data...
                    </div>
                    <div id="selected-template-info" style="display: none;" class="mt-2 p-3 bg-light border-left border-info">
                        <div class="row">
                            <div class="col-md-4"><strong><i class="fas fa-user-md text-primary mr-1"></i>Assigned Doctor:</strong> <span id="selected-doctor-name" class="badge badge-primary p-2"></span></div>
                            <div class="col-md-4"><strong><i class="fas fa-check-circle text-success mr-1"></i>Selected Template:</strong> <span id="selected-template-name"></span></div>
                            <div class="col-md-4 text-right"><span id="selected-template-count" class="badge badge-info p-2"></span></div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mt-4">
                    <ul class="nav nav-tabs" id="templateTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="medicines-tab" data-toggle="tab" href="#medicines" role="tab" data-step="1">
                                <i class="fas fa-pills text-primary"></i> Medicines <span class="step-badge badge badge-primary">1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="admission-rx-tab" data-toggle="tab" href="#admission-rx" role="tab" data-step="2">
                                <i class="fas fa-hospital-user text-muted"></i> Admission Rx <span class="step-badge badge badge-secondary">2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="diagnosis-tab" data-toggle="tab" href="#diagnosis" role="tab" data-step="3">
                                <i class="fas fa-stethoscope text-muted"></i> Diagnosis <span class="step-badge badge badge-secondary">3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="investigations-tab" data-toggle="tab" href="#investigations" role="tab" data-step="4">
                                <i class="fas fa-flask text-muted"></i> Investigations <span class="step-badge badge badge-secondary">4</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="pre-op-tab" data-toggle="tab" href="#pre-op" role="tab" data-step="5">
                                <i class="fas fa-procedures text-muted"></i> Pre-operative Order <span class="step-badge badge badge-secondary">5</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="post-op-tab" data-toggle="tab" href="#post-op" role="tab" data-step="6">
                                <i class="fas fa-band-aid text-muted"></i> Post-operative Order <span class="step-badge badge badge-secondary">6</span>
                            </a>
                        </li>
                                                <li class="nav-item">
                            <a class="nav-link disabled" id="fresh-tab" data-toggle="tab" href="#fresh" role="tab" data-step="7">
                                <i class="fas fa-plus-circle text-muted"></i> Fresh Prescription <span class="step-badge badge badge-secondary">7</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="advice-tab" data-toggle="tab" href="#advice" role="tab" data-step="8">
                                <i class="fas fa-comment text-muted"></i> Advice <span class="step-badge badge badge-secondary">8</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="discharge-tab" data-toggle="tab" href="#discharge" role="tab" data-step="9">
                                <i class="fas fa-hospital text-muted"></i> Discharge <span class="step-badge badge badge-secondary">9</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="display-tab" data-toggle="tab" href="#display" role="tab">
                                <i class="fas fa-eye text-dark"></i> Display Template
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="templateTabsContent">

                        <!-- ===== TAB 1: MEDICINES ===== -->
                        <div class="tab-pane fade show active" id="medicines" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Medicine Manually</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Medicine Name *</label>
                                                <select class="form-control" id="medicine-search" style="width: 100%;"></select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dosage</label>
                                                        <select class="form-control" id="dosage">
                                                            <option value="1+0+1">1+0+1 (Morning + Night)</option>
                                                            <option value="1+1+1">1+1+1 (Three Times)</option>
                                                            <option value="0+0+1">0+0+1 (Night Only)</option>
                                                            <option value="1+1+0">1+1+0 (Morning + Afternoon)</option>
                                                            <option value="0+1+0">0+1+0 (Afternoon Only)</option>
                                                            <option value="1+0+0">1+0+0 (Morning Only)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Duration</label>
                                                        <input type="text" class="form-control" id="duration" placeholder="e.g., 7 days">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Route</label>
                                                        <select class="form-control" id="route">
                                                            <option value="Oral">Oral</option>
                                                            <option value="IV">IV</option>
                                                            <option value="IM">IM</option>
                                                            <option value="SC">SC</option>
                                                            <option value="Topical">Topical</option>
                                                            <option value="Inhalation">Inhalation</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Frequency</label>
                                                        <input type="text" class="form-control" id="frequency" placeholder="e.g., 8 hourly">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Medicine Type</label>
                                                        <select class="form-control" id="medicine-type">
                                                            <option value="Tablet">Tablet</option>
                                                            <option value="Capsule">Capsule</option>
                                                            <option value="Injection">Injection</option>
                                                            <option value="Syrup">Syrup</option>
                                                            <option value="Cream">Cream</option>
                                                            <option value="Ointment">Ointment</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Order Type</label>
                                                        <select class="form-control" id="order-type">
                                                            <option value="pre-op">Pre-Operative</option>
                                                            <option value="post-op">Post-Operative</option>
                                                            <option value="admission">Admission</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Instructions</label>
                                                <textarea class="form-control" id="medicine-instruction" rows="2" placeholder="Special instructions..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addMedicine()">
                                                <i class="fas fa-plus mr-2"></i>Add Medicine
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Medicines List <span class="badge badge-success ml-1" id="med-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover medicine-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Dosage</th>
                                                        <th>Duration</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="medicine-list">
                                                    <tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No medicines added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <span></span>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(2)">
                                    Next: Admission Rx <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 2: ADMISSION RX ===== -->
                        <div class="tab-pane fade" id="admission-rx" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2" style="text-align: center; width: 100%;">
                                            <h6 class="card-title mb-0" style="font-size: 16px; font-weight: bold; line-height: 1.2;">
                                                প্রফেসর ক্লিনিক<br>
                                                <span style="font-size: 14px; font-weight: normal;">মাঝিড়া, শাজাহানপুর, বগুড়া।</span>
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Admission prescription will show automatically
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(1)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Medicines
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(3)">
                                    Next: Diagnosis <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 3: DIAGNOSIS ===== -->
                        <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Diagnosis Manually</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Diagnosis Name *</label>
                                                <input type="text" class="form-control" id="diagnosis-name" placeholder="Enter diagnosis">
                                            </div>
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" id="diagnosis-note" rows="3" placeholder="Additional notes..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addDiagnosis()">
                                                <i class="fas fa-plus mr-2"></i>Add Diagnosis
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Diagnosis List <span class="badge badge-success ml-1" id="diag-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr><th>Diagnosis</th><th>Notes</th><th>Action</th></tr>
                                                </thead>
                                                <tbody id="diagnosis-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No diagnosis added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(2)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Admission Rx
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(4)">
                                    Next: Investigations <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 4: INVESTIGATIONS ===== -->
                        <div class="tab-pane fade" id="investigations" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Investigation Manually</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Investigation Name *</label>
                                                <input type="text" class="form-control" id="investigation-name" placeholder="Enter investigation">
                                            </div>
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" id="investigation-note" rows="3" placeholder="Additional notes..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addInvestigation()">
                                                <i class="fas fa-plus mr-2"></i>Add Investigation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Investigations List <span class="badge badge-success ml-1" id="inv-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr><th>Investigation</th><th>Notes</th><th>Action</th></tr>
                                                </thead>
                                                <tbody id="investigation-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No investigations added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(3)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Diagnosis
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(5)">
                                    Next: Advice <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 5: PRE-OPERATIVE ORDER ===== -->
                        <div class="tab-pane fade" id="pre-op" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2" style="text-align: center; width: 100%;">
                                            <h6 class="card-title mb-0" style="font-size: 16px; font-weight: bold; line-height: 1.2;">
                                                প্রফেসর ক্লিনিক<br>
                                                <span style="font-size: 14px; font-weight: normal;">মাঝিড়া, শাজাহানপুর, বগুড়া।</span>
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Pre-operative prescription will show automatically
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(4)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Investigations
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(6)">
                                    Next: Post-operative Order <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 6: POST-OPERATIVE ORDER ===== -->
                        <div class="tab-pane fade" id="post-op" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2" style="text-align: center; width: 100%;">
                                            <h6 class="card-title mb-0" style="font-size: 16px; font-weight: bold; line-height: 1.2;">
                                                প্রফেসর ক্লিনিক<br>
                                                <span style="font-size: 14px; font-weight: normal;">মাঝিড়া, শাজাহানপুর, বগুড়া।</span>
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Post-operative prescription will show automatically
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(5)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Pre-operative Order
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(7)">
                                    Next: Fresh Prescription <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        
                        <!-- ===== TAB 8: ADVICE ===== -->
                        <div class="tab-pane fade" id="advice" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Advice Manually</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Advice *</label>
                                                <textarea class="form-control" id="advice-text" rows="4" placeholder="Enter advice..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addAdvice()">
                                                <i class="fas fa-plus mr-2"></i>Add Advice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Advice List <span class="badge badge-info ml-1" id="adv-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr><th>Advice</th><th>Action</th></tr>
                                                </thead>
                                                <tbody id="advice-list">
                                                    <tr><td colspan="2" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No advice added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(4)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Investigations
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(6)">
                                    Next: Fresh Prescription <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 6: FRESH PRESCRIPTION ===== -->
                        <div class="tab-pane fade" id="fresh" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Fresh Prescription Manually</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Prescription Name *</label>
                                                <input type="text" class="form-control" id="fresh-name" placeholder="Enter prescription name">
                                            </div>
                                            <div class="form-group">
                                                <label>Details</label>
                                                <textarea class="form-control" id="fresh-details" rows="4" placeholder="Enter prescription details..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addFreshPrescription()">
                                                <i class="fas fa-plus-circle mr-2"></i>Add Fresh Prescription
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Fresh Prescriptions <span class="badge badge-info ml-1" id="fresh-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr><th>Name</th><th>Details</th><th>Action</th></tr>
                                                </thead>
                                                <tbody id="fresh-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No fresh prescriptions added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(5)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Advice
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(7)">
                                    Next: Discharge <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 7: DISCHARGE ===== -->
                        <div class="tab-pane fade" id="discharge" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="existing-data-panel">
                                        <div class="panel-title">
                                            <i class="fas fa-database"></i>
                                            Search from Existing Discharge Summaries
                                            <span class="badge">Database</span>
                                        </div>
                                        <input type="text" class="existing-search-input" id="existing-discharge-search"
                                               placeholder="🔍 Type treatment or condition to search...">
                                        <div class="existing-results-list" id="existing-discharge-results"></div>
                                    </div>
                                    <div class="or-divider">OR FILL MANUALLY</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card card-outline card-danger">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Add Discharge Summary</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Treatment Given</label>
                                                <textarea class="form-control" id="discharge-treatment" rows="2" placeholder="Treatment given..."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Condition at Discharge</label>
                                                <textarea class="form-control" id="discharge-condition" rows="2" placeholder="Condition at discharge..."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Follow-up Instructions</label>
                                                <textarea class="form-control" id="discharge-followup" rows="2" placeholder="Follow-up instructions..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addDischarge()">
                                                <i class="fas fa-save mr-2"></i>Save Discharge Summary
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(6)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Fresh Prescription
                                </button>
                                <button type="button" class="btn btn-success" onclick="completeAllSteps()">
                                    <i class="fas fa-check mr-2"></i> Complete All Steps
                                </button>
                            </div>
                        </div>

                        <!-- ===== DISPLAY TEMPLATE TAB ===== -->
                        <div class="tab-pane fade" id="display" role="tabpanel">
                            <div class="card card-outline card-dark">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">Display Template Information</h6>
                                </div>
                                <div class="card-body" id="template-display-content">
                                    <p class="text-muted text-center">Select a template and click "Load Template" to view details</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                            </div>
            <div class="card-footer footer-buttons">
                <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                    <i class="fas fa-arrow-left mr-2"></i>Previous
                </button>
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                    Review <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- ======= STEP 3: Review & Print ======= -->
        <div class="card step-content" id="step3-content" style="display: none;">
            <div class="card-header no-print">
                <h5><i class="fas fa-check-circle mr-2"></i></h5>
            </div>
            <div class="card-body">
                <div id="review-content"></div>
            </div>
            <div class="card-footer footer-buttons no-print">
                <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                    <i class="fas fa-arrow-left mr-2"></i>Previous
                </button>
                <button type="button" class="btn btn-success" onclick="printPrescription()">
                    <i class="fas fa-print mr-2"></i>Print Prescription
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ==================== GLOBAL VARIABLES ====================
let selectedPatient = null;
let medicines = [];
let admissionMedicines = [];
let diagnoses = [];
let investigations = [];
let advices = [];
let freshPrescriptions = [];
let discharge = null;
let allPatients = [];
let selectedTemplate = null;
let currentStep = 1;
let completedSteps = [];

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    toastr.clear();
    toastr.options.preventDuplicates = true;
    toastr.options.newestOnTop = false;

    loadAllPatients();
    loadTemplates();
    loadDoctors();

    $('#template-select').select2({ theme: 'bootstrap4', placeholder: 'Select a template', allowClear: true });
    $('#doctor-select').select2({ theme: 'bootstrap4', placeholder: 'Select a doctor', allowClear: true });

    let searchTimeout;
    $('#patient-search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        let term = $(this).val();
        searchTimeout = setTimeout(() => filterPatients(term), 300);
    });

    $('#medicine-search').select2({ theme: 'bootstrap4', placeholder: 'Search medicine', allowClear: true });

    $('#medicine-search').on('select2:opening', function() {
        let $this = $(this);
        if ($this.data('loaded')) return;
        $this.data('loaded', true);
        $.ajax({
            url: '{{ route("surgery-prescriptions.search-medicines") }}',
            method: 'GET',
            data: { q: '' },
            success: function(response) {
                let items = response.data || response || [];
                let options = items.map(item => ({
                    id: item.id,
                    text: (item.name || '') + (item.strength ? ' ' + item.strength : ''),
                    template_medicine: item
                }));
                $this.empty().append('<option></option>');
                options.forEach(opt => { $this.append(new Option(opt.text, opt.id, false, false)); });
                $this.data('optionsMap', options.reduce((acc, opt) => { acc[opt.id] = opt.template_medicine; return acc; }, {}));
                $this.trigger('change.select2');
            },
            error: () => toastr.error('Failed to load medicines')
        });
    });

    $('#medicine-search').on('select2:select', function(e) {
        let item = e.params?.data?.template_medicine;
        if (!item && $(this).data('optionsMap')) item = $(this).data('optionsMap')[e.params.data.id];
        if (!item) return;
        if (item.route) $('#route').val(item.route);
        if (item.frequency) $('#frequency').val(item.frequency);
        if (item.duration) $('#duration').val(item.duration);
        if (item.order_type) $('#order-type').val(item.order_type);
        if (item.note) $('#medicine-instruction').val(item.note);
        if (item.dose) {
            let $dosage = $('#dosage');
            if ($dosage.find(`option[value="${item.dose}"]`).length) $dosage.val(item.dose);
        }
    });

    $('#admission-medicine-search').select2({ theme: 'bootstrap4', placeholder: 'Search medicine', allowClear: true });

    $('#admission-medicine-search').on('select2:opening', function() {
        let $this = $(this);
        if ($this.data('loaded')) return;
        $this.data('loaded', true);
        $.ajax({
            url: '{{ route("surgery-prescriptions.search-medicines") }}',
            method: 'GET',
            data: { q: '' },
            success: function(response) {
                let items = response.data || response || [];
                let options = items.map(item => ({
                    id: item.id,
                    text: (item.name || '') + (item.strength ? ' ' + item.strength : ''),
                    template_medicine: item
                }));
                $this.empty().append('<option></option>');
                options.forEach(opt => { $this.append(new Option(opt.text, opt.id, false, false)); });
                $this.data('optionsMap', options.reduce((acc, opt) => { acc[opt.id] = opt.template_medicine; return acc; }, {}));
                $this.trigger('change.select2');
            },
            error: () => toastr.error('Failed to load medicines')
        });
    });

    $('#admission-medicine-search').on('select2:select', function(e) {
        let item = e.params?.data?.template_medicine;
        if (!item && $(this).data('optionsMap')) item = $(this).data('optionsMap')[e.params.data.id];
        if (!item) return;
        if (item.route) $('#admission-route').val(item.route);
        if (item.frequency) $('#admission-frequency').val(item.frequency);
        if (item.duration) $('#admission-duration').val(item.duration);
        if (item.order_type) $('#admission-order-type').val(item.order_type);
        if (item.note) $('#admission-medicine-instruction').val(item.note);
        if (item.dose) {
            let $dosage = $('#admission-dosage');
            if ($dosage.find(`option[value="${item.dose}"]`).length) $dosage.val(item.dose);
        }
    });

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        newestOnTop: false,
        timeOut: 5000,
        extendedTimeOut: 2000
    };

    setupExistingSearch('existing-diagnosis-search',   'existing-diagnosis-results',   'diagnosis',     'surgery-prescriptions.search-diagnoses');
    setupExistingSearch('existing-investigation-search','existing-investigation-results','investigation', 'surgery-prescriptions.search-investigations');
    setupExistingSearch('existing-advice-search',       'existing-advice-results',       'advice',        'surgery-prescriptions.search-advices');
    setupExistingSearch('existing-fresh-search',        'existing-fresh-results',        'fresh',         'surgery-prescriptions.search-fresh-prescriptions');
    setupExistingSearch('existing-discharge-search',    'existing-discharge-results',    'discharge',     'surgery-prescriptions.search-discharge-summaries');

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.existing-data-panel').length) $('.existing-results-list').hide();
    });

    $(document).on('click', '.nav-tabs .nav-link[data-step]', function(e) {
        let step = parseInt($(this).data('step'));
        if ($(this).attr('id') === 'display-tab') return true;
        if (!validateStep(step)) { e.preventDefault(); return false; }
        currentStep = step;
        updateTabBadges();
    });

    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            if (currentStep === 3) saveToDatabase(true);
            else toastr.info('Please complete all steps before saving');
        }
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            if (currentStep === 3) printPrescription();
            else toastr.info('Please complete all steps before printing');
        }
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            if (currentStep === 1) nextStep(1);
            else if (currentStep === 2) nextStep(2);
        }
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            if (currentStep === 2) prevStep(2);
            else if (currentStep === 3) prevStep(3);
        }
        if (e.ctrlKey && e.key === 'l' && currentStep === 2) {
            e.preventDefault();
            loadSelectedTemplate();
        }
        if (e.key === 'Escape') {
            $('.existing-results-list').hide();
            if (Swal.isVisible()) Swal.close();
        }
    });

    console.log('Keyboard Shortcuts:\nCtrl+S: Save\nCtrl+P: Print\nCtrl+N: Next\nCtrl+B: Back\nCtrl+L: Load Template\nEscape: Close');
});

// ==================== EXISTING DATA SEARCH ENGINE ====================

function setupExistingSearch(inputId, resultsId, type, routeName) {
    let timeout;
    $('#' + inputId).on('input', function() {
        clearTimeout(timeout);
        let term = $(this).val().trim();
        let $results = $('#' + resultsId);
        if (term.length < 1) { $results.hide(); return; }
        timeout = setTimeout(function() {
            $.ajax({
                url: getRouteUrl(routeName),
                method: 'GET',
                data: { q: term },
                success: function(response) {
                    let data = response.data || response.results || response || [];
                    if (!Array.isArray(data)) data = [];
                    if (data.length === 0) {
                        $results.html('<div class="existing-result-item"><span class="text-muted">No results found</span></div>').show();
                        return;
                    }
                    let html = '';
                    data.slice(0, 10).forEach(item => { html += buildResultItem(item, type); });
                    $results.html(html).show();
                },
                error: function() {
                    $results.html('<div class="existing-result-item text-danger"><i class="fas fa-exclamation-circle mr-1"></i>Search failed</div>').show();
                }
            });
        }, 350);
    });
}

function getRouteUrl(routeName) {
    const routes = {
        'surgery-prescriptions.search-medicines':           '{{ route("surgery-prescriptions.search-medicines") }}',
        'surgery-prescriptions.search-diagnoses':           '{{ route("surgery-prescriptions.search-diagnoses") }}',
        'surgery-prescriptions.search-investigations':      '{{ route("surgery-prescriptions.search-investigations") }}',
        'surgery-prescriptions.search-advices':             '{{ route("surgery-prescriptions.search-advices") }}',
        'surgery-prescriptions.search-fresh-prescriptions': '{{ route("surgery-prescriptions.search-fresh-prescriptions") }}',
        'surgery-prescriptions.search-discharge-summaries': '{{ route("surgery-prescriptions.search-discharge-summaries") }}',
    };
    return routes[routeName] || '#';
}

function buildResultItem(item, type) {
    let mainText = '', subText = '', onclickFn = '';
    switch(type) {
        case 'medicine':
            mainText = (item.name || '') + (item.strength ? ' <small class="text-muted">' + item.strength + '</small>' : '');
            subText  = (item.brand || item.generic || '') + (item.form ? ' · ' + item.form : '');
            onclickFn = `selectExistingMedicine(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
        case 'diagnosis':
            mainText = item.name || item.diagnosis_name || '';
            subText  = item.icd_code ? 'ICD: ' + item.icd_code : (item.category || '');
            onclickFn = `selectExistingDiagnosis(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
        case 'investigation':
            mainText = item.name || item.investigation_name || '';
            subText  = item.category || item.department || '';
            onclickFn = `selectExistingInvestigation(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
        case 'advice':
            mainText = item.advice || item.name || '';
            subText  = item.category || '';
            onclickFn = `selectExistingAdvice(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
        case 'fresh':
            mainText = item.name || item.prescription_name || '';
            subText  = item.details ? item.details.substring(0, 60) + '...' : '';
            onclickFn = `selectExistingFresh(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
        case 'discharge':
            mainText = item.treatment ? item.treatment.substring(0, 50) + '...' : (item.condition || '');
            subText  = item.follow_up ? 'Follow-up: ' + item.follow_up.substring(0, 40) + '...' : '';
            onclickFn = `selectExistingDischarge(${JSON.stringify(item).replace(/'/g, "&#39;")})`;
            break;
    }
    return `<div class="existing-result-item" onclick='${onclickFn}'>
        <div>
            <div class="result-main">${mainText}</div>
            ${subText ? '<div class="result-sub">' + subText + '</div>' : ''}
        </div>
        <button type="button" class="btn-use-existing"><i class="fas fa-plus mr-1"></i>Use</button>
    </div>`;
}

// ==================== SELECT EXISTING ITEM HANDLERS ====================

function selectExistingMedicine(item) {
    let medData = {
        template_medicine_id: item.id,
        name: item.name || '',
        strength: item.strength || '',
        brand: item.brand || item.generic || 'Generic',
        dosage: item.dosage || '1+0+1',
        duration: item.duration || '7 days',
        order_type: item.order_type || 'pre-op',
        route: item.route || 'Oral',
        frequency: item.frequency || '',
        medicine_type: item.medicine_type || item.form || 'Tablet',
        instructions: item.instructions || ''
    };
    medicines.push(medData);
    updateMedicineList();
    $('#existing-medicine-search').val('');
    $('#existing-medicine-results').hide();
    toastr.success('Medicine added: ' + medData.name);
}

function selectExistingDiagnosis(item) {
    let diag = { id: Date.now(), name: item.name || item.diagnosis_name || '', note: item.note || item.notes || '' };
    diagnoses.push(diag);
    updateDiagnosisList();
    $('#existing-diagnosis-search').val('');
    $('#existing-diagnosis-results').hide();
    toastr.success('Diagnosis added: ' + diag.name);
}

function selectExistingInvestigation(item) {
    let inv = { id: Date.now(), name: item.name || item.investigation_name || '', note: item.note || item.notes || '' };
    investigations.push(inv);
    updateInvestigationList();
    $('#existing-investigation-search').val('');
    $('#existing-investigation-results').hide();
    toastr.success('Investigation added: ' + inv.name);
}

function selectExistingAdvice(item) {
    let adv = { id: Date.now(), advice: item.advice || item.name || '' };
    advices.push(adv);
    updateAdviceList();
    $('#existing-advice-search').val('');
    $('#existing-advice-results').hide();
    toastr.success('Advice added');
}

function selectExistingFresh(item) {
    let fresh = { id: Date.now(), name: item.name || item.prescription_name || '', details: item.details || '' };
    freshPrescriptions.push(fresh);
    updateFreshList();
    $('#existing-fresh-search').val('');
    $('#existing-fresh-results').hide();
    toastr.success('Fresh prescription added: ' + fresh.name);
}

function selectExistingDischarge(item) {
    if (item.treatment) $('#discharge-treatment').val(item.treatment);
    if (item.condition) $('#discharge-condition').val(item.condition);
    if (item.follow_up) $('#discharge-followup').val(item.follow_up);
    $('#existing-discharge-search').val('');
    $('#existing-discharge-results').hide();
    toastr.success('Discharge summary loaded from existing data');
}

// ==================== TEMPLATE FUNCTIONS ====================

function loadTemplates() {
    $.ajax({
        url: '{{ route("surgery-prescriptions.get-templates") }}',
        method: 'GET',
        success: function(response) {
            let templates = (response.success && response.data) ? response.data : (Array.isArray(response) ? response : []);
            let options = '<option value="">-- Choose a template --</option>';
            templates.forEach(t => {
                options += `<option value="${t.id}" data-title="${t.title||t.name}" data-surgery_type="${t.surgery_type||''}">
                    ${t.title||t.name} (${t.templateid||'TPL-'+t.id})
                </option>`;
            });
            $('#template-select').html(options).trigger('change');
        },
        error: () => toastr.error('Failed to load templates')
    });
}

function loadDoctors() {
    $.ajax({
        url: '{{ route("surgery-prescriptions.get-doctors") }}',
        method: 'GET',
        success: function(response) {
            let doctors = (response.success && response.data) ? response.data : (Array.isArray(response) ? response : []);
            let options = '<option value="">-- Choose a doctor --</option>';
            doctors.forEach(d => {
                options += `<option value="${d.id}" data-name="${d.name||d.doctor_name}" data-specialization="${d.specialization||''}">
                    ${d.name||d.doctor_name}${d.specialization ? ' (' + d.specialization + ')' : ''}
                </option>`;
            });
            $('#doctor-select').html(options).trigger('change');
        },
        error: () => toastr.error('Failed to load doctors')
    });
}

function loadSelectedTemplate() {
    let templateId = $('#template-select').val();
    if (!templateId) { toastr.error('Please select a template'); return; }
    let doctorId = $('#doctor-select').val();
    if (!doctorId) { toastr.error('Please select a doctor'); return; }

    let selected = $('#template-select').find('option:selected');
    let templateName = selected.data('title') || selected.text();
    let surgeryType = selected.data('surgery_type');
    let doctorOption = $('#doctor-select').find('option:selected');
    let doctorName = doctorOption.text();

    let $loadBtn = $('button[onclick="loadSelectedTemplate()"]');
    let originalHtml = $loadBtn.html();
    $loadBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...').prop('disabled', true);

    if (medicines.length > 0 || diagnoses.length > 0 || investigations.length > 0 || advices.length > 0) {
        Swal.fire({
            title: 'Load Template?',
            text: `Loading "${templateName}" will replace your current items. Continue?`,
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, load'
        }).then(r => {
            if (r.isConfirmed) fetchTemplateData(templateId, surgeryType, $loadBtn, originalHtml);
            else $loadBtn.html(originalHtml).prop('disabled', false);
        });
    } else {
        fetchTemplateData(templateId, surgeryType, $loadBtn, originalHtml);
    }
}

function fetchTemplateData(templateId, surgeryType, $loadBtn, originalHtml) {
    $('#template-loading').show();
    let url = '{{ route("surgery-prescriptions.get-template-data", ["id" => "REPLACE_ID"]) }}'.replace('REPLACE_ID', templateId);
    $.ajax({
        url: url, method: 'GET',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        success: function(response) {
            $('#template-loading').hide();
            $loadBtn.html(originalHtml).prop('disabled', false);
            if (response.success) {
                let medicines = [], diagnoses = [], investigations = [], advices = [], freshPrescriptions = [], discharge = {};
                let admissionMedicines = [];
                let allergyWarnings = [];
                if (selectedPatient && selectedPatient.allergies && response.medicines?.length) {
                    let patientAllergies = (selectedPatient.allergies).toLowerCase().split(',').map(a => a.trim());
                    response.medicines.forEach(med => {
                        let medName = (med.name || '').toLowerCase();
                        patientAllergies.forEach(allergy => {
                            if (allergy && medName.includes(allergy)) allergyWarnings.push(`${med.name} - Patient allergic to: ${allergy}`);
                        });
                    });
                }
                if (allergyWarnings.length > 0) {
                    Swal.fire({
                        title: '<span style="color: #dc3545;"><i class="fas fa-exclamation-triangle mr-2"></i>Allergy Warning!</span>',
                        html: `<div class="alert alert-danger mb-0">
                            <strong><i class="fas fa-allergies mr-2"></i>Danger: Patient Allergies Detected</strong><br><br>
                            The following medicines may cause allergic reactions:<br>
                            <ul class="text-left mt-2 mb-0">${allergyWarnings.map(w => `<li><strong>${w}</strong></li>`).join('')}</ul>
                            <br><small class="text-muted">Please review and modify the prescription before proceeding.</small>
                        </div>`,
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Load Anyway', cancelButtonText: 'Cancel', width: '600px'
                    }).then(result => { if (result.isConfirmed) populateTemplateData(response, surgeryType); });
                } else {
                    populateTemplateData(response, surgeryType);
                }
            } else {
                toastr.error('Failed: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            $('#template-loading').hide();
            $loadBtn.html(originalHtml).prop('disabled', false);
            let msg = 'Error: ';
            if (xhr.status === 404) msg += 'Template not found';
            else if (xhr.status === 500) msg += 'Server error';
            else msg += xhr.responseJSON?.message || 'Unknown';
            toastr.error(msg);
        }
    });
}

function populateTemplateData(response, surgeryType) {
    let itemCount = 0;
    let templateName = $('#template-select option:selected').text();
    let doctorName = $('#doctor-select').find('option:selected').text();

    if (response.medicines?.length)     { medicines = response.medicines; updateMedicineList(); itemCount += response.medicines.length; }
    if (response.admission_medicines?.length) { admissionMedicines = response.admission_medicines; updateAdmissionMedicineList(); itemCount += response.admission_medicines.length; }
    if (response.diagnoses?.length)     { diagnoses = response.diagnoses; updateDiagnosisList(); itemCount += response.diagnoses.length; }
    if (response.investigations?.length){ investigations = response.investigations; updateInvestigationList(); itemCount += response.investigations.length; }
    if (response.advices?.length) {
        advices = response.advices; updateAdviceList(); itemCount += response.advices.length;
        if (!$('#advice-text').val() && response.advices.length > 0) {
            $('#advice-text').val(response.advices.map(a => a.advice).join('\n'));
        }
    }
    if (response.discharge) {
        discharge = response.discharge;
        if (discharge.treatment) $('#discharge-treatment').val(discharge.treatment);
        if (discharge.condition) $('#discharge-condition').val(discharge.condition);
        if (discharge.follow_up) $('#discharge-followup').val(discharge.follow_up);
    }

    $('#selected-template-info').show();
    $('#selected-template-name').text(response.template.title || 'Template');
    $('#selected-doctor-name').text(doctorName);
    let total = (response.counts?.medicines||0)+(response.counts?.admission_medicines||0)+(response.counts?.diagnoses||0)+(response.counts?.investigations||0)+(response.counts?.advices||0);
    $('#selected-template-count').text(total + ' items loaded');

    if (response.template.surgery_type && !$('#surgery-type').val()) $('#surgery-type').val(response.template.surgery_type);
    else if (surgeryType && !$('#surgery-type').val()) $('#surgery-type').val(surgeryType);

    displayTemplateInfo(response);
    selectedTemplate = response.template;
    toastr.success(`Template "${templateName}" loaded successfully with ${total} items`);
}

function displayTemplateInfo(response) {
    let html = `<div class="card"><div class="card-body"><h5>${response.template.title}</h5><p>${response.template.description||'No description'}</p>`;
    const sections = [
        { key: 'medicines',     label: 'Medicines',     render: m => `${m.name} - ${m.dosage} (${m.duration}) - ${m.route||'Oral'}` },
        { key: 'diagnoses',     label: 'Diagnosis',     render: d => `${d.name}${d.note?' - '+d.note:''}` },
        { key: 'investigations',label: 'Investigations', render: i => `${i.name}${i.note?' - '+i.note:''}` },
        { key: 'advices',       label: 'Advices',       render: a => a.advice },
    ];
    sections.forEach(s => {
        html += `<h6 class="mt-3">${s.label} (${response[s.key]?.length||0})</h6><ul class="list-group mb-3">`;
        (response[s.key]||[]).forEach(item => { html += `<li class="list-group-item">${s.render(item)}</li>`; });
        if (!response[s.key]?.length) html += `<li class="list-group-item text-muted">None</li>`;
        html += '</ul>';
    });
    if (response.discharge) {
        html += `<h6>Discharge Summary</h6><div class="card"><div class="card-body">
            <p><strong>Treatment:</strong> ${response.discharge.treatment||'N/A'}</p>
            <p><strong>Condition:</strong> ${response.discharge.condition||'N/A'}</p>
            <p><strong>Follow-up:</strong> ${response.discharge.follow_up||'N/A'}</p>
        </div></div>`;
    }
    html += '</div></div>';
    $('#template-display-content').html(html);
}

// ==================== PATIENT FUNCTIONS ====================

function loadAllPatients() {
    $.ajax({
        url: '{{ route("surgery-prescriptions.search-patients") }}',
        method: 'GET', data: { q: '' },
        success: function(response) {
            allPatients = response.data || response.patients || response || [];
            if (allPatients.length > 0) {
                displayPatients(allPatients);
                $('#patient-count').text(`Total ${allPatients.length} patient(s) found`);
            } else {
                $('#patients-table-body').html('<tr><td colspan="6" class="text-center">No patients found</td></tr>');
                $('#patient-count').text('No patients found');
            }
        },
        error: () => $('#patients-table-body').html('<tr><td colspan="6" class="text-center text-danger">Failed to load patients</td></tr>')
    });
}

function filterPatients(term) {
    if (!term) { displayPatients(allPatients); $('#patient-count').text(`Total ${allPatients.length} patient(s) found`); return; }
    term = term.toLowerCase();
    let filtered = allPatients.filter(p =>
        ((p.patientname||p.name||'').toLowerCase().includes(term)) ||
        ((p.patientcode||p.patient_id||'').toLowerCase().includes(term)) ||
        ((p.mobile_no||p.phone||p.mobile||'').toLowerCase().includes(term))
    );
    displayPatients(filtered);
    $('#patient-count').text(`Found ${filtered.length} patient(s)`);
}

function displayPatients(patients) {
    if (!patients.length) {
        $('#patients-table-body').html('<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-users-slash fa-2x"></i><p class="mt-2">No patients found</p></td></tr>');
        return;
    }
    let html = '';
    patients.forEach(p => {
        let id = p.id;
        let code   = p.patientcode || p.patient_id || p.uhid || 'N/A';
        let name   = p.patientname || p.name || 'Unknown';
        let age    = p.age || p.age_years || '';
        let gender = p.gender || p.sex || '';
        let phone  = p.mobile_no || p.phone || p.mobile || '';
        let sel    = (selectedPatient && selectedPatient.id === id) ? 'selected' : '';
        html += `<tr class="${sel}" data-id="${id}" data-code="${code}" data-name="${name}" data-age="${age}" data-gender="${gender}" data-phone="${phone}">
            <td>${id}</td>
            <td><span class="patient-id-badge">${code}</span></td>
            <td><strong>${name}</strong></td>
            <td>${age} / ${gender}</td>
            <td>${phone}</td>
            <td><button type="button" class="select-patient-btn" onclick="selectPatientFromRow(this)"><i class="fas fa-check"></i> Select</button></td>
        </tr>`;
    });
    $('#patients-table-body').html(html);
}

function selectPatientFromRow(el) {
    let row = $(el).closest('tr');
    let patientId = row.data('id');
    let code = row.data('code'), name = row.data('name');
    let age = row.data('age'), gender = row.data('gender'), phone = row.data('phone');
    let patient = allPatients.find(p => p.id == patientId);
    selectedPatient = patient || { id: patientId, patientcode: code, name, age, gender, phone };
    $('.patient-table tbody tr').removeClass('selected');
    row.addClass('selected');
    $('#patient-details').show();
    $('#patient-name').text(name);
    $('#patient-id').text(code);
    $('#patient-age-gender').text(age + ' / ' + gender);
    $('#patient-phone').text(phone);
    toastr.success('Patient selected: ' + name);
}

$(document).on('click', '.patient-table tbody tr', function() {
    selectPatientFromRow($(this).find('.select-patient-btn').first());
});

// ==================== MEDICINE FUNCTIONS ====================

function addMedicine() {
    let medicine = $('#medicine-search').select2('data')[0];
    if (!medicine) { toastr.error('Please select a medicine'); return; }
    let medData = {
        template_medicine_id: medicine.id,
        name: medicine.template_medicine?.name || medicine.text.split(' ')[0],
        strength: medicine.template_medicine?.strength || '',
        brand: medicine.template_medicine?.brand || 'Generic',
        dosage: $('#dosage').val(),
        duration: $('#duration').val() || '7 days',
        order_type: $('#order-type').val(),
        route: $('#route').val(),
        frequency: $('#frequency').val(),
        medicine_type: $('#medicine-type').val(),
        instructions: $('#medicine-instruction').val()
    };
    medicines.push(medData);
    updateMedicineList();
    $('#medicine-search').val(null).trigger('change');
    $('#duration').val(''); $('#medicine-instruction').val(''); $('#frequency').val('');
    toastr.success('Medicine added successfully');
}

function updateMedicineList() {
    $('#med-count-badge').text(medicines.length);
    if (!medicines.length) {
        $('#medicine-list').html('<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No medicines added yet</td></tr>');
        return;
    }
    let html = '';
    medicines.forEach((med, i) => {
        let bc = med.order_type==='pre-op'?'badge-warning':(med.order_type==='post-op'?'badge-success':'badge-info');
        html += `<tr>
            <td>${med.name} ${med.strength?'<small class="text-muted">'+med.strength+'</small>':''}</td>
            <td>${med.dosage}</td>
            <td>${med.duration}</td>
            <td><span class="badge ${bc}">${med.order_type}</span></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeMedicine(${i})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    $('#medicine-list').html(html);
}

function removeMedicine(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Medicine to remove:</strong> ${medicines[i].name}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedMed = medicines[i];
            medicines.splice(i, 1); updateMedicineList();
            toastr.warning(`Medicine removed: ${removedMed.name}`);
        }
    });
}

// ==================== POPULATE ADMISSION RX FROM IMAGE ====================

function populateAdmissionRxFromImage() {
    // Clear existing admission medicines
    admissionMedicines = [];
    
    // Add NPO-TFO as special instruction
    admissionMedicines.push({
        template_medicine_id: Date.now() + 1,
        name: 'NPO-TFO',
        strength: '',
        brand: 'Special Instruction',
        dosage: '',
        duration: '',
        order_type: 'admission',
        route: '',
        frequency: '',
        medicine_type: 'Instruction',
        instructions: 'Nothing by mouth - Tea, Food, Orally'
    });
    
    // Add Inf. Hartman
    admissionMedicines.push({
        template_medicine_id: Date.now() + 2,
        name: 'Inf. Hartman',
        strength: '',
        brand: 'IV Fluid',
        dosage: '1000 cc',
        duration: '',
        order_type: 'admission',
        route: 'IV',
        frequency: 'Stat @ 30 d/m',
        medicine_type: 'IV Fluid',
        instructions: 'Intravenous fluid at 30 drops per minute'
    });
    
    // Add Inj. Prizon
    admissionMedicines.push({
        template_medicine_id: Date.now() + 3,
        name: 'Inj. Prizon',
        strength: '1gm',
        brand: 'Antibiotic',
        dosage: '1gm',
        duration: '',
        order_type: 'admission',
        route: 'IV',
        frequency: '1 vial IV stat & hourly',
        medicine_type: 'Injection',
        instructions: '1 vial intravenous stat then hourly'
    });
    
    // Update the admission medicine list display
    updateAdmissionMedicineList();
    
    // Show success message
    toastr.success('Admission Rx prescription loaded successfully');
}

// ==================== SHOW ADMISSION RX ====================

function showAdmissionRx() {
    if (!admissionMedicines.length) { 
        toastr.error('No admission medicines added to show'); 
        return; 
    }

    // Get patient and doctor info
    let patientName = selectedPatient?.patientname || selectedPatient?.name || 'Patient';
    let patientAge = selectedPatient?.age || selectedPatient?.age_years || '';
    let patientGender = selectedPatient?.gender || selectedPatient?.sex || '';
    let patientCode = selectedPatient?.patientcode || selectedPatient?.patient_id || '';
    let surgeryDate = $('#surgery-date').val() || new Date().toLocaleDateString('en-GB');
    let doctorName = $('#doctor-select').find('option:selected').text() || '';
    
    // Build admission prescription HTML for display
    let html = `
    <div id="show-admission-rx" style="font-family: Arial, sans-serif; max-width: 820px; margin: 0 auto; color: #004085; font-size: 13px; padding: 20px; border: 2px solid #004085; background: #fff;">

        <!-- HEADER -->
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #004085; padding-bottom: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; text-align: left; padding: 10px 0;">
                        <div style="font-size: 24px; font-weight: bold; color: #004085;">P</div>
                        <div style="font-size: 14px; font-weight: bold; color: #004085;">PROFESSOR</div>
                        <div style="font-size: 14px; color: #004085;">CLINIC</div>
                    </td>
                    <td style="width: 60%; text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #004085;">প্রফেসর ক্লিনিক</div>
                        <div style="font-size: 25px; margin-top: 5px;">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        <div style="font-size: 14px; margin-top: 8px;">
                            01720-039005, 01720-039006, 01720-039007, 01720-039008
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; padding: 10px 0;">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </div>

        <!-- PATIENT INFO & O/E & RX -->
        <div style="margin-bottom: 15px;">
            <div style="margin-bottom: 10px;">
                <strong>Name:</strong> ${patientName} ${patientCode ? '[' + patientCode + ']' : ''} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <strong>Date:</strong> ${surgeryDate} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <strong>Age/Gender:</strong> ${patientAge}${patientGender ? ' / ' + patientGender : ''}
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 5px; vertical-align: top;">
                        <div style="margin-top: 15px;">
                            <strong>O/E:</strong><br>
                            <div style="margin-left: 10px; margin-top: 5px; display: inline-block; border-bottom: 1px solid #004085; padding-bottom: 10px;">
                                <!-- O/E content will go here -->
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; padding: 5px; vertical-align: top;">
                        <div style="text-align: right; margin-bottom: 10px;">
                            <strong>Rx On admission On</strong><br>
                            <div style="border-bottom: 1px solid #004085; padding-bottom: 5px;">
                                &nbsp;
                            </div>
                        </div>
                        ${admissionMedicines.map((med, index) => `
                            <div style="margin-bottom: 8px; padding: 5px; border-left: 3px solid #004085; background: #f9f9f9;">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="flex: 1;">
                                        ${med.name} ${med.strength ? '<small>' + med.strength + '</small>' : ''}
                                    </div>
                                    <div style="text-align: right;">
                                        ${med.dosage || ''}
                                    </div>
                                </div>
                                ${med.route ? '<div style="margin-left: 20px; font-size: 12px;">Route: ' + med.route + '</div>' : ''}
                                ${med.frequency ? '<div style="margin-left: 20px; font-size: 12px;">Frequency: ' + med.frequency + '</div>' : ''}
                                ${med.instructions ? '<div style="margin-left: 20px; font-size: 12px; font-style: italic;">' + med.instructions + '</div>' : ''}
                            </div>
                        `).join('')}
                    </td>
                </tr>
            </table>
        </div>

        
    </div>`;

    // Show in modal or replace current content
    Swal.fire({
        title: '',
        html: html,
        width: '900px',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-print mr-2"></i>Print',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Close',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            printAdmissionRx();
        }
    });
}

// ==================== PRINT ADMISSION RX ====================

function printAdmissionRx() {
    if (!admissionMedicines.length) { 
        toastr.error('No admission medicines added to print'); 
        return; 
    }

    // Get patient and doctor info
    let patientName = selectedPatient?.patientname || selectedPatient?.name || 'Patient';
    let patientAge = selectedPatient?.age || selectedPatient?.age_years || '';
    let patientGender = selectedPatient?.gender || selectedPatient?.sex || '';
    let patientCode = selectedPatient?.patientcode || selectedPatient?.patient_id || '';
    let surgeryDate = $('#surgery-date').val() || new Date().toLocaleDateString('en-GB');
    let doctorName = $('#doctor-select').find('option:selected').text() || '';
    
    // Build admission prescription HTML
    let html = `
    <div id="print-admission-rx" style="font-family: Arial, sans-serif; width: 210mm; margin: 0 auto; color: #004085; font-size: 13px; padding: 20px; border: 2px solid #004085; background: #fff; box-sizing: border-box;">

        <!-- HEADER -->
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #004085; padding-bottom: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; text-align: left; padding: 10px 0;">
                        <div style="font-size: 24px; font-weight: bold; color: #004085;">P</div>
                        <div style="font-size: 14px; font-weight: bold; color: #004085;">PROFESSOR</div>
                        <div style="font-size: 14px; color: #004085;">CLINIC</div>
                    </td>
                    <td style="width: 60%; text-align: center;">
                        <div style="font-size: 32px; font-weight: bold; color: #004085;">প্রফেসর ক্লিনিক</div>
                        <div style="font-size: 25px; margin-top: 5px;">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        <div style="font-size: 14px; margin-top: 8px;">
                            01720-039005, 01720-039006, 01720-039007, 01720-039008
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right; padding: 10px 0;">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </div>

        <!-- PATIENT INFO & O/E & RX -->
        <div style="margin-bottom: 15px;">
            <div style="margin-bottom: 10px;">
                <strong>Name:</strong> ${patientName} ${patientCode ? '[' + patientCode + ']' : ''} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <strong>Date:</strong> ${surgeryDate} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <strong>Age/Gender:</strong> ${patientAge}${patientGender ? ' / ' + patientGender : ''}
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 5px; vertical-align: top;">
                        <div style="margin-top: 15px;">
                            <strong>O/E:</strong><br>
                            <div style="margin-left: 10px; margin-top: 5px; display: inline-block; border-bottom: 1px solid #004085; padding-bottom: 10px;">
                                <!-- O/E content will go here -->
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; padding: 5px; vertical-align: top;">
                        <div style="text-align: right; margin-bottom: 10px;">
                            <strong>Rx On admission On</strong><br>
                            <div style="border-bottom: 1px solid #004085; padding-bottom: 5px;">
                                &nbsp;
                            </div>
                        </div>
                        ${admissionMedicines.map((med, index) => `
                            <div style="margin-bottom: 8px; padding: 5px; border-left: 3px solid #004085; background: #f9f9f9;">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="flex: 1;">
                                        ${med.name} ${med.strength ? '<small>' + med.strength + '</small>' : ''}
                                    </div>
                                    <div style="text-align: right;">
                                        ${med.dosage || ''}
                                    </div>
                                </div>
                                ${med.route ? '<div style="margin-left: 20px; font-size: 12px;">Route: ' + med.route + '</div>' : ''}
                                ${med.frequency ? '<div style="margin-left: 20px; font-size: 12px;">Frequency: ' + med.frequency + '</div>' : ''}
                                ${med.instructions ? '<div style="margin-left: 20px; font-size: 12px; font-style: italic;">' + med.instructions + '</div>' : ''}
                            </div>
                        `).join('')}
                    </td>
                </tr>
            </table>
        </div>

        
    </div>`;

    // Create print window
    let printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admission Prescription - ${patientName}</title>
            <style>
                @page {
                    size: A4;
                    margin: 10mm;
                }
                body { 
                    margin: 0; 
                    padding: 0; 
                    font-family: Arial, sans-serif; 
                    width: 210mm;
                }
                @media print { 
                    body { 
                        margin: 0; 
                        padding: 0; 
                        width: 210mm;
                    } 
                }
            </style>
        </head>
        <body>
            ${html}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for content to load then print
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
        toastr.success('Admission prescription printed successfully');
    }, 500);
}

// ==================== ADMISSION MEDICINE FUNCTIONS ====================

function addAdmissionMedicine() {
    let medicine = $('#admission-medicine-search').select2('data')[0];
    if (!medicine) { toastr.error('Please select a medicine'); return; }
    let medData = {
        template_medicine_id: medicine.id,
        name: medicine.template_medicine?.name || medicine.text.split(' ')[0],
        strength: medicine.template_medicine?.strength || '',
        brand: medicine.template_medicine?.brand || 'Generic',
        dosage: $('#admission-dosage').val(),
        duration: $('#admission-duration').val() || '7 days',
        order_type: $('#admission-order-type').val(),
        route: $('#admission-route').val(),
        frequency: $('#admission-frequency').val(),
        medicine_type: $('#admission-medicine-type').val(),
        instructions: $('#admission-medicine-instruction').val()
    };
    admissionMedicines.push(medData);
    updateAdmissionMedicineList();
    $('#admission-medicine-search').val(null).trigger('change');
    $('#admission-duration').val(''); $('#admission-medicine-instruction').val(''); $('#admission-frequency').val('');
    toastr.success('Admission medicine added successfully');
}

function updateAdmissionMedicineList() {
    $('#admission-med-count-badge').text(admissionMedicines.length);
    if (!admissionMedicines.length) {
        $('#admission-medicine-list').html('<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No admission medicines added yet</td></tr>');
        return;
    }
    let html = '';
    admissionMedicines.forEach((med, i) => {
        let bc = med.order_type==='admission'?'badge-warning':(med.order_type==='pre-op'?'badge-info':'badge-success');
        html += `<tr>
            <td>${med.name} ${med.strength?'<small class="text-muted">'+med.strength+'</small>':''}</td>
            <td>${med.dosage}</td>
            <td>${med.duration}</td>
            <td><span class="badge ${bc}">${med.order_type}</span></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeAdmissionMedicine(${i})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    $('#admission-medicine-list').html(html);
}

function removeAdmissionMedicine(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Admission medicine to remove:</strong> ${admissionMedicines[i].name}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedMed = admissionMedicines[i];
            admissionMedicines.splice(i, 1); updateAdmissionMedicineList();
            toastr.warning(`Admission medicine removed: ${removedMed.name}`);
        }
    });
}

// ==================== DIAGNOSIS FUNCTIONS ====================

function addDiagnosis() {
    let name = $('#diagnosis-name').val();
    if (!name) { toastr.error('Please enter diagnosis name'); return; }
    diagnoses.push({ id: Date.now(), name, note: $('#diagnosis-note').val() });
    updateDiagnosisList();
    $('#diagnosis-name').val(''); $('#diagnosis-note').val('');
    toastr.success('Diagnosis added');
}

function updateDiagnosisList() {
    $('#diag-count-badge').text(diagnoses.length);
    if (!diagnoses.length) {
        $('#diagnosis-list').html('<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No diagnosis added yet</td></tr>');
        return;
    }
    $('#diagnosis-list').html(diagnoses.map((d,i)=>`<tr>
        <td>${d.name}</td><td>${d.note||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeDiagnosis(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeDiagnosis(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Diagnosis to remove:</strong> ${diagnoses[i].name}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedDiag = diagnoses[i];
            diagnoses.splice(i, 1); updateDiagnosisList();
            toastr.warning(`Diagnosis removed: ${removedDiag.name}`);
        }
    });
}

// ==================== INVESTIGATION FUNCTIONS ====================

function addInvestigation() {
    let name = $('#investigation-name').val();
    if (!name) { toastr.error('Please enter investigation name'); return; }
    investigations.push({ id: Date.now(), name, note: $('#investigation-note').val() });
    updateInvestigationList();
    $('#investigation-name').val(''); $('#investigation-note').val('');
    toastr.success('Investigation added');
}

function updateInvestigationList() {
    $('#inv-count-badge').text(investigations.length);
    if (!investigations.length) {
        $('#investigation-list').html('<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No investigations added yet</td></tr>');
        return;
    }
    $('#investigation-list').html(investigations.map((inv,i)=>`<tr>
        <td>${inv.name}</td><td>${inv.note||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeInvestigation(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeInvestigation(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Investigation to remove:</strong> ${investigations[i].name}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedInv = investigations[i];
            investigations.splice(i, 1); updateInvestigationList();
            toastr.warning(`Investigation removed: ${removedInv.name}`);
        }
    });
}

// ==================== ADVICE FUNCTIONS ====================

function addAdvice() {
    let text = $('#advice-text').val();
    if (!text) { toastr.error('Please enter advice'); return; }
    advices.push({ id: Date.now(), advice: text });
    updateAdviceList();
    $('#advice-text').val('');
    toastr.success('Advice added');
}

function updateAdviceList() {
    $('#adv-count-badge').text(advices.length);
    if (!advices.length) {
        $('#advice-list').html('<tr><td colspan="2" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No advice added yet</td></tr>');
        return;
    }
    $('#advice-list').html(advices.map((a,i)=>`<tr>
        <td>${a.advice}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeAdvice(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeAdvice(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Advice to remove:</strong> ${advices[i].advice}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedAdv = advices[i];
            advices.splice(i, 1); updateAdviceList();
            toastr.warning(`Advice removed: ${removedAdv.advice.substring(0, 30)}...`);
        }
    });
}

// ==================== FRESH PRESCRIPTION FUNCTIONS ====================

function addFreshPrescription() {
    let name = $('#fresh-name').val();
    if (!name) { toastr.error('Please enter prescription name'); return; }
    freshPrescriptions.push({ id: Date.now(), name, details: $('#fresh-details').val() });
    updateFreshList();
    $('#fresh-name').val(''); $('#fresh-details').val('');
    toastr.success('Fresh prescription added');
}

function updateFreshList() {
    $('#fresh-count-badge').text(freshPrescriptions.length);
    if (!freshPrescriptions.length) {
        $('#fresh-list').html('<tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No fresh prescriptions added yet</td></tr>');
        return;
    }
    $('#fresh-list').html(freshPrescriptions.map((f,i)=>`<tr>
        <td>${f.name}</td><td>${f.details||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeFresh(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeFresh(i) {
    Swal.fire({
        title: 'Are you sure?',
        html: `<div class="text-left"><strong>Fresh prescription to remove:</strong> ${freshPrescriptions[i].name}<br><small class="text-muted">This action cannot be undone.</small></div>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete it!', cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let removedFresh = freshPrescriptions[i];
            freshPrescriptions.splice(i, 1); updateFreshList();
            toastr.warning(`Fresh prescription removed: ${removedFresh.name}`);
        }
    });
}

// ==================== DISCHARGE FUNCTIONS ====================

function addDischarge() {
    discharge = {
        treatment: $('#discharge-treatment').val(),
        condition:  $('#discharge-condition').val(),
        follow_up:  $('#discharge-followup').val()
    };
    toastr.success('Discharge summary saved');
}

// ==================== STEP WORKFLOW ====================

function validateStep(step) {
    if (completedSteps.includes(step)) return true;
    if (step > currentStep) { toastr.error('Please complete the previous steps first'); return false; }
    return true;
}

function validateCurrentStep() {
    const rules = {
        1: { arr: medicines,          msg: 'Please add at least one medicine before proceeding' },
        2: { arr: admissionMedicines, msg: 'Please add at least one admission medicine before proceeding' },
        3: { arr: diagnoses,          msg: 'Please add at least one diagnosis before proceeding' },
        4: { arr: investigations,     msg: 'Please add at least one investigation before proceeding' },
        5: { arr: advices,            msg: 'Please add at least one advice before proceeding' },
        6: { arr: freshPrescriptions, msg: 'Please add at least one fresh prescription before proceeding' },
    };
    if (rules[currentStep] && rules[currentStep].arr.length === 0) {
        toastr.error(rules[currentStep].msg);
        return false;
    }
    return true;
}

function proceedToStep(step) {
    if (!validateCurrentStep()) return;
    if (!completedSteps.includes(currentStep)) completedSteps.push(currentStep);
    updateStepCompletion(currentStep, true);
    if (step <= 10) {
        activateTab(step);
        currentStep = step;
        updateTabBadges();
        const names = ['','Medicines','Admission Rx','Diagnosis','Investigations','Pre-operative Order','Post-operative Order','Fresh Prescription','Advice','Discharge'];
        toastr.success(`Proceeding to ${names[step]}`);
    }
}

function activateTab(step) {
    $('.nav-tabs .nav-link').removeClass('active');
    $('.tab-pane').removeClass('show active');
    let tabs = ['medicines','admission-rx','diagnosis','investigations','pre-op','post-op','fresh','advice','discharge'];
    let tabId = tabs[step - 1];
    $(`#${tabId}-tab`).addClass('active').removeClass('disabled');
    $(`#${tabId}`).addClass('show active');
    if (step < 9) $(`#${tabs[step]}-tab`).removeClass('disabled');
    
    // Automatically load sample data and show admission prescription when Admission Rx, Pre-op, or Post-op tabs are activated
    if (step === 2 || step === 5 || step === 6) {
        setTimeout(() => {
            populateAdmissionRxFromImage();
            setTimeout(() => {
                showAdmissionRx();
            }, 1000);
        }, 500);
    }
}

function updateStepCompletion(step, done) {
    let tabs = ['medicines','admission-rx','diagnosis','investigations','pre-op','post-op','post-op-order','fresh','advice','discharge'];
    let tabId = tabs[step - 1];
    let badge = $(`#${tabId}-tab .step-badge`);
    if (done) {
        badge.removeClass('badge-secondary badge-primary').addClass('badge-success').html(`${step} <i class="fas fa-check"></i>`);
        if (!$(`#${tabId}-tab .step-completed`).length) $(`#${tabId}-tab`).append('<i class="fas fa-check-circle step-completed"></i>');
    }
}

function updateTabBadges() {
    let tabs = ['medicines','admission-rx','diagnosis','investigations','pre-op','post-op','post-op-order','fresh','advice','discharge'];
    for (let i = 1; i <= 10; i++) {
        let tabId = tabs[i-1];
        let badge = $(`#${tabId}-tab .step-badge`);
        if (completedSteps.includes(i))      badge.removeClass('badge-secondary badge-primary').addClass('badge-success').html(`${i} <i class="fas fa-check"></i>`);
        else if (i === currentStep)           badge.removeClass('badge-secondary badge-success').addClass('badge-primary').html(i);
        else                                  badge.removeClass('badge-primary badge-success').addClass('badge-secondary').html(i);
    }
}

function completeAllSteps() {
    if (!validateCurrentStep()) return;
    if (!completedSteps.includes(currentStep)) completedSteps.push(currentStep);
    updateStepCompletion(currentStep, true);
    Swal.fire({
        title: 'All Steps Completed!',
        text: 'You have successfully completed all prescription steps.',
        icon: 'success', confirmButtonColor: '#28a745', confirmButtonText: 'Proceed to Review'
    }).then(r => { if (r.isConfirmed) nextStep(2); });
}

function nextStep(step) {
    if (step === 1) {
        if (!selectedPatient) { toastr.error('Please select a patient from the list'); return; }
        updateStepIndicator(1);
        $('#step1-content').hide(); $('#step2-content').show();
        updateProgressBar(2);
    } else if (step === 2) {
        if (medicines.length === 0) { toastr.error('Please add at least one medicine'); return; }
        updateStepIndicator(2);
        $('#step2-content').hide(); $('#step3-content').show();
        updateProgressBar(3);
        displayReview();
    }
}

function prevStep(step) {
    if (step === 2) { updateStepIndicator(1,'back'); $('#step2-content').hide(); $('#step1-content').show(); updateProgressBar(1); }
    else if (step === 3) { updateStepIndicator(2,'back'); $('#step3-content').hide(); $('#step2-content').show(); updateProgressBar(2); }
}

function updateStepIndicator(step) {
    if (step === 1) {
        $('#step1-indicator .step-number').removeClass('bg-primary').addClass('bg-success');
        $('#step2-indicator .step-number').removeClass('bg-success bg-primary').addClass('bg-secondary');
    } else if (step === 2) {
        $('#step1-indicator .step-number').removeClass('bg-primary').addClass('bg-success');
        $('#step2-indicator .step-number').removeClass('bg-secondary').addClass('bg-primary');
        $('#step3-indicator .step-number').removeClass('bg-success bg-primary').addClass('bg-secondary');
    } else if (step === 3) {
        $('#step1-indicator .step-number').removeClass('bg-primary').addClass('bg-success');
        $('#step2-indicator .step-number').removeClass('bg-secondary bg-primary').addClass('bg-success');
        $('#step3-indicator .step-number').removeClass('bg-secondary').addClass('bg-primary');
    }
}

function updateProgressBar(step) {
    let w = step===1?'33%':(step===2?'66%':'100%');
    $('#progress-bar').css('width', w).text('Step ' + step);
}

// ==================== REVIEW - EXACT PRESCRIPTION PAD LAYOUT ====================

function displayReview() {
    // --- Doctor label ---
    let rawDoctor = $('#doctor-select').find('option:selected').text() || '';
    // Strip placeholder text
    if (rawDoctor.trim().startsWith('--') || rawDoctor.trim() === '') rawDoctor = '';
    let doctorLabel = rawDoctor
        ? (/^\s*dr\.?\s+/i.test(rawDoctor) ? rawDoctor : ' ' + rawDoctor)
        : '';

    // --- Date ---
    let surgeryDate = new Date().toLocaleDateString('en-GB');
    let patientName = selectedPatient.patientname || selectedPatient.name || '';
    let patientAge  = selectedPatient.age || selectedPatient.age_years || '';
    let patientGender = selectedPatient.gender || selectedPatient.sex || '';
    let patientCode = selectedPatient.patientcode || selectedPatient.patient_id || '';
    let patientPhone = selectedPatient.mobile_no || selectedPatient.phone || '';

    // ===== BUILD PRESCRIPTION PAD HTML =====
    let html = `
    <div id="print-prescription" style="font-family: Arial, sans-serif; max-width: 820px; margin: 0 auto; color: #000; font-size: 13px;">

        <!-- ========== HEADER ========== -->
        <table id="rx-header-table" style="width:100%; background:#ADD8E6; border-bottom: 3px solid #005F02; padding-bottom: 8px; margin-bottom: 0;">
            <tr>
                <!-- LEFT: Logo + Clinic Name + Address -->
                <td style="width:60%; vertical-align:top;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="vertical-align:top; padding-right:10px;">
                                <!-- P Circle Logo -->
                                <div style="width:56px; height:56px; border-radius:50%; border:3px solid #e0001a; display:inline-flex; align-items:center; justify-content:center; background:#fff; font-size:20px; font-weight:900; line-height:1;">
                                    <span style="color:#e0001a;"></span><span style="color:#005F02;">P</span>
                                </div>
                            </td>
                            <td style="vertical-align:top;">
                                <div style="color:#e0001a; font-size:11px; font-weight:600; line-height:1.3;">চেম্বার :</div>
                                <div style="color:#005F02; font-size:26px; font-weight:900; line-height:1.15; font-family:Arial,sans-serif;">প্রফেসর ক্লিনিক</div>
                                <div style="color:#444; font-size:11px; line-height:1.5;">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                <div style="color:#444; font-size:11px; line-height:1.5;">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬</div>
                                <div style="color:#444; font-size:11px; line-height:1.5;">০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- RIGHT: Doctor Info -->
                <td style="width:40%; vertical-align:top; text-align:right; padding-left:20px; padding-right:20px;">
                    <div style="color:#005F02; font-size:15px; font-weight:800; line-height:1.4;">${doctorLabel}</div>
                    <div style="color:#333; font-size:11px; line-height:1.5;">এম.বি.বি.এস; বি.সি.এস (স্বাস্থ্য)</div>
                    <div style="color:#333; font-size:11px; line-height:1.5;">ডিপ্লোমা ইন মেডিকেল আল্ট্রাসাউন্ড</div>
                    <div style="color:#e0001a; font-size:11px; font-weight:600; line-height:1.5;">শহীদ জিয়াউর রহমান মেডিকেল কলেজ, বগুড়া।</div>
                </td>
            </tr>
        </table>

        <!-- ========== NAME / AGE / DATE ROW ========== -->
        <table id="rx-name-row" style="width:100%; background:#fce4ec; border-bottom:1px solid #e0001a; border-top:1px solid #e0001a; margin:0;">
            <tr>
                <td style="padding:6px 12px; font-size:13px; width:55%;"><strong>Name :</strong> ${patientName}
                    ${patientCode ? ' &nbsp; <span style="color:#6c757d;font-size:11px;">[' + patientCode + ']</span>' : ''}
                </td>
                <td style="padding:6px 12px; font-size:13px; width:20%;"><strong>Age :</strong> ${patientAge}${patientGender ? ' / ' + patientGender : ''}</td>
                <td style="padding:6px 12px; font-size:13px; width:25%; text-align:right;"><strong>Date :</strong> ${surgeryDate}</td>
            </tr>
        </table>

        <!-- ========== BODY: 2-COLUMN LAYOUT ========== -->
        <table style="width:100%; border-collapse:collapse; min-height:560px;">
            <tr>

                <!-- ===== LEFT COLUMN: C/C + O/E + Inv ===== -->
                <td id="rx-body-left" style="width:37%; vertical-align:top; border-right:2px solid #005F02; padding:10px 10px 10px 5px; font-size:12px; background:#ADD8E6;">

                    <!-- C/C (Chief Complaint = Diagnosis) -->
                    <div style="margin-bottom:18px;">
                        <div style="font-weight:700; text-decoration:underline; font-size:13px; margin-bottom:5px;">C/C</div>
                        ${diagnoses.length
                            ? diagnoses.map(d => `<div style="margin-left:6px; line-height:1.6;">• ${d.name}${d.note ? ' <span style="color:#555;font-size:11px;">- ' + d.note + '</span>' : ''}</div>`).join('')
                            : '<div style="margin-left:6px; color:#aaa;">—</div>'
                        }
                    </div>

                    <!-- O/E (On Examination) -->
                    <div style="margin-bottom:18px;">
                        <div style="font-weight:700; text-decoration:underline; font-size:13px; margin-bottom:5px;">O/E</div>
                        <div style="margin-left:6px; line-height:1.8;">. Pulse</div>
                        <div style="margin-left:6px; line-height:1.8;">. BP</div>
                        <div style="margin-left:6px; line-height:1.8;">. Anaemia</div>
                        <div style="margin-left:6px; line-height:1.8;">. Jaundice</div>
                        <div style="margin-left:6px; line-height:1.8;">. Tem</div>
                        <div style="margin-left:6px; line-height:1.8;">. Oedema</div>
                        <div style="margin-left:6px; line-height:1.8;">. Weight</div>
                        <div style="margin-left:6px; line-height:1.8;">. Heart</div>
                        <div style="margin-left:6px; line-height:1.8;">. Lungs</div>
                        <div style="margin-left:6px; line-height:1.8;">. FM</div>
                    </div>

                    <!-- Inv (Investigations) -->
                    <div>
                        <div style="font-weight:700; text-decoration:underline; font-size:13px; margin-bottom:5px;">Inv</div>
                        ${investigations.length
                            ? investigations.map(i => `<div style="margin-left:6px; line-height:1.8;">. ${i.name}${i.note ? ' <span style="color:#555;font-size:11px;">- ' + i.note + '</span>' : ''}</div>`).join('')
                            : `<div style="margin-left:6px; line-height:1.75;">. CBC/Hb%</div>
                               <div style="margin-left:6px; line-height:1.75;">. Urine R/M/E</div>
                               <div style="margin-left:6px; line-height:1.75;">. RBS/FBS</div>
                               <div style="margin-left:6px; line-height:1.75;">. HBs Ag</div>
                               <div style="margin-left:6px; line-height:1.75;">. VDRL</div>
                               <div style="margin-left:6px; line-height:1.75;">. Blood grouping</div>
                               <div style="margin-left:6px; line-height:1.75;">. S. bilirubin</div>
                               <div style="margin-left:6px; line-height:1.75;">. Widal test</div>
                               <div style="margin-left:6px; line-height:1.75;">. Blood urea</div>
                               <div style="margin-left:6px; line-height:1.75;">. S. creatinine</div>
                               <div style="margin-left:6px; line-height:1.75;">. ASo titre</div>
                               <div style="margin-left:6px; line-height:1.75;">. RA test</div>
                               <div style="margin-left:6px; line-height:1.75;">. U.R/E</div>
                               <div style="margin-left:6px; line-height:1.75;">. USG of</div>
                               <div style="margin-left:6px; line-height:1.75;">. X-ray of</div>
                               <div style="margin-left:6px; line-height:1.75;">. ECG</div>`
                        }
                    </div>

                                    </td>

                <!-- ===== RIGHT COLUMN: Rx Medicines ===== -->
                <td style="width:63%; vertical-align:top; padding:10px 10px 10px 16px; font-size:13px;">

                    <!-- Rx Symbol -->
                    <div style="color:#005F02; font-size:26px; font-style:italic; font-weight:900; margin-bottom:14px; font-family:Georgia,serif;">&#8478;</div>

                    <!-- Professional Medicine Pad Table -->
                    <table style="width:100%; border-collapse:collapse; border:2px solid #005F02; margin-bottom:15px; font-size:12px;">
                        <!-- Table Header -->
                        <thead>
                            <tr style="background:#ADD8E6; border-bottom:2px solid #005F02;">
                                <th style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:700; font-size:12px; width:45%;">ঔষধের নাম</th>
                                <th colspan="3" style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:700; font-size:12px; width:30%;">কখন খাবেন?</th>
                                <th colspan="2" style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:700; font-size:12px; width:15%;">আহারের</th>
                                <th colspan="3" style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:700; font-size:12px; width:10%;">কতদিন?</th>
                            </tr>
                            <tr style="background:#ADD8E6; border-bottom:2px solid #005F02;">
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px;"></th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:10%;">সকাল</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:10%;">দুপুর</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:10%;">রাত</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:7.5%;">আগে</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:7.5%;">পরে</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:4%;">দিন</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:3%;">মাস</th>
                                <th style="border:1px solid #005F02; padding:4px 6px; text-align:center; font-weight:700; font-size:11px; width:3%;">চলবে</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody>
                            ${medicines.length > 0 ? medicines.map((m, idx) => {
                                // Parse dosage to extract morning, noon, night values
                                let dosageParts = { morning: '', noon: '', night: '' };
                                if (m.dosage) {
                                    let parts = m.dosage.split('+');
                                    dosageParts.morning = parts[0] || '';
                                    dosageParts.noon = parts[1] || '';
                                    dosageParts.night = parts[2] || '';
                                }
                                
                                // Extract meal timing from instructions or default to After
                                let mealBefore = '';
                                let mealAfter = '✓';
                                if (m.instructions) {
                                    let inst = m.instructions.toLowerCase();
                                    if (inst.includes('before') || inst.includes('ac')) {
                                        mealBefore = '✓';
                                        mealAfter = '';
                                    } else if (inst.includes('after') || inst.includes('pc')) {
                                        mealBefore = '';
                                        mealAfter = '✓';
                                    }
                                }
                                
                                // Extract duration type
                                let durationDays = '';
                                let durationMonths = '';
                                let durationOngoing = '';
                                let durationValue = m.duration || '';
                                if (durationValue) {
                                    let dur = durationValue.toLowerCase();
                                    if (dur.includes('month') || dur.includes('mo')) {
                                        let match = dur.match(/(\d+)/);
                                        durationMonths = match ? match[1] : '';
                                    } else if (dur.includes('ongoing') || dur.includes('continue') || dur.includes('life')) {
                                        durationOngoing = '✓';
                                    } else {
                                        let match = dur.match(/(\d+)/);
                                        durationDays = match ? match[1] : '';
                                    }
                                }
                                
                                return `
                                <tr style="border-bottom:1px solid #ddd; background:#ffffff;">
                                    <td style="border:1px solid #005F02; padding:6px 8px; font-weight:600; font-size:12px; color:#000000;">
                                        ${m.name}${m.strength ? '<br><small style="color:#000000;">' + m.strength + '</small>' : ''}
                                        ${m.medicine_type ? '<br><small style="color:#000000;">(' + m.medicine_type + ')</small>' : ''}
                                    </td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; background:#ffffff; color:#000000;">${dosageParts.morning}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; background:#ffffff; color:#000000;">${dosageParts.noon}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; background:#ffffff; color:#000000;">${dosageParts.night}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:600; color:#000000;">${mealBefore}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:600; color:#000000;">${mealAfter}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:600; color:#000000;">${durationDays}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:600; color:#000000;">${durationMonths}</td>
                                    <td style="border:1px solid #005F02; padding:6px 8px; text-align:center; font-weight:600; color:#000000;">${durationOngoing}</td>
                                </tr>
                                ${m.instructions ? `
                                <tr>
                                    <td colspan="9" style="border:1px solid #005F02; padding:4px 8px; background:#ffffff; font-size:11px; color:#000000; font-style:italic;">
                                        <strong>Instructions:</strong> ${m.instructions}
                                    </td>
                                </tr>` : ''}
                            `}).join('') : `
                                <tr>
                                    <td colspan="9" style="border:1px solid #005F02; padding:20px 8px; text-align:center; background:#ffffff; color:#000000; font-style:italic;">
                                        No medicines prescribed
                                    </td>
                                </tr>
                            `}
                        </tbody>
                    </table>

                    
                    <!-- Fresh Prescriptions -->
                    ${freshPrescriptions.length ? `
                        <div style="margin-top:18px;">
                            <div style="font-weight:700; text-decoration:underline; font-size:13px; margin-bottom:6px;">Other Instructions:</div>
                            ${freshPrescriptions.map(f => `<div style="margin-left:8px; line-height:1.7;"><strong>${f.name}:</strong> ${f.details || ''}</div>`).join('')}
                        </div>
                    ` : ''}

                    
                    <!-- Discharge Summary -->
                    ${discharge && (discharge.treatment || discharge.condition || discharge.follow_up) ? `
                        <div style="margin-top:18px; border:1px solid #005F02; border-radius:4px; padding:8px 10px;">
                            <div style="font-weight:700; color:#005F02; margin-bottom:6px; font-size:13px;">Discharge Summary</div>
                            ${discharge.treatment ? '<div><strong>Treatment:</strong> ' + discharge.treatment + '</div>' : ''}
                            ${discharge.condition ? '<div><strong>Condition:</strong> ' + discharge.condition + '</div>' : ''}
                            ${discharge.follow_up ? '<div><strong>Follow-up:</strong> ' + discharge.follow_up + '</div>' : ''}
                        </div>
                    ` : ''}

                </td>
            </tr>
        </table>

        <!-- ========== FOOTER ========== -->
        <table id="rx-footer-table" style="width:100%; border-top:2px solid #005F02; margin-top:8px;">
            <tr>
                <td style="padding:6px 8px; font-size:12px; color:#333; text-align:center; width:100%;">
                    বিঃ দ্রঃ ......................................&nbsp;&nbsp;&nbsp;&nbsp;দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।
                </td>
            </tr>
        </table>

    </div>`;

    $('#review-content').html(html);
}

// ==================== SAVE & PRINT ====================

function printPrescription() {
    if (!selectedPatient) { toastr.error('Patient information is missing'); return; }
    if (!medicines.length && !admissionMedicines.length) { toastr.error('No medicines added'); return; }
    saveToDatabase(false);

    let step1WasVisible = $('#step1-content').is(':visible');
    let step2WasVisible = $('#step2-content').is(':visible');
    let step3WasVisible = $('#step3-content').is(':visible');

    displayReview();
    $('#step1-content').hide();
    $('#step2-content').hide();
    $('#step3-content').show();

    let originalTitle = document.title;
    let patientName  = (selectedPatient.patientname || selectedPatient.name || 'Patient').toString();
    let patientId    = (selectedPatient.patientcode || selectedPatient.patient_id || selectedPatient.id || '').toString();
    let surgeryDate  = ($('#surgery-date').val() || '').toString();
    let safe = (s) => s.replace(/[\\/:*?"<>|]/g, '').replace(/\s+/g, ' ').trim();
    let filename = safe(`Prescription_${patientName}${patientId ? '_' + patientId : ''}${surgeryDate ? '_' + surgeryDate : ''}`);

    document.title = filename || originalTitle;

    window.onafterprint = function() {
        document.title = originalTitle;
        if (step1WasVisible) $('#step1-content').show(); else $('#step1-content').hide();
        if (step2WasVisible) $('#step2-content').show(); else $('#step2-content').hide();
        if (step3WasVisible) $('#step3-content').show(); else $('#step3-content').hide();
        window.onafterprint = null;
    };

    setTimeout(() => window.print(), 500);
}

function saveToDatabase(showMessage = true) {
    let doctorName = $('#doctor-select').find('option:selected').text() || '';
    let formData = {
        patient_id: selectedPatient.id,
        doctor_name: doctorName,
        medicines, admission_medicines: admissionMedicines, diagnoses, investigations, advices,
        fresh_prescriptions: freshPrescriptions, discharge,
        template_id: selectedTemplate?.id || null,
        _token: '{{ csrf_token() }}'
    };
    $.ajax({
        url: '{{ route("surgery-prescriptions.store") }}',
        type: 'POST', data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(r) { if (r.success && showMessage) toastr.success('Prescription saved successfully'); },
        error: function(xhr) { toastr.error('Error: ' + (xhr.responseJSON?.message || 'Unknown error')); }
    });
}
</script>
@stop