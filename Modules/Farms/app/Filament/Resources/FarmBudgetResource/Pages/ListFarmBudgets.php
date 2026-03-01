<?php

namespace Modules\Farms\Filament\Resources\FarmBudgetResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmBudgetResource;

class ListFarmBudgets extends ListRecords
{
    protected static string $resource = FarmBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}