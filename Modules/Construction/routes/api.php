<?php

use Illuminate\Support\Facades\Route;
use Modules\Construction\Http\Controllers\ConstructionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('constructions', ConstructionController::class)->names('construction');
});
