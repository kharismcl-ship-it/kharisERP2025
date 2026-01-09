<?php

namespace Modules\Hostels\Filament\Resources\IncidentResource\Pages;

    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Modules\Hostels\Filament\Resources\IncidentResource;

    class ListIncidents extends ListRecords {
        protected static string $resource = IncidentResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
