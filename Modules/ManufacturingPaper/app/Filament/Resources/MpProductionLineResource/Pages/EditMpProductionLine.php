<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource;

class EditMpProductionLine extends EditRecord
{
    protected static string $resource = MpProductionLineResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
