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
    /* ===== BASE ===== */
    .progress { height: 20px; margin-bottom: 20px; }
    .progress-bar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: width 0.6s ease; }
    .step-indicator { margin-bottom: 30px; }
    .step-item { text-align: center; position: relative; }
    .step-number { width: 40px; height: 40px; line-height: 40px; border-radius: 50%; display: inline-block; font-weight: bold; margin-bottom: 10px; transition: all 0.3s; }
    .step-number.bg-success { background: #28a745 !important; }
    .step-label { font-size: 14px; font-weight: 500; }
    .step.active .step-number { transform: scale(1.1); box-shadow: 0 0 15px rgba(40,167,69,0.5); }
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
    .medicine-checkbox { transform: scale(1.2); cursor: pointer; }
    .select2-container--bootstrap4 .select2-selection { border-radius: 5px; border-color: #ced4da; }
    .btn-delete { background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 6px; transition: all 0.3s; font-weight: 600; }
    .btn-delete:hover { background: #c82333; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(220,53,69,0.3); }
    .btn-add { background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s; width: 100%; font-size: 15px; }
    .btn-add:hover { background: #218838; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(40,167,69,0.4); }
    .footer-buttons { display: flex; justify-content: space-between; align-items: center; }
    .footer-buttons .btn { padding: 12px 30px; font-size: 16px; font-weight: 600; min-width: 150px; border-radius: 8px; }
    .footer-buttons .btn-success { background: #28a745; border-color: #28a745; }
    .footer-buttons .btn-success:hover { background: #218838; border-color: #218838; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(40,167,69,0.3); }
    .footer-buttons .btn-primary { background: #007bff; border-color: #007bff; }
    .footer-buttons .btn-primary:hover { background: #0056b3; border-color: #0056b3; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,123,255,0.3); }
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

    /* ===== EXISTING DATA SEARCH ===== */
    .existing-data-panel { background: linear-gradient(135deg,#f8f9ff 0%,#e8f4fd 100%); border: 2px solid #007bff; border-radius: 10px; padding: 15px; margin-bottom: 15px; position: relative; }
    .existing-data-panel .panel-title { font-size: 13px; font-weight: 700; color: #007bff; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .existing-data-panel .panel-title .badge { font-size: 10px; background: #007bff; color: white; padding: 2px 8px; border-radius: 10px; }
    .existing-search-input { border: 1px solid #007bff !important; border-radius: 20px !important; padding: 6px 14px !important; font-size: 13px !important; width: 100%; transition: all 0.2s; }
    .existing-search-input:focus { outline: none; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.2) !important; }
    .existing-results-list { max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: white; margin-top: 8px; display: none; }
    .existing-result-item { padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f1f1f1; transition: background 0.15s; display: flex; justify-content: space-between; align-items: center; }
    .existing-result-item:hover { background: #e8f4fd; }
    .existing-result-item:last-child { border-bottom: none; }
    .existing-result-item .result-main { font-weight: 500; color: #333; }
    .existing-result-item .result-sub { font-size: 11px; color: #6c757d; }
    .btn-use-existing { background: #007bff; color: white; border: none; padding: 2px 8px; border-radius: 4px; font-size: 11px; white-space: nowrap; cursor: pointer; transition: background 0.2s; }
    .btn-use-existing:hover { background: #0056b3; }
    .or-divider { text-align: center; margin: 12px 0; position: relative; color: #adb5bd; font-size: 12px; font-weight: 600; }
    .or-divider::before, .or-divider::after { content: ''; position: absolute; top: 50%; width: 42%; height: 1px; background: #dee2e6; }
    .or-divider::before { left: 0; }
    .or-divider::after { right: 0; }

    /* ===== BABY NOTE SECTION ===== */
    .baby-note-card {
        background: linear-gradient(135deg, #fff0f5 0%, #fce4ec 100%);
        border: 2px solid #e91e63;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        position: relative;
        overflow: hidden;
    }
    .baby-note-card::before {
        content: '👶';
        position: absolute;
        top: -5px;
        right: 15px;
        font-size: 48px;
        opacity: 0.15;
    }
    .baby-note-title {
        color: #c2185b;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .baby-note-title i { color: #e91e63; }
    .sex-toggle-group { display: flex; gap: 10px; align-items: center; }
    .sex-toggle-btn {
        display: flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 25px; cursor: pointer;
        border: 2px solid #dee2e6; background: #fff;
        font-size: 13px; font-weight: 600; transition: all 0.25s;
        user-select: none;
    }
    .sex-toggle-btn:hover { border-color: #e91e63; }
    .sex-toggle-btn.active-male { border-color: #2196f3; background: #e3f2fd; color: #1565c0; }
    .sex-toggle-btn.active-female { border-color: #e91e63; background: #fce4ec; color: #c2185b; }
    .sex-toggle-btn input[type=radio] { display: none; }
    .baby-note-field label { font-size: 12px; font-weight: 600; color: #6c757d; margin-bottom: 4px; }
    .baby-note-field input, .baby-note-field select {
        border: 1.5px solid #f48fb1;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        background: #fff;
        width: 100%;
        transition: border-color 0.2s;
    }
    .baby-note-field input:focus, .baby-note-field select:focus {
        border-color: #e91e63;
        outline: none;
        box-shadow: 0 0 0 3px rgba(233,30,99,0.12);
    }

    /* ===== POST-OP MEDICINE UX ===== */
    .postop-medicine-form {
        background: linear-gradient(135deg, #f0fff4 0%, #e8f5e9 100%);
        border: 2px solid #43a047;
        border-radius: 12px;
        padding: 22px;
        margin-bottom: 20px;
    }
    .postop-medicine-form-title {
        color: #2e7d32;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .postop-field label { font-size: 12px; font-weight: 600; color: #388e3c; margin-bottom: 4px; display: block; }
    .postop-field input, .postop-field select, .postop-field textarea {
        border: 1.5px solid #a5d6a7;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        background: #fff;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .postop-field input:focus, .postop-field select:focus, .postop-field textarea:focus {
        border-color: #43a047;
        outline: none;
        box-shadow: 0 0 0 3px rgba(67,160,71,0.15);
    }
    .btn-postop-add {
        background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%);
        color: #fff; border: none;
        padding: 11px 28px; border-radius: 8px;
        font-weight: 700; font-size: 14px;
        display: flex; align-items: center; gap: 8px;
        cursor: pointer; transition: all 0.25s;
        box-shadow: 0 4px 12px rgba(67,160,71,0.3);
    }
    .btn-postop-add:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(67,160,71,0.4); }
    .postop-medicine-row {
        display: flex; align-items: center; gap: 0;
        background: #fff; border: 1.5px solid #c8e6c9;
        border-radius: 10px; padding: 12px 14px;
        margin-bottom: 10px; transition: all 0.2s;
        position: relative;
    }
    .postop-medicine-row:hover { border-color: #43a047; box-shadow: 0 2px 10px rgba(67,160,71,0.15); }
    .postop-medicine-row .med-number {
        background: #43a047; color: #fff;
        width: 26px; height: 26px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; flex-shrink: 0; margin-right: 12px;
    }
    .postop-medicine-row .med-info { flex: 1; }
    .postop-medicine-row .med-name { font-size: 13px; font-weight: 700; color: #1b5e20; }
    .postop-medicine-row .med-details { font-size: 11px; color: #4caf50; margin-top: 2px; }
    .postop-medicine-row .med-badge { font-size: 10px; padding: 2px 8px; border-radius: 10px; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; margin-right: 6px; }
    .postop-medicine-row .med-actions { display: flex; align-items: center; gap: 6px; }
    .postop-medicine-row .med-check { transform: scale(1.3); cursor: pointer; accent-color: #43a047; }
    .btn-postop-delete { background: #fff; border: 1.5px solid #ef9a9a; color: #e53935; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
    .btn-postop-delete:hover { background: #ffebee; border-color: #e53935; }
    .postop-empty-state { text-align: center; padding: 40px 20px; color: #9e9e9e; }
    .postop-empty-state i { font-size: 48px; color: #c8e6c9; margin-bottom: 12px; display: block; }
    .postop-print-bar {
        background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
        border-radius: 10px; padding: 16px 20px;
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 20px;
    }
    .postop-print-bar .count-info { color: #a5d6a7; font-size: 13px; }
    .postop-print-bar .count-info strong { color: #fff; font-size: 22px; display: block; line-height: 1; }
    .btn-postop-print {
        background: #fff; color: #2e7d32;
        border: none; padding: 11px 24px;
        border-radius: 8px; font-weight: 700; font-size: 14px;
        display: flex; align-items: center; gap: 8px;
        cursor: pointer; transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .btn-postop-print:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,0.25); }
    .btn-postop-clear {
        background: rgba(255,255,255,0.15); color: #fff;
        border: 1.5px solid rgba(255,255,255,0.4); padding: 10px 18px;
        border-radius: 8px; font-weight: 600; font-size: 13px;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-postop-clear:hover { background: rgba(255,255,255,0.25); }
    .postop-dosage-quick { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .dosage-chip {
        padding: 4px 10px; border-radius: 14px; font-size: 11px; font-weight: 600;
        border: 1.5px solid #a5d6a7; background: #fff; color: #388e3c;
        cursor: pointer; transition: all 0.18s;
    }
    .dosage-chip:hover, .dosage-chip.active { background: #43a047; color: #fff; border-color: #43a047; }

    /* ===== FULL PAGE LOADING OVERLAY ===== */
    .tab-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.98);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .tab-loading-logo {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        animation: logoPulse 2s ease-in-out infinite;
        border: 3px solid #e3e8f0;
    }
    .tab-loading-logo img {
        width: 45px !important;
        height: 45px !important;
        object-fit: contain !important;
    }
    .tab-loading-text {
        font-size: 16px;
        font-weight: 600;
        color: #007bff;
        margin-bottom: 10px;
    }
    .tab-loading-subtext {
        font-size: 13px;
        color: #6c757d;
    }
    @keyframes logoPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }

    /* ===== PRINT STYLES ===== */
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
        #rx-header-table { width: 100%; }
        #rx-name-row { background: #fce4ec !important; }
        #rx-body-left { border-right: 2px solid #005F02 !important; }
        #rx-footer-table { width: 100%; border-top: 2px solid #005F02 !important; }
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
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
                <div class="step-label">Load Template & Add Items</div>
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
                                <th>ID</th><th>Patient ID</th><th>Name</th><th>Age/Gender</th><th>Phone</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body">
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading patients...</p></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="patient-count" id="patient-count">Loading...</div>
                <div id="patient-details" style="display:none;" class="patient-info-card">
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
        <div class="card step-content no-print" id="step2-content" style="display:none;">
            <div class="card-header">
                <h5><i class="fas fa-pills mr-2"></i>Step 2: Load Template & Add All Items</h5>
            </div>
            <div class="card-body">
                <div class="mt-4">
                    <ul class="nav nav-tabs" id="templateTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="admission-rx-tab" data-toggle="tab" href="#admission-rx" role="tab">
                                <i class="fas fa-hospital-user text-primary"></i> Admission Rx <span class="step-badge badge badge-primary">1</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pre-op-tab" data-toggle="tab" href="#pre-op" role="tab">
                                <i class="fas fa-procedures text-primary"></i> Pre-operative Order <span class="step-badge badge badge-primary">2</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="post-op-tab" data-toggle="tab" href="#post-op" role="tab">
                                <i class="fas fa-band-aid text-primary"></i> Post-operative Order <span class="step-badge badge badge-primary">3</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="fresh-tab" data-toggle="tab" href="#fresh" role="tab">
                                <i class="fas fa-plus-circle text-primary"></i> Fresh Prescription <span class="step-badge badge badge-primary">4</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="discharge-tab" data-toggle="tab" href="#discharge" role="tab">
                                <i class="fas fa-hospital text-muted"></i> Discharge <span class="step-badge badge badge-secondary">5</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="display-tab" data-toggle="tab" href="#display" role="tab">
                                <i class="fas fa-eye text-dark"></i> Display Template
                            </a>
                        </li>
                    </ul>

                    <!-- Full Page Loading Overlay -->
    <div class="tab-loading-overlay" id="tabLoadingOverlay" style="display: none;">
        <div class="tab-loading-logo">
            <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE" style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <div class="tab-loading-text">Loading Tab Content</div>
        <div class="tab-loading-subtext">Please wait while we prepare the content...</div>
    </div>

    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="templateTabsContent">

                        <!-- ===== TAB 1: ADMISSION RX ===== -->
                        <div class="tab-pane fade show active" id="admission-rx" role="tabpanel">
                            <div class="template-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required"><i class="fas fa-file-medical text-info mr-2"></i>Select Template *</label>
                                            <select class="form-control select2" id="template-select" style="width:100%;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-info btn-sm" onclick="loadSelectedTemplate()">
                                            <i class="fas fa-download mr-2"></i>Load Template
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Admission Medicines List <span class="badge badge-success ml-1" id="admission-med-count-badge">0</span></h6>
                                            <div class="card-tools">
                                                <div class="input-group input-group-sm" style="width:200px;">
                                                    <input type="text" class="form-control" id="admission-medicine-search-filter" placeholder="Search medicines...">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-default" id="clear-admission-medicine-search"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover medicine-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="40px"><input type="checkbox" id="select-all-admission-medicines" title="Select All"></th>
                                                        <th>Medicine</th><th>Dosage</th><th>Duration</th><th>Type</th><th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="admission-medicine-list">
                                                    <tr><td colspan="6" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No admission medicines added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="selected-admission-template-info" style="display:none;" class="mt-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle mr-2"></i>Template Loaded: <strong id="selected-admission-template-name">-</strong></h6>
                                    <p class="mb-0"><small>Doctor: <span id="selected-admission-doctor-name">-</span></small></p>
                                </div>
                            </div>
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(1)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Patient Selection
                                </button>
                                <button type="button" class="btn btn-info" onclick="printAdmissionRxPrescription()">
                                    <i class="fas fa-print mr-2"></i>Print Admission Rx
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(2)">
                                    Next: Pre-operative Order <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 2: PRE-OPERATIVE ORDER ===== -->
                        <div class="tab-pane fade" id="pre-op" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info py-2 mb-3">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Pre-operative Order</strong> — Add advice/instructions and fill in Baby Note details below. These will be printed on the Pre-operative Order sheet.
                                    </div>
                                </div>
                            </div>

                            <!-- Advice Section for Pre-op -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0"><i class="fas fa-comment-medical text-success mr-2"></i>Add Advice / Adv</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Advice / Instruction *</label>
                                                <textarea class="form-control" id="preop-advice-text" rows="4" placeholder="Enter pre-operative advice or instructions..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addPreOpAdvice()">
                                                <i class="fas fa-plus mr-2"></i>Add Advice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2">
                                            <h6 class="card-title mb-0">Added Pre-op Advice <span class="badge badge-info ml-1" id="preop-adv-count-badge">0</span></h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr><th>#</th><th>Advice</th><th>Action</th></tr>
                                                </thead>
                                                <tbody id="preop-advice-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No advice added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== BABY NOTE SECTION ===== -->
                            <div class="baby-note-card">
                                <div class="baby-note-title">
                                    <i class="fas fa-baby"></i>
                                    Baby Note
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-venus-mars mr-1"></i> Baby Sex</label>
                                            <div class="sex-toggle-group mt-1">
                                                <label class="sex-toggle-btn" id="sex-male-btn" onclick="selectBabySex('Male')">
                                                    <input type="radio" name="baby_sex" id="baby-sex-male" value="Male">
                                                    <i class="fas fa-mars"></i> Male
                                                </label>
                                                <label class="sex-toggle-btn" id="sex-female-btn" onclick="selectBabySex('Female')">
                                                    <input type="radio" name="baby_sex" id="baby-sex-female" value="Female">
                                                    <i class="fas fa-venus"></i> Female
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-weight mr-1"></i> Baby Weight</label>
                                            <div class="input-group">
                                                <input type="text" id="baby-weight" placeholder="e.g. 3.2">
                                                <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:12px;color:#888;pointer-events:none;">kg</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-clock mr-1"></i> Time of Birth</label>
                                            <input type="time" id="baby-birth-time">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-calendar-alt mr-1"></i> Date of Birth</label>
                                            <input type="date" id="baby-birth-date">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-heartbeat mr-1"></i> APGAR Score</label>
                                            <input type="text" id="baby-apgar" placeholder="e.g. 8/10">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="baby-note-field">
                                            <label><i class="fas fa-sticky-note mr-1"></i> Additional Note</label>
                                            <input type="text" id="baby-additional-note" placeholder="Any note...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(1)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Admission Rx
                                </button>
                                <button type="button" class="btn btn-info" onclick="printPreOperativeOrderPrescription()">
                                    <i class="fas fa-print mr-2"></i>Print Pre-Op
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(3)">
                                    Next: Post-operative Order <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 3: POST-OPERATIVE ORDER ===== -->
                        <div class="tab-pane fade" id="post-op" role="tabpanel">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-success py-2 mb-3">
                                        <i class="fas fa-band-aid mr-2"></i>
                                        <strong>Post-operative Order</strong> — Add post-operative medicines. Selected medicines will be printed on the Post-operative Order sheet.
                                    </div>
                                </div>
                            </div>

                            <!-- ===== POST-OP TEMPLATE LOAD SECTION ===== -->
                            <div class="template-section mb-3">
                                <div class="row align-items-end">
                                    <div class="col-md-9">
                                        <div class="form-group mb-0">
                                            <label><i class="fas fa-file-medical text-success mr-2"></i>Load Template (medicines only)</label>
                                            <select class="form-control" id="postop-template-select" style="width:100%;">
                                                <option value="">-- Choose a template --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-success btn-block" onclick="loadPostOpTemplate()">
                                            <i class="fas fa-download mr-2"></i>Load Medicines
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Medicine Add Form -->
                            <div class="postop-medicine-form">
                                <div class="postop-medicine-form-title">
                                    <i class="fas fa-pills"></i> Add Post-operative Medicine
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-capsules mr-1"></i> Medicine Name *</label>
                                            <input type="text" id="postop-medicine-name" placeholder="Type medicine name...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-prescription-bottle mr-1"></i> Strength / Dose</label>
                                            <input type="text" id="postop-medicine-strength" placeholder="e.g. 500mg, 1gm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-tablets mr-1"></i> Medicine Type</label>
                                            <select id="postop-medicine-type">
                                                <option value="Tablet">Tablet</option>
                                                <option value="Capsule">Capsule</option>
                                                <option value="Injection">Injection</option>
                                                <option value="Syrup">Syrup</option>
                                                <option value="IV Fluid">IV Fluid</option>
                                                <option value="Cream">Cream</option>
                                                <option value="Ointment">Ointment</option>
                                                <option value="Suppository">Suppository</option>
                                                <option value="Inhaler">Inhaler</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-clock mr-1"></i> Dosage Schedule</label>
                                            <select id="postop-dosage">
                                                <option value="1+0+1">1+0+1 (Morning + Night)</option>
                                                <option value="1+1+1">1+1+1 (Three Times)</option>
                                                <option value="0+0+1">0+0+1 (Night Only)</option>
                                                <option value="1+1+0">1+1+0 (Morning + Afternoon)</option>
                                                <option value="0+1+0">0+1+0 (Afternoon Only)</option>
                                                <option value="1+0+0">1+0+0 (Morning Only)</option>
                                                <option value="SOS">SOS (As needed)</option>
                                                <option value="Stat">Stat (Immediately)</option>
                                            </select>
                                            <div class="postop-dosage-quick">
                                                <span class="dosage-chip" onclick="setPostOpDosage('1+0+1')">1+0+1</span>
                                                <span class="dosage-chip" onclick="setPostOpDosage('1+1+1')">1+1+1</span>
                                                <span class="dosage-chip" onclick="setPostOpDosage('0+0+1')">0+0+1</span>
                                                <span class="dosage-chip" onclick="setPostOpDosage('Stat')">Stat</span>
                                                <span class="dosage-chip" onclick="setPostOpDosage('SOS')">SOS</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-route mr-1"></i> Route</label>
                                            <select id="postop-route">
                                                <option value="Oral">Oral</option>
                                                <option value="IV">IV (Intravenous)</option>
                                                <option value="IM">IM (Intramuscular)</option>
                                                <option value="SC">SC (Subcutaneous)</option>
                                                <option value="Topical">Topical</option>
                                                <option value="Inhalation">Inhalation</option>
                                                <option value="Sublingual">Sublingual</option>
                                                <option value="Rectal">Rectal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-calendar-week mr-1"></i> Duration</label>
                                            <input type="text" id="postop-duration" placeholder="e.g. 7 days, 2 weeks">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="postop-field mb-3">
                                            <label><i class="fas fa-comment-alt mr-1"></i> Frequency / Instructions</label>
                                            <input type="text" id="postop-frequency" placeholder="e.g. 8 hourly, after meals, before sleep...">
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn-postop-add" onclick="addPostOpMedicine()">
                                            <i class="fas fa-plus-circle"></i> Add Medicine to Post-op Order
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Medicine List -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="background:#fff; border:2px solid #c8e6c9; border-radius:12px; padding:16px;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                                            <div style="font-size:15px; font-weight:700; color:#2e7d32;">
                                                <i class="fas fa-list-ul mr-2"></i>
                                                Post-operative Medicines
                                                <span class="badge badge-success ml-2" id="postop-med-count-badge">0</span>
                                            </div>
                                            <div style="display:flex; gap:8px; align-items:center;">
                                                <input type="text" id="postop-search-filter" placeholder="🔍 Search..." style="border:1.5px solid #a5d6a7; border-radius:20px; padding:5px 14px; font-size:12px; width:180px;">
                                                <label style="display:flex; align-items:center; gap:5px; font-size:12px; color:#388e3c; cursor:pointer; margin:0;">
                                                    <input type="checkbox" id="select-all-postop" style="transform:scale(1.2); accent-color:#43a047;"> Select All
                                                </label>
                                            </div>
                                        </div>
                                        <div id="postop-medicine-list-container">
                                            <div class="postop-empty-state">
                                                <i class="fas fa-prescription-bottle-alt"></i>
                                                <p style="font-size:14px; margin:0;">No post-operative medicines added yet</p>
                                                <small>Use the form above to add medicines</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Print Bar -->
                            <div class="postop-print-bar mt-3">
                                <div class="count-info">
                                    <strong id="postop-selected-count">0</strong>
                                    medicines selected for print
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button type="button" class="btn-postop-clear" onclick="clearPostOpMedicines()">
                                        <i class="fas fa-trash mr-1"></i> Clear All
                                    </button>
                                    <button type="button" class="btn-postop-print" onclick="printPostOperativeOrderPrescription()">
                                        <i class="fas fa-print"></i> Print Post-Op Order
                                    </button>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(2)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Pre-operative Order
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(4)">
                                    Next: Fresh Prescription <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 4: FRESH PRESCRIPTION ===== -->
                        <div class="tab-pane fade" id="fresh" role="tabpanel">
                            <div class="template-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-user-md text-success mr-2"></i>Select Doctor</label>
                                            <select class="form-control select2" id="doctor-select" style="width:100%;">
                                                <option value="">-- Choose a doctor --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required"><i class="fas fa-file-medical text-info mr-2"></i>Select Template *</label>
                                            <select class="form-control select2" id="fresh-template-select" style="width:100%;">
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
                                <div id="template-loading" style="display:none;" class="alert alert-info py-2">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading template data...
                                </div>
                                <div id="selected-template-info" style="display:none;" class="mt-2 p-3 bg-light border-left border-info">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Doctor:</strong> <span id="selected-doctor-name" class="badge badge-primary p-2"></span></div>
                                        <div class="col-md-4"><strong>Template:</strong> <span id="selected-template-name"></span></div>
                                        <div class="col-md-4 text-right"><span id="selected-template-count" class="badge badge-info p-2"></span></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Medicines -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Add Medicine Manually</h6></div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Medicine Name *</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="medicine-search" placeholder="Search or enter medicine name...">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary" id="quick-add-medicine" title="Quick Add"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dosage</label>
                                                        <select class="form-control" id="dosage">
                                                            <option value="1+0+1">1+0+1</option>
                                                            <option value="1+1+1">1+1+1</option>
                                                            <option value="0+0+1">0+0+1</option>
                                                            <option value="1+1+0">1+1+0</option>
                                                            <option value="0+1+0">0+1+0</option>
                                                            <option value="1+0+0">1+0+0</option>
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
                                            <h6 class="card-title mb-0">Added Medicines <span class="badge badge-success ml-1" id="med-count-badge">0</span></h6>
                                            <div class="card-tools">
                                                <div class="input-group input-group-sm" style="width:200px;">
                                                    <input type="text" class="form-control" id="medicine-search-filter" placeholder="Search...">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-default" id="clear-medicine-search"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover medicine-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="40px"><input type="checkbox" id="select-all-medicines" title="Select All"></th>
                                                        <th>Medicine</th><th>Dosage</th><th>Duration</th><th>Type</th><th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="medicine-list">
                                                    <tr><td colspan="6" class="text-center text-muted py-3">No medicines added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Diagnosis -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Add Diagnosis</h6></div>
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
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Diagnosis List <span class="badge badge-success ml-1" id="diag-count-badge">0</span></h6></div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead><tr><th>Diagnosis</th><th>Notes</th><th>Action</th></tr></thead>
                                                <tbody id="diagnosis-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3">No diagnosis added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Investigations -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Add Investigation</h6></div>
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
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Investigations <span class="badge badge-success ml-1" id="inv-count-badge">0</span></h6></div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead><tr><th>Investigation</th><th>Notes</th><th>Action</th></tr></thead>
                                                <tbody id="investigation-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3">No investigations added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fresh Prescription -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Add Fresh Prescription</h6></div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="required">Prescription Name *</label>
                                                <input type="text" class="form-control" id="fresh-name" placeholder="Enter prescription name">
                                            </div>
                                            <div class="form-group">
                                                <label>Details</label>
                                                <textarea class="form-control" id="fresh-details" rows="4" placeholder="Enter details..."></textarea>
                                            </div>
                                            <button type="button" class="btn-add" onclick="addFreshPrescription()">
                                                <i class="fas fa-plus-circle mr-2"></i>Add Fresh Prescription
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Fresh Prescriptions <span class="badge badge-info ml-1" id="fresh-count-badge">0</span></h6></div>
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead><tr><th>Name</th><th>Details</th><th>Action</th></tr></thead>
                                                <tbody id="fresh-list">
                                                    <tr><td colspan="3" class="text-center text-muted py-3">No fresh prescriptions added yet</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(3)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Post-operative Order
                                </button>
                                <button type="button" class="btn btn-primary" onclick="proceedToStep(5)">
                                    Next: Discharge <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ===== TAB 5: DISCHARGE ===== -->
                        <div class="tab-pane fade" id="discharge" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="existing-data-panel">
                                        <div class="panel-title">
                                            <i class="fas fa-database"></i> Search Existing Discharge Summaries <span class="badge">Database</span>
                                        </div>
                                        <input type="text" class="existing-search-input" id="existing-discharge-search" placeholder="🔍 Type treatment or condition to search...">
                                        <div class="existing-results-list" id="existing-discharge-results"></div>
                                    </div>
                                    <div class="or-divider">OR FILL MANUALLY</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card card-outline card-danger">
                                        <div class="card-header py-2"><h6 class="card-title mb-0">Add Discharge Summary</h6></div>
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
                                <button type="button" class="btn btn-secondary" onclick="proceedToStep(4)">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous: Fresh Prescription
                                </button>
                                <button type="button" class="btn btn-success" onclick="completeAllSteps()">
                                    <i class="fas fa-check mr-2"></i> Complete All Steps
                                </button>
                            </div>
                        </div>

                        <!-- ===== DISPLAY TEMPLATE ===== -->
                        <div class="tab-pane fade" id="display" role="tabpanel">
                            <div class="card card-outline card-dark">
                                <div class="card-header py-2"><h6 class="card-title mb-0">Display Template Information</h6></div>
                                <div class="card-body" id="template-display-content">
                                    <p class="text-muted text-center">Select a template and click "Load Template" to view details</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surgery Details -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Surgery Type</label>
                            <input type="text" class="form-control" id="surgery-type" placeholder="e.g., Appendectomy">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required">Surgery Date</label>
                            <input type="date" class="form-control" id="surgery-date" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Anesthesia Type</label>
                            <input type="text" class="form-control" id="anesthesia-type" placeholder="e.g., General">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ward/Bed</label>
                            <input type="text" class="form-control" id="ward-bed" placeholder="e.g., Ward A, Bed 10">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>BP</label>
                            <input type="text" class="form-control" id="bp" placeholder="e.g., 120/80">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pulse</label>
                            <input type="text" class="form-control" id="pulse" placeholder="e.g., 72">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Temperature</label>
                            <input type="text" class="form-control" id="temperature" placeholder="e.g., 98.6">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Weight</label>
                            <input type="text" class="form-control" id="weight" placeholder="e.g., 70 kg">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Instructions</label>
                            <textarea class="form-control" id="instructions" rows="3" placeholder="Any special instructions..."></textarea>
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
        <div class="card step-content" id="step3-content" style="display:none;">
            <div class="card-header no-print">
                <h5><i class="fas fa-check-circle mr-2"></i>Review & Print</h5>
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
let postOpMedicines = [];
let preOpAdvices = [];
let diagnoses = [];
let investigations = [];
let freshPrescriptions = [];
let discharge = null;
let allPatients = [];
let selectedTemplate = null;
let currentStep = 1;
let completedSteps = [];
let selectedBabySex = '';

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    toastr.options.preventDuplicates = true;
    toastr.options.newestOnTop = false;

    loadAllPatients();
    loadTemplates();
    loadDoctors();

    $('#template-select').select2({ theme: 'bootstrap4', placeholder: 'Select a template', allowClear: true });
    $('#fresh-template-select').select2({ theme: 'bootstrap4', placeholder: 'Select a template', allowClear: true });
    $('#doctor-select').select2({ theme: 'bootstrap4', placeholder: 'Select a doctor', allowClear: true });
    $('#postop-template-select').select2({ theme: 'bootstrap4', placeholder: 'Select a template', allowClear: true });

    // Tab Loading Effect
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        let targetTab = $(e.target).attr('href');
        let tabName = $(e.target).text().trim();
        
        // Show loading overlay
        $('#tabLoadingOverlay').show();
        $('.tab-loading-text').text('Loading ' + tabName);
        $('.tab-loading-subtext').text('Please wait while we prepare the content...');
        
        // Simulate loading time (you can adjust this or remove if you want instant)
        setTimeout(function() {
            $('#tabLoadingOverlay').hide();
        }, 800);
    });

    // Patient search
    let searchTimeout;
    $('#patient-search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        let term = $(this).val();
        searchTimeout = setTimeout(() => filterPatients(term), 300);
    });

    // Replace select2 with text input for medicine search
    $('#medicine-search').replaceWith('<input type="text" class="form-control" id="medicine-search" placeholder="Search or enter medicine name...">');

    // Quick add button
    $(document).on('click', '#quick-add-medicine', function() {
        let name = $('#medicine-search').val().trim();
        if (!name) { toastr.error('Please enter medicine name'); return; }
        addQuickMedicineFromForm(name);
    });

    // Medicine search filter
    $('#medicine-search-filter').on('input', function() {
        let term = $(this).val().toLowerCase();
        $('#medicine-list tr').each(function() {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });
    $('#clear-medicine-search').on('click', function() {
        $('#medicine-search-filter').val('');
        $('#medicine-list tr').show();
    });

    // Checkbox handlers - fresh medicines
    $('#select-all-medicines').on('change', function() {
        let c = $(this).is(':checked');
        $('.medicine-checkbox').prop('checked', c);
        medicines.forEach(m => m.selected = c);
    });
    $(document).on('change', '.medicine-checkbox', function() {
        let idx = $(this).data('index');
        medicines[idx].selected = $(this).is(':checked');
        updateSelectAllState('#select-all-medicines', medicines);
    });

    // Checkbox handlers - admission medicines
    $('#select-all-admission-medicines').on('change', function() {
        let c = $(this).is(':checked');
        $('.admission-medicine-checkbox').prop('checked', c);
        admissionMedicines.forEach(m => m.selected = c);
    });
    $(document).on('change', '.admission-medicine-checkbox', function() {
        let idx = $(this).data('index');
        admissionMedicines[idx].selected = $(this).is(':checked');
        updateSelectAllState('#select-all-admission-medicines', admissionMedicines);
    });

    // Post-op select all checkbox
    $(document).on('change', '#select-all-postop', function() {
        let c = $(this).is(':checked');
        $('.postop-med-check').prop('checked', c);
        postOpMedicines.forEach(m => m.selected = c);
        updatePostOpSelectedCount();
    });
    $(document).on('change', '.postop-med-check', function() {
        let idx = parseInt($(this).data('index'));
        postOpMedicines[idx].selected = $(this).is(':checked');
        updatePostOpSelectedCount();
        let allC = postOpMedicines.every(m => m.selected);
        let anyC = postOpMedicines.some(m => m.selected);
        $('#select-all-postop').prop('checked', allC).prop('indeterminate', anyC && !allC);
    });

    // Post-op search filter
    $(document).on('input', '#postop-search-filter', function() {
        let term = $(this).val().toLowerCase();
        $('.postop-medicine-row').each(function() {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });
});

// ==================== HELPER ====================
function updateSelectAllState(checkboxId, arr) {
    let allC = arr.every(m => m.selected);
    let anyC = arr.some(m => m.selected);
    $(checkboxId).prop('checked', allC).prop('indeterminate', anyC && !allC);
}

// ==================== BABY SEX TOGGLE ====================
function selectBabySex(sex) {
    selectedBabySex = sex;
    $('#sex-male-btn').removeClass('active-male active-female');
    $('#sex-female-btn').removeClass('active-male active-female');
    if (sex === 'Male') {
        $('#sex-male-btn').addClass('active-male');
        $('#baby-sex-male').prop('checked', true);
    } else {
        $('#sex-female-btn').addClass('active-female');
        $('#baby-sex-female').prop('checked', true);
    }
}

// ==================== POST-OP MEDICINE FUNCTIONS ====================
function setPostOpDosage(val) {
    $('#postop-dosage').val(val);
    $('.dosage-chip').removeClass('active');
    $('.dosage-chip').each(function() {
        if ($(this).text() === val) $(this).addClass('active');
    });
}

function addPostOpMedicine() {
    let name = $('#postop-medicine-name').val().trim();
    if (!name) { toastr.error('Please enter medicine name'); return; }
    let med = {
        id: Date.now(),
        name: name,
        strength: $('#postop-medicine-strength').val().trim(),
        type: $('#postop-medicine-type').val(),
        dosage: $('#postop-dosage').val(),
        route: $('#postop-route').val(),
        duration: $('#postop-duration').val().trim(),
        frequency: $('#postop-frequency').val().trim(),
        selected: true
    };
    postOpMedicines.push(med);
    renderPostOpMedicines();
    $('#postop-medicine-name').val('');
    $('#postop-medicine-strength').val('');
    $('#postop-duration').val('');
    $('#postop-frequency').val('');
    $('#postop-dosage').val('1+0+1');
    $('.dosage-chip').removeClass('active');
    toastr.success('Post-op medicine added: ' + name);
}

function renderPostOpMedicines() {
    let $container = $('#postop-medicine-list-container');
    $('#postop-med-count-badge').text(postOpMedicines.length);
    if (!postOpMedicines.length) {
        $container.html(`<div class="postop-empty-state">
            <i class="fas fa-prescription-bottle-alt"></i>
            <p style="font-size:14px;margin:0;">No post-operative medicines added yet</p>
            <small>Use the form above to add medicines</small>
        </div>`);
        updatePostOpSelectedCount();
        return;
    }
    let html = '';
    postOpMedicines.forEach((m, i) => {
        let routeBadge = m.route ? `<span class="med-badge">${m.route}</span>` : '';
        let typeBadge = m.type ? `<span class="med-badge">${m.type}</span>` : '';
        let details = [m.dosage, m.duration, m.frequency].filter(Boolean).join(' · ');
        html += `<div class="postop-medicine-row" id="postop-row-${i}">
            <div class="med-number">${i+1}</div>
            <div class="med-info">
                <div class="med-name">${m.name}${m.strength ? ' <span style="font-weight:400;color:#388e3c;font-size:12px;">' + m.strength + '</span>' : ''}</div>
                <div class="med-details">${routeBadge}${typeBadge}${details}</div>
            </div>
            <div class="med-actions">
                <input type="checkbox" class="postop-med-check" data-index="${i}" ${m.selected ? 'checked' : ''} title="Include in print">
                <button type="button" class="btn-postop-delete" onclick="removePostOpMedicine(${i})" title="Remove">
                    <i class="fas fa-times" style="font-size:11px;"></i>
                </button>
            </div>
        </div>`;
    });
    $container.html(html);
    updatePostOpSelectedCount();
}

function updatePostOpSelectedCount() {
    let count = postOpMedicines.filter(m => m.selected).length;
    $('#postop-selected-count').text(count);
}

function removePostOpMedicine(i) {
    let name = postOpMedicines[i].name;
    postOpMedicines.splice(i, 1);
    renderPostOpMedicines();
    toastr.warning('Removed: ' + name);
}

function clearPostOpMedicines() {
    if (!postOpMedicines.length) return;
    Swal.fire({
        title: 'Clear all post-op medicines?',
        text: 'This cannot be undone.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear all'
    }).then(r => {
        if (r.isConfirmed) { postOpMedicines = []; renderPostOpMedicines(); toastr.info('All post-op medicines cleared'); }
    });
}

// ==================== POST-OP TEMPLATE LOAD ====================
function loadPostOpTemplate() {
    let templateId = $('#postop-template-select').val();
    if (!templateId) { toastr.error('Please select a template'); return; }

    let selected = $('#postop-template-select').find('option:selected');
    let templateName = selected.data('title') || selected.text();

    let $btn = $('button[onclick="loadPostOpTemplate()"]');
    let origHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...').prop('disabled', true);

    let url = '{{ route("surgery-prescriptions.get-template-data", ["id" => "REPLACE_ID"]) }}'.replace('REPLACE_ID', templateId);

    $.ajax({
        url: url,
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        success: function(response) {
            $btn.html(origHtml).prop('disabled', false);
            if (!response.success) { toastr.error('Failed: ' + (response.message || 'Unknown error')); return; }

            let meds = [];
            if (response.medicines?.length) {
                response.medicines.forEach(m => {
                    meds.push({
                        id: Date.now() + Math.random(),
                        name: m.name || '',
                        strength: m.strength || '',
                        type: m.medicine_type || m.type || 'Tablet',
                        dosage: m.dosage || '1+0+1',
                        route: m.route || 'Oral',
                        duration: m.duration || '',
                        frequency: m.frequency || '',
                        selected: true
                    });
                });
            }
            if (response.post_op_medicines?.length) {
                response.post_op_medicines.forEach(m => {
                    meds.push({
                        id: Date.now() + Math.random(),
                        name: m.name || '',
                        strength: m.strength || '',
                        type: m.medicine_type || m.type || 'Tablet',
                        dosage: m.dosage || '1+0+1',
                        route: m.route || 'Oral',
                        duration: m.duration || '',
                        frequency: m.frequency || '',
                        selected: true
                    });
                });
            }

            if (!meds.length) { toastr.warning('No medicines found in this template'); return; }

            if (postOpMedicines.length) {
                Swal.fire({
                    title: 'Replace existing medicines?',
                    html: `Loading <strong>"${templateName}"</strong> will replace current post-op medicines. Continue?`,
                    icon: 'question', showCancelButton: true,
                    confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, load', cancelButtonText: 'Cancel'
                }).then(r => {
                    if (r.isConfirmed) {
                        postOpMedicines = meds;
                        renderPostOpMedicines();
                        toastr.success(`${meds.length} medicine(s) loaded from "${templateName}"`);
                    }
                });
            } else {
                postOpMedicines = meds;
                renderPostOpMedicines();
                toastr.success(`${meds.length} medicine(s) loaded from "${templateName}"`);
            }
        },
        error: function(xhr) {
            $btn.html(origHtml).prop('disabled', false);
            let msg = xhr.status === 404 ? 'Template not found' : (xhr.responseJSON?.message || 'Unknown error');
            toastr.error('Error: ' + msg);
        }
    });
}

// ==================== PRE-OP ADVICE FUNCTIONS ====================
function addPreOpAdvice() {
    let text = $('#preop-advice-text').val().trim();
    if (!text) { toastr.error('Please enter advice'); return; }
    preOpAdvices.push({ id: Date.now(), advice: text });
    updatePreOpAdviceList();
    $('#preop-advice-text').val('');
    toastr.success('Pre-op advice added');
}

function updatePreOpAdviceList() {
    $('#preop-adv-count-badge').text(preOpAdvices.length);
    if (!preOpAdvices.length) {
        $('#preop-advice-list').html('<tr><td colspan="3" class="text-center text-muted py-3">No advice added yet</td></tr>');
        return;
    }
    $('#preop-advice-list').html(preOpAdvices.map((a, i) => `<tr>
        <td>${i+1}</td>
        <td>${a.advice}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removePreOpAdvice(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removePreOpAdvice(i) {
    preOpAdvices.splice(i, 1);
    updatePreOpAdviceList();
    toastr.warning('Pre-op advice removed');
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
            $('#template-select, #fresh-template-select, #postop-template-select').html(options).trigger('change');
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
                options += `<option value="${d.id}" data-name="${d.name||d.doctor_name}">
                    ${d.name||d.doctor_name}${d.specialization ? ' (' + d.specialization + ')' : ''}
                </option>`;
            });
            $('#doctor-select').html(options).trigger('change');
        },
        error: () => toastr.error('Failed to load doctors')
    });
}

function loadSelectedTemplate() {
    let isFreshTab = $('#fresh').hasClass('active');
    let $templateSelect = isFreshTab ? $('#fresh-template-select') : $('#template-select');
    let templateId = $templateSelect.val();
    if (!templateId) { toastr.error('Please select a template'); return; }
    if (isFreshTab && !$('#doctor-select').val()) { toastr.error('Please select a doctor'); return; }

    let selected = $templateSelect.find('option:selected');
    let templateName = selected.data('title') || selected.text();
    let surgeryType = selected.data('surgery_type');
    let doctorName = $('#doctor-select').find('option:selected').text();

    let $loadBtn = $('button[onclick="loadSelectedTemplate()"]');
    let origHtml = $loadBtn.html();
    $loadBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...').prop('disabled', true);

    let hasData = medicines.length || diagnoses.length || investigations.length;
    if (hasData) {
        Swal.fire({
            title: 'Load Template?', text: `Loading "${templateName}" will replace current items. Continue?`,
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, load'
        }).then(r => {
            if (r.isConfirmed) fetchTemplateData(templateId, surgeryType, templateName, doctorName, $loadBtn, origHtml);
            else $loadBtn.html(origHtml).prop('disabled', false);
        });
    } else {
        fetchTemplateData(templateId, surgeryType, templateName, doctorName, $loadBtn, origHtml);
    }
}

function fetchTemplateData(templateId, surgeryType, templateName, doctorName, $loadBtn, origHtml) {
    $('#template-loading').show();
    let url = '{{ route("surgery-prescriptions.get-template-data", ["id" => "REPLACE_ID"]) }}'.replace('REPLACE_ID', templateId);
    $.ajax({
        url: url, method: 'GET',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        success: function(response) {
            $('#template-loading').hide();
            $loadBtn.html(origHtml).prop('disabled', false);
            if (response.success) {
                let allergyWarnings = [];
                if (selectedPatient?.allergies && response.medicines?.length) {
                    let patientAllergies = selectedPatient.allergies.toLowerCase().split(',').map(a => a.trim());
                    response.medicines.forEach(med => {
                        let mn = (med.name||'').toLowerCase();
                        patientAllergies.forEach(allergy => {
                            if (allergy && mn.includes(allergy)) allergyWarnings.push(`${med.name} - Patient allergic to: ${allergy}`);
                        });
                    });
                }
                if (allergyWarnings.length > 0) {
                    Swal.fire({
                        title: '<span style="color:#dc3545;"><i class="fas fa-exclamation-triangle mr-2"></i>Allergy Warning!</span>',
                        html: `<div class="alert alert-danger mb-0"><strong>Danger: Allergy Detected</strong><br><ul class="text-left mt-2 mb-0">${allergyWarnings.map(w=>`<li><strong>${w}</strong></li>`).join('')}</ul></div>`,
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Load Anyway', cancelButtonText: 'Cancel'
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
            $loadBtn.html(origHtml).prop('disabled', false);
            let msg = xhr.status===404?'Template not found':(xhr.status===500?'Server error':(xhr.responseJSON?.message||'Unknown'));
            toastr.error('Error: ' + msg);
        }
    });
}

function populateTemplateData(response, surgeryType) {
    let isFreshTab = $('#fresh').hasClass('active');
    let doctorName = $('#doctor-select').find('option:selected').text();
    let templateName = (isFreshTab ? $('#fresh-template-select') : $('#template-select')).find('option:selected').text();

    if (isFreshTab) {
        if (response.medicines?.length) {
            medicines = response.medicines.map(m => ({...m, selected: true}));
            updateMedicineList();
            setTimeout(() => { $('.medicine-checkbox').prop('checked', true); $('#select-all-medicines').prop('checked', true); }, 100);
        }
        let total = (response.counts?.medicines||0)+(response.counts?.diagnoses||0)+(response.counts?.investigations||0)+(response.counts?.advices||0);
        $('#selected-template-info').show();
        $('#selected-template-name').text(response.template.title||'Template');
        $('#selected-doctor-name').text(doctorName);
        $('#selected-template-count').text(total + ' items loaded');
    } else {
        let meds = [];
        if (response.medicines?.length) meds = [...response.medicines.map(m=>({...m, selected:true, order_type:'admission'}))];
        if (response.admission_medicines?.length) meds = [...meds, ...response.admission_medicines.map(m=>({...m, selected:true}))];
        admissionMedicines = meds;
        if (admissionMedicines.length) {
            updateAdmissionMedicineList();
            setTimeout(() => { $('.admission-medicine-checkbox').prop('checked', true); $('#select-all-admission-medicines').prop('checked', true); }, 100);
        }
        $('#selected-admission-template-info').show();
        $('#selected-admission-template-name').text(response.template.title||'Template');
        $('#selected-admission-doctor-name').text(doctorName);
    }

    if (response.diagnoses?.length) { diagnoses = response.diagnoses; updateDiagnosisList(); }
    if (response.investigations?.length) { investigations = response.investigations; updateInvestigationList(); }
    if (response.advices?.length) { advices = response.advices; updateAdviceList(); }
    if (response.discharge) {
        discharge = response.discharge;
        if (discharge.treatment) $('#discharge-treatment').val(discharge.treatment);
        if (discharge.condition) $('#discharge-condition').val(discharge.condition);
        if (discharge.follow_up) $('#discharge-followup').val(discharge.follow_up);
    }
    if (response.template.surgery_type && !$('#surgery-type').val()) $('#surgery-type').val(response.template.surgery_type);
    else if (surgeryType && !$('#surgery-type').val()) $('#surgery-type').val(surgeryType);

    displayTemplateInfo(response);
    selectedTemplate = response.template;
    toastr.success(`Template "${templateName}" loaded successfully`);
}

function displayTemplateInfo(response) {
    let html = `<div class="card"><div class="card-body"><h5>${response.template.title}</h5><p>${response.template.description||'No description'}</p>`;
    const sections = [
        { key:'medicines',      label:'Medicines',      render:m=>`${m.name} - ${m.dosage} (${m.duration})` },
        { key:'diagnoses',      label:'Diagnosis',      render:d=>`${d.name}${d.note?' - '+d.note:''}` },
        { key:'investigations', label:'Investigations',  render:i=>`${i.name}${i.note?' - '+i.note:''}` },
        { key:'advices',        label:'Advices',         render:a=>a.advice },
    ];
    sections.forEach(s => {
        html += `<h6 class="mt-3">${s.label} (${response[s.key]?.length||0})</h6><ul class="list-group mb-3">`;
        (response[s.key]||[]).forEach(item => { html += `<li class="list-group-item">${s.render(item)}</li>`; });
        if (!response[s.key]?.length) html += `<li class="list-group-item text-muted">None</li>`;
        html += '</ul>';
    });
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
            if (allPatients.length) { displayPatients(allPatients); $('#patient-count').text(`Total ${allPatients.length} patient(s) found`); }
            else { $('#patients-table-body').html('<tr><td colspan="6" class="text-center">No patients found</td></tr>'); $('#patient-count').text('No patients found'); }
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
        let id = p.id, code = p.patientcode||p.patient_id||p.uhid||'N/A', name = p.patientname||p.name||'Unknown';
        let age = p.age||p.age_years||'', gender = p.gender||p.sex||'', phone = p.mobile_no||p.phone||p.mobile||'';
        let sel = (selectedPatient && selectedPatient.id === id) ? 'selected' : '';
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
    let code = row.data('code'), name = row.data('name'), age = row.data('age'), gender = row.data('gender'), phone = row.data('phone');
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

// ==================== FRESH MEDICINE FUNCTIONS ====================
function addMedicine() {
    let name = $('#medicine-search').val().trim();
    if (!name) { toastr.error('Please enter medicine name'); return; }
    medicines.push({
        template_medicine_id: Date.now(), name, strength: '', brand: 'Manual Entry',
        dosage: $('#dosage').val()||'1+0+1', duration: $('#duration').val()||'7 days',
        order_type: $('#order-type').val()||'pre-op', route: $('#route').val()||'Oral',
        frequency: $('#frequency').val(), medicine_type: $('#medicine-type').val()||'Tablet',
        instructions: $('#medicine-instruction').val(), selected: true
    });
    updateMedicineList();
    clearMedicineForm();
    toastr.success('Medicine added successfully');
}

function addQuickMedicineFromForm(name) {
    medicines.push({
        template_medicine_id: Date.now(), name, strength: '', brand: 'Quick Entry',
        dosage: '1+0+1', duration: '7 days', order_type: 'pre-op', route: 'Oral',
        frequency: '', medicine_type: 'Tablet', instructions: '', selected: true
    });
    updateMedicineList();
    $('#medicine-search').val('');
    toastr.success('Quick medicine added: ' + name);
}

function clearMedicineForm() {
    $('#medicine-search').val(''); $('#duration').val(''); $('#medicine-instruction').val(''); $('#frequency').val('');
}

function updateMedicineList() {
    let sel = medicines.filter(m => m.selected).length;
    $('#med-count-badge').text(`${sel}/${medicines.length}`);
    if (!medicines.length) { $('#medicine-list').html('<tr><td colspan="6" class="text-center text-muted py-3">No medicines added yet</td></tr>'); return; }
    let html = '';
    medicines.forEach((m, i) => {
        let bc = m.order_type==='pre-op'?'badge-warning':(m.order_type==='post-op'?'badge-success':'badge-info');
        html += `<tr>
            <td><input type="checkbox" class="medicine-checkbox" data-index="${i}" ${m.selected?'checked':''}></td>
            <td>${m.name}${m.strength?'<small class="text-muted ml-1">'+m.strength+'</small>':''}</td>
            <td>${m.dosage}</td><td>${m.duration}</td>
            <td><span class="badge ${bc}">${m.order_type}</span></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeMedicine(${i})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    $('#medicine-list').html(html);
}

function removeMedicine(i) {
    Swal.fire({ title:'Remove?', html:`Remove <strong>${medicines[i].name}</strong>?`, icon:'warning', showCancelButton:true, confirmButtonColor:'#dc3545', confirmButtonText:'Yes, delete' })
    .then(r => { if(r.isConfirmed) { let n=medicines[i].name; medicines.splice(i,1); updateMedicineList(); toastr.warning('Removed: '+n); }});
}

// ==================== ADMISSION MEDICINE FUNCTIONS ====================
function updateAdmissionMedicineList() {
    let sel = admissionMedicines.filter(m => m.selected).length;
    $('#admission-med-count-badge').text(`${sel}/${admissionMedicines.length}`);
    if (!admissionMedicines.length) { $('#admission-medicine-list').html('<tr><td colspan="6" class="text-center text-muted py-3">No admission medicines added yet</td></tr>'); return; }
    let html = '';
    admissionMedicines.forEach((m, i) => {
        let bc = m.order_type==='admission'?'badge-primary':'badge-info';
        html += `<tr>
            <td><input type="checkbox" class="admission-medicine-checkbox" data-index="${i}" ${m.selected?'checked':''}></td>
            <td>${m.name}${m.strength?'<small class="text-muted ml-1">'+m.strength+'</small>':''}</td>
            <td>${m.dosage||''}</td><td>${m.duration||''}</td>
            <td><span class="badge ${bc}">${m.order_type}</span></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeAdmissionMedicine(${i})"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    });
    $('#admission-medicine-list').html(html);
}

function removeAdmissionMedicine(i) {
    Swal.fire({ title:'Remove?', html:`Remove <strong>${admissionMedicines[i].name}</strong>?`, icon:'warning', showCancelButton:true, confirmButtonColor:'#dc3545', confirmButtonText:'Yes, delete' })
    .then(r => { if(r.isConfirmed) { let n=admissionMedicines[i].name; admissionMedicines.splice(i,1); updateAdmissionMedicineList(); toastr.warning('Removed: '+n); }});
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
    if (!diagnoses.length) { $('#diagnosis-list').html('<tr><td colspan="3" class="text-center text-muted py-3">No diagnosis added yet</td></tr>'); return; }
    $('#diagnosis-list').html(diagnoses.map((d,i)=>`<tr>
        <td>${d.name}</td><td>${d.note||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeDiagnosis(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeDiagnosis(i) {
    diagnoses.splice(i,1); updateDiagnosisList(); toastr.warning('Diagnosis removed');
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
    if (!investigations.length) { $('#investigation-list').html('<tr><td colspan="3" class="text-center text-muted py-3">No investigations added yet</td></tr>'); return; }
    $('#investigation-list').html(investigations.map((inv,i)=>`<tr>
        <td>${inv.name}</td><td>${inv.note||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeInvestigation(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeInvestigation(i) {
    investigations.splice(i,1); updateInvestigationList(); toastr.warning('Investigation removed');
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
    if (!freshPrescriptions.length) { $('#fresh-list').html('<tr><td colspan="3" class="text-center text-muted py-3">No fresh prescriptions added yet</td></tr>'); return; }
    $('#fresh-list').html(freshPrescriptions.map((f,i)=>`<tr>
        <td>${f.name}</td><td>${f.details||''}</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeFresh(${i})"><i class="fas fa-trash"></i></button></td>
    </tr>`).join(''));
}

function removeFresh(i) {
    freshPrescriptions.splice(i,1); updateFreshList(); toastr.warning('Removed');
}

// ==================== DISCHARGE FUNCTIONS ====================
function addDischarge() {
    discharge = { treatment:$('#discharge-treatment').val(), condition:$('#discharge-condition').val(), follow_up:$('#discharge-followup').val() };
    toastr.success('Discharge summary saved');
}

function selectExistingDischarge(item) {
    if (item.treatment) $('#discharge-treatment').val(item.treatment);
    if (item.condition) $('#discharge-condition').val(item.condition);
    if (item.follow_up) $('#discharge-followup').val(item.follow_up);
    $('#existing-discharge-search').val('');
    $('#existing-discharge-results').hide();
    toastr.success('Discharge summary loaded from existing data');
}

// ==================== STEP WORKFLOW ====================
function validateCurrentStep() { return true; }

function proceedToStep(step) {
    if (!completedSteps.includes(currentStep)) completedSteps.push(currentStep);
    updateStepCompletion(currentStep, true);
    if (step <= 5) {
        activateTab(step);
        currentStep = step;
        updateTabBadges();
    }
}

function activateTab(step) {
    $('.nav-tabs .nav-link').removeClass('active');
    $('.tab-pane').removeClass('show active');
    let tabs = ['admission-rx','pre-op','post-op','fresh','discharge'];
    let tabId = tabs[step - 1];
    $(`#${tabId}-tab`).addClass('active').removeClass('disabled');
    $(`#${tabId}`).addClass('show active');
    if (step < 5) $(`#${tabs[step]}-tab`).removeClass('disabled');
}

function updateStepCompletion(step, done) {
    let tabs = ['admission-rx','pre-op','post-op','fresh','discharge'];
    let tabId = tabs[step - 1];
    let badge = $(`#${tabId}-tab .step-badge`);
    if (done) {
        badge.removeClass('badge-secondary badge-primary').addClass('badge-success').html(`${step} <i class="fas fa-check"></i>`);
        if (!$(`#${tabId}-tab .step-completed`).length) $(`#${tabId}-tab`).append('<i class="fas fa-check-circle step-completed"></i>');
    }
}

function updateTabBadges() {
    let tabs = ['admission-rx','pre-op','post-op','fresh','discharge'];
    for (let i = 1; i <= 5; i++) {
        let tabId = tabs[i-1];
        let badge = $(`#${tabId}-tab .step-badge`);
        if (completedSteps.includes(i))     badge.removeClass('badge-secondary badge-primary').addClass('badge-success').html(`${i} <i class="fas fa-check"></i>`);
        else if (i === currentStep)          badge.removeClass('badge-secondary badge-success').addClass('badge-primary').html(i);
        else                                 badge.removeClass('badge-primary badge-success').addClass('badge-secondary').html(i);
    }
}

function completeAllSteps() {
    if (!completedSteps.includes(currentStep)) completedSteps.push(currentStep);
    updateStepCompletion(currentStep, true);
    Swal.fire({ title:'All Steps Completed!', text:'You have successfully completed all prescription steps.', icon:'success', confirmButtonColor:'#28a745', confirmButtonText:'Proceed to Review' })
    .then(r => { if(r.isConfirmed) nextStep(2); });
}

function nextStep(step) {
    // Show loading overlay
    $('#tabLoadingOverlay').show();
    $('.tab-loading-text').text('Loading Step ' + (step + 1));
    $('.tab-loading-subtext').text('Please wait while we prepare the next step...');
    
    if (step === 1) {
        if (!selectedPatient) { 
            $('#tabLoadingOverlay').hide();
            toastr.error('Please select a patient from the list'); 
            return; 
        }
        
        setTimeout(function() {
            updateStepIndicator(1);
            $('#step1-content').hide(); $('#step2-content').show();
            updateProgressBar(2);
            $('#tab2-indicator .step-number').removeClass('bg-secondary').addClass('bg-primary');
            $('#tabLoadingOverlay').hide();
        }, 800);
        
    } else if (step === 2) {
        let selectedMedicines = medicines.filter(m => m.selected);
        if (!selectedMedicines.length) { 
            $('#tabLoadingOverlay').hide();
            toastr.error('Please select at least one medicine'); 
            return; 
        }
        if (!$('#surgery-type').val()) { 
            $('#tabLoadingOverlay').hide();
            toastr.error('Please enter surgery type'); 
            return; 
        }
        if (!$('#surgery-date').val()) { 
            $('#tabLoadingOverlay').hide();
            toastr.error('Please select surgery date'); 
            return; 
        }
        
        setTimeout(function() {
            updateStepIndicator(2);
            $('#step2-content').hide(); $('#step3-content').show();
            updateProgressBar(3);
            displayReview();
            $('#tabLoadingOverlay').hide();
        }, 800);
    }
}

function prevStep(step) {
    // Show loading overlay
    $('#tabLoadingOverlay').show();
    $('.tab-loading-text').text('Loading Step ' + (step - 1));
    $('.tab-loading-subtext').text('Please wait while we prepare the previous step...');
    
    setTimeout(function() {
        if (step === 2) { 
            updateStepIndicator(1,'back'); 
            $('#step2-content').hide(); 
            $('#step1-content').show(); 
            updateProgressBar(1); 
        }
        else if (step === 3) { 
            updateStepIndicator(2,'back'); 
            $('#step3-content').hide(); 
            $('#step2-content').show(); 
            updateProgressBar(2); 
        }
        $('#tabLoadingOverlay').hide();
    }, 800);
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
    $('#progress-bar').css('width',w).text('Step '+step);
}

// ==================== REVIEW ====================
function displayReview() {
    let rawDoctor = $('#doctor-select').find('option:selected').text()||'';
    if (rawDoctor.trim().startsWith('--') || rawDoctor.trim()==='') rawDoctor='';
    let doctorLabel = rawDoctor ? (/^\s*dr\.?\s+/i.test(rawDoctor) ? rawDoctor : ' ' + rawDoctor) : '';
    let surgeryDate = $('#surgery-date').val()||new Date().toLocaleDateString('en-GB');
    let patientName = selectedPatient.patientname||selectedPatient.name||'';
    let patientAge = selectedPatient.age||selectedPatient.age_years||'';
    let patientGender = selectedPatient.gender||selectedPatient.sex||'';
    let patientCode = selectedPatient.patientcode||selectedPatient.patient_id||'';
    let pulse=$('#pulse').val()||'', bp=$('#bp').val()||'', temp=$('#temperature').val()||'', wt=$('#weight').val()||'';

    let html = `
    <div id="print-prescription" style="font-family:Arial,sans-serif;max-width:820px;margin:0 auto;color:#000;font-size:13px;">
        <table id="rx-header-table" style="width:100%;background:#ADD8E6;border-bottom:3px solid #005F02;padding-bottom:8px;margin-bottom:0;">
            <tr>
                <td style="width:60%;vertical-align:top;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="vertical-align:top;padding-right:10px;">
                                <div style="width:56px;height:56px;border-radius:50%;border:3px solid #e0001a;display:inline-flex;align-items:center;justify-content:center;background:#fff;font-size:20px;font-weight:900;">
                                    <span style="color:#005F02;">P</span>
                                </div>
                            </td>
                            <td style="vertical-align:top;">
                                <div style="color:#e0001a;font-size:11px;font-weight:600;">চেম্বার :</div>
                                <div style="color:#005F02;font-size:26px;font-weight:900;">প্রফেসর ক্লিনিক</div>
                                <div style="color:#444;font-size:11px;">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                                <div style="color:#444;font-size:11px;">মোবাঃ ০১৭২০-০৩৯০০৫, ০১৭২০-০৩৯০০৬, ০১৭২০-০৩৯০০৭, ০১৭২০-০৩৯০০৮</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:40%;vertical-align:top;text-align:right;padding-left:20px;padding-right:20px;">
                    <div style="color:#005F02;font-size:15px;font-weight:800;">${doctorLabel}</div>
                    <div style="color:#333;font-size:11px;">এম.বি.বি.এস; বি.সি.এস (স্বাস্থ্য)</div>
                    <div style="color:#333;font-size:11px;">ডিপ্লোমা ইন মেডিকেল আল্ট্রাসাউন্ড</div>
                    <div style="color:#e0001a;font-size:11px;font-weight:600;">শহীদ জিয়াউর রহমান মেডিকেল কলেজ, বগুড়া।</div>
                </td>
            </tr>
        </table>
        <table id="rx-name-row" style="width:100%;background:#fce4ec;border-bottom:1px solid #e0001a;border-top:1px solid #e0001a;margin:0;">
            <tr>
                <td style="padding:6px 12px;font-size:13px;width:55%;"><strong>Name :</strong> ${patientName}${patientCode?' <span style="color:#6c757d;font-size:11px;">['+patientCode+']</span>':''}</td>
                <td style="padding:6px 12px;font-size:13px;width:20%;"><strong>Age :</strong> ${patientAge}${patientGender?' / '+patientGender:''}</td>
                <td style="padding:6px 12px;font-size:13px;width:25%;text-align:right;"><strong>Date :</strong> ${surgeryDate}</td>
            </tr>
        </table>
        <table style="width:100%;border-collapse:collapse;min-height:560px;">
            <tr>
                <td id="rx-body-left" style="width:37%;vertical-align:top;border-right:2px solid #005F02;padding:10px 10px 10px 5px;font-size:12px;background:#ADD8E6;">
                    <div style="margin-bottom:18px;">
                        <div style="font-weight:700;text-decoration:underline;font-size:13px;margin-bottom:5px;">C/C</div>
                        ${diagnoses.length ? diagnoses.map(d=>`<div style="margin-left:6px;line-height:1.6;">• ${d.name}${d.note?' <span style="color:#555;font-size:11px;">- '+d.note+'</span>':''}</div>`).join('') : '<div style="margin-left:6px;color:#aaa;">—</div>'}
                    </div>
                    <div style="margin-bottom:18px;">
                        <div style="font-weight:700;text-decoration:underline;font-size:13px;margin-bottom:5px;">O/E</div>
                        <div style="margin-left:6px;line-height:1.8;">. Pulse &nbsp;&nbsp;&nbsp;&nbsp; ${pulse}</div>
                        <div style="margin-left:6px;line-height:1.8;">. BP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${bp}</div>
                        <div style="margin-left:6px;line-height:1.8;">. Tem &nbsp;&nbsp;&nbsp;&nbsp; ${temp}</div>
                        <div style="margin-left:6px;line-height:1.8;">. Weight &nbsp; ${wt}</div>
                    </div>
                    <div>
                        <div style="font-weight:700;text-decoration:underline;font-size:13px;margin-bottom:5px;">Inv</div>
                        ${investigations.length
                            ? investigations.map(i=>`<div style="margin-left:6px;line-height:1.8;">. ${i.name}${i.note?' <span style="color:#555;font-size:11px;">- '+i.note+'</span>':''}</div>`).join('')
                            : `<div style="margin-left:6px;line-height:1.75;">. CBC/Hb%</div><div style="margin-left:6px;line-height:1.75;">. Urine R/M/E</div><div style="margin-left:6px;line-height:1.75;">. RBS/FBS</div><div style="margin-left:6px;line-height:1.75;">. HBs Ag</div><div style="margin-left:6px;line-height:1.75;">. VDRL</div><div style="margin-left:6px;line-height:1.75;">. Blood grouping</div><div style="margin-left:6px;line-height:1.75;">. S. bilirubin</div><div style="margin-left:6px;line-height:1.75;">. ECG</div>`
                        }
                    </div>
                    ${$('#surgery-type').val()?`<div style="margin-top:14px;padding-top:8px;border-top:1px dashed #ccc;font-size:11px;color:#555;"><strong>Surgery:</strong> ${$('#surgery-type').val()}</div>`:''}
                </td>
                <td style="width:63%;vertical-align:top;padding:10px 10px 10px 16px;font-size:13px;">
                    <div style="color:#005F02;font-size:26px;font-style:italic;font-weight:900;margin-bottom:14px;font-family:Georgia,serif;">&#8478;</div>
                    <table style="width:100%;border-collapse:collapse;border:2px solid #005F02;margin-bottom:15px;font-size:12px;">
                        <thead>
                            <tr style="background:#ADD8E6;border-bottom:2px solid #005F02;">
                                <th style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:700;width:45%;">ঔষধের নাম</th>
                                <th colspan="3" style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:700;width:30%;">কখন খাবেন?</th>
                                <th colspan="2" style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:700;width:15%;">আহারের</th>
                                <th colspan="3" style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:700;width:10%;">কতদিন?</th>
                            </tr>
                            <tr style="background:#ADD8E6;border-bottom:2px solid #005F02;">
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;"></th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:10%;">সকাল</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:10%;">দুপুর</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:10%;">রাত</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:7.5%;">আগে</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:7.5%;">পরে</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:4%;">দিন</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:3%;">মাস</th>
                                <th style="border:1px solid #005F02;padding:4px 6px;text-align:center;width:3%;">চলবে</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(() => {
                                let sel = medicines.filter(m=>m.selected);
                                return sel.length > 0 ? sel.map(m => {
                                    let p = m.dosage?m.dosage.split('+'):['','',''];
                                    let mb='', ma='✓';
                                    if (m.instructions) { let inst=m.instructions.toLowerCase(); if(inst.includes('before')||inst.includes('ac')){mb='✓';ma='';} }
                                    let dd='',dm='',do2='';
                                    if(m.duration){let dur=m.duration.toLowerCase();if(dur.includes('month')||dur.includes('mo')){let x=dur.match(/(\d+)/);dm=x?x[1]:'';}else if(dur.includes('ongoing')||dur.includes('continue')){do2='✓';}else{let x=dur.match(/(\d+)/);dd=x?x[1]:'';}}
                                    return `<tr style="border-bottom:1px solid #ddd;background:#fff;">
                                        <td style="border:1px solid #005F02;padding:6px 8px;font-weight:600;color:#000;">${m.name}${m.strength?'<br><small>'+m.strength+'</small>':''}${m.medicine_type?'<br><small>('+m.medicine_type+')</small>':''}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;">${p[0]||''}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;">${p[1]||''}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;">${p[2]||''}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:600;">${mb}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:600;">${ma}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:600;">${dd}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:600;">${dm}</td>
                                        <td style="border:1px solid #005F02;padding:6px 8px;text-align:center;font-weight:600;">${do2}</td>
                                    </tr>${m.instructions?`<tr><td colspan="9" style="border:1px solid #005F02;padding:4px 8px;background:#fff;font-size:11px;font-style:italic;"><strong>Instructions:</strong> ${m.instructions}</td></tr>`:''}`;
                                }).join('') : `<tr><td colspan="9" style="border:1px solid #005F02;padding:20px 8px;text-align:center;color:#666;font-style:italic;">No medicines prescribed</td></tr>`;
                            })()}
                        </tbody>
                    </table>
                    ${freshPrescriptions.length?`<div style="margin-top:18px;"><div style="font-weight:700;text-decoration:underline;font-size:13px;margin-bottom:6px;">Other Instructions:</div>${freshPrescriptions.map(f=>`<div style="margin-left:8px;line-height:1.7;"><strong>${f.name}:</strong> ${f.details||''}</div>`).join('')}</div>`:''}
                    ${$('#instructions').val()?`<div style="margin-top:18px;"><div style="font-weight:700;text-decoration:underline;font-size:13px;margin-bottom:4px;">Instructions:</div><div style="margin-left:8px;">${$('#instructions').val()}</div></div>`:''}
                </td>
            </tr>
        </table>
    </div>`;
    $('#review-content').html(html);
}

// ==================== PRINT FUNCTIONS ====================
function printPrescription() {
    if (!selectedPatient) { toastr.error('Patient information is missing'); return; }
    let sel = medicines.filter(m => m.selected);
    if (!sel.length && !admissionMedicines.length) { toastr.error('No medicines selected for printing'); return; }
    saveToDatabase(false);
    displayReview();
    $('#step1-content,#step2-content').hide();
    $('#step3-content').show();
    let originalTitle = document.title;
    let patientName = (selectedPatient.patientname||selectedPatient.name||'Patient').toString();
    let patientId = (selectedPatient.patientcode||selectedPatient.patient_id||selectedPatient.id||'').toString();
    let surgeryDate = ($('#surgery-date').val()||'').toString();
    let safe = s => s.replace(/[\\/:*?"<>|]/g,'').replace(/\s+/g,' ').trim();
    document.title = safe(`Prescription_${patientName}${patientId?'_'+patientId:''}${surgeryDate?'_'+surgeryDate:''}`);
    window.onafterprint = function() { document.title = originalTitle; window.onafterprint = null; };
    setTimeout(() => window.print(), 500);
}

function saveToDatabase(showMessage=true) {
    let doctorName = $('#doctor-select').find('option:selected').text()||'';
    let formData = {
        patient_id: selectedPatient.id, doctor_name: doctorName,
        surgery_name: $('#surgery-type').val(), surgery_date: $('#surgery-date').val(),
        anesthesia_type: $('#anesthesia-type').val(), ward_bed: $('#ward-bed').val(),
        bp: $('#bp').val(), pulse: $('#pulse').val(), temperature: $('#temperature').val(), weight: $('#weight').val(),
        medicines, admission_medicines: admissionMedicines, post_op_medicines: postOpMedicines,
        pre_op_advices: preOpAdvices, diagnoses, investigations,
        fresh_prescriptions: freshPrescriptions, discharge, instructions: $('#instructions').val(),
        baby_sex: selectedBabySex, baby_weight: $('#baby-weight').val(),
        baby_birth_time: $('#baby-birth-time').val(), baby_birth_date: $('#baby-birth-date').val(),
        baby_apgar: $('#baby-apgar').val(), baby_note: $('#baby-additional-note').val(),
        template_id: selectedTemplate?.id||null, _token: '{{ csrf_token() }}'
    };
    $.ajax({
        url: '{{ route("surgery-prescriptions.store") }}', type: 'POST', data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(r) { if (r.success && showMessage) toastr.success('Prescription saved successfully'); },
        error: function(xhr) { toastr.error('Error: ' + (xhr.responseJSON?.message||'Unknown error')); }
    });
}

// ==================== ADMISSION RX PRINT ====================
function printAdmissionRxPrescription() {
    if (!selectedPatient) { toastr.error('Patient information is missing'); return; }
    let selectedAdmissionMeds = admissionMedicines.filter(m => m.selected);
    if (!selectedAdmissionMeds.length) { toastr.error('No admission medicines selected for printing'); return; }
    saveToDatabase(false);

    let patientName = selectedPatient.patientname||selectedPatient.name||'Patient';
    let patientAge = selectedPatient.age||selectedPatient.age_years||'';
    let patientGender = selectedPatient.gender||selectedPatient.sex||'';
    let patientCode = selectedPatient.patientcode||selectedPatient.patient_id||'';
    let surgeryDate = $('#surgery-date').val()||new Date().toLocaleDateString('en-GB');
    let pulse=$('#pulse').val()||'', bp=$('#bp').val()||'', temp=$('#temperature').val()||'', wt=$('#weight').val()||'';

    let html = buildClinicHeader(patientName, patientCode, surgeryDate, patientAge, patientGender) + `
        <div style="margin-bottom:15px;">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="width:50%;padding:5px;vertical-align:top;">
                        <strong>O/E:</strong><br>
                        <div style="margin-left:10px;margin-top:5px;">
                            . Pulse &nbsp;&nbsp; ${pulse}<br>. BP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${bp}<br>. Tem &nbsp;&nbsp;&nbsp; ${temp}<br>. Weight ${wt}
                        </div>
                    </td>
                    <td style="width:50%;padding:5px;vertical-align:top;">
                        <strong>Rx On admission</strong><br>
                        ${selectedAdmissionMeds.map(med=>`
                            <div style="margin-bottom:8px;padding:5px;border-left:3px solid #004085;background:#f9f9f9;">
                                <strong>${med.name}${med.strength?' '+med.strength:''}</strong> <span style="float:right;">${med.dosage||''}</span><br>
                                ${med.route?'<small>Route: '+med.route+'</small><br>':''}
                                ${med.frequency?'<small>'+med.frequency+'</small><br>':''}
                                ${med.instructions?'<small style="font-style:italic;">'+med.instructions+'</small>':''}
                            </div>`).join('')}
                    </td>
                </tr>
            </table>
        </div>
        <table style="width:100%;border-top:2px solid #004085;margin-top:8px;">
            <tr><td style="padding:6px 8px;font-size:12px;color:#333;text-align:center;">বিঃ দ্রঃ ......................................&nbsp;&nbsp;&nbsp;&nbsp;দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।</td></tr>
        </table>
    </div>`;

    openPrintWindow(html, `AdmissionRx_${patientName}_${surgeryDate}`);
    toastr.success('Admission Rx printed');
}

// ==================== PRE-OP PRINT ====================
function printPreOperativeOrderPrescription() {
    if (!selectedPatient) { toastr.error('Patient information is missing'); return; }
    saveToDatabase(false);

    let patientName = selectedPatient.patientname||selectedPatient.name||'Patient';
    let patientAge = selectedPatient.age||selectedPatient.age_years||'';
    let patientGender = selectedPatient.gender||selectedPatient.sex||'';
    let patientCode = selectedPatient.patientcode||selectedPatient.patient_id||'';
    let surgeryDate = $('#surgery-date').val()||new Date().toLocaleDateString('en-GB');
    let pulse=$('#pulse').val()||'', bp=$('#bp').val()||'', temp=$('#temperature').val()||'', wt=$('#weight').val()||'';

    let babySex = selectedBabySex || '';
    let babyWeight = $('#baby-weight').val()||'';
    let babyTime = $('#baby-birth-time').val()||'';
    let babyDate = $('#baby-birth-date').val()||'';
    let babyApgar = $('#baby-apgar').val()||'';
    let babyNote = $('#baby-additional-note').val()||'';

    // ===== বাম দিক: O/E (Vitals) =====
    let leftHtml = `
        <div style="margin-bottom:14px;">
            <div style="font-weight:700;font-size:13px;text-decoration:underline;margin-bottom:6px;">O/E</div>
            <div style="line-height:2;">
                <div>. Pulse &nbsp;&nbsp;&nbsp;&nbsp; ${pulse}</div>
                <div>. BP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${bp}</div>
                <div>. Anaemia</div>
                <div>. Jaundice</div>
                <div>. Tem &nbsp;&nbsp;&nbsp;&nbsp; ${temp}</div>
                <div>. Oedema</div>
                <div>. Weight &nbsp; ${wt}</div>
                <div>. Heart</div>
                <div>. Lungs</div>
                <div>. FM</div>
            </div>
        </div>`;

    // ===== ডান দিক: Advice =====
    let advItems = preOpAdvices.length
        ? preOpAdvices.map(a => `<div style="margin-bottom:6px;line-height:1.8;">• ${a.advice}</div>`).join('')
        : `<div style="color:#aaa;font-style:italic;">No advice added</div>`;

    let rightHtml = `
        <div style="margin-bottom:14px;">
            <div style="font-weight:700;font-size:13px;text-decoration:underline;margin-bottom:6px;">Adv:</div>
            <div style="line-height:1.9;">${advItems}</div>
        </div>`;

    // ===== Baby Note =====
    let babyNoteHtml = '';
    if (babySex || babyWeight || babyTime || babyDate || babyApgar || babyNote) {
        babyNoteHtml = `
        <tr>
            <td colspan="2" style="padding:0;padding-top:14px;">
                <div style="border:2px solid #e91e63;border-radius:8px;padding:14px;background:#fff0f5;">
                    <div style="color:#c2185b;font-size:14px;font-weight:700;margin-bottom:10px;">👶 Baby Note</div>
                    <table style="width:100%;font-size:12px;border-collapse:collapse;">
                        <tr>
                            ${babySex?`<td style="padding:4px 8px;"><strong>Sex:</strong>
                                <span style="background:${babySex==='Male'?'#e3f2fd':'#fce4ec'};color:${babySex==='Male'?'#1565c0':'#c2185b'};padding:2px 10px;border-radius:12px;font-weight:700;">
                                    ${babySex==='Male'?'♂ Male':'♀ Female'}
                                </span></td>`:''}
                            ${babyWeight?`<td style="padding:4px 8px;"><strong>Weight:</strong> ${babyWeight} kg</td>`:''}
                            ${babyTime?`<td style="padding:4px 8px;"><strong>Time:</strong> ${babyTime}</td>`:''}
                            ${babyDate?`<td style="padding:4px 8px;"><strong>DOB:</strong> ${babyDate}</td>`:''}
                        </tr>
                        ${babyApgar||babyNote?`<tr>
                            ${babyApgar?`<td style="padding:4px 8px;" colspan="2"><strong>APGAR:</strong> ${babyApgar}</td>`:''}
                            ${babyNote?`<td style="padding:4px 8px;" colspan="2"><strong>Note:</strong> ${babyNote}</td>`:''}
                        </tr>`:''}
                    </table>
                </div>
            </td>
        </tr>`;
    }

    let html = buildClinicHeader(patientName, patientCode, surgeryDate, patientAge, patientGender) + `
        <div style="font-size:16px;font-weight:700;color:#004085;margin-bottom:12px;
                    border-bottom:2px solid #004085;padding-bottom:6px;letter-spacing:0.5px;">
            Pre-operative Order
        </div>
        <table style="width:100%;border-collapse:collapse;min-height:400px;">
            <tr>
                <td style="width:40%;vertical-align:top;padding:0 14px 10px 0;
                           border-right:2px solid #004085;font-size:12px;">
                    ${leftHtml}
                </td>
                <td style="width:60%;vertical-align:top;padding:0 0 10px 16px;font-size:12px;">
                    ${rightHtml}
                </td>
            </tr>
            ${babyNoteHtml}
        </table>
        <table style="width:100%;border-top:2px solid #004085;margin-top:16px;">
            <tr>
                <td style="padding:6px 8px;font-size:12px;color:#333;text-align:center;">
                    বিঃ দ্রঃ ......................................&nbsp;&nbsp;&nbsp;&nbsp;দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।
                </td>
            </tr>
        </table>
    </div>`;

    openPrintWindow(html, `PreOpOrder_${patientName}_${surgeryDate}`);
    toastr.success('Pre-operative order printed');
}

// ==================== POST-OP PRINT ====================
function printPostOperativeOrderPrescription() {
    if (!selectedPatient) { toastr.error('Patient information is missing'); return; }
    let selectedMeds = postOpMedicines.filter(m => m.selected);
    if (!selectedMeds.length) { toastr.error('No post-op medicines selected for printing'); return; }
    saveToDatabase(false);

    let patientName = selectedPatient.patientname||selectedPatient.name||'Patient';
    let patientAge = selectedPatient.age||selectedPatient.age_years||'';
    let patientGender = selectedPatient.gender||selectedPatient.sex||'';
    let patientCode = selectedPatient.patientcode||selectedPatient.patient_id||'';
    let surgeryDate = $('#surgery-date').val()||new Date().toLocaleDateString('en-GB');

    let html = buildClinicHeader(patientName, patientCode, surgeryDate, patientAge, patientGender) + `
        <div style="margin-bottom:15px;">
            <div style="font-size:15px;font-weight:700;color:#004085;margin-bottom:12px;border-bottom:2px solid #004085;padding-bottom:6px;">
                Post-operative Order
            </div>
            <table style="width:100%;border-collapse:collapse;border:2px solid #004085;font-size:12px;">
                <thead>
                    <tr style="background:#e3f2fd;border-bottom:2px solid #004085;">
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:left;width:30%;">Medicine</th>
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:center;width:10%;">Type</th>
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:center;width:15%;">Dosage</th>
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:center;width:10%;">Route</th>
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:center;width:15%;">Duration</th>
                        <th style="border:1px solid #004085;padding:8px 10px;text-align:left;width:20%;">Frequency / Note</th>
                    </tr>
                </thead>
                <tbody>
                    ${selectedMeds.map((m,i)=>`
                    <tr style="border-bottom:1px solid #c5e1f5;background:${i%2===0?'#fff':'#f8fbff'};">
                        <td style="border:1px solid #004085;padding:8px 10px;font-weight:700;color:#1a237e;">${m.name}${m.strength?'<br><small style="font-weight:400;color:#0277bd;">'+m.strength+'</small>':''}</td>
                        <td style="border:1px solid #004085;padding:8px 10px;text-align:center;"><span style="background:#e3f2fd;color:#1565c0;padding:2px 8px;border-radius:10px;font-size:10px;">${m.type||''}</span></td>
                        <td style="border:1px solid #004085;padding:8px 10px;text-align:center;font-weight:700;color:#2e7d32;">${m.dosage||''}</td>
                        <td style="border:1px solid #004085;padding:8px 10px;text-align:center;">${m.route||''}</td>
                        <td style="border:1px solid #004085;padding:8px 10px;text-align:center;">${m.duration||''}</td>
                        <td style="border:1px solid #004085;padding:8px 10px;font-size:11px;">${m.frequency||''}</td>
                    </tr>`).join('')}
                </tbody>
            </table>
        </div>
        <table style="width:100%;border-top:2px solid #004085;margin-top:8px;">
            <tr><td style="padding:6px 8px;font-size:12px;color:#333;text-align:center;">বিঃ দ্রঃ ......................................&nbsp;&nbsp;&nbsp;&nbsp;দিন/মাস পর ব্যবস্থাপত্র সহ সাক্ষাৎ করিবেন।</td></tr>
        </table>
    </div>`;

    openPrintWindow(html, `PostOpOrder_${patientName}_${surgeryDate}`);
    toastr.success('Post-operative order printed');
}

// ==================== SHARED HELPERS ====================
function buildClinicHeader(patientName, patientCode, surgeryDate, patientAge, patientGender) {
    return `<div style="font-family:Arial,sans-serif;width:210mm;margin:0 auto;color:#004085;font-size:13px;padding:20px;border:2px solid #004085;background:#fff;box-sizing:border-box;">
        <div style="text-align:center;margin-bottom:16px;border-bottom:2px solid #004085;padding-bottom:10px;">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="width:20%;text-align:left;padding:10px 0;">
                        <div style="font-size:24px;font-weight:bold;color:#004085;">P</div>
                        <div style="font-size:14px;font-weight:bold;color:#004085;">PROFESSOR</div>
                        <div style="font-size:14px;color:#004085;">CLINIC</div>
                    </td>
                    <td style="width:60%;text-align:center;">
                        <div style="font-size:32px;font-weight:bold;color:#004085;">প্রফেসর ক্লিনিক</div>
                        <div style="font-size:20px;margin-top:5px;">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                        <div style="font-size:13px;margin-top:6px;">01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
                    </td>
                    <td style="width:20%;text-align:right;padding:10px 0;">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="margin-bottom:12px;padding:6px 10px;background:#e8f4fd;border-radius:4px;font-size:13px;">
            <strong>Name:</strong> ${patientName}${patientCode?' ['+patientCode+']':''} &nbsp;|&nbsp;
            <strong>Date:</strong> ${surgeryDate} &nbsp;|&nbsp;
            <strong>Age/Gender:</strong> ${patientAge}${patientGender?' / '+patientGender:''}
        </div>`;
}

function openPrintWindow(html, title) {
    let printWindow = window.open('','_blank','width=850,height=700');
    printWindow.document.write(`<!DOCTYPE html><html><head><title>${title}</title>
        <style>
            body{margin:0;padding:10px;font-family:Arial,sans-serif;}
            @media print{body{margin:0;padding:0;} @page{size:A4;margin:8mm;}}
        </style></head><body>${html}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 600);
}

function populateAdmissionRxFromImage() {
    admissionMedicines = [
        { template_medicine_id:Date.now()+1, name:'NPO-TFO', strength:'', brand:'Special Instruction', dosage:'', duration:'', order_type:'admission', route:'', frequency:'', medicine_type:'Instruction', instructions:'Nothing by mouth', selected:true },
        { template_medicine_id:Date.now()+2, name:'Inf. Hartman', strength:'', brand:'IV Fluid', dosage:'1000 cc', duration:'', order_type:'admission', route:'IV', frequency:'Stat @ 30 d/m', medicine_type:'IV Fluid', instructions:'', selected:true },
        { template_medicine_id:Date.now()+3, name:'Inj. Prizon', strength:'1gm', brand:'Antibiotic', dosage:'1gm', duration:'', order_type:'admission', route:'IV', frequency:'1 vial IV stat & hourly', medicine_type:'Injection', instructions:'', selected:true }
    ];
    updateAdmissionMedicineList();
}
</script>
@stop