<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class Show extends Component
{
    use WithPagination;

    public Hostel $hostel;

    public $selectedRoomType = '';

    public $selectedGenderPolicy = '';

    protected $queryString = ['selectedRoomType', 'selectedGenderPolicy'];

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getRoomsProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)
            ->where('status', 'available')
            ->when($this->selectedRoomType, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($this->selectedGenderPolicy, function ($query, $policy) {
                $query->where('gender_policy', $policy);
            })
            ->with(['beds' => function ($query) {
                $query->where('status', 'available');
            }])
            ->paginate(12); // Paginate rooms to reduce memory usage
    }

    public function render()
    {
        // Cache room types and gender policies to avoid redundant queries
        $roomTypes = cache()->remember("hostel_{$this->hostel->id}_room_types", 3600, function () {
            return Room::where('hostel_id', $this->hostel->id)
                ->where('status', 'available')
                ->distinct()
                ->pluck('type');
        });

        $genderPolicies = cache()->remember("hostel_{$this->hostel->id}_gender_policies", 3600, function () {
            return Room::where('hostel_id', $this->hostel->id)
                ->where('status', 'available')
                ->whereNotNull('gender_policy')
                ->distinct()
                ->pluck('gender_policy');
        });

        return view('hostels::livewire.public.show', [
            'roomTypes' => $roomTypes,
            'genderPolicies' => $genderPolicies,
        ])->layout('hostels::layouts.public');
    }
}
