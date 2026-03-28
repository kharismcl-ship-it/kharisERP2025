<?php

namespace Modules\Finance\Filament\Resources\ExpenseClaimResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\ExpenseClaimResource;

class EditExpenseClaim extends EditRecord
{
    protected static string $resource = ExpenseClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}