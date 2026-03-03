<?php

namespace Modules\Core\Filament\Resources\AutomationLogResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\AutomationLogResource;

class ViewAutomationLog extends ViewRecord
{
    protected static string $resource = AutomationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
