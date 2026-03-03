<?php

namespace Modules\Construction\Filament\Resources\ProjectTaskResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\ProjectTaskResource;

class CreateProjectTask extends CreateRecord
{
    protected static string $resource = ProjectTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
