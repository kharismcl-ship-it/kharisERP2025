<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource;

class EditMpQualityRecord extends EditRecord
{
    protected static string $resource = MpQualityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
