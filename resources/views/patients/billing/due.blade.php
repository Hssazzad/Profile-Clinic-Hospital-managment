@extends('adminlte::page')
@section('title', 'Due Payments')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Due Payments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Due Payments</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-circle text-danger"></i> Patients with Due Amount
                </h3>
                <div class="card-tools">
                    <a href="{{ route('patients.billing.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Payment
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Patient Name</th>
                            <th>Total Amount</th>
                            <th>Discount</th>
                            <th>Payable</th>
                            <th>Paid</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $key => $pay)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $pay->invoice_no }}</td>
                            <td>{{ $pay->patient->patientname ?? 'N/A' }}</td>
                            <td>{{ number_format($pay->total_amount, 2) }}</td>
                            <td>{{ number_format($pay->discount, 2) }}</td>
                            <td>{{ number_format($pay->payable_amount, 2) }}</td>
                            <td>{{ number_format($pay->paid_amount, 2) }}</td>
                            <td class="text-danger font-weight-bold">
                                {{ number_format($pay->due_amount, 2) }} ৳
                            </td>
                            <td>
                                @if($pay->payment_status == 'Partial')
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Due</span>
                                @endif
                            </td>
                            <td>{{ $pay->payment_date }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-success">
                                <i class="fas fa-check-circle"></i> No due payments found!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection