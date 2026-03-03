<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Pages\Page;
use Modules\Farms\Filament\Widgets\FarmsMapWidget;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockHealthRecord;

class FarmDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Farm Dashboard';

    protected string $view = 'farms::filament.pages.farm-dashboard';

    public array $stats = [];

    public array $alerts = [];

    protected function getHeaderWidgets(): array
    {
        return [FarmsMapWidget::class];
    }

    public function mount(): void
    {
        $companyId = auth()->user()->current_company_id ?? null;

        $this->stats  = $this->buildStats($companyId);
        $this->alerts = $this->buildAlerts($companyId);
    }

    protected function buildStats(?int $companyId): array
    {
        $year = now()->year;

        $farmQ     = Farm::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $cycleQ    = CropCycle::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $batchQ    = LivestockBatch::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $harvestQ  = HarvestRecord::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $expenseQ  = FarmExpense::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $saleQ     = FarmSale::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $taskQ     = FarmTask::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $budgetQ   = FarmBudget::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $ytdRevenue  = $harvestQ->whereYear('harvest_date', $year)->sum('total_revenue')
                     + $saleQ->whereYear('sale_date', $year)->sum('total_amount');
        $ytdExpenses = $expenseQ->whereYear('expense_date', $year)->sum('amount');
        $netProfit   = $ytdRevenue - $ytdExpenses;

        $totalBudgeted = $budgetQ->where('budget_year', $year)->sum('budgeted_amount');

        return [
            'total_farms'      => $farmQ->count(),
            'active_crops'     => $cycleQ->where('status', 'growing')->count(),
            'livestock_count'  => $batchQ->where('status', 'active')->sum('current_count'),
            'livestock_batches'=> $batchQ->where('status', 'active')->count(),
            'ytd_revenue'      => round($ytdRevenue, 2),
            'ytd_expenses'     => round($ytdExpenses, 2),
            'net_profit'       => round($netProfit, 2),
            'total_budgeted'   => round($totalBudgeted, 2),
            'budget_utilisation' => $totalBudgeted > 0
                ? round(($ytdExpenses / $totalBudgeted) * 100, 1)
                : null,
            'open_tasks'       => $taskQ->whereNull('completed_at')->count(),
            'overdue_tasks'    => $taskQ->whereNull('completed_at')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->count(),
        ];
    }

    protected function buildAlerts(?int $companyId): array
    {
        $alerts = [];

        // Crops ready to harvest (past expected harvest date, still growing)
        $overdueHarvests = CropCycle::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'growing')
            ->whereNotNull('expected_harvest_date')
            ->whereDate('expected_harvest_date', '<', now())
            ->count();

        if ($overdueHarvests > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => "{$overdueHarvests} crop cycle(s) are past their expected harvest date.",
                'link'    => '/farms/crop-cycles',
            ];
        }

        // Livestock health reminders due
        $healthDue = LivestockHealthRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays(7))
            ->count();

        if ($healthDue > 0) {
            $alerts[] = [
                'type'    => 'info',
                'message' => "{$healthDue} livestock health treatment(s) due within 7 days.",
                'link'    => '/farms/livestock-health-records',
            ];
        }

        // Overdue tasks
        $overdueCount = FarmTask::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$overdueCount} overdue farm task(s) need attention.",
                'link'    => '/farms/farm-tasks',
            ];
        }

        // Open scouting alerts (unresolved pest/disease, critical/high severity)
        $scoutingAlerts = \Modules\Farms\Models\CropScoutingRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNull('resolved_at')
            ->whereIn('finding_type', ['pest', 'disease'])
            ->whereIn('severity', ['critical', 'high'])
            ->count();

        if ($scoutingAlerts > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$scoutingAlerts} unresolved critical/high pest or disease finding(s).",
                'link'    => '/farms/crop-scouting',
            ];
        }

        return $alerts;
    }
}