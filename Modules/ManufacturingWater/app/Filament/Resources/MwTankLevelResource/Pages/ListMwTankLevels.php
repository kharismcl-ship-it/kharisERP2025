<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource;

class ListMwTankLevels extends ListRecords
{
    protected static string $resource = MwTankLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
