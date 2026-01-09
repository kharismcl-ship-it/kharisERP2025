<?php

use Illuminate\Support\Facades\Route;
use Modules\ManufacturingPaper\Http\Controllers\ManufacturingPaperController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manufacturingpapers', ManufacturingPaperController::class)->names('manufacturingpaper');
});
