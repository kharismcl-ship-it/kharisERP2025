<?php

namespace Modules\Finance\Listeners\HR;

use Modules\Finance\Services\EnhancedIntegrationService;

class RecordPayrollExpense
{
    /**
     * Handle the payroll event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        // When payroll is processed, record it as an expense
        $integrationService = app(EnhancedIntegrationService::class);

        // This would be implemented when the HR module is fully developed
        // $integrationService->recordPayrollExpense($event->payroll);
    }
}
