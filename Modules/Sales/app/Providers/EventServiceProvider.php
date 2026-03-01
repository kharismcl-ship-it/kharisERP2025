<?php

namespace Modules\Sales\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Sales\Events\SalesOrderConfirmed;
use Modules\Sales\Events\SalesOrderFulfilled;
use Modules\Sales\Events\PosSaleCompleted;
use Modules\Sales\Events\QuotationSent;
use Modules\Sales\Events\DiningOrderSentToKitchen;
use Modules\Sales\Listeners\FulfillSalesOrder;
use Modules\Sales\Listeners\CreateInvoiceForSalesOrder;
use Modules\Sales\Listeners\SendOrderFulfilledSms;
use Modules\Sales\Listeners\FulfillPosSale;
use Modules\Sales\Listeners\CreateInvoiceForPosSale;
use Modules\Sales\Listeners\SendPosReceiptSms;
use Modules\Sales\Listeners\SendQuotationEmail;
use Modules\Sales\Listeners\CreateKitchenTickets;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SalesOrderConfirmed::class => [
            FulfillSalesOrder::class,
        ],
        SalesOrderFulfilled::class => [
            CreateInvoiceForSalesOrder::class,
            SendOrderFulfilledSms::class,
        ],
        PosSaleCompleted::class => [
            CreateInvoiceForPosSale::class,
            SendPosReceiptSms::class,
            FulfillPosSale::class,
        ],
        QuotationSent::class => [
            SendQuotationEmail::class,
        ],
        DiningOrderSentToKitchen::class => [
            CreateKitchenTickets::class,
        ],
    ];

    protected static $shouldDiscoverEvents = false;
}