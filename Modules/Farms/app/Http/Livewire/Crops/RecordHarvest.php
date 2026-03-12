<?php

namespace Modules\Farms\Http\Livewire\Crops;

use Livewire\Component;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Services\FarmService;

class RecordHarvest extends Component
{
    public Farm $farm;

    public CropCycle $cropCycle;

    public string $harvestDate;

    public float $quantity = 0;

    public string $unit = 'kg';

    public float $unitPrice = 0;

    public string $buyerName = '';

    public string $storageLocation = '';

    public function mount(Farm $farm, CropCycle $cropCycle): void
    {
        $this->farm = $farm;
        $this->cropCycle = $cropCycle;
        $this->harvestDate = now()->format('Y-m-d');
        $this->unit = $cropCycle->yield_unit ?? 'kg';
    }

    public function getYieldVsTargetProperty(): ?float
    {
        return app(FarmService::class)->yieldVsTarget($this->cropCycle);
    }

    public function getTotalHarvestedProperty(): float
    {
        return (float) $this->cropCycle->harvestRecords()->sum('quantity');
    }

    protected function rules(): array
    {
        return [
            'harvestDate' => 'required|date',
            'quantity'    => 'required|numeric|min:0.01',
            'unit'        => 'required|string',
        ];
    }

    public function recordHarvest(): void
    {
        $this->validate();

        $data = [
            'harvest_date'     => $this->harvestDate,
            'quantity'         => $this->quantity,
            'unit'             => $this->unit,
            'unit_price'       => $this->unitPrice ?: null,
            'total_revenue'    => $this->unitPrice > 0 ? $this->quantity * $this->unitPrice : null,
            'buyer_name'       => $this->buyerName ?: null,
            'storage_location' => $this->storageLocation ?: null,
        ];

        app(FarmService::class)->recordHarvest($this->cropCycle, $data);

        session()->flash('success', 'Harvest recorded successfully.');
        $this->redirect(route('farms.crops.show', [$this->farm->slug, $this->cropCycle]));
    }

    public function render()
    {
        return view('farms::livewire.crops.record-harvest')
            ->layout('farms::layouts.app');
    }
}
