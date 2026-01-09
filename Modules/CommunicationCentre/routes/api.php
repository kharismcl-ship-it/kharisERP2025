<?php

use Illuminate\Support\Facades\Route;
use Modules\CommunicationCentre\Http\Controllers\CommunicationCentreController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('communicationcentres', CommunicationCentreController::class)->names('communicationcentre');
});
