<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ProcurementInventory\Filament\Vendor\Resources\VendorAsnResource;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class CreateVendorAsn extends CreateRecord
{
    protected static string $resource = VendorAsnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $vendorId = auth('vendor')->user()?->vendor_id;
        $data['vendor_id']    = $vendorId;
        $data['status']       = 'submitted';
        $data['submitted_at'] = now();

        $po = PurchaseOrder::find($data['purchase_order_id']);
        $data['company_id'] = $po?->company_id;

        return $data;
    }
}