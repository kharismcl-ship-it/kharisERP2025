<?php

use Illuminate\Support\Facades\Route;
use Modules\CommunicationCentre\Http\Controllers\API\AnalyticsController;
use Modules\CommunicationCentre\Http\Controllers\CommunicationCentreController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('communicationcentres', CommunicationCentreController::class)->names('communicationcentre');
});

// Analytics API Routes
Route::prefix('api/communication-centre')->middleware(['auth:api', 'verified'])->group(function () {
    Route::prefix('analytics')->group(function () {
        Route::get('delivery-stats', [AnalyticsController::class, 'deliveryStats']);
        Route::get('channel-stats', [AnalyticsController::class, 'channelStats']);
        Route::get('provider-stats', [AnalyticsController::class, 'providerStats']);
        Route::get('template-stats', [AnalyticsController::class, 'templateStats']);
        Route::get('daily-volume', [AnalyticsController::class, 'dailyVolume']);
        Route::get('performance-trend', [AnalyticsController::class, 'performanceTrend']);
        Route::get('failure-analysis', [AnalyticsController::class, 'failureAnalysis']);
        Route::get('dashboard', [AnalyticsController::class, 'dashboard']);
    });
});
