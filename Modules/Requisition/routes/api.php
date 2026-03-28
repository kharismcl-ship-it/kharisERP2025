<?php

use Illuminate\Support\Facades\Route;
use Modules\Requisition\Http\Controllers\Api\RequisitionApiController;

Route::prefix('requisitions')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [RequisitionApiController::class, 'index']);
    Route::post('/', [RequisitionApiController::class, 'store']);
    Route::get('/{requisition}', [RequisitionApiController::class, 'show']);
    Route::patch('/{requisition}/status', [RequisitionApiController::class, 'updateStatus']);
    Route::post('/{requisition}/submit', [RequisitionApiController::class, 'submit']);
    Route::get('/{requisition}/items', [RequisitionApiController::class, 'items']);
    Route::post('/{requisition}/items', [RequisitionApiController::class, 'addItem']);
});