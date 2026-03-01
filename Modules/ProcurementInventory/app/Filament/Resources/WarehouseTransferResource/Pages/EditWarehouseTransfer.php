<?php

namespace Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource;

class EditWarehouseTransfer extends EditRecord
{
    protected static string $resource = WarehouseTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}