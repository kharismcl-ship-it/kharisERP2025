<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Livewire\Component;
use Modules\Hostels\Models\Hostel;

class Index extends Component
{
    public $search = '';

    public $location = '';

    public $genderPolicy = '';

    public $minPrice = '';

    public $maxPrice = '';

    protected $queryString = ['search', 'location', 'genderPolicy'];

    public function render()
    {
        $hostels = Hostel::where('status', 'active')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhere('location', 'like', '%'.$search.'%');
                });
            })
            ->when($this->location, function ($query, $location) {
                $query->where('location', 'like', '%'.$location.'%')
                    ->orWhere('city', 'like', '%'.$location.'%');
            })
            ->when($this->genderPolicy, function ($query, $policy) {
                $query->where('gender_policy', $policy);
            })
            ->withCount(['rooms as total_rooms', 'rooms as available_rooms' => function ($query) {
                $query->where('status', 'available');
            }])
            ->paginate(9);

        return view('hostels::livewire.public.index', [
            'hostels' => $hostels,
        ])->layout('hostels::layouts.public');
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->location = '';
        $this->genderPolicy = '';
        $this->minPrice = '';
        $this->maxPrice = '';
    }
}
