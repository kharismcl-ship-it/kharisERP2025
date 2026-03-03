<?php

namespace Modules\Farms\Filament\Resources\FarmSeasonResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmSeasonResource;

class EditFarmSeason extends EditRecord
{
    protected static string $resource = FarmSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
