<?php

namespace Modules\Core\Filament\Resources\AutomationLogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\AutomationLogResource;

class ListAutomationLogs extends ListRecords
{
    protected static string $resource = AutomationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
