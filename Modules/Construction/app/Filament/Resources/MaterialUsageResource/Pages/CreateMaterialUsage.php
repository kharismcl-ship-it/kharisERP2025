<?php

namespace Modules\Construction\Filament\Resources\MaterialUsageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\MaterialUsageResource;

class CreateMaterialUsage extends CreateRecord
{
    protected static string $resource = MaterialUsageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
