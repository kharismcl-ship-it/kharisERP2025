<?php

namespace Modules\Construction\Filament\Resources\ProjectBudgetItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource;

class CreateProjectBudgetItem extends CreateRecord
{
    protected static string $resource = ProjectBudgetItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
