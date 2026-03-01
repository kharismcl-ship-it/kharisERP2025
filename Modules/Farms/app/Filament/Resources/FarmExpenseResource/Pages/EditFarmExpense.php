<?php

namespace Modules\Farms\Filament\Resources\FarmExpenseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmExpenseResource;

class EditFarmExpense extends EditRecord
{
    protected static string $resource = FarmExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
