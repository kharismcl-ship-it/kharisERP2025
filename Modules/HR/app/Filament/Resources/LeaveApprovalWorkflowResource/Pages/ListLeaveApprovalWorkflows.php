<?php

namespace Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource;

class ListLeaveApprovalWorkflows extends ListRecords
{
    protected static string $resource = LeaveApprovalWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
