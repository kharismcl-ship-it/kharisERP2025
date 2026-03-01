<?php

namespace Modules\Farms\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Farms\Listeners\FarmSalePaymentListener;
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
        // Farm sale comms — CommunicationCentre integration
        \Modules\Farms\Events\FarmSaleCreated::class => [
            \Modules\Farms\Listeners\SendFarmSaleConfirmation::class,
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
