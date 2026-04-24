<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\DischargeBillPaymentController;
use App\Http\Controllers\Billing\CreateInvoiceController;

/*
|--------------------------------------------------------------------------
| Billing Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('Billing')->name('billing.')->group(function () {

    /*
    |------------------------------------------------------------------
    | Create Invoice
    | IMPORTANT: AJAX routes MUST come before /{patientId} wildcard
    |------------------------------------------------------------------
    */
    Route::get ('CreateInvoice',                       [CreateInvoiceController::class, 'index']        )->name('invoice.index');
    Route::post('CreateInvoice/store',                 [CreateInvoiceController::class, 'store']        )->name('invoice.store');

    // AJAX endpoints — defined BEFORE /{patientId} to prevent route collision
    Route::get   ('CreateInvoice/ajax/list',           [CreateInvoiceController::class, 'list']        )->name('invoice.list');
    Route::get   ('CreateInvoice/ajax/bill-types',     [CreateInvoiceController::class, 'getBillTypes'] )->name('invoice.getBillTypes');
    Route::get   ('CreateInvoice/ajax/search-patient', [CreateInvoiceController::class, 'searchPatient'])->name('invoice.searchPatient');
    Route::get   ('CreateInvoice/ajax/main-categories',[CreateInvoiceController::class, 'getMain']      )->name('invoice.getMain');
    Route::get   ('CreateInvoice/ajax/sub-categories', [CreateInvoiceController::class, 'getSub']       )->name('invoice.getSub');
    Route::get   ('CreateInvoice/ajax/get-doctors',    [CreateInvoiceController::class, 'getDoctors']   )->name('invoice.getDoctors');
    Route::get   ('CreateInvoice/ajax/get-tmp',        [CreateInvoiceController::class, 'getTmp']       )->name('invoice.getTmp');
    Route::post  ('CreateInvoice/ajax/add-tmp',        [CreateInvoiceController::class, 'addTmp']       )->name('invoice.addTmp');
    Route::delete('CreateInvoice/ajax/remove-tmp',     [CreateInvoiceController::class, 'removeTmp']    )->name('invoice.removeTmp');
    Route::post  ('CreateInvoice/ajax/clear-tmp',      [CreateInvoiceController::class, 'clearTmp']     )->name('invoice.clearTmp');
    Route::get   ('CreateInvoice/ajax/get-collectors', [CreateInvoiceController::class, 'getCollectors'] )->name('invoice.getCollectors');

    // Wildcard routes — MUST come AFTER all static routes
    Route::get('CreateInvoice/{id}/print',             [CreateInvoiceController::class, 'printInvoice'] )->name('invoice.print');
    Route::get('CreateInvoice/{patientId}',            [CreateInvoiceController::class, 'show']         )->name('invoice.show');

    /*
    |------------------------------------------------------------------
    | Payment
    |------------------------------------------------------------------
    */
    Route::get ('payment',             [PaymentController::class, 'index']        )->name('payment.index');
    Route::post('payment/store',       [PaymentController::class, 'store']        )->name('payment.store');
    Route::get ('payment/{id}/print',  [PaymentController::class, 'printInvoice'] )->name('payment.print');
    Route::get ('payment/{patientId}', [PaymentController::class, 'show']         )->name('payment.show');

    /*
    |------------------------------------------------------------------
    | Discharge Bill Payment
    |------------------------------------------------------------------
    */
    Route::get   ('DischargeBillPayment',                              [DischargeBillPaymentController::class, 'index']                      )->name('discharge.index');
    Route::post  ('DischargeBillPayment/store',                        [DischargeBillPaymentController::class, 'store']                      )->name('discharge.store');
    Route::get   ('DischargeBillPayment/detail/{id}',                  [DischargeBillPaymentController::class, 'detail']                     )->name('discharge.detail');
    Route::get   ('DischargeBillPayment/patient-payments/{patientId}', [DischargeBillPaymentController::class, 'patientPayments']            )->name('discharge.patientPayments');
    Route::get   ('DischargeBillPayment/by-admission/{admissionId}',   [DischargeBillPaymentController::class, 'byAdmission']                )->name('discharge.byAdmission');
    Route::delete('DischargeBillPayment/delete/{id}',                  [DischargeBillPaymentController::class, 'destroy']                    )->name('discharge.destroy');
    Route::get   ('DischargeBillPayment/patient-data/{patientId}',     [DischargeBillPaymentController::class, 'getPatientData']             )->name('discharge.patientData');
    Route::get   ('DischargeBillPayment/investigations/{patientId}',   [DischargeBillPaymentController::class, 'getInvestigations']          )->name('discharge.investigations');
    Route::get   ('DischargeBillPayment/payment-history/{patientId}',  [DischargeBillPaymentController::class, 'getPaymentHistory']          )->name('discharge.paymentHistory');
    Route::post  ('DischargeBillPayment/record-payment',               [DischargeBillPaymentController::class, 'recordPayment']              )->name('discharge.recordPayment');
    Route::get   ('DischargeBillPayment/get-investigations',           [DischargeBillPaymentController::class, 'getInvestigationsFromDatabase'])->name('discharge.getInvestigations');

});