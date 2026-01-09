<?php

namespace Modules\HR\Filament\Resources\HostelStaffAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource;

class EditHostelStaffAssignment extends EditRecord
{
    protected static string $resource = HostelStaffAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
