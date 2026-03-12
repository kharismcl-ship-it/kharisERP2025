<?php

namespace Modules\Farms\Http\Livewire\Reports;

use Illuminate\Support\Carbon;
use Livewire\Component;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Services\FarmService;

class Index extends Component
{
    public Farm $farm;

    public string $activeTab = 'financials';

    public string $fromDate;

    public string $toDate;

    public function mount(Farm $farm): void
    {
        $this->farm     = $farm;
        $this->fromDate = now()->startOfYear()->format('Y-m-d');
        $this->toDate   = now()->format('Y-m-d');
    }

    public function getFinancialsProperty(): array
    {
        $svc     = app(FarmService::class);
        $revenue = $svc->totalRevenue($this->farm, $this->fromDate, $this->toDate);
        $expenses = $svc->totalExpenses($this->farm, $this->fromDate, $this->toDate);

        return [
            'revenue'  => $revenue,
            'expenses' => $expenses,
            'profit'   => $revenue - $expenses,
        ];
    }

    public function getBudgetProperty(): array
    {
        return app(FarmService::class)->budgetVsActual($this->farm, (int) Carbon::parse($this->fromDate)->year);
    }

    public function getCropPnlProperty(): \Illuminate\Support\Collection
    {
        $svc = app(FarmService::class);

        return CropCycle::where('farm_id', $this->farm->id)
            ->whereBetween('planting_date', [$this->fromDate, $this->toDate])
            ->with(['harvestRecords', 'inputApplications', 'activities'])
            ->get()
            ->map(fn ($cycle) => array_merge(
                ['cycle' => $cycle],
                $svc->cropCyclePnL($cycle)
            ));
    }

    public function getLivestockStatsProperty(): \Illuminate\Support\Collection
    {
        $svc = app(FarmService::class);

        return $this->farm->livestockBatches()
            ->with(['feedRecords', 'weightRecords', 'mortalityLogs'])
            ->get()
            ->map(fn ($batch) => [
                'batch'   => $batch,
                'fcr'     => $svc->feedConversionRatio($batch),
                'growth'  => $svc->livestockGrowthRate($batch),
                'deaths'  => $batch->mortalityLogs()
                    ->whereBetween('mortality_date', [$this->fromDate, $this->toDate])
                    ->sum('count'),
            ]);
    }

    public function getTaskStatsProperty(): array
    {
        $base = FarmTask::where('farm_id', $this->farm->id);

        $total     = (clone $base)->count();
        $completed = (clone $base)->whereNotNull('completed_at')->count();
        $open      = (clone $base)->whereNull('completed_at')->count();
        $overdue   = (clone $base)->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->count();

        $byType = (clone $base)->selectRaw('task_type, count(*) as total')
            ->groupBy('task_type')
            ->pluck('total', 'task_type')
            ->toArray();

        return compact('total', 'completed', 'open', 'overdue', 'byType');
    }

    public function render()
    {
        return view('farms::livewire.reports.index')
            ->layout('farms::layouts.app');
    }
}
