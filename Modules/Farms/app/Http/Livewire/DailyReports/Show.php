<?php

namespace Modules\Farms\Http\Livewire\DailyReports;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmDailyReport;

class Show extends Component
{
    public Farm $farm;

    public FarmDailyReport $report;

    public function mount(Farm $farm, FarmDailyReport $report): void
    {
        $this->farm = $farm;
        $this->report = $report->load('worker');
    }

    public function render()
    {
        return view('farms::livewire.daily-reports.show')
            ->layout('farms::layouts.app');
    }
}
