<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Vendor\Resources\VendorAsnResource;

class ListVendorAsns extends ListRecords
{
    protected static string $resource = VendorAsnResource::class;
}