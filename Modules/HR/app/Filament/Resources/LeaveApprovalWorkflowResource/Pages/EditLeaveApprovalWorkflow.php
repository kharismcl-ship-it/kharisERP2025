<?php

namespace Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource;

class EditLeaveApprovalWorkflow extends EditRecord
{
    protected static string $resource = LeaveApprovalWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
