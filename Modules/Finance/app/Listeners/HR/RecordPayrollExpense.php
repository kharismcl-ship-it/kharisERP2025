<?php

namespace Modules\Finance\Listeners\HR;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\HR\Events\PayrollFinalized;

class RecordPayrollExpense
{
    /**
     * When payroll is marked as paid, post the following double-entry:
     *
     *   DR  5210 Salaries & Wages         total_gross
     *   CR  2140 Income Tax Payable        sum of paye_tax
     *   CR  2150 Pension Payable (SSNIT)   sum of ssnit_employee + ssnit_employer
     *   CR  1120 Bank Account              total_net (remaining net payout)
     */
    public function handle(PayrollFinalized $event): void
    {
        $payroll = $event->payrollRun->load('lines');
        $companyId = $payroll->company_id;

        $totalGross = (float) $payroll->total_gross;
        $totalNet   = (float) $payroll->total_net;

        // Aggregate tax and pension from lines
        $totalPaye    = $payroll->lines->sum('paye_tax');
        $totalSsnit   = $payroll->lines->sum(fn ($l) => (float) $l->ssnit_employee + (float) $l->ssnit_employer);
        $totalOtherDeductions = $totalGross - $totalNet - $totalPaye - $totalSsnit;
        if ($totalOtherDeductions < 0) {
            $totalOtherDeductions = 0;
        }

        $periodLabel = $payroll->period_label ?? "{$payroll->period_month}/{$payroll->period_year}";

        $entry = JournalEntry::create([
            'company_id'  => $companyId,
            'date'        => $payroll->payment_date ?? now(),
            'reference'   => 'PAYROLL-'.strtoupper($payroll->period_year).'-'.str_pad($payroll->period_month, 2, '0', STR_PAD_LEFT),
            'description' => "Payroll expense — {$periodLabel} ({$payroll->employee_count} employees)",
        ]);

        $lines = [];

        // DR Salaries & Wages (5210)
        if ($account = $this->account('5210', $companyId)) {
            $lines[] = ['account_id' => $account->id, 'debit' => $totalGross, 'credit' => 0];
        }

        // CR Income Tax Payable (2140)
        if ($totalPaye > 0 && ($account = $this->account('2140', $companyId))) {
            $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $totalPaye];
        }

        // CR Pension Payable — SSNIT (2150)
        if ($totalSsnit > 0 && ($account = $this->account('2150', $companyId))) {
            $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $totalSsnit];
        }

        // CR Bank Account (1120) for net payout
        $creditBank = $totalGross - $totalPaye - $totalSsnit;
        if ($creditBank > 0 && ($account = $this->account('1120', $companyId))) {
            $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $creditBank];
        }

        foreach ($lines as $line) {
            JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
        }

        Log::info('RecordPayrollExpense: payroll journal posted', [
            'journal_entry_id' => $entry->id,
            'payroll_run_id'   => $payroll->id,
            'period'           => $periodLabel,
            'total_gross'      => $totalGross,
            'total_net'        => $totalNet,
            'lines'            => count($lines),
        ]);
    }

    private function account(string $code, ?int $companyId): ?Account
    {
        return Account::where('code', $code)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->first();
    }
}
