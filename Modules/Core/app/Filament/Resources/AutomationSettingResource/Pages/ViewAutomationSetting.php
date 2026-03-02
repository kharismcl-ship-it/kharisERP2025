<?php

namespace Modules\Core\Filament\Resources\AutomationSettingResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\AutomationSettingResource;
use Filament\Actions\EditAction;

class ViewAutomationSetting extends ViewRecord
{
    protected static string $resource = AutomationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
