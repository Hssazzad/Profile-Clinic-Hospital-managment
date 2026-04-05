<?php
use App\Http\Controllers\AdmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // --- Admission Routes ---
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
		
    // --- Prescription Routes ---
    Route::get('admission/{id}/rx-admit', [AdmissionController::class, 'rxAdmit'])
        ->name('admission.rx.admit');

    Route::get('/admission/releasepatient', [AdmissionController::class, 'releasePatientForm'])
        ->name('admission.releasepatient.form');

    Route::post('/admission/releasepatient/save', [AdmissionController::class, 'storeReleasePatient'])
        ->name('admission.releasepatient.store');

    Route::post('/admission/releasepatient/meds', [AdmissionController::class, 'releasePatientMedsAjax'])
        ->name('admission.releasepatient.meds.ajax');


    /* -------------------------------------------------------------------------- */
    /* 🏥 ROUND PATIENT ROUTES (ইউআরএল ফিক্স করা হয়েছে)                             */
    /* -------------------------------------------------------------------------- */
    
    // ১. ভর্তিকৃত রোগীদের তালিকা (রাউন্ডের জন্য)
    // URL: https://profclinic.erpbd.org/admission/roundpatient
    Route::get('admission/roundpatient', [AdmissionController::class, 'roundPatientList'])
        ->name('admission.round.index');

    // ২. রাউন্ড নোট এন্ট্রি ফর্ম
    Route::get('admission/roundpatient/{id}/create', [AdmissionController::class, 'roundPatientCreate'])
        ->name('admission.round.create');

    // ৩. রাউন্ড নোট সেভ
    Route::post('admission/roundpatient/store', [AdmissionController::class, 'roundPatientStore'])
        ->name('admission.round.store');

    // ৪. রাউন্ড হিস্ট্রি
    Route::get('admission/roundpatient/{id}/history', [AdmissionController::class, 'roundPatientHistory'])
        ->name('admission.round.history');

});