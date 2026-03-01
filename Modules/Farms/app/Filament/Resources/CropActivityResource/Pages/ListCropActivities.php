<?php

namespace Modules\Farms\Filament\Resources\CropActivityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\CropActivityResource;

class ListCropActivities extends ListRecords
{
    protected static string $resource = CropActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
