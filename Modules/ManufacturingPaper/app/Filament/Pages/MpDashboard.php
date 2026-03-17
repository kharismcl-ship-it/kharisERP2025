<?php

namespace Modules\ManufacturingPaper\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Modules\ManufacturingPaper\Models\MpEquipmentLog;
use Modules\ManufacturingPaper\Models\MpPlant;
use Modules\ManufacturingPaper\Models\MpProductionBatch;
use Modules\ManufacturingPaper\Models\MpQualityRecord;

class MpDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 0;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'manufacturingpaper::filament.pages.mp-dashboard';

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
        $plantQ  = MpPlant::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $batchQ  = MpProductionBatch::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $qualityQ = MpQualityRecord::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $equipQ  = MpEquipmentLog::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $month       = now()->month;
        $year        = now()->year;
        $totalQuality = $qualityQ->whereMonth('test_date', $month)->whereYear('test_date', $year)->count();
        $failedQuality = $qualityQ->whereMonth('test_date', $month)->whereYear('test_date', $year)->where('passed', false)->count();

        return [
            'active_plants'         => $plantQ->where('status', 'active')->count(),
            'total_plants'          => $plantQ->count(),
            'batches_in_progress'   => $batchQ->where('status', 'in_progress')->count(),
            'batches_completed_mtd' => $batchQ->where('status', 'completed')
                ->whereMonth('end_time', $month)->whereYear('end_time', $year)->count(),
            'planned_volume_mtd'    => $batchQ->whereMonth('created_at', $month)->whereYear('created_at', $year)
                ->sum('quantity_planned'),
            'produced_volume_mtd'   => $batchQ->where('status', 'completed')
                ->whereMonth('end_time', $month)->whereYear('end_time', $year)
                ->sum('quantity_produced'),
            'quality_tests_mtd'     => $totalQuality,
            'quality_fail_rate'     => $totalQuality > 0 ? round(($failedQuality / $totalQuality) * 100, 1) : 0,
            'open_equipment_issues' => $equipQ->whereIn('status', ['open', 'in_progress'])->count(),
            'breakdown_count'       => $equipQ->where('log_type', 'breakdown')
                ->whereIn('status', ['open', 'in_progress'])->count(),
        ];
    }

    protected function buildAlerts(?int $companyId): array
    {
        $alerts = [];

        $openBreakdowns = MpEquipmentLog::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('log_type', 'breakdown')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        if ($openBreakdowns > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$openBreakdowns} open equipment breakdown(s) require attention.",
            ];
        }

        $failedThisMonth = MpQualityRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('passed', false)
            ->whereMonth('test_date', now()->month)
            ->whereYear('test_date', now()->year)
            ->count();

        if ($failedThisMonth > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => "{$failedThisMonth} quality test(s) failed this month.",
            ];
        }

        $staleBatches = MpProductionBatch::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'in_progress')
            ->where('start_time', '<', now()->subDays(3))
            ->count();

        if ($staleBatches > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => "{$staleBatches} production batch(es) have been in progress for more than 3 days.",
            ];
        }

        return $alerts;
    }
}
