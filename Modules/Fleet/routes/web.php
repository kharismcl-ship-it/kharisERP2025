<?php

use Illuminate\Support\Facades\Route;
use Modules\Fleet\Http\Livewire\FleetIndex;

Route::middleware(['web', 'auth', 'set-company:fleet'])
    ->prefix('fleet')
    ->name('fleet.')
    ->group(function () {
        Route::get('/', FleetIndex::class)->name('index');
    });
