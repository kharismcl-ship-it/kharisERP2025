<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentsChannel\Http\Controllers\PaymentsChannelController;
use Modules\PaymentsChannel\Http\Controllers\WebhookController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paymentschannels', PaymentsChannelController::class)->names('paymentschannel');
});

Route::prefix('payment/webhooks')
    ->middleware(['throttle:webhooks'])
    ->group(function () {
        Route::post('flutterwave', [WebhookController::class, 'flutterwave'])->name('payment.webhook.flutterwave');
        Route::post('paystack', [WebhookController::class, 'paystack'])->name('payment.webhook.paystack');
        Route::post('payswitch', [WebhookController::class, 'payswitch'])->name('payment.webhook.payswitch');
        Route::post('stripe', [WebhookController::class, 'stripe'])->name('payment.webhook.stripe');
        Route::post('ghanapay', [WebhookController::class, 'ghanapay'])->name('payment.webhook.ghanapay');
    });
