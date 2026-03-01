<?php

namespace Modules\Sales\Contracts;

use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrderLine;

interface FulfillmentHandlerInterface
{
    /**
     * Handle fulfillment for a specific order line.
     * Returns true on success, false if this handler does not apply to the line.
     */
    public function handle(SalesOrder $order, SalesOrderLine $line): bool;

    /**
     * The source_module value this handler supports.
     */
    public function sourceModule(): string;
}