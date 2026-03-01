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
        \Modules\Hostels\Events\BookingConfirmed::class => [
            \Modules\Finance\Listeners\Hostel\UpdateInvoiceOnBookingConfirmed::class,
        ],
        \Modules\Hostels\Events\BookingCancelled::class => [
            \Modules\Finance\Listeners\Hostel\CancelInvoiceOnBookingCancelled::class,
        ],
        \Modules\PaymentsChannel\Events\PaymentSucceeded::class => [
            \Modules\Finance\Listeners\Payments\RecordPaymentOnSuccess::class,
        ],
        \Modules\Core\Events\PaymentCompleted::class => [
            \Modules\Finance\Listeners\ProcessUnifiedPayment::class,
        ],
        \Modules\HR\Events\PayrollFinalized::class => [
            \Modules\Finance\Listeners\HR\RecordPayrollExpense::class,
        ],
        \Modules\ProcurementInventory\Events\PurchaseOrderApproved::class => [
            \Modules\Finance\Listeners\ProcurementInventory\RecordPurchaseOrderExpense::class,
        ],
        \Modules\ProcurementInventory\Events\GoodsReceived::class => [
            \Modules\Finance\Listeners\ProcurementInventory\PostInventoryOnGoodsReceived::class,
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
