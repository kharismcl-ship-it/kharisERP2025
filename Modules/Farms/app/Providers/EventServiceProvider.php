<?php

namespace Modules\Farms\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Farms\Events\FarmOrderPlaced;
use Modules\Farms\Listeners\FarmSalePaymentListener;
use Modules\Farms\Listeners\SendFarmOrderConfirmation;
use Modules\PaymentsChannel\Events\PaymentSucceeded;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        PaymentSucceeded::class => [
            FarmSalePaymentListener::class,
        ],
        // Marketplace order placed — send confirmation email/SMS
        FarmOrderPlaced::class => [
            SendFarmOrderConfirmation::class,
        ],
        // Farm sale comms — CommunicationCentre integration
        \Modules\Farms\Events\FarmSaleCreated::class => [
            \Modules\Farms\Listeners\SendFarmSaleConfirmation::class,
        ],
        // Phase 2 — new event listeners
        \Modules\Farms\Events\FarmDailyReportSubmitted::class => [
            \Modules\Farms\Listeners\NotifyFarmDailyReportSubmitted::class,
        ],
        \Modules\Farms\Events\FarmRequestStatusChanged::class => [
            \Modules\Farms\Listeners\NotifyFarmRequestStatusChanged::class,
        ],
        \Modules\Farms\Events\FarmWorkerAssigned::class => [
            \Modules\Farms\Listeners\NotifyFarmWorkerAssigned::class,
        ],
        \Modules\Farms\Events\CropCycleStarted::class => [
            \Modules\Farms\Listeners\NotifyCropCycleStarted::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
