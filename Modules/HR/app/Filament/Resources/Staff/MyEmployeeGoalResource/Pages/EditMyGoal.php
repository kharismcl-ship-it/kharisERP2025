<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource;

class EditMyGoal extends EditRecord
{
    protected static string $resource = MyEmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
