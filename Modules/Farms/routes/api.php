<?php

use Illuminate\Support\Facades\Route;
use Modules\Farms\Http\Controllers\FarmsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('farms', FarmsController::class)->names('farms');
});
