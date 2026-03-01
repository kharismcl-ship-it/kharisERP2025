<?php

use Illuminate\Support\Facades\Route;
use Modules\CommunicationCentre\Http\Controllers\CommunicationCentreController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('communicationcentres', CommunicationCentreController::class)->names('communicationcentre');

    // Template discovery endpoints
    Route::get('templates', [CommunicationCentreController::class, 'templates'])->name('communicationcentre.templates');
    Route::get('templates/{code}', [CommunicationCentreController::class, 'templateByCode'])->name('communicationcentre.templates.by-code');
});
