<?php

namespace Modules\HR\Filament\Resources\HostelStaffAssignmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource;

class ListHostelStaffAssignments extends ListRecords
{
    protected static string $resource = HostelStaffAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
