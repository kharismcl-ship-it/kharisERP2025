<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'set-company:sales'])
    ->prefix('sales')
    ->name('sales.')
    ->group(function () {
        // Filament manages the admin UI; web routes are minimal
    });
