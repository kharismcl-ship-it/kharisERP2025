<?php

namespace Modules\Sales\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Sales\Models\SalesLead;
use Modules\Sales\Models\SalesOpportunity;
use Modules\Sales\Models\SalesOrder;

class SalesDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Sales Dashboard';

    protected string $view = 'sales::filament.pages.sales-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $leadBase  = SalesLead::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $oppBase   = SalesOpportunity::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $orderBase = SalesOrder::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();
        $yearStart  = Carbon::now()->startOfYear();

        $totalLeads    = (clone $leadBase)->count();
        $newLeadsToday = (clone $leadBase)->whereDate('created_at', $today)->count();

        $closedStages = ['closed_won', 'closed_lost'];

        $pipelineValue = (clone $oppBase)
            ->whereNotIn('stage', $closedStages)
            ->selectRaw('SUM(estimated_value * (probability_pct / 100)) as weighted')
            ->value('weighted') ?? 0;

        $revenueMtd = (clone $orderBase)
            ->where('status', 'fulfilled')
            ->whereBetween('fulfilled_at', [$monthStart, $monthEnd])
            ->sum('total');

        $revenueYtd = (clone $orderBase)
            ->where('status', 'fulfilled')
            ->whereBetween('fulfilled_at', [$yearStart, now()])
            ->sum('total');

        $openOpportunities = (clone $oppBase)
            ->whereNotIn('stage', $closedStages)
            ->count();

        $wonCount  = (clone $oppBase)->where('stage', 'closed_won')->count();
        $lostCount = (clone $oppBase)->where('stage', 'closed_lost')->count();
        $winRate   = ($wonCount + $lostCount) > 0
            ? round(($wonCount / ($wonCount + $lostCount)) * 100, 1)
            : 0;

        $topOpportunities = (clone $oppBase)
            ->whereNotIn('stage', $closedStages)
            ->with('contact')
            ->orderByDesc('estimated_value')
            ->limit(5)
            ->get(['id', 'title', 'contact_id', 'stage', 'estimated_value']);

        $ordersByStatus = (clone $orderBase)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return compact(
            'totalLeads',
            'newLeadsToday',
            'pipelineValue',
            'revenueMtd',
            'revenueYtd',
            'openOpportunities',
            'winRate',
            'topOpportunities',
            'ordersByStatus'
        );
    }

    public function getTitle(): string
    {
        return 'Sales Dashboard';
    }
}
