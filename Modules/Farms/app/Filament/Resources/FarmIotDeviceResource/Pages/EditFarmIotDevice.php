<?php

namespace Modules\Farms\Filament\Resources\FarmIotDeviceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmIotDeviceResource;

class EditFarmIotDevice extends EditRecord
{
    protected static string $resource = FarmIotDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}