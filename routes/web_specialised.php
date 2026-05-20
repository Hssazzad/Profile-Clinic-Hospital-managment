<?php
use App\Http\Controllers\ConfigSpecialityController;
Route::prefix('settings')->group(function () {
Route::resource('configspeciality', ConfigSpecialityController::class)
    ->except(['show']);
});