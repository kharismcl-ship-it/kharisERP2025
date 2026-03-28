<?php

namespace Modules\Finance\Filament\Resources\ExpenseCategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\ExpenseCategoryResource;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;
}