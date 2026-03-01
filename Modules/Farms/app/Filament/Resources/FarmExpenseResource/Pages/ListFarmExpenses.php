<?php

namespace Modules\Farms\Filament\Resources\FarmExpenseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmExpenseResource;

class ListFarmExpenses extends ListRecords
{
    protected static string $resource = FarmExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
