<?php

namespace Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource;

class ViewWarehouseTransfer extends ViewRecord
{
    protected static string $resource = WarehouseTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}