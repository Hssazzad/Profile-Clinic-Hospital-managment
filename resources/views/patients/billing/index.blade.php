@extends('adminlte::page')
@section('title', 'Billing Overview')

@section('css')
<style>
    .billing-table thead th {
        background-color: #1a3a5c;
        color: #fff;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 12px;
        border-color: #1a3a5c;
    }
    .billing-table tbody td {
        font-size: 13px;
        padding: 8px 12px;
        vertical-align: middle;
        color: #333;
    }
    .billing-table tbody tr:hover {
        background-color: #f0f4f8;
    }
    .invoice-no {
        font-family: monospace;
        font-weight: 600;
        color: #1a3a5c;
        font-size: 12px;
    }
    .patient-name {
        font-weight: 600;
        color: #222;
    }
    .amount-cell {
        font-family: monospace;
        font-size: 13px;
    }
    .due-amount {
        color: #c0392b;
        font-weight: 700;
    }
    .paid-amount {
        color: #27ae60;
        font-weight: 600;
    }
    .status-badge {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 3px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .card-header-custom {
        background-color: #1a3a5c;
        color: #fff;
        padding: 10px 16px;
        border-bottom: 3px solid #c8a214;
    }
    .card-header-custom h3 {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin: 0;
    }
    .btn-action {
        border: none;
        background: transparent;
        padding: 3px 7px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
    }
    .btn-action:hover {
        background-color: #e9ecef;
    }
    .dropdown-menu {
        border-radius: 4px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 140px;
        padding: 4px 0;
    }
    .dropdown-item {
        font-size: 13px;
        padding: 7px 16px;
    }
    .dropdown-item:hover {
        background-color: #f0f4f8;
    }
    .page-header-line {
        border-left: 4px solid #c8a214;
        padding-left: 10px;
    }
    .summary-bar {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-left: 4px solid #1a3a5c;
        padding: 10px 16px;
        margin-bottom: 16px;
        border-radius: 0 4px 4px 0;
    }
    .summary-bar .summary-item {
        display: inline-block;
        margin-right: 24px;
        font-size: 13px;
        color: #555;
    }
    .summary-bar .summary-item strong {
        color: #1a3a5c;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 page-header-line" style="font-size:20px; color:#1a3a5c; font-weight:700;">
                    Billing Overview
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Billing Overview</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:3px; font-size:13px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Summary Bar --}}
        <div class="summary-bar">
            <span class="summary-item">
                Total Records: <strong>{{ $payments->count() }}</strong>
            </span>
            <span class="summary-item">
                Total Paid: <strong class="text-success">৳ {{ number_format($payments->sum('paid_amount'), 2) }}</strong>
            </span>
            <span class="summary-item">
                Total Due: <strong class="text-danger">৳ {{ number_format($payments->sum('due_amount'), 2) }}</strong>
            </span>
        </div>

        <div class="card" style="border:1px solid #dee2e6; border-top: 3px solid #1a3a5c; border-radius:4px;">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-file-invoice-dollar mr-2" style="color:#c8a214;"></i> All Payment Records</h3>
                <a href="{{ route('patients.billing.create') }}" class="btn btn-sm"
                    style="background:#c8a214; color:#fff; font-size:12px; font-weight:600; border-radius:3px;">
                    <i class="fas fa-plus mr-1"></i> Add Payment
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover billing-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Invoice No</th>
                            <th>Patient Name</th>
                            <th class="text-right">Total (৳)</th>
                            <th class="text-right">Discount (৳)</th>
                            <th class="text-right">Payable (৳)</th>
                            <th class="text-right">Paid (৳)</th>
                            <th class="text-right">Due (৳)</th>
                            <th class="text-center">Status</th>
                            <th>Date</th>
                            <th class="text-center" style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $key => $pay)
                        <tr>
                            <td class="text-muted" style="font-size:12px;">{{ $key + 1 }}</td>
                            <td><span class="invoice-no">{{ $pay->invoice_no }}</span></td>
                            <td><span class="patient-name">{{ $pay->patient->patientname ?? 'N/A' }}</span></td>
                            <td class="text-right amount-cell">{{ number_format($pay->total_amount, 2) }}</td>
                            <td class="text-right amount-cell text-muted">{{ number_format($pay->discount, 2) }}</td>
                            <td class="text-right amount-cell">{{ number_format($pay->payable_amount, 2) }}</td>
                            <td class="text-right amount-cell paid-amount">{{ number_format($pay->paid_amount, 2) }}</td>
                            <td class="text-right amount-cell">
                                @if($pay->due_amount > 0)
                                    <span class="due-amount">{{ number_format($pay->due_amount, 2) }}</span>
                                @else
                                    <span class="paid-amount">0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pay->payment_status == 'Paid')
                                    <span class="status-badge" style="background:#d4edda; color:#155724;">Paid</span>
                                @elseif($pay->payment_status == 'Partial')
                                    <span class="status-badge" style="background:#fff3cd; color:#856404;">Partial</span>
                                @elseif($pay->payment_status == 'Refunded')
                                    <span class="status-badge" style="background:#d1ecf1; color:#0c5460;">Refunded</span>
                                @else
                                    <span class="status-badge" style="background:#f8d7da; color:#721c24;">Due</span>
                                @endif
                            </td>
                            <td style="font-size:12px; color:#555;">{{ $pay->payment_date }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn-action dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        title="Actions">
                                        <i class="fas fa-ellipsis-v" style="color:#1a3a5c;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('patients.billing.edit', $pay->id) }}">
                                            <i class="fas fa-edit mr-2" style="color:#c8a214;"></i> Edit
                                        </a>
                                        <div class="dropdown-divider my-1"></div>
                                        <a class="dropdown-item text-danger delete-btn" href="#"
                                            data-id="{{ $pay->id }}">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </a>
                                    </div>
                                </div>
                                <form id="delete-form-{{ $pay->id }}"
                                    action="{{ route('patients.billing.destroy', $pay->id) }}"
                                    method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block" style="color:#ccc;"></i>
                                No payment records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius:4px; border-top: 3px solid #c0392b;">
            <div class="modal-header" style="background:#c0392b; padding:10px 16px;">
                <h5 class="modal-title text-white" style="font-size:14px; font-weight:700;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="font-size:16px;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-3">
                <i class="fas fa-trash-alt fa-2x text-danger mb-2 d-block"></i>
                <p class="mb-1" style="font-size:13px; font-weight:600;">এই payment record টি delete করতে চান?</p>
                <small class="text-muted">এই action টি undo করা যাবে না।</small>
            </div>
            <div class="modal-footer" style="padding:8px 16px;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function () {
    var deleteId = null;

    $(document).on('click', '.delete-btn', function (e) {
        e.preventDefault();
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function () {
        if (deleteId) {
            $('#delete-form-' + deleteId).submit();
        }
    });
});
</script>
@endsection