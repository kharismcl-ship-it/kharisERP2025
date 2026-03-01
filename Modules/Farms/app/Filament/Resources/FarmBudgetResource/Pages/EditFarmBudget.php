<?php

namespace Modules\Farms\Filament\Resources\FarmBudgetResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmBudgetResource;

class EditFarmBudget extends EditRecord
{
    protected static string $resource = FarmBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}