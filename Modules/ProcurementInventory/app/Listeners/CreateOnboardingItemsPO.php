<?php

namespace Modules\ProcurementInventory\Listeners;

use Modules\HR\Events\NewEmployeeOnboarded;

class CreateOnboardingItemsPO
{
    public function __construct(protected CreateDraftPurchaseOrder $creator) {}

    public function handle(NewEmployeeOnboarded $event): void
    {
        $employee  = $event->employee;
        $companyId = $employee->company_id ?? null;

        if (! $companyId || empty($event->onboardingItems)) {
            return;
        }

        $this->creator->create(
            $companyId,
            "Auto-draft from HR — New employee onboarding: {$employee->first_name} {$employee->last_name}. Please assign vendor and submit.",
            $event->onboardingItems,
            ['module_tag' => 'hr']
        );
    }
}
