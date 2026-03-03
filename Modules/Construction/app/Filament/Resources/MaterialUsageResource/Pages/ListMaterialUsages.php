<?php

namespace Modules\Construction\Filament\Resources\MaterialUsageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\MaterialUsageResource;

class ListMaterialUsages extends ListRecords
{
    protected static string $resource = MaterialUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
