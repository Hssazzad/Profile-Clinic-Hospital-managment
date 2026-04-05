<?php
use App\Http\Controllers\DiagnosisController;

Route::prefix('settings')->group(function () {

    Route::get('diagnosis', [DiagnosisController::class, 'index'])
        ->name('settings.diagnosis.index');

    Route::get('AddDiagonosis', [DiagnosisController::class, 'create'])
        ->name('settings.diagnosis.create');

    Route::post('AddDiagonosis', [DiagnosisController::class, 'store'])
        ->name('settings.diagnosis.store');

    Route::get('diagnosis/{id}/edit', [DiagnosisController::class, 'edit'])
        ->name('settings.diagnosis.edit');

    Route::put('diagnosis/{id}', [DiagnosisController::class, 'update'])
        ->name('settings.diagnosis.update');

    Route::delete('diagnosis/{id}', [DiagnosisController::class, 'destroy'])
        ->name('settings.diagnosis.destroy');
});
