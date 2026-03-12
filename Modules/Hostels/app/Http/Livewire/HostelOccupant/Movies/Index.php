<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Movies;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\HostelMovie;
use Modules\Hostels\Models\HostelMoviePurchase;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $hostelId;
    public int $occupantId;

    public function mount(): void
    {
        $user             = auth('hostel_occupant')->user();
        $this->occupantId = $user->hostel_occupant_id;
        $this->hostelId   = $user->hostelOccupant->hostel_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $movies = HostelMovie::where('is_active', true)
            ->where(function ($q) {
                $q->where('hostel_id', $this->hostelId)
                  ->orWhere('is_globally_available', true);
            })
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(12);

        $purchasedIds = HostelMoviePurchase::where('hostel_occupant_id', $this->occupantId)
            ->where('status', 'paid')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->pluck('hostel_movie_id')
            ->toArray();

        return view('hostels::livewire.hostel-occupant.movies.index', [
            'movies'       => $movies,
            'purchasedIds' => $purchasedIds,
        ])->layout('hostels::layouts.occupant');
    }
}
