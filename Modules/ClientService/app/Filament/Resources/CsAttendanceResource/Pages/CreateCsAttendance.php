<?php

namespace Modules\ClientService\Filament\Resources\CsAttendanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ClientService\Filament\Resources\CsAttendanceResource;

class CreateCsAttendance extends CreateRecord
{
    protected static string $resource = CsAttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['company_id'])) {
            $data['company_id'] = filament()->getTenant()?->id;
        }

        return $data;
    }
}
