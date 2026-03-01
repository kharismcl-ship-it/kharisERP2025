<?php

namespace Modules\Farms\Filament\Resources\FarmResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmResource;

class CreateFarm extends CreateRecord
{
    protected static string $resource = FarmResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
