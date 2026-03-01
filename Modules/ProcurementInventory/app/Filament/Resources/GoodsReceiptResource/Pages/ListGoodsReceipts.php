<?php

namespace Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource;

class ListGoodsReceipts extends ListRecords
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
