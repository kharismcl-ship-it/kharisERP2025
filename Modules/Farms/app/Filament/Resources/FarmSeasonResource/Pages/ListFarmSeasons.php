<?php

namespace Modules\Farms\Filament\Resources\FarmSeasonResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmSeasonResource;

class ListFarmSeasons extends ListRecords
{
    protected static string $resource = FarmSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
