<?php

namespace Modules\Farms\Services;

use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class FarmService
{
    /**
     * Mark a crop cycle as harvested and record the harvest.
     */
    public function recordHarvest(CropCycle $cycle, array $harvestData): HarvestRecord
    {
        $record = HarvestRecord::create(array_merge($harvestData, [
            'farm_id'       => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'company_id'    => $cycle->company_id,
        ]));

        // Auto-mark cycle as harvested when first harvest is recorded
        if ($cycle->status !== 'harvested') {
            $cycle->update([
                'status'              => 'harvested',
                'actual_harvest_date' => $record->harvest_date,
            ]);
        }

        return $record;
    }

    /**
     * Update livestock batch count (mortality, sales, etc.).
     */
    public function updateLivestockCount(LivestockBatch $batch, int $newCount, string $reason = ''): void
    {
        $batch->update(['current_count' => $newCount]);

        if ($newCount === 0) {
            $batch->update(['status' => 'sold']);
        }
    }

    /**
     * Total farm revenue (all harvest records).
     */
    public function totalRevenue(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        $query = $farm->harvestRecords();
        if ($from && $to) {
            $query->whereBetween('harvest_date', [$from, $to]);
        }
        return (float) $query->sum('total_revenue');
    }

    /**
     * Total farm expenses.
     */
    public function totalExpenses(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        $query = $farm->expenses();
        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }
        return (float) $query->sum('amount');
    }

    /**
     * Net profit for a farm within a date range.
     */
    public function netProfit(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        return $this->totalRevenue($farm, $from, $to) - $this->totalExpenses($farm, $from, $to);
    }

    /**
     * Full P&L breakdown for a single crop cycle.
     */
    public function cropCyclePnL(CropCycle $cycle): array
    {
        $revenue       = (float) $cycle->harvestRecords()->sum('total_revenue');
        $inputCost     = (float) $cycle->inputApplications()->sum('total_cost');
        $activityCost  = (float) $cycle->activities()->sum('cost');
        $otherExpense  = (float) FarmExpense::where('crop_cycle_id', $cycle->id)->sum('amount');
        $totalCost     = $inputCost + $activityCost + $otherExpense;
        $netProfit     = $revenue - $totalCost;
        $totalHarvested = (float) $cycle->harvestRecords()->sum('quantity');
        $yieldPct      = ($cycle->expected_yield && $cycle->expected_yield > 0)
            ? round(($totalHarvested / $cycle->expected_yield) * 100, 1)
            : null;

        return compact('revenue', 'inputCost', 'activityCost', 'otherExpense', 'totalCost', 'netProfit', 'yieldPct');
    }

    /**
     * Yield achievement as a percentage: actual / expected * 100.
     */
    public function yieldVsTarget(CropCycle $cycle): ?float
    {
        if (! $cycle->expected_yield || $cycle->expected_yield <= 0) {
            return null;
        }
        $actual = (float) $cycle->harvestRecords()->sum('quantity');
        return round(($actual / $cycle->expected_yield) * 100, 1);
    }

    /**
     * Cost per unit of harvest output.
     */
    public function costPerUnit(CropCycle $cycle): ?float
    {
        $totalHarvested = (float) $cycle->harvestRecords()->sum('quantity');
        if ($totalHarvested <= 0) {
            return null;
        }
        $pnl = $this->cropCyclePnL($cycle);
        return round($pnl['totalCost'] / $totalHarvested, 4);
    }

    /**
     * Recent health summary for a livestock batch.
     */
    public function livestockHealthSummary(LivestockBatch $batch): array
    {
        $recentEvents = $batch->healthRecords()
            ->orderByDesc('event_date')
            ->limit(5)
            ->get();

        $nextDue = $batch->healthRecords()
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', now()->toDateString())
            ->orderBy('next_due_date')
            ->first();

        return ['recent_events' => $recentEvents, 'next_due' => $nextDue];
    }

    /**
     * Average daily weight gain (kg/day) from weight records.
     */
    public function livestockGrowthRate(LivestockBatch $batch): ?float
    {
        $records = $batch->weightRecords()->orderBy('record_date')->get();
        if ($records->count() < 2) {
            return null;
        }
        $first = $records->first();
        $last  = $records->last();
        $days  = $first->record_date->diffInDays($last->record_date);
        if ($days <= 0) {
            return null;
        }
        return round(($last->avg_weight_kg - $first->avg_weight_kg) / $days, 4);
    }

    /**
     * Feed conversion ratio: total feed kg / total weight gained kg.
     */
    public function feedConversionRatio(LivestockBatch $batch): ?float
    {
        $totalFeedKg    = (float) $batch->feedRecords()->sum('quantity_kg');
        $weightGained   = null;
        $records = $batch->weightRecords()->orderBy('record_date')->get();
        if ($records->count() >= 2) {
            $weightGained = $records->last()->avg_weight_kg - $records->first()->avg_weight_kg;
        }
        if (! $totalFeedKg || ! $weightGained || $weightGained <= 0) {
            return null;
        }
        return round($totalFeedKg / $weightGained, 2);
    }

    // ── Phase 4 — Farm Tasks & HR Workers ─────────────────────────────────

    /**
     * Total labour cost for a crop cycle (activity worker costs).
     */
    public function labourCostForCycle(CropCycle $cycle): float
    {
        return (float) $cycle->activities()->sum('cost');
    }

    /**
     * All open (incomplete) tasks for a farm.
     */
    public function openTasksByFarm(Farm $farm): \Illuminate\Database\Eloquent\Collection
    {
        return FarmTask::where('farm_id', $farm->id)
            ->whereNull('completed_at')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * All overdue tasks for a farm (past due date and not completed).
     */
    public function overdueTasksByFarm(Farm $farm): \Illuminate\Database\Eloquent\Collection
    {
        return FarmTask::where('farm_id', $farm->id)
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
    }

    // ── Finance GL Integration ─────────────────────────────────────────────

    /**
     * Post a single FarmExpense to the Finance General Ledger as a journal entry.
     * DR: Expense account  /  CR: Cash/Bank account
     * Returns null if Finance module is unavailable or amount is zero.
     */
    public function createExpenseJournalEntry(FarmExpense $expense): ?JournalEntry
    {
        if (! class_exists(JournalEntry::class) || ! $expense->amount || $expense->amount <= 0) {
            return null;
        }

        $entry = JournalEntry::create([
            'company_id'  => $expense->company_id,
            'date'        => $expense->expense_date,
            'reference'   => 'FARM-EXP-' . $expense->id,
            'description' => "Farm expense: {$expense->category} — {$expense->farm?->name}",
        ]);

        // DR Expense account
        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $this->resolveAccountId('expense', $expense->company_id),
            'debit'            => $expense->amount,
            'credit'           => 0,
        ]);

        // CR Cash/Bank account
        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'account_id'       => $this->resolveAccountId('cash', $expense->company_id),
            'debit'            => 0,
            'credit'           => $expense->amount,
        ]);

        return $entry;
    }

    /**
     * Batch-post all FarmExpenses for a farm within a date range to Finance GL.
     * Skips expenses that already have a journal_entry_reference set.
     */
    public function postAllExpensesToFinance(Farm $farm, string $from, string $to): int
    {
        if (! class_exists(JournalEntry::class)) {
            return 0;
        }

        $expenses = FarmExpense::where('farm_id', $farm->id)
            ->whereBetween('expense_date', [$from, $to])
            ->get();

        $posted = 0;
        foreach ($expenses as $expense) {
            if ($this->createExpenseJournalEntry($expense)) {
                $posted++;
            }
        }

        return $posted;
    }

    /**
     * Resolve a Finance account ID by semantic type (expense, cash, revenue, receivable).
     * Mirrors IntegrationService::getAccountId() pattern.
     */
    private function resolveAccountId(string $type, ?int $companyId): ?int
    {
        $map = [
            'expense'    => ['code' => 'EXP',  'type' => 'expense'],
            'cash'       => ['code' => 'CASH', 'type' => 'asset'],
            'revenue'    => ['code' => 'REV',  'type' => 'income'],
            'receivable' => ['code' => 'AR',   'type' => 'asset'],
        ];

        $hint = $map[$type] ?? null;

        $query = Account::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        if ($hint) {
            $account = (clone $query)->where('code', $hint['code'])->first()
                    ?? (clone $query)->where('type', $hint['type'])->first();
            if ($account) {
                return $account->id;
            }
        }

        // Last-resort: create a minimal account so the journal doesn't fail
        if ($companyId) {
            $fallbackCode = strtoupper($type);
            return Account::firstOrCreate(
                ['company_id' => $companyId, 'code' => $fallbackCode],
                ['name' => ucfirst($type) . ' Account', 'type' => $hint['type'] ?? 'asset']
            )->id;
        }

        return null;
    }

    // ── Phase 5 — Financial Integration ───────────────────────────────────

    /**
     * Create a Finance invoice from a FarmSale and link it back.
     */
    public function createSaleInvoice(FarmSale $sale): ?\Modules\Finance\Models\Invoice
    {
        // Guard: Finance module must be available
        if (! class_exists(\Modules\Finance\Models\Invoice::class)) {
            return null;
        }

        $invoice = \Modules\Finance\Models\Invoice::create([
            'company_id'   => $sale->company_id,
            'invoice_date' => $sale->sale_date,
            'due_date'     => $sale->sale_date->addDays(30),
            'status'       => 'draft',
            'notes'        => "Farm sale: {$sale->product_name} ({$sale->farm->name})",
        ]);

        // Add invoice line
        \Modules\Finance\Models\InvoiceLine::create([
            'invoice_id'  => $invoice->id,
            'description' => "{$sale->quantity} {$sale->unit} of {$sale->product_name}",
            'quantity'    => $sale->quantity,
            'unit_price'  => $sale->unit_price,
            'amount'      => $sale->total_amount,
        ]);

        $sale->update(['invoice_id' => $invoice->id, 'payment_status' => 'pending']);

        return $invoice;
    }

    /**
     * Budget vs actual summary for a farm, optionally for a specific year.
     */
    public function budgetVsActual(Farm $farm, ?int $year = null): array
    {
        $year  = $year ?? now()->year;
        $budgets = FarmBudget::where('farm_id', $farm->id)
            ->where('budget_year', $year)
            ->get();

        $totalBudgeted = $budgets->sum('budgeted_amount');
        $totalActual   = $budgets->sum('actual_amount');
        $variance      = $totalActual - $totalBudgeted;
        $variancePct   = $totalBudgeted > 0
            ? round(($variance / $totalBudgeted) * 100, 1)
            : null;

        $byCategory = $budgets->groupBy('category')->map(fn ($group) => [
            'budgeted' => $group->sum('budgeted_amount'),
            'actual'   => $group->sum('actual_amount'),
            'variance' => $group->sum('actual_amount') - $group->sum('budgeted_amount'),
        ])->toArray();

        return compact('totalBudgeted', 'totalActual', 'variance', 'variancePct', 'byCategory');
    }

    /**
     * List workers for a farm who are currently on leave (HR integration).
     */
    public function workersOnLeave(Farm $farm): array
    {
        $workers = FarmWorker::where('farm_id', $farm->id)
            ->whereNotNull('employee_id')
            ->with('employee')
            ->get();

        return $workers->filter(function ($worker) {
            return \Modules\HR\Models\LeaveRequest::where('employee_id', $worker->employee_id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->exists();
        })->values()->all();
    }
}
