@extends('adminlte::page')

@section('title', 'Patient Payment')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="m-0 text-dark">
            <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Patient Payment
        </h1>
        <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('billing.payment.index') }}">Billing</a></li>
            <li class="breadcrumb-item active">Payment</li>
        </ol>
    </div>
@stop

@section('content')

{{-- -- Success / Error Alerts -------------------------------- --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif

@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first('error') }}
    </div>
@endif

<div class="row">

    {{-- ------------------------------------------------------
         LEFT — Patient Info Card
    ------------------------------------------------------- --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-1"></i> Patient Information
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="text-muted pl-3" style="width:45%">Patient Code</th>
                        <td><strong>{{ $patient->patientcode }}</strong></td>
                    </tr>
                    <tr class="bg-light">
                        <th class="text-muted pl-3">Full Name</th>
                        <td><strong>{{ $patient->patientname }}</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted pl-3">Age / Gender</th>
                        <td>{{ $patient->age }} / {{ $patient->gender }}</td>
                    </tr>
                    <tr class="bg-light">
                        <th class="text-muted pl-3">Mobile</th>
                        <td>{{ $patient->mobile_no }}</td>
                    </tr>
                    @if($admission)
                    <tr>
                        <th class="text-muted pl-3">Admission Date</th>
                        <td>{{ $admission->admission_date }}</td>
                    </tr>
                    <tr class="bg-light">
                        <th class="text-muted pl-3">Admission ID</th>
                        <td>#{{ $admission->id }}</td>
                    </tr>
                    @else
                    <tr>
                        <th class="text-muted pl-3">Admission</th>
                        <td><span class="badge badge-secondary">Not admitted</span></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Minimum payment info box --}}
        <div class="card bg-light border">
            <div class="card-body py-2 px-3">
                <small class="text-muted d-block mb-1">
                    <i class="fas fa-info-circle text-primary mr-1"></i>
                    Payment Policy
                </small>
                <p class="mb-0 small">
                    Minimum <strong class="text-danger">25%</strong> of total bill must be paid at the time of registration.
                </p>
                <div class="mt-2">
                    <span class="text-muted small">Minimum required: </span>
                    <strong class="text-danger" id="minRequiredDisplay">? 0.00</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ------------------------------------------------------
         RIGHT — Payment Form
    ------------------------------------------------------- --}}
    <div class="col-md-8">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave mr-1"></i> Payment Details
                </h3>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('billing.payment.store') }}" id="paymentForm" novalidate>
                    @csrf

                    {{-- Hidden patient fields — no PHP in id/name --}}
                    <input type="hidden" name="patient_id"    value="{{ $patient->id }}">
                    <input type="hidden" name="patient_name"  value="{{ $patient->patientname }}">
                    <input type="hidden" name="patient_code"  value="{{ $patient->patientcode }}">
                    <input type="hidden" name="patient_age"   value="{{ $patient->age }}">
                    <input type="hidden" name="mobile_no"     value="{{ $patient->mobile_no }}">
                    <input type="hidden" name="admission_id"  value="{{ $admission->id ?? '' }}">

                    {{-- Row 1 --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Total Bill Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">?</span>
                                    </div>
                                    <input type="number"
                                           name="total_bill"
                                           id="totalBill"
                                           class="form-control form-control-lg @error('total_bill') is-invalid @enderror"
                                           placeholder="0.00"
                                           value="{{ old('total_bill') }}"
                                           min="1"
                                           step="0.01"
                                           required>
                                </div>
                                @error('total_bill')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Discount (?)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">?</span>
                                    </div>
                                    <input type="number"
                                           name="discount"
                                           id="discountAmt"
                                           class="form-control"
                                           placeholder="0.00"
                                           value="{{ old('discount', 0) }}"
                                           min="0"
                                           step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Net payable display --}}
                    <div class="alert alert-info py-2 px-3 mb-3">
                        <div class="row">
                            <div class="col-4 text-center border-right">
                                <div class="text-muted small">Net Payable</div>
                                <div class="font-weight-bold h5 mb-0 text-primary" id="netPayableDisplay">? 0.00</div>
                            </div>
                            <div class="col-4 text-center border-right">
                                <div class="text-muted small">Min. Required (25%)</div>
                                <div class="font-weight-bold h5 mb-0 text-danger" id="minRequired25Display">? 0.00</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="text-muted small">Due After Payment</div>
                                <div class="font-weight-bold h5 mb-0 text-warning" id="dueDisplay">? 0.00</div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2 --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Paid Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white">?</span>
                                    </div>
                                    <input type="number"
                                           name="paid_amount"
                                           id="paidAmt"
                                           class="form-control form-control-lg @error('paid_amount') is-invalid @enderror"
                                           placeholder="0.00"
                                           value="{{ old('paid_amount') }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                </div>
                                @error('paid_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">Minimum 25% of net payable required</small>
                                {{-- JS validation message --}}
                                <div id="paidValidationMsg" class="text-danger small mt-1" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="payment_date"
                                       id="paymentDate"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', date('Y-m-d')) }}"
                                       required>
                                @error('payment_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Row 3 --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="paymentMethod" class="form-control" required>
                                    <option value="cash"           {{ old('payment_method') == 'cash'           ? 'selected' : '' }}>Cash</option>
                                    <option value="mobile_banking" {{ old('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking (bKash / Nagad)</option>
                                    <option value="card"           {{ old('payment_method') == 'card'           ? 'selected' : '' }}>Card</option>
                                    <option value="bank_transfer"  {{ old('payment_method') == 'bank_transfer'  ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cheque"         {{ old('payment_method') == 'cheque'         ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Transaction Ref <small class="text-muted">(optional)</small></label>
                                <input type="text"
                                       name="transaction_ref"
                                       id="transactionRef"
                                       class="form-control"
                                       placeholder="Ref / TrxID"
                                       value="{{ old('transaction_ref') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Row 4 --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Collected By</label>
                                <input type="text"
                                       name="collected_by"
                                       id="collectedBy"
                                       class="form-control"
                                       placeholder="Staff name"
                                       value="{{ old('collected_by') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes"
                                          id="paymentNotes"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Any remarks…">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="row mt-2">
                        <div class="col-12 text-right">
                            <a href="{{ route('billing.payment.index') }}" class="btn btn-default mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSubmitPayment">
                                <i class="fas fa-save mr-2"></i> Save & Print Invoice
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

@stop

@section('css')
<style>
    .form-control-lg { font-size: 1.1rem; }
    .card-outline.card-success > .card-header { border-top: 3px solid #28a745; }
    .card-outline.card-primary > .card-header { border-top: 3px solid #007bff; }
</style>
@stop

@section('js')
<script>
(function () {
    'use strict';

    // -- DOM refs ----------------------------------------------
    const totalBillEl  = document.getElementById('totalBill');
    const discountEl   = document.getElementById('discountAmt');
    const paidEl       = document.getElementById('paidAmt');
    const netDisplay   = document.getElementById('netPayableDisplay');
    const min25Display = document.getElementById('minRequired25Display');
    const minSideCard  = document.getElementById('minRequiredDisplay');
    const dueDisplay   = document.getElementById('dueDisplay');
    const validationMsg = document.getElementById('paidValidationMsg');
    const form         = document.getElementById('paymentForm');
    const submitBtn    = document.getElementById('btnSubmitPayment');

    // -- Recalculate on input ----------------------------------
    function recalc() {
        const total    = parseFloat(totalBillEl.value)  || 0;
        const discount = parseFloat(discountEl.value)   || 0;
        const paid     = parseFloat(paidEl.value)       || 0;

        const net      = Math.max(0, total - discount);
        const min25    = net * 0.25;
        const due      = Math.max(0, net - paid);

        netDisplay.textContent   = '? ' + net.toFixed(2);
        min25Display.textContent = '? ' + min25.toFixed(2);
        minSideCard.textContent  = '? ' + min25.toFixed(2);
        dueDisplay.textContent   = '? ' + due.toFixed(2);

        // Live validation hint
        if (paid > 0 && paid < min25) {
            validationMsg.textContent = 'Minimum 25% required: ? ' + min25.toFixed(2);
            validationMsg.style.display = 'block';
            paidEl.classList.add('is-invalid');
        } else {
            validationMsg.style.display = 'none';
            paidEl.classList.remove('is-invalid');
        }
    }

    totalBillEl.addEventListener('input', recalc);
    discountEl.addEventListener('input', recalc);
    paidEl.addEventListener('input', recalc);

    // -- Form submit — JS guard (server also validates) --------
    form.addEventListener('submit', function (e) {
        const total    = parseFloat(totalBillEl.value)  || 0;
        const discount = parseFloat(discountEl.value)   || 0;
        const paid     = parseFloat(paidEl.value)       || 0;
        const net      = Math.max(0, total - discount);
        const min25    = net * 0.25;

        if (total <= 0) {
            e.preventDefault();
            totalBillEl.focus();
            totalBillEl.classList.add('is-invalid');
            toastr.warning('Please enter the total bill amount.');
            return;
        }

        if (paid < min25) {
            e.preventDefault();
            paidEl.focus();
            validationMsg.textContent = 'Minimum 25% of ? ' + net.toFixed(2) + ' is required: ? ' + min25.toFixed(2);
            validationMsg.style.display = 'block';
            paidEl.classList.add('is-invalid');
            toastr.error('Paid amount must be at least 25% of net payable (? ' + min25.toFixed(2) + ').');
            return;
        }

        // All good — disable button to prevent double submit
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving…';
    });

    // Run once on load (for old() values)
    recalc();

})();
</script>
@stop