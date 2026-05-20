<?php
use App\Http\Controllers\DoctorController;

Route::prefix('settings')->group(function () {
    // Old-style URL you wanted:
    Route::get('AddDoctor', [DoctorController::class, 'create'])->name('settings.doctor.create');
    Route::post('AddDoctor', [DoctorController::class, 'store'])->name('settings.doctor.store');

    // Full CRUD list/edit/delete:
    Route::get('doctors', [DoctorController::class, 'index'])->name('settings.doctors.index');
    Route::get('doctors/{id}/edit', [DoctorController::class, 'edit'])->name('settings.doctors.edit');
    Route::put('doctors/{id}', [DoctorController::class, 'update'])->name('settings.doctors.update');
    Route::delete('doctors/{id}', [DoctorController::class, 'destroy'])->name('settings.doctors.destroy');
});
