<?php

namespace Modules\Farms\Filament\Resources\FarmSeasonResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmSeasonResource;

class ViewFarmSeason extends ViewRecord
{
    protected static string $resource = FarmSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
