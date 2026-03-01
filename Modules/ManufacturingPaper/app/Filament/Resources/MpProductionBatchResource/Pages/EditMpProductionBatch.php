<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource;

class EditMpProductionBatch extends EditRecord
{
    protected static string $resource = MpProductionBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
