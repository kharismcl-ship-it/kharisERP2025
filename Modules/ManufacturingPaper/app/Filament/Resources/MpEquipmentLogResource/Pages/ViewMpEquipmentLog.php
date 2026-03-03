<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource;

class ViewMpEquipmentLog extends ViewRecord
{
    protected static string $resource = MpEquipmentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
