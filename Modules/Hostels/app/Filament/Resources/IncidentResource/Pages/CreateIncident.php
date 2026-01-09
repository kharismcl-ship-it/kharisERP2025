<?php

namespace Modules\Hostels\Filament\Resources\IncidentResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\IncidentResource;

    class CreateIncident extends CreateRecord {
        protected static string $resource = IncidentResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
