<?php

namespace Modules\Finance\Filament\Resources\BudgetResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\BudgetResource;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}