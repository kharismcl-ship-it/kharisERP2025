<?php

namespace Modules\Finance\Filament\Resources\ExpenseClaimResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\ExpenseClaimResource;

class CreateExpenseClaim extends CreateRecord
{
    protected static string $resource = ExpenseClaimResource::class;
}