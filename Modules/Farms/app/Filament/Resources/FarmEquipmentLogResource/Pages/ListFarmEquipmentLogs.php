<?php

namespace Modules\Farms\Filament\Resources\FarmEquipmentLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmEquipmentLogResource;

class ListFarmEquipmentLogs extends ListRecords
{
    protected static string $resource = FarmEquipmentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
