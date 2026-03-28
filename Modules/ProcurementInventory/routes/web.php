<?php

use Illuminate\Support\Facades\Route;
use Modules\ProcurementInventory\Http\Livewire\ProcurementInventoryIndex;
use Modules\ProcurementInventory\Http\Controllers\VendorApplicationController;

Route::middleware(['web', 'auth', 'set-company:procurement-inventory'])
    ->prefix('procurement-inventory')
    ->name('procurement_inventory.')
    ->group(function () {
        Route::get('/', ProcurementInventoryIndex::class)->name('index');
    });

// Public vendor self-registration (no auth required)
Route::middleware(['web'])
    ->prefix('vendor-apply')
    ->name('vendor.apply.')
    ->group(function () {
        Route::get('/{company}', [VendorApplicationController::class, 'create'])->name('create');
        Route::post('/{company}', [VendorApplicationController::class, 'store'])->name('store');
        Route::get('/confirmation/success', [VendorApplicationController::class, 'success'])->name('success');
    });
