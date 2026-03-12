<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Movies;

use Livewire\Component;
use Modules\Hostels\Models\HostelMovie;
use Modules\Hostels\Models\HostelMoviePurchase;

class Watch extends Component
{
    public HostelMovie $movie;
    public bool $hasAccess = false;
    public int $occupantId;

    public function mount(HostelMovie $movie): void
    {
        $this->movie      = $movie;
        $this->occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;
        $this->hasAccess  = $movie->hasAccessFor($this->occupantId);
    }

    public function initiatePayment(): void
    {
        // Create a purchase record
        $purchase = HostelMoviePurchase::create([
            'hostel_movie_id'    => $this->movie->id,
            'hostel_occupant_id' => $this->occupantId,
            'amount_paid'        => $this->movie->price,
            'status'             => 'pending',
            'expires_at'         => now()->addHours(48),
        ]);

        // Mark as paid immediately (placeholder — wire real PaymentsChannel gateway in production)
        $purchase->update(['status' => 'paid', 'paid_at' => now()]);

        $this->hasAccess = true;
        session()->flash('success', 'Payment successful! Enjoy the movie.');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.movies.watch')
            ->layout('hostels::layouts.occupant');
    }
}
