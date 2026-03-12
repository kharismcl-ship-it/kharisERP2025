<?php

namespace Modules\Farms\Http\Livewire;

use Livewire\Component;
use Modules\Farms\Models\Farm;

class FarmMap extends Component
{
    public Farm $farm;

    public function mount(Farm $farm): void
    {
        $this->farm = $farm->load(['plots', 'livestockBatches', 'cropCycles']);
    }

    public function getPlotsGeoJsonProperty(): string
    {
        $features = $this->farm->plots
            ->filter(fn ($p) => !empty($p->geometry))
            ->map(function ($plot) {
                return [
                    'type'       => 'Feature',
                    'properties' => [
                        'id'     => $plot->id,
                        'name'   => $plot->name ?? 'Plot '.$plot->id,
                        'status' => $plot->status ?? 'active',
                        'area'   => $plot->area,
                    ],
                    'geometry'   => is_array($plot->geometry) ? $plot->geometry : json_decode($plot->geometry, true),
                ];
            })
            ->values();

        return json_encode([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function render()
    {
        return view('farms::livewire.farm-map')
            ->layout('farms::layouts.app');
    }
}
