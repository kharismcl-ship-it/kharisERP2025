<?php

namespace Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource;

class ListEmployeeCompanyAssignments extends ListRecords
{
    protected static string $resource = EmployeeCompanyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
