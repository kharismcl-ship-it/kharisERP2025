<?php

namespace Modules\Farms\Filament\Resources\FarmEquipmentLogResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmEquipmentLogResource;

class EditFarmEquipmentLog extends EditRecord
{
    protected static string $resource = FarmEquipmentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
