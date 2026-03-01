<?php

namespace Modules\ProcurementInventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProcurementInventory\Models\WarehouseTransfer;

class WarehouseTransferCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly WarehouseTransfer $transfer
    ) {}
}