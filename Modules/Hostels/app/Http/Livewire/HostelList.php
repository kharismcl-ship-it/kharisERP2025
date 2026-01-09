<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\Hostel;

class HostelList extends Component
{
    public function mount()
    {
        $hostels = Hostel::orderBy('name')->get();

        if ($hostels->isEmpty()) {
            abort(404, 'No hostels found');
        }

        if (! request()->route('hostel')) {
            return redirect()->route('hostels.dashboard', ['hostel' => $hostels->first()->slug]);
        }
    }

    public function render()
    {
        $hostels = Hostel::orderBy('name')->get();

        return view('hostels::livewire.hostel-list', [
            'hostels' => $hostels,
        ])->layout('layouts.app');
    }
}
