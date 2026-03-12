<?php

namespace Modules\Farms\Http\Livewire;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Services\FarmService;

class FarmDashboard extends Component
{
    public Farm $farm;

    public function mount(Farm $farm): void
    {
        $this->farm = $farm->load(['cropCycles', 'livestockBatches', 'dailyReports']);
    }

    public function getActiveCropCyclesProperty()
    {
        return $this->farm->cropCycles()->where('status', 'growing')->get();
    }

    public function getOpenTasksProperty()
    {
        return app(FarmService::class)->openTasksByFarm($this->farm);
    }

    public function getOverdueTasksProperty()
    {
        return app(FarmService::class)->overdueTasksByFarm($this->farm);
    }

    public function getLivestockSummaryProperty()
    {
        return $this->farm->livestockBatches()
            ->where('status', 'active')
            ->selectRaw('animal_type, COUNT(*) as batch_count, SUM(current_count) as total_count')
            ->groupBy('animal_type')
            ->get();
    }

    public function getNetProfitProperty(): float
    {
        $from = now()->startOfMonth()->toDateString();
        $to   = now()->toDateString();

        return app(FarmService::class)->netProfit($this->farm, $from, $to);
    }

    public function getRecentReportsProperty()
    {
        return $this->farm->dailyReports()->with('worker')->latest()->take(5)->get();
    }

    public function render()
    {
        return view('farms::livewire.farm-dashboard')
            ->layout('farms::layouts.app');
    }
}
