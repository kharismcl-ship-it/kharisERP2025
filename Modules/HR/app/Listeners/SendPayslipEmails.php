<?php

namespace Modules\HR\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Events\PayrollFinalized;

class SendPayslipEmails
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    public function handle(PayrollFinalized $event): void
    {
        $run = $event->payrollRun;

        $run->lines()->with('employee')->get()->each(function ($line) use ($run) {
            $employee = $line->employee;

            if (! $employee || ! $employee->email) {
                return;
            }

            try {
                $month    = \Carbon\Carbon::createFromDate($run->period_year, $run->period_month, 1)->format('F Y');
                $gross    = number_format((float) $line->gross_salary, 2);
                $paye     = number_format((float) $line->paye_tax, 2);
                $ssnit    = number_format((float) $line->ssnit_employee, 2);
                $net      = number_format((float) $line->net_salary, 2);

                $body = "Dear {$employee->full_name},\n\n"
                    . "Your payslip for {$month} is ready.\n\n"
                    . "────────────────────────\n"
                    . "  Gross Salary:   GHS {$gross}\n"
                    . "  PAYE Tax:       GHS {$paye}\n"
                    . "  SSNIT (5.5%):   GHS {$ssnit}\n"
                    . "  Net Pay:        GHS {$net}\n"
                    . "────────────────────────\n\n"
                    . "Please log in to the HR portal to view your full payslip details.\n\n"
                    . "HR Department";

                $this->communicationService->sendRawEmail(
                    $employee->email,
                    $employee->full_name,
                    "Payslip — {$month}",
                    $body,
                );
            } catch (\Throwable $e) {
                Log::warning('SendPayslipEmails: Failed for employee.', [
                    'employee_id'    => $employee->id,
                    'payroll_run_id' => $run->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        });
    }
}
