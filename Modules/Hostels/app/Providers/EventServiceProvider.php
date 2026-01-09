<?php

namespace Modules\Hostels\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Hostels\Events\BookingConfirmed;
use Modules\Hostels\Events\TenantCheckedIn;
use Modules\Hostels\Events\TenantOtpRequested;
use Modules\Hostels\Events\TenantUserCreated;
use Modules\Hostels\Events\TenantUserReactivated;
use Modules\Hostels\Listeners\SendBookingConfirmation;
use Modules\Hostels\Listeners\SendCheckInNotification;
use Modules\Hostels\Listeners\SendPaymentReceipt;
use Modules\Hostels\Listeners\SendPreArrivalWelcome;
use Modules\Hostels\Listeners\SendTenantOtp;
use Modules\Hostels\Listeners\SendTenantReactivatedNotification;
use Modules\Hostels\Listeners\SendTenantWelcomeNotification;
use Modules\Hostels\Listeners\UpdateBookingOnPayment;
use Modules\PaymentsChannel\Events\PaymentFailed;
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
            UpdateBookingOnPayment::class,
            SendPaymentReceipt::class,
        ],
        PaymentFailed::class => [
            UpdateBookingOnPayment::class,
        ],
        BookingConfirmed::class => [
            SendBookingConfirmation::class,
            SendPreArrivalWelcome::class,
        ],
        TenantCheckedIn::class => [
            SendCheckInNotification::class,
        ],
        TenantOtpRequested::class => [
            SendTenantOtp::class,
        ],
        TenantUserCreated::class => [
            SendTenantWelcomeNotification::class,
        ],
        TenantUserReactivated::class => [
            SendTenantReactivatedNotification::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
