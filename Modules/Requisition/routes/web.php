<?php

use Illuminate\Support\Facades\Route;
use Modules\Requisition\Http\Controllers\ApprovalController;
use Modules\Requisition\Http\Controllers\PwaController;

Route::middleware(['web'])->group(function () {
    // Email-based approval routes (token-authenticated, no session required)
    Route::get('requisition-approval/{token}/approve', [ApprovalController::class, 'approve'])
        ->name('requisition.email-approve');

    Route::get('requisition-approval/{token}/reject', [ApprovalController::class, 'reject'])
        ->name('requisition.email-reject');

    // PWA manifest and service worker
    Route::get('/requisition-manifest.json', [PwaController::class, 'manifest'])
        ->name('requisition.pwa.manifest');

    Route::get('/requisition-sw.js', [PwaController::class, 'serviceWorker'])
        ->name('requisition.pwa.sw');
});