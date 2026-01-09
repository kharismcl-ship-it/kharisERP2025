<?php

use Illuminate\Support\Facades\Route;
use Modules\CommunicationCentre\Http\Controllers\CommunicationCentreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('communicationcentres', CommunicationCentreController::class)->names('communicationcentre');
});
