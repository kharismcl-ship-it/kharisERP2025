<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fleet\Filament\Resources\VehicleResource;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
