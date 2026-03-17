<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource;

class ListMyGoals extends ListRecords
{
    protected static string $resource = MyEmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
