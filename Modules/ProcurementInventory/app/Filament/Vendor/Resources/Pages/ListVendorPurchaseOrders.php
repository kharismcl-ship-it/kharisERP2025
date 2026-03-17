<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Vendor\Resources\VendorPurchaseOrderResource;

class ListVendorPurchaseOrders extends ListRecords
{
    protected static string $resource = VendorPurchaseOrderResource::class;
}
