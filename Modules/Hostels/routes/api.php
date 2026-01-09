<?php

use Illuminate\Support\Facades\Route;
use Modules\Hostels\Http\Controllers\HostelsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hostels', HostelsController::class)->names('hostels');
});
