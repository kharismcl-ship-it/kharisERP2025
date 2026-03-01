<?php

namespace Modules\Hostels\Services\Automation;

use Illuminate\Support\Facades\Log;
use Modules\Core\Models\AutomationSetting;
use Modules\Hostels\Services\HostelBillingService;

class BillingCycleGenerationHandler
{
    public function __construct(protected HostelBillingService $billingService) {}

    public function execute(AutomationSetting $setting): array
    {
        try {
            $this->billingService->generateRecurringBilling();

            Log::info('Hostels automation: billing cycle generation completed', [
                'company_id' => $setting->company_id,
            ]);

            return [
                'success'          => true,
                'records_processed' => 1,
                'details'          => ['message' => 'Recurring billing cycles processed successfully'],
            ];
        } catch (\Exception $e) {
            Log::error('Hostels automation: billing cycle generation failed', [
                'error'      => $e->getMessage(),
                'company_id' => $setting->company_id,
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
