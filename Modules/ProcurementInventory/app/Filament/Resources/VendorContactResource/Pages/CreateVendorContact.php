<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\ProcurementInventory\Filament\Resources\VendorContactResource;

class CreateVendorContact extends CreateRecord
{
    protected static string $resource = VendorContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
