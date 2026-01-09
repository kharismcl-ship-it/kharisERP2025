<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\EmployeeResource;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
