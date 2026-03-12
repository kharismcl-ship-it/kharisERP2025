<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\WhatsAppGroups;

use Livewire\Component;
use Modules\Hostels\Models\HostelWhatsAppGroup;

class Index extends Component
{
    public int $hostelId;
    public int $occupantId;
    public array $joinedGroupIds = [];

    public function mount(): void
    {
        $user           = auth('hostel_occupant')->user();
        $this->occupantId = $user->hostel_occupant_id;
        $occupant       = $user->hostelOccupant;
        $this->hostelId = $occupant->hostel_id;

        $this->loadJoinedGroupIds();
    }

    protected function loadJoinedGroupIds(): void
    {
        $this->joinedGroupIds = HostelWhatsAppGroup::where('hostel_id', $this->hostelId)
            ->whereHas('occupants', fn ($q) => $q->where('hostel_occupant_id', $this->occupantId))
            ->pluck('id')
            ->toArray();
    }

    public function joinGroup(int $groupId): void
    {
        $group = HostelWhatsAppGroup::where('hostel_id', $this->hostelId)
            ->where('is_active', true)
            ->findOrFail($groupId);

        $alreadyJoined = $group->occupants()
            ->where('hostel_occupant_id', $this->occupantId)
            ->exists();

        if (! $alreadyJoined) {
            $group->occupants()->attach($this->occupantId);
            $this->loadJoinedGroupIds();
            session()->flash('success', 'You have joined "' . $group->name . '".');
        }
    }

    public function render()
    {
        $groups = HostelWhatsAppGroup::where('hostel_id', $this->hostelId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hostels::livewire.hostel-occupant.whatsapp-groups.index', [
            'groups' => $groups,
        ])->layout('hostels::layouts.occupant');
    }
}
