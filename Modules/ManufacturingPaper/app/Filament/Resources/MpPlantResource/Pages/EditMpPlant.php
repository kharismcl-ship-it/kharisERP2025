<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource;

class EditMpPlant extends EditRecord
{
    protected static string $resource = MpPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
