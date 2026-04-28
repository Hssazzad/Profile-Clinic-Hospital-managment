<?php
use App\Http\Controllers\AdmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // Admission routes
    Route::get('admission/admitpatient', [AdmissionController::class, 'create'])
        ->name('admission.admitpatient');

    Route::post('admission/admitpatient', [AdmissionController::class, 'store'])
        ->name('admission.admitpatient.store');

    Route::get('admission/print/{id}', [AdmissionController::class, 'print'])
        ->name('admission.print');
		
    Route::get('admission/list', [AdmissionController::class, 'list'])
        ->name('admission.list');
	
    Route::post('admission/discharge/{id}', [AdmissionController::class, 'discharge'])
        ->name('admission.discharge');
		
    // Prescription routes
    Route::get('admission/{id}/rx-admit', [AdmissionController::class, 'rxAdmit'])
        ->name('admission.rx.admit');

    Route::get('/admission/releasepatient', [AdmissionController::class, 'releasePatientForm'])
        ->name('admission.releasepatient.form');

    Route::post('/admission/releasepatient/save', [AdmissionController::class, 'storeReleasePatient'])
        ->name('admission.releasepatient.store');

    Route::post('/admission/releasepatient/meds', [AdmissionController::class, 'releasePatientMedsAjax'])
        ->name('admission.releasepatient.meds.ajax');


    // Round patient routes
    Route::get('admission/roundpatient', [AdmissionController::class, 'roundPatientList'])
        ->name('admission.round.index');

    Route::get('admission/roundpatient/{id}/create', [AdmissionController::class, 'roundPatientCreate'])
        ->name('admission.round.create');

    Route::post('admission/roundpatient/store', [AdmissionController::class, 'roundPatientStore'])
        ->name('admission.round.store');

    Route::get('admission/roundpatient/{id}/history', [AdmissionController::class, 'roundPatientHistory'])
        ->name('admission.round.history');

});
