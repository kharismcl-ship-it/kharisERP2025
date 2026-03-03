<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource;

class ViewMpProductionLine extends ViewRecord
{
    protected static string $resource = MpProductionLineResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
