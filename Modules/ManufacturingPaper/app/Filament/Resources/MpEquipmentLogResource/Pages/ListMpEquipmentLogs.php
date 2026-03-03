<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource;

class ListMpEquipmentLogs extends ListRecords
{
    protected static string $resource = MpEquipmentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
