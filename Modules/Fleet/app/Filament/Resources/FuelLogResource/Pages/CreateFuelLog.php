<?php

namespace Modules\Fleet\Filament\Resources\FuelLogResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fleet\Filament\Resources\FuelLogResource;

class CreateFuelLog extends CreateRecord
{
    protected static string $resource = FuelLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
