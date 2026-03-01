<?php

namespace Modules\Sales\Filament\Resources\CatalogItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\CatalogItemResource;

class ListCatalogItems extends ListRecords
{
    protected static string $resource = CatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
