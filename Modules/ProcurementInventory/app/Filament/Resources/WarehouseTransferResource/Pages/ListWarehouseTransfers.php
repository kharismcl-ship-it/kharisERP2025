<?php

namespace Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource;

class ListWarehouseTransfers extends ListRecords
{
    protected static string $resource = WarehouseTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}