<?php

namespace Modules\ProcurementInventory\Filament\Resources\StockLotResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\StockLotResource;

class ListStockLots extends ListRecords
{
    protected static string $resource = StockLotResource::class;
}