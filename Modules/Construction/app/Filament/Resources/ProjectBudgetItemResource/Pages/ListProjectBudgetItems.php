<?php

namespace Modules\Construction\Filament\Resources\ProjectBudgetItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource;

class ListProjectBudgetItems extends ListRecords
{
    protected static string $resource = ProjectBudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
