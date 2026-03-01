<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource;

class EditMwDistributionRecord extends EditRecord
{
    protected static string $resource = MwDistributionRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
