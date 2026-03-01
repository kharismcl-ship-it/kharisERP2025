<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\CropCycleResource;

class ListCropCycles extends ListRecords
{
    protected static string $resource = CropCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
