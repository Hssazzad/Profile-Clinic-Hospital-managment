<?php
use App\Http\Controllers\InvestigationController;

Route::prefix('settings')->group(function () {

    Route::get('investigations', [InvestigationController::class, 'index'])
        ->name('settings.investigation.index');

    Route::get('AddInvestigation', [InvestigationController::class, 'create'])
        ->name('settings.investigation.create');

    Route::post('investigations/add', [InvestigationController::class, 'store'])
        ->name('settings.investigation.store');

    Route::get('investigations/{id}/edit', [InvestigationController::class, 'edit'])
        ->name('settings.investigation.edit');

    Route::put('investigations/{id}', [InvestigationController::class, 'update'])
        ->name('settings.investigation.update');

    Route::delete('investigations/{id}', [InvestigationController::class, 'destroy'])
        ->name('settings.investigation.destroy');
});
