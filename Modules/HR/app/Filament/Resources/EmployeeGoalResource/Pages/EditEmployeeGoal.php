<?php

namespace Modules\HR\Filament\Resources\EmployeeGoalResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\EmployeeGoalResource;

class EditEmployeeGoal extends EditRecord
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}