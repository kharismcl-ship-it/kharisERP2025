<?php

use Illuminate\Support\Facades\Route;
use Modules\ManufacturingWater\Http\Livewire\ManufacturingWaterIndex;

Route::middleware(['web', 'auth', 'set-company:manufacturing-water'])
    ->prefix('manufacturing-water')
    ->name('manufacturing_water.')
    ->group(function () {
        Route::get('/', ManufacturingWaterIndex::class)->name('index');
    });
