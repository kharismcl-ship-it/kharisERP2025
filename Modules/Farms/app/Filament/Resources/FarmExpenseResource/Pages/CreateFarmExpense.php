<?php

namespace Modules\Farms\Filament\Resources\FarmExpenseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmExpenseResource;

class CreateFarmExpense extends CreateRecord
{
    protected static string $resource = FarmExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->current_company_id ?? auth()->user()->companies()->first()?->id;
        return $data;
    }
}
