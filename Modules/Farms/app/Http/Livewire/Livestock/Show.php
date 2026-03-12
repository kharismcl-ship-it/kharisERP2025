<?php

namespace Modules\Farms\Http\Livewire\Livestock;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockFeedRecord;
use Modules\Farms\Models\LivestockHealthRecord;
use Modules\Farms\Models\LivestockMortalityLog;
use Modules\Farms\Models\LivestockWeightRecord;
use Modules\Farms\Services\FarmService;

class Show extends Component
{
    public Farm $farm;

    public LivestockBatch $batch;

    public string $activeTab = 'summary';

    // Feed modal
    public bool $showFeedModal = false;
    public string $feedDate;
    public float $feedQuantityKg = 0;
    public string $feedType = '';
    public float $feedCost = 0;

    // Weight modal
    public bool $showWeightModal = false;
    public string $weightDate;
    public float $avgWeightKg = 0;

    // Mortality modal
    public bool $showMortalityModal = false;
    public string $mortalityDate;
    public int $mortalityCount = 1;
    public string $mortalityCause = '';

    // Health modal
    public bool $showHealthModal = false;
    public string $healthDate;
    public string $healthEventType = 'vaccination';
    public string $healthDescription = '';
    public ?string $nextDueDate = null;

    public function mount(Farm $farm, LivestockBatch $batch): void
    {
        $this->farm = $farm;
        $this->batch = $batch;
        $this->feedDate = now()->format('Y-m-d');
        $this->weightDate = now()->format('Y-m-d');
        $this->mortalityDate = now()->format('Y-m-d');
        $this->healthDate = now()->format('Y-m-d');
    }

    public function getHealthSummaryProperty(): array
    {
        return app(FarmService::class)->livestockHealthSummary($this->batch);
    }

    public function getGrowthRateProperty(): ?float
    {
        return app(FarmService::class)->livestockGrowthRate($this->batch);
    }

    public function getFcrProperty(): ?float
    {
        return app(FarmService::class)->feedConversionRatio($this->batch);
    }

    public function logFeed(): void
    {
        $this->validate([
            'feedDate'         => 'required|date',
            'feedQuantityKg'   => 'required|numeric|min:0.01',
        ]);

        LivestockFeedRecord::create([
            'livestock_batch_id' => $this->batch->id,
            'farm_id'            => $this->farm->id,
            'company_id'         => $this->farm->company_id,
            'feed_date'          => $this->feedDate,
            'feed_type'          => $this->feedType ?: null,
            'quantity_kg'        => $this->feedQuantityKg,
            'cost'               => $this->feedCost ?: null,
        ]);

        $this->showFeedModal = false;
        $this->feedQuantityKg = 0;
        $this->dispatch('notify', type: 'success', message: 'Feed logged.');
    }

    public function logWeight(): void
    {
        $this->validate([
            'weightDate'   => 'required|date',
            'avgWeightKg'  => 'required|numeric|min:0.01',
        ]);

        LivestockWeightRecord::create([
            'livestock_batch_id' => $this->batch->id,
            'farm_id'            => $this->farm->id,
            'company_id'         => $this->farm->company_id,
            'record_date'        => $this->weightDate,
            'avg_weight_kg'      => $this->avgWeightKg,
        ]);

        $this->showWeightModal = false;
        $this->dispatch('notify', type: 'success', message: 'Weight recorded.');
    }

    public function logMortality(): void
    {
        $this->validate([
            'mortalityDate'  => 'required|date',
            'mortalityCount' => 'required|integer|min:1',
        ]);

        LivestockMortalityLog::create([
            'livestock_batch_id' => $this->batch->id,
            'farm_id'            => $this->farm->id,
            'company_id'         => $this->farm->company_id,
            'mortality_date'     => $this->mortalityDate,
            'count'              => $this->mortalityCount,
            'cause'              => $this->mortalityCause ?: null,
        ]);

        // Decrement batch count
        $newCount = max(0, $this->batch->current_count - $this->mortalityCount);
        $this->batch->update(['current_count' => $newCount]);
        $this->batch->refresh();

        $this->showMortalityModal = false;
        $this->mortalityCount = 1;
        $this->dispatch('notify', type: 'success', message: 'Mortality logged. Batch count updated.');
    }

    public function logHealthEvent(): void
    {
        $this->validate([
            'healthDate'        => 'required|date',
            'healthEventType'   => 'required|string',
            'healthDescription' => 'required|string',
        ]);

        LivestockHealthRecord::create([
            'livestock_batch_id' => $this->batch->id,
            'farm_id'            => $this->farm->id,
            'company_id'         => $this->farm->company_id,
            'event_date'         => $this->healthDate,
            'event_type'         => $this->healthEventType,
            'description'        => $this->healthDescription,
            'next_due_date'      => $this->nextDueDate ?: null,
        ]);

        $this->showHealthModal = false;
        $this->healthDescription = '';
        $this->dispatch('notify', type: 'success', message: 'Health event logged.');
    }

    public function render()
    {
        return view('farms::livewire.livestock.show')
            ->layout('farms::layouts.app');
    }
}
