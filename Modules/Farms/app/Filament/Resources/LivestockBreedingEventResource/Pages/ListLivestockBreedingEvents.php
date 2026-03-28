<?php

namespace Modules\Farms\Filament\Resources\LivestockBreedingEventResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\LivestockBreedingEventResource;

class ListLivestockBreedingEvents extends ListRecords
{
    protected static string $resource = LivestockBreedingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}