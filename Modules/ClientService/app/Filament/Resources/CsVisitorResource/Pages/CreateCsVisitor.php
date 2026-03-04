<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ClientService\Filament\Resources\CsVisitorResource;

class CreateCsVisitor extends CreateRecord
{
    protected static string $resource = CsVisitorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['company_id'])) {
            $data['company_id'] = filament()->getTenant()?->id;
        }

        return $data;
    }
}
