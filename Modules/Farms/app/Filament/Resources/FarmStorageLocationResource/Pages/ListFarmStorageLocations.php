<?php

namespace Modules\Farms\Filament\Resources\FarmStorageLocationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmStorageLocationResource;

class ListFarmStorageLocations extends ListRecords
{
    protected static string $resource = FarmStorageLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}