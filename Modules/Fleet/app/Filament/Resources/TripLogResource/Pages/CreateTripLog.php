<?php

namespace Modules\Fleet\Filament\Resources\TripLogResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fleet\Filament\Resources\TripLogResource;

class CreateTripLog extends CreateRecord
{
    protected static string $resource = TripLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
