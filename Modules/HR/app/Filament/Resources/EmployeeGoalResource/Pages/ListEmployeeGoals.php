<?php

namespace Modules\HR\Filament\Resources\EmployeeGoalResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmployeeGoalResource;

class ListEmployeeGoals extends ListRecords
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }
}