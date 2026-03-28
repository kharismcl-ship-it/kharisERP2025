<?php

namespace Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MySafetyIncidentResource;

class ListMySafetyIncidents extends ListRecords
{
    protected static string $resource = MySafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Report Incident')];
    }
}