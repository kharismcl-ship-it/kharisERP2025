<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource;

class ListMwWaterTestRecords extends ListRecords
{
    protected static string $resource = MwWaterTestRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
