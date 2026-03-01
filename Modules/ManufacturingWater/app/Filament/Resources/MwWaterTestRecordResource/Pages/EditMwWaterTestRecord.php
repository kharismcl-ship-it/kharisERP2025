<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource;

class EditMwWaterTestRecord extends EditRecord
{
    protected static string $resource = MwWaterTestRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}