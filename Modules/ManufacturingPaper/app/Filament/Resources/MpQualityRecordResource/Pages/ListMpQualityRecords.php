<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource;

class ListMpQualityRecords extends ListRecords
{
    protected static string $resource = MpQualityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
