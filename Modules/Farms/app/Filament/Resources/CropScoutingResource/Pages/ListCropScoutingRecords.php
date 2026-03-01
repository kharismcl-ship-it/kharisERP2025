<?php

namespace Modules\Farms\Filament\Resources\CropScoutingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\CropScoutingResource;

class ListCropScoutingRecords extends ListRecords
{
    protected static string $resource = CropScoutingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}