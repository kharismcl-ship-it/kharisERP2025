<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource;

class CreateMyVisitorLog extends CreateRecord
{
    protected static string $resource = MyVisitorLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by_user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
