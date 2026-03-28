<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource;

class CreateRequisitionWorkflowRule extends CreateRecord
{
    protected static string $resource = RequisitionWorkflowRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $approvers = request()->input('data.approvers', []);
        if (is_array($approvers)) {
            $data['approver_employee_ids'] = array_values(array_column($approvers, 'employee_id'));
            $data['approver_roles']        = array_values(array_column($approvers, 'role'));
        }
        unset($data['approvers']);

        // Scope to current tenant
        $data['company_id'] = filament()->getTenant()?->id ?? $data['company_id'] ?? null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}