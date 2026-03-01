<?php

namespace Modules\Core\Filament\Resources\AutomationSettingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\AutomationSettingResource;

class ListAutomationSettings extends ListRecords
{
    protected static string $resource = AutomationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
