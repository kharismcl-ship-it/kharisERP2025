<?php

namespace Modules\ProcurementInventory\Filament\Resources\StockLevelResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\StockLevelResource;

class ListStockLevels extends ListRecords
{
    protected static string $resource = StockLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
