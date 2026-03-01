<?php

namespace Modules\ProcurementInventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ProcurementInventory\Models\Item;
use Modules\ProcurementInventory\Models\StockLevel;

class StockLevelLow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly StockLevel $stockLevel,
        public readonly Item $item
    ) {}
}