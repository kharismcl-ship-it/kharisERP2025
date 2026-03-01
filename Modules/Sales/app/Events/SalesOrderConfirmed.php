<?php

namespace Modules\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Sales\Models\SalesOrder;

class SalesOrderConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly SalesOrder $order) {}
}