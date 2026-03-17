<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource;

class CreateMyIncident extends CreateRecord
{
    protected static string $resource = MyIncidentResource::class;

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
