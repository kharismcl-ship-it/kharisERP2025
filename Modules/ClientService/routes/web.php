<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientService\Livewire\VisitorCheckIn;
use Modules\ClientService\Livewire\VisitorCheckOut;

/*
|--------------------------------------------------------------------------
| ClientService Web Routes
|--------------------------------------------------------------------------
|
| Public kiosk routes. No auth middleware — intended for a kiosk device
| at reception. The company is resolved by slug so each tenant gets
| their own URL.
|
*/

Route::middleware(['web'])->group(function () {

    // First-visit / walk-in check-in
    Route::get('/check-in/{company:slug}', VisitorCheckIn::class)
        ->name('clientservice.visitor-check-in');

    // Returning-visitor check-in — profile QR scan pre-fills the form
    Route::get('/check-in/{company:slug}/r/{profileToken}', VisitorCheckIn::class)
        ->name('clientservice.visitor-check-in.returning');

    // Check-out kiosk — badge QR scan closes the visit and releases the badge
    Route::get('/check-out/{company:slug}/{checkInToken}', VisitorCheckOut::class)
        ->name('clientservice.visitor-check-out');
});