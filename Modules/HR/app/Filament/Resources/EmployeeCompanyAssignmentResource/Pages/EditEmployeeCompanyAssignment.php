<?php

namespace Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource;

class EditEmployeeCompanyAssignment extends EditRecord
{
    protected static string $resource = EmployeeCompanyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
