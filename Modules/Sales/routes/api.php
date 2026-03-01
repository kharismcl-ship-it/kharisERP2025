<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])
    ->prefix('api/sales')
    ->name('api.sales.')
    ->group(function () {
        //
    });