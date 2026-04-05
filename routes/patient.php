<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/
Route::get('patients/newpatient', [PatientController::class, 'newpatient'])->name('patients.newpatient');
Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
Route::get('patients/searchpatient', [PatientController::class, 'searchpatient'])->name('patients.searchpatient');
Route::get('patients/editpatient', [PatientController::class, 'editpatient'])
    ->name('patients.editpatient');
	
/*
|--------------------------------------------------------------------------
| Appointment Routes (যেই এররগুলো আসছিল তা এখানে ফিক্স করা হয়েছে)
|--------------------------------------------------------------------------
*/
Route::prefix('appointments')->group(function () {
    // অ্যাপয়েন্টমেন্ট লিস্ট দেখার রাউট
    Route::get('/appointmentapprove', [AppointmentController::class, 'appointmentapprove'])
        ->name('appointments.approve');

    // অ্যাপয়েন্টমেন্ট স্ট্যাটাস আপডেট/অ্যাপ্রুভ করার রাউট (POST)
    Route::post('/appointmentapprove', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.updateStatus');

    // অ্যাপয়েন্টমেন্ট ক্রিয়েট করার পেজ এবং স্টোর রাউট
    Route::get('/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/store', [AppointmentController::class, 'store'])->name('appointments.store');

    // AJAX রাউটসমূহ
    Route::get('/next-serial', [AppointmentController::class, 'nextSerial'])->name('appointments.nextSerial');
    Route::get('/available-serials', [AppointmentController::class, 'availableSerials'])->name('appointments.availableSerials');
    Route::get('/check-patient-date', [AppointmentController::class, 'checkPatientDate'])->name('appointments.checkPatientDate');
});

/*
|--------------------------------------------------------------------------
| Location & API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Fetch Data
    Route::post('/fetch-district', [LocationController::class, 'fetchDistrict'])->name('api.fetch_district');
    Route::post('/fetch-upozila', [LocationController::class, 'fetch_upozila'])->name('api.fetch_upozila');
    Route::post('/fetch-union', [LocationController::class, 'fetch_union'])->name('api.fetch_union');
    Route::post('/fetch-village', [LocationController::class, 'fetch_village'])->name('api.fetch_village');

    // Store Data
    Route::post('/district/store', [LocationController::class, 'storeDistrict'])->name('api.district.store');
    Route::post('/upozila/store', [LocationController::class, 'storeUpozila'])->name('api.upozila.store');
    Route::post('/union/store', [LocationController::class, 'storeUnion'])->name('api.union.store');
    Route::post('/village/store', [LocationController::class, 'storeVillage'])->name('api.village.store');

    // Reference Person
    Route::post('/fetch-reference-person', [PatientController::class, 'fetch_reference_person'])->name('api.fetch_reference_person');
    Route::post('/reference-person/store', [PatientController::class, 'store_reference_person'])->name('api.reference_person.store');
});