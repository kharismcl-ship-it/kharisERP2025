<?php

use Illuminate\Support\Facades\Route;
use Modules\Farms\Http\Livewire\FarmIndex;

Route::middleware(['web', 'auth', 'set-company:farms'])
    ->prefix('farms')
    ->name('farms.')
    ->group(function () {
        Route::get('/', FarmIndex::class)->name('index');
    });
