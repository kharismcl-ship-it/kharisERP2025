<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\CropCycleResource;

class EditCropCycle extends EditRecord
{
    protected static string $resource = CropCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}