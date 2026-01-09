<?php

namespace Modules\Finance\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\Hostels\Events\BookingPaymentCompleted::class => [
            \Modules\Finance\Listeners\Hostel\CreateInvoiceForBooking::class,
        ],
        \Modules\PaymentsChannel\Events\PaymentSucceeded::class => [
            \Modules\Finance\Listeners\Payments\RecordPaymentOnSuccess::class,
        ],
        \Modules\Core\Events\PaymentCompleted::class => [
            \Modules\Finance\Listeners\ProcessUnifiedPayment::class,
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
