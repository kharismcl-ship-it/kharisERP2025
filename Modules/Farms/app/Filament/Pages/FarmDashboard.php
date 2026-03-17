<?php

namespace Modules\Farms\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Modules\Farms\Filament\Widgets\FarmDashboardStatsWidget;
use Modules\Farms\Filament\Widgets\FarmsMapWidget;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\CropScoutingRecord;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\LivestockHealthRecord;

class FarmDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 1;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Farm Dashboard';

    protected string $view = 'farms::filament.pages.farm-dashboard';

    public array $alerts = [];

    protected function getHeaderWidgets(): array
    {
        return [
            FarmsMapWidget::class,
            FarmDashboardStatsWidget::class,
        ];
    }

    public function mount(): void
    {
        $companyId    = auth()->user()->current_company_id ?? null;
        $this->alerts = $this->buildAlerts($companyId);
    }

    protected function buildAlerts(?int $companyId): array
    {
        $alerts = [];

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
            ];
        }

        $healthDue = LivestockHealthRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays(7))
            ->count();

        if ($healthDue > 0) {
            $alerts[] = [
                'type'    => 'info',
                'message' => "{$healthDue} livestock health treatment(s) due within 7 days.",
            ];
        }

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
            ];
        }

        $scoutingAlerts = CropScoutingRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNull('resolved_at')
            ->whereIn('finding_type', ['pest', 'disease'])
            ->whereIn('severity', ['critical', 'high'])
            ->count();

        if ($scoutingAlerts > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$scoutingAlerts} unresolved critical/high pest or disease finding(s).",
            ];
        }

        return $alerts;
    }
}
