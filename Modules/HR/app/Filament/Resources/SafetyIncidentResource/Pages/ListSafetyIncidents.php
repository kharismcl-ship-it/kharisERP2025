<?php

namespace Modules\HR\Filament\Resources\SafetyIncidentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\SafetyIncidentResource;

class ListSafetyIncidents extends ListRecords
{
    protected static string $resource = SafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}