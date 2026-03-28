<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorStatementResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ProcurementInventory\Filament\Resources\VendorStatementResource;

class ListVendorStatements extends ListRecords
{
    protected static string $resource = VendorStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}