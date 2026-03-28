<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource;

class EditRequisitionWorkflowRule extends EditRecord
{
    protected static string $resource = RequisitionWorkflowRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $approvers = request()->input('data.approvers', []);
        if (is_array($approvers)) {
            $data['approver_employee_ids'] = array_values(array_column($approvers, 'employee_id'));
            $data['approver_roles']        = array_values(array_column($approvers, 'role'));
        }
        unset($data['approvers']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}