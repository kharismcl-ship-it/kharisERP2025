<?php

namespace Modules\HR\Filament\Resources\EmployeeDocumentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\EmployeeDocumentResource;

class CreateEmployeeDocument extends CreateRecord
{
    protected static string $resource = EmployeeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
