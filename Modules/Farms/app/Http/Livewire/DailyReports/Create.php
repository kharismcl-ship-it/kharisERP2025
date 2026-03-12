<?php

namespace Modules\Farms\Http\Livewire\DailyReports;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmDailyReport;
use Modules\Farms\Models\FarmWorker;

class Create extends Component
{
    public Farm $farm;

    public string $reportDate;

    public ?int $workerId = null;

    public string $summary = '';

    public string $activitiesDone = '';

    public string $issuesNoted = '';

    public string $recommendations = '';

    public string $weatherObservation = '';

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
        $this->reportDate = now()->format('Y-m-d');
    }

    public function getWorkersProperty()
    {
        return FarmWorker::where('farm_id', $this->farm->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function rules(): array
    {
        return [
            'reportDate'         => 'required|date',
            'summary'            => 'required|string|min:10',
            'activitiesDone'     => 'required|string',
            'issuesNoted'        => 'nullable|string',
            'recommendations'    => 'nullable|string',
            'weatherObservation' => 'nullable|string',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        FarmDailyReport::create([
            'farm_id'             => $this->farm->id,
            'company_id'          => $this->farm->company_id,
            'farm_worker_id'      => $this->workerId ?: null,
            'report_date'         => $this->reportDate,
            'summary'             => $this->summary,
            'activities_done'     => $this->activitiesDone,
            'issues_noted'        => $this->issuesNoted ?: null,
            'recommendations'     => $this->recommendations ?: null,
            'weather_observation' => $this->weatherObservation ?: null,
            'status'              => 'submitted',
        ]);

        session()->flash('success', 'Daily report submitted.');
        $this->redirect(route('farms.daily-reports.index', $this->farm->slug));
    }

    public function render()
    {
        return view('farms::livewire.daily-reports.create', [
            'workers' => $this->workers,
        ])->layout('farms::layouts.app');
    }
}
