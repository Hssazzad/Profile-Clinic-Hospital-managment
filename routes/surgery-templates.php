<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurgeryTemplateController;

// Surgery Template Management Routes
Route::middleware(['auth'])->group(function () {

    // Template CRUD routes
    Route::get('/surgery-templates', [SurgeryTemplateController::class, 'index'])
        ->name('surgery-templates.index');
    
    Route::get('/surgery-templates/create', [SurgeryTemplateController::class, 'create'])
        ->name('surgery-templates.create');
    
    Route::post('/surgery-templates', [SurgeryTemplateController::class, 'store'])
        ->name('surgery-templates.store');
    
    Route::get('/surgery-templates/{surgeryTemplate}/edit', [SurgeryTemplateController::class, 'edit'])
        ->name('surgery-templates.edit');
    
    Route::put('/surgery-templates/{surgeryTemplate}', [SurgeryTemplateController::class, 'update'])
        ->name('surgery-templates.update');
    
    Route::delete('/surgery-templates/{surgeryTemplate}', [SurgeryTemplateController::class, 'destroy'])
        ->name('surgery-templates.destroy');
    
    Route::get('/surgery-templates/{surgeryTemplate}/print', [SurgeryTemplateController::class, 'print'])
        ->name('surgery-templates.print');

    // AJAX routes for dynamic functionality
    Route::get('/api/medicines/by-type', [SurgeryTemplateController::class, 'getMedicinesByType'])
        ->name('api.medicines.by-type');
    
    Route::get('/api/surgery-templates', [SurgeryTemplateController::class, 'getTemplates'])
        ->name('api.surgery-templates.list');
    
    Route::get('/api/surgery-templates/{surgeryTemplate}/data', [SurgeryTemplateController::class, 'getTemplateData'])
        ->name('api.surgery-templates.data');

});
