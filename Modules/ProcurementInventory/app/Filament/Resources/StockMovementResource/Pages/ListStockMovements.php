<?php

namespace Modules\ProcurementInventory\Filament\Resources\StockMovementResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\StockMovementResource;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
