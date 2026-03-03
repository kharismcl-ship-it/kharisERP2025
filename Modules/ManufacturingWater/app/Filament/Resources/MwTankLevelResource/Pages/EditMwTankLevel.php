<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource;

class EditMwTankLevel extends EditRecord
{
    protected static string $resource = MwTankLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
