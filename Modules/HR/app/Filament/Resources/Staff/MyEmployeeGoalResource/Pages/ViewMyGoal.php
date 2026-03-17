<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource;

class ViewMyGoal extends ViewRecord
{
    protected static string $resource = MyEmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
