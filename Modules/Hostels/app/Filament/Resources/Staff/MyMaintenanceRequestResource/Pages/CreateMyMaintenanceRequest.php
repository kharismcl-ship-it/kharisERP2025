<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource;

class CreateMyMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MyMaintenanceRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reported_by_user_id'] = auth()->id();
        $data['reported_at']         = now();
        $data['status']              = 'open';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
