<?php

namespace Modules\Construction\Filament\Resources\ContractorResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\ContractorResource;

class CreateContractor extends CreateRecord
{
    protected static string $resource = ContractorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
