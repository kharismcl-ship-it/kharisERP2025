<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Visitors;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\VisitorLog;

class Create extends Component
{
    public string $visitorName = '';

    public string $visitorPhone = '';

    public string $purpose = '';

    public string $expectedArrival = '';

    public ?int $hostelId = null;

    public function mount(): void
    {
        $occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $activeBooking = Booking::where('hostel_occupant_id', $occupantId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest()
            ->first();

        if ($activeBooking) {
            $this->hostelId = $activeBooking->hostel_id;
        }

        $this->expectedArrival = now()->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'visitorName'     => 'required|string|max:255',
            'visitorPhone'    => 'required|string|max:20',
            'purpose'         => 'required|string|max:255',
            'expectedArrival' => 'required|date',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        if (! $this->hostelId) {
            $this->dispatch('notify', type: 'error', message: 'No active booking found. Cannot pre-register visitor without an active booking.');
            return;
        }

        VisitorLog::create([
            'hostel_id'          => $this->hostelId,
            'hostel_occupant_id' => auth('hostel_occupant')->user()->hostel_occupant_id,
            'visitor_name'       => $this->visitorName,
            'visitor_phone'      => $this->visitorPhone,
            'purpose'            => $this->purpose,
            'check_in_at'        => $this->expectedArrival,
            'check_out_at'       => null,
        ]);

        session()->flash('success', 'Visitor pre-registered. They will be confirmed by reception on arrival.');
        $this->redirect(route('hostel_occupant.dashboard'));
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.visitors.create')
            ->layout('hostels::layouts.occupant');
    }
}
