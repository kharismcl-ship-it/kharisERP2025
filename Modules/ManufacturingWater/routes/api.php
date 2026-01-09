<?php

use Illuminate\Support\Facades\Route;
use Modules\ManufacturingWater\Http\Controllers\ManufacturingWaterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manufacturingwaters', ManufacturingWaterController::class)->names('manufacturingwater');
});
