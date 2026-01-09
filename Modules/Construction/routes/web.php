<?php

use Illuminate\Support\Facades\Route;
use Modules\Construction\Http\Livewire\ConstructionIndex;

Route::middleware(['web', 'auth', 'set-company:construction'])
    ->prefix('construction')
    ->name('construction.')
    ->group(function () {
        Route::get('/', ConstructionIndex::class)->name('index');
    });
