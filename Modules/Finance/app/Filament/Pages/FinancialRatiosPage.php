<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalLine;

class FinancialRatiosPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 62;

    protected static ?string $navigationLabel = 'Financial Ratios';

    protected string $view = 'finance::filament.pages.financial-ratios';

    public string $period = 'ytd';
    public string $asOf;

    public array $ratios = [];

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
        $this->calculateRatios();
    }

    public function updatedPeriod(): void
    {
        $this->calculateRatios();
    }

    public function updatedAsOf(): void
    {
        $this->calculateRatios();
    }

    protected function calculateRatios(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $asOf      = $this->asOf ?: now()->toDateString();

        $periodStart = match ($this->period) {
            'mtd' => now()->startOfMonth()->toDateString(),
            'qtd' => now()->startOfQuarter()->toDateString(),
            'ytd' => now()->startOfYear()->toDateString(),
            default => now()->startOfYear()->toDateString(),
        };

        // Get account IDs by type
        $accountQuery = Account::query()->when($companyId, fn ($q) => $q->where(
            fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')
        ));

        $assetAccounts    = (clone $accountQuery)->where('type', 'asset')->pluck('id');
        $liabilityAccounts = (clone $accountQuery)->where('type', 'liability')->pluck('id');
        $revenueAccounts  = (clone $accountQuery)->where('type', 'revenue')->pluck('id');
        $expenseAccounts  = (clone $accountQuery)->where('type', 'expense')->pluck('id');

        // Helper: sum net balance for account IDs up to asOf date
        $netBalance = function (array $ids) use ($asOf): float {
            if (empty($ids)) {
                return 0.0;
            }
            $row = JournalLine::whereIn('account_id', $ids)
                ->whereHas('journalEntry', fn ($q) => $q->where('date', '<=', $asOf))
                ->selectRaw('SUM(debit) - SUM(credit) as net')
                ->first();
            return (float) ($row?->net ?? 0);
        };

        // Helper: sum net balance in period
        $periodBalance = function (array $ids) use ($periodStart, $asOf): float {
            if (empty($ids)) {
                return 0.0;
            }
            $row = JournalLine::whereIn('account_id', $ids)
                ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$periodStart, $asOf]))
                ->selectRaw('SUM(debit) - SUM(credit) as net')
                ->first();
            return (float) ($row?->net ?? 0);
        };

        $assetIds     = $assetAccounts->toArray();
        $liabIds      = $liabilityAccounts->toArray();
        $revenueIds   = $revenueAccounts->toArray();
        $expenseIds   = $expenseAccounts->toArray();

        // Balance sheet figures (cumulative to asOf)
        $totalAssets       = abs($netBalance($assetIds));
        $totalLiabilities  = abs($netBalance($liabIds));

        // Approximate current assets / liabilities from account names
        $currentAssetIds = Account::whereIn('id', $assetAccounts)
            ->where(fn ($q) => $q
                ->where('name', 'like', '%cash%')
                ->orWhere('name', 'like', '%receivable%')
                ->orWhere('name', 'like', '%inventory%')
                ->orWhere('name', 'like', '%prepaid%')
                ->orWhere('name', 'like', '%current%')
            )
            ->pluck('id')
            ->toArray();

        $inventoryIds = Account::whereIn('id', $assetAccounts)
            ->where('name', 'like', '%inventory%')
            ->pluck('id')
            ->toArray();

        $currentLiabIds = Account::whereIn('id', $liabilityAccounts)
            ->where(fn ($q) => $q
                ->where('name', 'like', '%payable%')
                ->orWhere('name', 'like', '%current%')
                ->orWhere('name', 'like', '%accrued%')
            )
            ->pluck('id')
            ->toArray();

        $arIds = Account::whereIn('id', $assetAccounts)
            ->where('name', 'like', '%receivable%')
            ->pluck('id')
            ->toArray();

        $apIds = Account::whereIn('id', $liabilityAccounts)
            ->where('name', 'like', '%payable%')
            ->pluck('id')
            ->toArray();

        $currentAssets = abs($netBalance($currentAssetIds)) ?: ($totalAssets * 0.6);
        $inventory     = abs($netBalance($inventoryIds));
        $currentLiabs  = abs($netBalance($currentLiabIds)) ?: ($totalLiabilities * 0.5);
        $arBalance     = abs($netBalance($arIds));
        $apBalance     = abs($netBalance($apIds));

        // Income statement figures (period)
        $revenue = abs($periodBalance($revenueIds));
        $expenses = abs($periodBalance($expenseIds));

        // COGS approximation — first 60% of expenses
        $cogsIds = Account::whereIn('id', $expenseAccounts)
            ->where(fn ($q) => $q
                ->where('name', 'like', '%cost%')
                ->orWhere('name', 'like', '%cogs%')
                ->orWhere('name', 'like', '%goods%')
                ->orWhere('name', 'like', '%purchases%')
            )
            ->pluck('id')
            ->toArray();
        $cogs = count($cogsIds) ? abs($periodBalance($cogsIds)) : ($expenses * 0.6);

        $netIncome     = $revenue - $expenses;
        $workingCapital = $currentAssets - $currentLiabs;

        // Ratios
        $currentRatio = $currentLiabs > 0 ? round($currentAssets / $currentLiabs, 2) : null;
        $quickRatio   = $currentLiabs > 0 ? round(($currentAssets - $inventory) / $currentLiabs, 2) : null;
        $dso          = $revenue > 0 ? round(($arBalance / $revenue) * 365, 1) : null;
        $dpo          = $cogs > 0 ? round(($apBalance / $cogs) * 365, 1) : null;
        $grossMargin  = $revenue > 0 ? round((($revenue - $cogs) / $revenue) * 100, 1) : null;
        $netMargin    = $revenue > 0 ? round(($netIncome / $revenue) * 100, 1) : null;

        $this->ratios = [
            [
                'name'      => 'Current Ratio',
                'value'     => $currentRatio,
                'unit'      => 'x',
                'benchmark' => '1.5x – 3.0x',
                'good_min'  => 1.5,
                'good_max'  => 3.0,
                'status'    => $this->rateRatio($currentRatio, 1.5, 3.0),
                'desc'      => 'Current Assets ÷ Current Liabilities. Measures short-term liquidity.',
                'icon'      => 'heroicon-o-scale',
            ],
            [
                'name'      => 'Quick Ratio',
                'value'     => $quickRatio,
                'unit'      => 'x',
                'benchmark' => '1.0x – 2.0x',
                'good_min'  => 1.0,
                'good_max'  => 2.0,
                'status'    => $this->rateRatio($quickRatio, 1.0, 2.0),
                'desc'      => '(Current Assets - Inventory) ÷ Current Liabilities. Acid test of liquidity.',
                'icon'      => 'heroicon-o-beaker',
            ],
            [
                'name'      => 'DSO (Days)',
                'value'     => $dso,
                'unit'      => ' days',
                'benchmark' => '< 45 days',
                'good_min'  => 0,
                'good_max'  => 45,
                'status'    => $dso !== null ? ($dso <= 45 ? 'good' : ($dso <= 60 ? 'warning' : 'danger')) : 'na',
                'desc'      => 'Days Sales Outstanding — how fast customers pay. Lower is better.',
                'icon'      => 'heroicon-o-clock',
            ],
            [
                'name'      => 'DPO (Days)',
                'value'     => $dpo,
                'unit'      => ' days',
                'benchmark' => '30 – 60 days',
                'good_min'  => 30,
                'good_max'  => 60,
                'status'    => $this->rateRatio($dpo, 30, 60),
                'desc'      => 'Days Payable Outstanding — how long you take to pay suppliers.',
                'icon'      => 'heroicon-o-calendar',
            ],
            [
                'name'      => 'Gross Margin',
                'value'     => $grossMargin,
                'unit'      => '%',
                'benchmark' => '> 30%',
                'good_min'  => 30,
                'good_max'  => 100,
                'status'    => $grossMargin !== null ? ($grossMargin >= 30 ? 'good' : ($grossMargin >= 15 ? 'warning' : 'danger')) : 'na',
                'desc'      => '(Revenue − COGS) ÷ Revenue. Efficiency of core operations.',
                'icon'      => 'heroicon-o-chart-bar',
            ],
            [
                'name'      => 'Net Profit Margin',
                'value'     => $netMargin,
                'unit'      => '%',
                'benchmark' => '> 10%',
                'good_min'  => 10,
                'good_max'  => 100,
                'status'    => $netMargin !== null ? ($netMargin >= 10 ? 'good' : ($netMargin >= 0 ? 'warning' : 'danger')) : 'na',
                'desc'      => 'Net Income ÷ Revenue. Overall profitability after all costs.',
                'icon'      => 'heroicon-o-currency-dollar',
            ],
            [
                'name'      => 'Working Capital',
                'value'     => round($workingCapital, 0),
                'unit'      => ' GHS',
                'benchmark' => 'Positive',
                'good_min'  => 1,
                'good_max'  => PHP_FLOAT_MAX,
                'status'    => $workingCapital > 0 ? 'good' : ($workingCapital >= -50000 ? 'warning' : 'danger'),
                'desc'      => 'Current Assets − Current Liabilities. Operational buffer.',
                'icon'      => 'heroicon-o-banknotes',
            ],
        ];
    }

    protected function rateRatio(?float $value, float $min, float $max): string
    {
        if ($value === null) {
            return 'na';
        }
        if ($value >= $min && $value <= $max) {
            return 'good';
        }
        if ($value >= $min * 0.7 && $value <= $max * 1.3) {
            return 'warning';
        }
        return 'danger';
    }
}