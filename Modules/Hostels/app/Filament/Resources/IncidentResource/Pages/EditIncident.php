<?php

namespace Modules\Hostels\Filament\Resources\IncidentResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\IncidentResource;

    class EditIncident extends EditRecord {
        protected static string $resource = IncidentResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
