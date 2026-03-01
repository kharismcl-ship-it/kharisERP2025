<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource;

class ViewMpProductionBatch extends ViewRecord
{
    protected static string $resource = MpProductionBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}