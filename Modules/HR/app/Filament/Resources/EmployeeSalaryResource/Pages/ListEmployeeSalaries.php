<?php

namespace Modules\HR\Filament\Resources\EmployeeSalaryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmployeeSalaryResource;
use Filament\Actions\CreateAction;

class ListEmployeeSalaries extends ListRecords
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
