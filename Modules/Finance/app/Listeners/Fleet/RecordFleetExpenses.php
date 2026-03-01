<?php

namespace Modules\Finance\Listeners\Fleet;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Fleet\Events\FuelLogged;
use Modules\Fleet\Events\MaintenanceCompleted;

class RecordFleetExpenses
{
    /**
     * Handle a fuel log event.
     *
     * GL posting:
     *   DR 6100 Fleet & Transport Expense   total_cost
     *   CR 1120 Bank Account                total_cost
     */
    public function handleFuelLog(FuelLogged $event): void
    {
        $log       = $event->fuelLog;
        $companyId = $log->company_id;
        $amount    = (float) $log->total_cost;

        if ($amount <= 0) {
            return;
        }

        try {
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $log->fill_date ?? now(),
                'reference'   => 'FLEET-FUEL-' . $log->id,
                'description' => "Fuel expense — {$log->vehicle?->plate} ({$log->litres}L @ {$log->price_per_litre}/L)",
            ]);

            $lines = [];

            if ($account = $this->account('6100', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $amount, 'credit' => 0];
            }

            if ($account = $this->account('1120', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $amount];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('RecordFleetExpenses: fuel GL posted', [
                'journal_entry_id' => $entry->id,
                'fuel_log_id'      => $log->id,
                'amount'           => $amount,
            ]);
        } catch (\Throwable $e) {
            Log::warning('RecordFleetExpenses::handleFuelLog failed', [
                'fuel_log_id' => $log->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a maintenance completed event.
     *
     * GL posting:
     *   DR 6110 Fleet Maintenance Expense   cost
     *   CR 1120 Bank Account                cost
     */
    public function handleMaintenanceRecord(MaintenanceCompleted $event): void
    {
        $record    = $event->maintenanceRecord;
        $companyId = $record->company_id;
        $amount    = (float) $record->cost;

        if ($amount <= 0) {
            return;
        }

        try {
            $entry = JournalEntry::create([
                'company_id'  => $companyId,
                'date'        => $record->service_date ?? now(),
                'reference'   => 'FLEET-MNT-' . $record->id,
                'description' => "Fleet maintenance — {$record->vehicle?->plate} ({$record->type})",
            ]);

            $lines = [];

            if ($account = $this->account('6110', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => $amount, 'credit' => 0];
            }

            if ($account = $this->account('1120', $companyId)) {
                $lines[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $amount];
            }

            foreach ($lines as $line) {
                JournalLine::create(array_merge(['journal_entry_id' => $entry->id], $line));
            }

            Log::info('RecordFleetExpenses: maintenance GL posted', [
                'journal_entry_id'      => $entry->id,
                'maintenance_record_id' => $record->id,
                'amount'                => $amount,
            ]);
        } catch (\Throwable $e) {
            Log::warning('RecordFleetExpenses::handleMaintenanceRecord failed', [
                'maintenance_record_id' => $record->id,
                'error'                 => $e->getMessage(),
            ]);
        }
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