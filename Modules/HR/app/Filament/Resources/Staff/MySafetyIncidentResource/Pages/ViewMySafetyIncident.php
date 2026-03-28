<?php

namespace Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource;

class ViewMySafetyIncident extends ViewRecord
{
    protected static string $resource = MySafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}