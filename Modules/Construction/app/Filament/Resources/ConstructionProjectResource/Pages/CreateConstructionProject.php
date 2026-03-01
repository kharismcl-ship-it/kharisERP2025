<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;

class CreateConstructionProject extends CreateRecord
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
