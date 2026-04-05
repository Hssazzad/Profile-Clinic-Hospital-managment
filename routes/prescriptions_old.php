<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrescriptionWizardController;


// Master (server-side tabs)
Route::get('/prescriptions/wizard', [PrescriptionWizardController::class, 'show'])->name('rx.wizard');

// Patients
Route::post('/prescriptions/patient/save', [PrescriptionWizardController::class, 'savePatient'])->name('rx.patient.save');

// Investigations (single-row add/update/delete)
Route::post('/prescriptions/investigations',                   [PrescriptionWizardController::class, 'storeInvestigation'])->name('rx.investigation.store');
Route::put('/prescriptions/investigations/{inv}',              [PrescriptionWizardController::class, 'updateInvestigation'])->name('rx.investigation.update');
Route::delete('/prescriptions/investigations/{inv}',           [PrescriptionWizardController::class, 'destroyInvestigation'])->name('rx.investigation.destroy');

// Diagnosis (single-row add/update/delete)
Route::post('/prescriptions/diagnoses',                        [PrescriptionWizardController::class, 'storeDiagnosis'])->name('rx.diagnosis.store');
Route::put('/prescriptions/diagnoses/{diag}',                  [PrescriptionWizardController::class, 'updateDiagnosis'])->name('rx.diagnosis.update');
Route::delete('/prescriptions/diagnoses/{diag}',               [PrescriptionWizardController::class, 'destroyDiagnosis'])->name('rx.diagnosis.destroy');

// Medicine (single-row add/update/delete)
Route::post('/prescriptions/medicines',                        [PrescriptionWizardController::class, 'storeMedicine'])->name('rx.medicine.store');
Route::put('/prescriptions/medicines/{med}',                   [PrescriptionWizardController::class, 'updateMedicine'])->name('rx.medicine.update');
Route::delete('/prescriptions/medicines/{med}',                [PrescriptionWizardController::class, 'destroyMedicine'])->name('rx.medicine.destroy');

Route::post('/rx/inv/list',   [PrescriptionWizardController::class, 'ajaxListInvestigations'])->name('rx.inv.list');
Route::post('/rx/inv/store',  [PrescriptionWizardController::class, 'ajaxStoreInvestigation'])->name('rx.inv.store');

// New for inline edit & delete
Route::post('/rx/inv/update/{id}', [PrescriptionWizardController::class, 'ajaxUpdateInvestigation'])->name('rx.inv.update');
Route::post('/rx/inv/delete/{id}', [PrescriptionWizardController::class, 'ajaxDeleteInvestigation'])->name('rx.inv.delete');

Route::post('/rx/inv/ajax', [PrescriptionWizardController::class, 'ajaxInvestigation'])
    ->name('rx.inv.ajax');
Route::post('/rx/diag/ajax', [PrescriptionWizardController::class, 'ajaxDiagnosis'])
    ->name('rx.diag.ajax');
    
Route::post('/rx/med/ajax', [PrescriptionWizardController::class, 'ajaxMedicine'])
    ->name('rx.med.ajax');
Route::post('/rx/finish/new', [PrescriptionWizardController::class, 'finishNew'])
    ->name('rx.finish.new');
 Route::post('/rx/complain/save', [PrescriptionWizardController::class, 'saveComplain'])
    ->name('rx.complain.save');   
Route::post('/rx/complain/ajax', [PrescriptionWizardController::class, 'ajaxComplain'])
    ->name('rx.complain.ajax');
    Route::get('/patients/search-ajax', [PrescriptionWizardController::class, 'patientsSearchAjax'])
    ->name('patients.search.ajax');
	
	// নতুন রাউট যা সরাসরি Caesarean ইউআরএল হ্যান্ডেল করবে
Route::get('/prescriptions/Caesarean', [PrescriptionWizardController::class, 'showCaesarean'])->name('rx.caesarean');
	
Route::get('/ajax/load-medicines', function () {
    return DB::table('common_medicine')
        ->orderBy('name')
        ->get(['id','name','strength','GroupName']);
})->name('ajax.load.medicines');
