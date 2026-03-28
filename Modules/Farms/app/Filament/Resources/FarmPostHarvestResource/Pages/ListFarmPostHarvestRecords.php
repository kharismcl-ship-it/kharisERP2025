<?php

namespace Modules\Farms\Filament\Resources\FarmPostHarvestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmPostHarvestResource;

class ListFarmPostHarvestRecords extends ListRecords
{
    protected static string $resource = FarmPostHarvestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}