<?php

namespace Modules\ProcurementInventory\Events;

use Illuminate\Queue\SerializesModels;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class PurchaseOrderApproved
{
    use SerializesModels;

    public PurchaseOrder $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }
}
