<?php
use App\Http\Controllers\ComplainController;

Route::prefix('settings')->group(function () {

    Route::get('complains', [ComplainController::class, 'index'])
        ->name('settings.complain.index');

    Route::get('AddComplain', [ComplainController::class, 'create'])
        ->name('settings.complain.create');

    Route::post('AddComplain', [ComplainController::class, 'store'])
        ->name('settings.complain.store');

    Route::get('complains/{id}/edit', [ComplainController::class, 'edit'])
        ->name('settings.complain.edit');

    Route::put('complains/{id}', [ComplainController::class, 'update'])
        ->name('settings.complain.update');

    Route::delete('complains/{id}', [ComplainController::class, 'destroy'])
        ->name('settings.complain.destroy');
});
