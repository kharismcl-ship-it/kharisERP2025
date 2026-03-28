<?php

namespace Modules\HR\Filament\Resources\SafetyIncidentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\SafetyIncidentResource;

class ViewSafetyIncident extends ViewRecord
{
    protected static string $resource = SafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}