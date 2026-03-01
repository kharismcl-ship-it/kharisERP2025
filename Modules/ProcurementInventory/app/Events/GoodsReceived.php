<?php

namespace Modules\ProcurementInventory\Events;

use Illuminate\Queue\SerializesModels;
use Modules\ProcurementInventory\Models\GoodsReceipt;

class GoodsReceived
{
    use SerializesModels;

    public GoodsReceipt $goodsReceipt;

    public function __construct(GoodsReceipt $goodsReceipt)
    {
        $this->goodsReceipt = $goodsReceipt;
    }
}
