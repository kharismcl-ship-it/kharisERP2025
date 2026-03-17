<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\VendorContactResource;

class ListVendorContacts extends ListRecords
{
    protected static string $resource = VendorContactResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
