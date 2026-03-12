<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Books;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\HostelBookOrder;

class Orders extends Component
{
    use WithPagination;

    public function render()
    {
        $occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $orders = HostelBookOrder::where('hostel_occupant_id', $occupantId)
            ->with(['items.book'])
            ->latest()
            ->paginate(10);

        return view('hostels::livewire.hostel-occupant.books.orders', [
            'orders' => $orders,
        ])->layout('hostels::layouts.occupant');
    }
}
