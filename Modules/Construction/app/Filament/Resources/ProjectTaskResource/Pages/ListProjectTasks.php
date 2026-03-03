<?php

namespace Modules\Construction\Filament\Resources\ProjectTaskResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ProjectTaskResource;

class ListProjectTasks extends ListRecords
{
    protected static string $resource = ProjectTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
