<?php

namespace Modules\Construction\Filament\Resources\ProjectBudgetItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource;

class EditProjectBudgetItem extends EditRecord
{
    protected static string $resource = ProjectBudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
