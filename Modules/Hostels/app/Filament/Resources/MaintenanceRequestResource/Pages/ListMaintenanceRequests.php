<?php

namespace Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;

    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Modules\Hostels\Filament\Resources\MaintenanceRequestResource;

    class ListMaintenanceRequests extends ListRecords {
        protected static string $resource = MaintenanceRequestResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
