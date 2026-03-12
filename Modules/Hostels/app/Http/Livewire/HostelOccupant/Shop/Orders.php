<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Sales\Models\PosSale;

class Orders extends Component
{
    use WithPagination;

    public function render()
    {
        $user     = auth('hostel_occupant')->user();
        $occupant = $user->hostelOccupant;

        // Query PosSales that contain the occupant's full name in the notes field.
        // This matches the pattern set by Checkout::placeOrder().
        $orders = PosSale::where('notes', 'like', '%' . $occupant->full_name . '%')
            ->with('lines')
            ->latest()
            ->paginate(10);

        return view('hostels::livewire.hostel-occupant.shop.orders', [
            'orders' => $orders,
        ])->layout('hostels::layouts.occupant');
    }
}
