<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource;

class ListMpProductionBatches extends ListRecords
{
    protected static string $resource = MpProductionBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
