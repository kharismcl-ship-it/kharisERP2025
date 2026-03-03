<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource;

class ListMpProductionLines extends ListRecords
{
    protected static string $resource = MpProductionLineResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
