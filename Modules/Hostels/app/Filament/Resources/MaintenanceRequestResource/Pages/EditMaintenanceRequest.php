<?php

namespace Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\MaintenanceRequestResource;

    class EditMaintenanceRequest extends EditRecord {
        protected static string $resource = MaintenanceRequestResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
