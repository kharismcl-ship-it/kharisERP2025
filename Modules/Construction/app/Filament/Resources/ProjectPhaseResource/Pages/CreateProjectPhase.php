<?php

namespace Modules\Construction\Filament\Resources\ProjectPhaseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\ProjectPhaseResource;

class CreateProjectPhase extends CreateRecord
{
    protected static string $resource = ProjectPhaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
