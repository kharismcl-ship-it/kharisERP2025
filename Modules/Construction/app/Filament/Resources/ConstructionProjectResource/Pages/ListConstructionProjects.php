<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;

class ListConstructionProjects extends ListRecords
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
