<?php

namespace Modules\Sales\Filament\Resources\DiningOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\DiningOrderResource;

class ListDiningOrders extends ListRecords
{
    protected static string $resource = DiningOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
