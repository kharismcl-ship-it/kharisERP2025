<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource;

class ListMwDistributionRecords extends ListRecords
{
    protected static string $resource = MwDistributionRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}