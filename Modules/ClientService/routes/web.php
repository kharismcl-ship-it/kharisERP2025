<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientService\Livewire\VisitorCheckIn;

/*
|--------------------------------------------------------------------------
| ClientService Web Routes
|--------------------------------------------------------------------------
|
| Public kiosk check-in route. No auth middleware — intended for a kiosk
| device at reception. The company is resolved by slug so each tenant gets
| their own URL: /check-in/acme-corp
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/check-in/{company:slug}', VisitorCheckIn::class)
        ->name('clientservice.visitor-check-in');
});
