<?php

namespace Modules\Farms\Filament\Resources\FarmB2bAccountResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource;

class CreateFarmB2bAccount extends CreateRecord
{
    protected static string $resource = FarmB2bAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id ?? auth()->user()?->current_company_id;
        return $data;
    }
}
