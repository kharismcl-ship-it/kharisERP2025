<?php

namespace Modules\Sales\Filament\Resources\SalesOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\SalesOrderResource;

class ListSalesOrders extends ListRecords
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
