<?php

namespace Modules\Farms\Http\Livewire;

use Livewire\Component;
use Modules\Farms\Models\Farm;

class Navigation extends Component
{
    public ?Farm $farm = null;

    public function mount(): void
    {
        // If current route has a farm slug, load it for active link detection
        if (request()->route('farm')) {
            $this->farm = request()->route('farm');
        }
    }

    public function render()
    {
        return view('farms::livewire.navigation');
    }
}
