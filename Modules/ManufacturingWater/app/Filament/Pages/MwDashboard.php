<?php

namespace Modules\ManufacturingWater\Filament\Pages;

use Filament\Pages\Page;
use Modules\ManufacturingWater\Models\MwDistributionRecord;
use Modules\ManufacturingWater\Models\MwPlant;
use Modules\ManufacturingWater\Models\MwTankLevel;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class MwDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'manufacturingwater::filament.pages.mw-dashboard';

    public array $stats = [];

    public array $alerts = [];

    public function mount(): void
    {
        $companyId = auth()->user()->current_company_id ?? null;

        $this->stats  = $this->buildStats($companyId);
        $this->alerts = $this->buildAlerts($companyId);
    }

    protected function buildStats(?int $companyId): array
    {
        $plantQ   = MwPlant::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $testQ    = MwWaterTestRecord::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $distQ    = MwDistributionRecord::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $tankQ    = MwTankLevel::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $month = now()->month;
        $year  = now()->year;

        $testsThisMonth   = $testQ->whereMonth('test_date', $month)->whereYear('test_date', $year)->count();
        $failedThisMonth  = $testQ->whereMonth('test_date', $month)->whereYear('test_date', $year)
            ->where('passed', false)->count();

        return [
            'active_plants'          => $plantQ->where('status', 'active')->count(),
            'total_plants'           => $plantQ->count(),
            'tests_today'            => $testQ->whereDate('test_date', today())->count(),
            'tests_mtd'              => $testsThisMonth,
            'failed_tests_mtd'       => $failedThisMonth,
            'quality_pass_rate'      => $testsThisMonth > 0
                ? round((($testsThisMonth - $failedThisMonth) / $testsThisMonth) * 100, 1)
                : null,
            'distribution_volume_mtd'=> $distQ->whereMonth('distribution_date', $month)
                ->whereYear('distribution_date', $year)->sum('volume_liters'),
            'distribution_revenue_mtd' => $distQ->whereMonth('distribution_date', $month)
                ->whereYear('distribution_date', $year)->sum('total_amount'),
            'low_tanks'              => $tankQ->whereRaw('(current_level_liters / capacity_liters) * 100 < 20')
                ->whereRaw('capacity_liters > 0')->count(),
        ];
    }

    protected function buildAlerts(?int $companyId): array
    {
        $alerts = [];

        $failedToday = MwWaterTestRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('passed', false)
            ->whereDate('test_date', today())
            ->count();

        if ($failedToday > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$failedToday} water quality test(s) failed today — immediate action required.",
            ];
        }

        $lowTanks = MwTankLevel::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereRaw('capacity_liters > 0')
            ->whereRaw('(current_level_liters / capacity_liters) * 100 < 20')
            ->count();

        if ($lowTanks > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => "{$lowTanks} tank(s) are below 20% capacity.",
            ];
        }

        $noTestToday = MwPlant::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->whereDoesntHave('waterTestRecords', fn ($q) => $q->whereDate('test_date', today()))
            ->count();

        if ($noTestToday > 0) {
            $alerts[] = [
                'type'    => 'info',
                'message' => "{$noTestToday} active plant(s) have not had a water quality test today.",
            ];
        }

        return $alerts;
    }
}
