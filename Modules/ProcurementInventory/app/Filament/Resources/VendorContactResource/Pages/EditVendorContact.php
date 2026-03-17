<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\VendorContactResource;

class EditVendorContact extends EditRecord
{
    protected static string $resource = VendorContactResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
