<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterDataController;

/*
|--------------------------------------------------------------------------
| Settings / Master Data Routes
|--------------------------------------------------------------------------
*/

Route::prefix('settings')->group(function () {

    // --- 1. Medicine Management ---
    Route::get('/AddMedicine', [MasterDataController::class, 'medicinesIndex'])
        ->name('settings.medicines.index');

    Route::post('/medicines/store', [MasterDataController::class, 'medicinesStore'])
        ->name('settings.medicines.store');

    Route::put('/medicines/update/{medicine}', [MasterDataController::class, 'medicinesUpdate'])
        ->name('settings.medicines.update');

    Route::delete('/medicines/delete/{medicine}', [MasterDataController::class, 'medicinesDestroy'])
        ->name('settings.medicines.destroy');


    // --- 2. Ward & Bed Management ---
    // এই রাউটটি আপনার 404 Not Found এরর সমাধান করবে
    Route::get('/AddWardBed', [MasterDataController::class, 'wardBedIndex'])
        ->name('settings.wardbed.index');

    Route::post('/wardbed/store', [MasterDataController::class, 'wardBedStore'])
        ->name('settings.wardbed.store');

    // Update রাউট (যদি ওয়ার্ড বা বেড এডিট করার প্রয়োজন হয়)
    Route::put('/wardbed/update/{id}', [MasterDataController::class, 'wardBedUpdate'])
        ->name('settings.wardbed.update');

    Route::delete('/wardbed/delete/{id}', [MasterDataController::class, 'wardBedDestroy'])
        ->name('settings.wardbed.destroy');

});