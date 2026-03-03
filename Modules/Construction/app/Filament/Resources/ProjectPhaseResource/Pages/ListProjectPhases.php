<?php

namespace Modules\Construction\Filament\Resources\ProjectPhaseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ProjectPhaseResource;

class ListProjectPhases extends ListRecords
{
    protected static string $resource = ProjectPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
