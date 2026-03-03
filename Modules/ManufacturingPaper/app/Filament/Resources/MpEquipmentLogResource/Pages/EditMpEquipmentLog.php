<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource;

class EditMpEquipmentLog extends EditRecord
{
    protected static string $resource = MpEquipmentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
