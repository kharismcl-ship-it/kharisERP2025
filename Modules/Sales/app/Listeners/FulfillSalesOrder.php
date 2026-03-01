<?php

namespace Modules\Sales\Listeners;

use Modules\Sales\Events\SalesOrderConfirmed;
use Modules\Sales\Services\SalesFulfillmentService;

class FulfillSalesOrder
{
    public function __construct(protected SalesFulfillmentService $fulfillmentService) {}

    public function handle(SalesOrderConfirmed $event): void
    {
        $this->fulfillmentService->fulfill($event->order);
    }
}