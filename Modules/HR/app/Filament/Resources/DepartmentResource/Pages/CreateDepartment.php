<?php

namespace Modules\HR\Filament\Resources\DepartmentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\DepartmentResource;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
