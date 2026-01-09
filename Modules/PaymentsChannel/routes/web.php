<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentsChannel\Http\Controllers\PaymentsChannelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('paymentschannels', PaymentsChannelController::class)->names('paymentschannel');
});
