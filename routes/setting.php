<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\InvestigationManageController;
use App\Http\Controllers\ReferencePersonController;

/*
|--------------------------------------------------------------------------
| Settings / Master Data Routes
|--------------------------------------------------------------------------
*/
Route::prefix('settings')->middleware('auth')->group(function () {

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
    Route::get('/AddWardBed', [MasterDataController::class, 'wardBedIndex'])
        ->name('settings.wardbed.index');
    Route::post('/wardbed/store', [MasterDataController::class, 'wardBedStore'])
        ->name('settings.wardbed.store');
    Route::put('/wardbed/update/{id}', [MasterDataController::class, 'wardBedUpdate'])
        ->name('settings.wardbed.update');
    Route::delete('/wardbed/delete/{id}', [MasterDataController::class, 'wardBedDestroy'])
        ->name('settings.wardbed.destroy');

    // --- 3. Investigation Management ---
    Route::prefix('InvestigationPayment')->name('investigations.')->group(function () {
        // GET routes
        Route::get('/', [InvestigationManageController::class, 'index'])
            ->name('index');
        Route::get('/categories', [InvestigationManageController::class, 'getCategories'])
            ->name('categories');
        
        // POST routes (Tests)
        Route::post('/store', [InvestigationManageController::class, 'store'])
            ->name('store');
        Route::post('/update', [InvestigationManageController::class, 'update'])
            ->name('update');
            
        // POST/DELETE routes (Categories)
        Route::post('/categories/store', [InvestigationManageController::class, 'storeCategory'])
            ->name('categories.store');
        Route::post('/categories/update', [InvestigationManageController::class, 'updateCategory'])
            ->name('categories.update');
        Route::delete('/categories/{id}', [InvestigationManageController::class, 'destroyCategory'])
            ->name('categories.destroy');
        
        // DELETE route (Tests) - Must be at the bottom to avoid overriding categories/{id}
        Route::delete('/{id}', [InvestigationManageController::class, 'destroy'])
            ->name('destroy');
    });

    // --- 4. Reference Person Management ---
    Route::get('/ReferencePerson', [ReferencePersonController::class, 'index'])
        ->name('settings.referenceperson.index');
    Route::post('/referenceperson/store', [ReferencePersonController::class, 'store'])
        ->name('settings.referenceperson.store');
    Route::put('/referenceperson/update/{id}', [ReferencePersonController::class, 'update'])
        ->name('settings.referenceperson.update');
    Route::delete('/referenceperson/delete/{id}', [ReferencePersonController::class, 'destroy'])
        ->name('settings.referenceperson.destroy');

});