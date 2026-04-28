@extends('adminlte::page')

@section('title', 'Add Payment')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add Payment</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.billing.index') }}">Billing</a></li>
                    <li class="breadcrumb-item active">Add Payment</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Payment Form</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('patients.billing.store') }}" method="POST">
                    @csrf
                    <div class="row">

                        {{-- Patient AJAX Search --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control" required style="width:100%;">
                                    <option value="">-- Search by Name / Code / Mobile --</option>
                                </select>
                                @error('patient_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Payment Date --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Total Amount --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="total_amount" id="total_amount"
                                    class="form-control" placeholder="0.00"
                                    value="{{ old('total_amount', 0) }}" min="0" step="0.01" required>
                                @error('total_amount')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Discount --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Discount (৳)</label>
                                <input type="number" name="discount" id="discount"
                                    class="form-control" placeholder="0.00"
                                    value="{{ old('discount', 0) }}" min="0" step="0.01">
                            </div>
                        </div>

                        {{-- Payable Amount (readonly) --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Payable Amount (৳)</label>
                                <input type="number" id="payable_amount"
                                    class="form-control bg-light" placeholder="0.00" readonly>
                            </div>
                        </div>

                        {{-- Paid Amount --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paid Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="paid_amount" id="paid_amount"
                                    class="form-control" placeholder="0.00"
                                    value="{{ old('paid_amount', 0) }}" min="0" step="0.01" required>
                                @error('paid_amount')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Due Amount (readonly) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Due Amount (৳)</label>
                                <input type="number" id="due_display"
                                    class="form-control bg-light text-danger font-weight-bold"
                                    placeholder="0.00" readonly>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"
                                    placeholder="Optional notes...">{{ old('remarks') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Payment
                            </button>
                            <a href="{{ route('patients.billing.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    // AJAX Patient Search
    $('#patient_id').select2({
        placeholder: '-- Search by Name / Code / Mobile --',
        allowClear: true,
        minimumInputLength: 1,
        width: '100%',
        theme: 'bootstrap4',
        ajax: {
            url: '{{ route("patients.billing.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        language: {
            inputTooShort: function () {
                return 'নাম / কোড / মোবাইল লিখুন...';
            },
            searching: function () {
                return 'খোঁজা হচ্ছে...';
            },
            noResults: function () {
                return 'কোনো patient পাওয়া যায়নি';
            }
        }
    });

    // Calculate
    function calculate() {
        var total   = parseFloat($('#total_amount').val()) || 0;
        var disc    = parseFloat($('#discount').val()) || 0;
        var paid    = parseFloat($('#paid_amount').val()) || 0;
        var payable = total - disc;
        var due     = payable - paid;

        $('#payable_amount').val(payable.toFixed(2));
        $('#due_display').val(due > 0 ? due.toFixed(2) : '0.00');
    }

    $('#total_amount, #discount, #paid_amount').on('input', function () {
        calculate();
    });

    calculate();
});
</script>
@endsection
