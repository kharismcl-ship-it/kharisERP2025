<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Visitors;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\VisitorLog;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $visitors = VisitorLog::where('hostel_occupant_id', $occupantId)
            ->latest()
            ->paginate(15);

        return view('hostels::livewire.hostel-occupant.visitors.index', [
            'visitors' => $visitors,
        ])->layout('hostels::layouts.occupant');
    }
}
