<?php

namespace Modules\ProcurementInventory\Filament\Resources\WarehouseResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ProcurementInventory\Filament\Resources\WarehouseResource;

class ViewWarehouse extends ViewRecord
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}