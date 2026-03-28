<?php

namespace Modules\HR\Filament\Resources\SafetyIncidentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\SafetyIncidentResource;

class EditSafetyIncident extends EditRecord
{
    protected static string $resource = SafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}