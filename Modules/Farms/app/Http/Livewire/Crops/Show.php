<?php

namespace Modules\Farms\Http\Livewire\Crops;

use Livewire\Component;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\CropScoutingRecord;
use Modules\Farms\Models\CropInputApplication;
use Modules\Farms\Models\CropActivity;
use Modules\Farms\Models\Farm;
use Modules\Farms\Services\FarmService;

class Show extends Component
{
    public Farm $farm;

    public CropCycle $cropCycle;

    public string $activeTab = 'overview';

    // Scouting modal
    public bool $showScoutingModal = false;

    public string $scoutingType = '';

    public string $scoutingNotes = '';

    public string $scoutingDate;

    // Input modal
    public bool $showInputModal = false;

    public string $inputType = '';

    public string $inputName = '';

    public float $inputQuantity = 0;

    public string $inputUnit = 'kg';

    public float $inputCost = 0;

    public string $inputDate;

    // Activity modal
    public bool $showActivityModal = false;

    public string $activityType = '';

    public string $activityDescription = '';

    public float $activityCost = 0;

    public string $activityDate;

    public function mount(Farm $farm, CropCycle $cropCycle): void
    {
        $this->farm = $farm;
        $this->cropCycle = $cropCycle;
        $this->scoutingDate = now()->format('Y-m-d');
        $this->inputDate = now()->format('Y-m-d');
        $this->activityDate = now()->format('Y-m-d');
    }

    public function getPnlProperty(): array
    {
        return app(FarmService::class)->cropCyclePnL($this->cropCycle);
    }

    public function getYieldVsTargetProperty(): ?float
    {
        return app(FarmService::class)->yieldVsTarget($this->cropCycle);
    }

    public function getScoutingRecordsProperty()
    {
        return $this->cropCycle->scoutingRecords()->latest('scouting_date')->get();
    }

    public function getInputApplicationsProperty()
    {
        return $this->cropCycle->inputApplications()->latest('application_date')->get();
    }

    public function getActivitiesProperty()
    {
        return $this->cropCycle->activities()->latest('activity_date')->get();
    }

    public function saveScouting(): void
    {
        $this->validate([
            'scoutingDate'  => 'required|date',
            'scoutingNotes' => 'required|string',
        ]);

        CropScoutingRecord::create([
            'crop_cycle_id' => $this->cropCycle->id,
            'farm_id'       => $this->farm->id,
            'company_id'    => $this->farm->company_id,
            'scouting_date' => $this->scoutingDate,
            'notes'         => $this->scoutingNotes,
            'scouting_type' => $this->scoutingType ?: null,
        ]);

        $this->showScoutingModal = false;
        $this->scoutingNotes = '';
        $this->dispatch('notify', type: 'success', message: 'Scouting record saved.');
    }

    public function saveInput(): void
    {
        $this->validate([
            'inputDate'     => 'required|date',
            'inputName'     => 'required|string',
            'inputQuantity' => 'required|numeric|min:0',
        ]);

        CropInputApplication::create([
            'crop_cycle_id'    => $this->cropCycle->id,
            'farm_id'          => $this->farm->id,
            'company_id'       => $this->farm->company_id,
            'application_date' => $this->inputDate,
            'input_type'       => $this->inputType ?: 'other',
            'input_name'       => $this->inputName,
            'quantity'         => $this->inputQuantity,
            'unit'             => $this->inputUnit,
            'total_cost'       => $this->inputCost,
        ]);

        $this->showInputModal = false;
        $this->inputName = '';
        $this->inputQuantity = 0;
        $this->dispatch('notify', type: 'success', message: 'Input application logged.');
    }

    public function saveActivity(): void
    {
        $this->validate([
            'activityDate'        => 'required|date',
            'activityDescription' => 'required|string',
        ]);

        CropActivity::create([
            'crop_cycle_id' => $this->cropCycle->id,
            'farm_id'       => $this->farm->id,
            'company_id'    => $this->farm->company_id,
            'activity_date' => $this->activityDate,
            'activity_type' => $this->activityType ?: 'other',
            'description'   => $this->activityDescription,
            'cost'          => $this->activityCost,
        ]);

        $this->showActivityModal = false;
        $this->activityDescription = '';
        $this->activityCost = 0;
        $this->dispatch('notify', type: 'success', message: 'Activity logged.');
    }

    public function render()
    {
        return view('farms::livewire.crops.show')
            ->layout('farms::layouts.app');
    }
}
