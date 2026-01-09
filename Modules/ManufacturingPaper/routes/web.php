<?php

use Illuminate\Support\Facades\Route;
use Modules\ManufacturingPaper\Http\Livewire\ManufacturingPaperIndex;

Route::middleware(['web', 'auth', 'set-company:manufacturing-paper'])
    ->prefix('manufacturing-paper')
    ->name('manufacturing_paper.')
    ->group(function () {
        Route::get('/', ManufacturingPaperIndex::class)->name('index');
    });
