<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\FarmWorkerAttendance;

class AdvancedFarmDashboardPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Farm Analytics';

    protected static ?int $navigationSort = 1;

    protected string $view = 'farms::filament.pages.advanced-farm-dashboard';

    public array $stats = [];
    public array $financialData = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $companyId   = Filament::getTenant()?->id;
        $currentYear = now()->year;

        // Active crop cycles
        $activeCycles = CropCycle::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        // Total harvest this season (current year)
        $totalHarvestKg = HarvestRecord::where('company_id', $companyId)
            ->whereYear('harvest_date', $currentYear)
            ->sum('quantity');

        // Total farm revenue this year
        $totalRevenue = FarmSale::where('company_id', $companyId)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_amount');

        // Total expenses this year
        $totalExpenses = FarmExpense::where('company_id', $companyId)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        // Livestock count
        $livestockCount = LivestockBatch::where('company_id', $companyId)
            ->where('status', 'active')
            ->sum('current_count');

        // Active workers today
        $activeWorkersToday = FarmWorkerAttendance::where('company_id', $companyId)
            ->where('attendance_date', today())
            ->where('status', 'present')
            ->count();

        // Unread weather alerts
        $weatherAlerts = 0;
        if (class_exists(\Modules\Farms\Models\FarmWeatherAlert::class)) {
            try {
                $weatherAlerts = \Modules\Farms\Models\FarmWeatherAlert::where('company_id', $companyId)
                    ->where('is_read', false)
                    ->count();
            } catch (\Throwable) {
                // Table may not exist yet
            }
        }

        // Yield vs target for harvested cycles
        $yieldEfficiency = null;
        try {
            $cyclesWithTarget = CropCycle::where('company_id', $companyId)
                ->where('status', 'harvested')
                ->whereYear('end_date', $currentYear)
                ->whereNotNull('expected_yield')
                ->with('harvestRecords')
                ->get();

            if ($cyclesWithTarget->count() > 0) {
                $efficiencies = $cyclesWithTarget->filter(fn ($c) => $c->expected_yield > 0)
                    ->map(fn ($c) => ($c->harvestRecords->sum('quantity') / $c->expected_yield) * 100);
                $yieldEfficiency = $efficiencies->count() > 0 ? $efficiencies->avg() : null;
            }
        } catch (\Throwable) {
            // harmless
        }

        $this->stats = [
            'active_cycles'    => $activeCycles,
            'harvest_kg'       => number_format((float) $totalHarvestKg, 0),
            'revenue'          => 'GHS ' . number_format((float) $totalRevenue, 2),
            'net_profit'       => 'GHS ' . number_format((float) ($totalRevenue - $totalExpenses), 2),
            'livestock'        => number_format((float) $livestockCount, 0),
            'workers_today'    => $activeWorkersToday,
            'weather_alerts'   => $weatherAlerts,
            'yield_efficiency' => $yieldEfficiency !== null ? round($yieldEfficiency, 1) . '%' : 'N/A',
        ];

        // Monthly expense chart data (last 6 months)
        try {
            $this->financialData = FarmExpense::where('company_id', $companyId)
                ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
                ->whereDate('expense_date', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
        } catch (\Throwable) {
            $this->financialData = [];
        }
    }
}