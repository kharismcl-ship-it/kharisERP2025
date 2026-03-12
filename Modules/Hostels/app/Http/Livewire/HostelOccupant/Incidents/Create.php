<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Incidents;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Incident;

class Create extends Component
{
    public string $title = '';

    public string $description = '';

    public string $severity = 'medium';

    public ?int $hostelId = null;

    public ?int $roomId = null;

    public function mount(): void
    {
        $occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $activeBooking = Booking::where('hostel_occupant_id', $occupantId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest()
            ->first();

        if ($activeBooking) {
            $this->hostelId = $activeBooking->hostel_id;
            $this->roomId = $activeBooking->room_id;
        }
    }

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'severity'    => 'required|in:low,medium,high,critical',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if (! $this->hostelId) {
            $this->dispatch('notify', type: 'error', message: 'No active booking found. Cannot file incident without an active booking.');
            return;
        }

        Incident::create([
            'hostel_id'          => $this->hostelId,
            'hostel_occupant_id' => auth('hostel_occupant')->user()->hostel_occupant_id,
            'room_id'            => $this->roomId,
            'title'              => $this->title,
            'description'        => $this->description,
            'severity'           => $this->severity,
            'status'             => 'open',
            'reported_at'        => now(),
        ]);

        session()->flash('success', 'Incident report submitted. Staff will be notified shortly.');
        $this->redirect(route('hostel_occupant.incidents.index'));
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.incidents.create')
            ->layout('hostels::layouts.occupant');
    }
}
