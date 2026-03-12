<?php

namespace Modules\Farms\Http\Livewire\Requests;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmRequest;

class Show extends Component
{
    public Farm $farm;

    public FarmRequest $request;

    public function mount(Farm $farm, FarmRequest $request): void
    {
        $this->farm = $farm;
        $this->request = $request->load('items');
    }

    public function render()
    {
        return view('farms::livewire.requests.show')
            ->layout('farms::layouts.app');
    }
}
