<?php

namespace Modules\HR\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\HR\Events\PayrollFinalized;
use Modules\HR\Services\PayrollService;

class PostPayrollToFinance
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function handle(PayrollFinalized $event): void
    {
        $run = $event->payrollRun;

        try {
            $this->payrollService->postToFinance($run);
        } catch (\Throwable $e) {
            Log::warning('PostPayrollToFinance: Failed to post payroll to Finance GL.', [
                'payroll_run_id' => $run->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
