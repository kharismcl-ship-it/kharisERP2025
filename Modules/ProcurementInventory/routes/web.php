<?php

use Illuminate\Support\Facades\Route;
use Modules\ProcurementInventory\Http\Livewire\ProcurementInventoryIndex;

Route::middleware(['web', 'auth', 'set-company:procurement-inventory'])
    ->prefix('procurement-inventory')
    ->name('procurement_inventory.')
    ->group(function () {
        Route::get('/', ProcurementInventoryIndex::class)->name('index');
    });
