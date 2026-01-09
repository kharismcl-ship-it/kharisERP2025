<?php

use Illuminate\Support\Facades\Route;
use Modules\ProcurementInventory\Http\Controllers\ProcurementInventoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procurementinventories', ProcurementInventoryController::class)->names('procurementinventory');
});
