<?php

namespace Modules\Sales\Filament\Resources\DiningTableResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\DiningTableResource;

class ListDiningTables extends ListRecords
{
    protected static string $resource = DiningTableResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
