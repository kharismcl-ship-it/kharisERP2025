<?php

namespace Modules\Hostels\Filament\Pages;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class RoomAvailabilityCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'hostels::filament.pages.room-availability-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 5;

    public ?string $selectedHostel = null;

    public ?string $selectedRoom = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?string $viewPeriod = 'month';

    public ?string $availabilityStatus = 'all';

    public array $availabilityData = [];

    public bool $showBookingModal = false;

    public ?string $selectedBedId = null;

    public ?string $bookingCheckInDate = null;

    public ?string $bookingCheckOutDate = null;

    public function mount(): void
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(30)->format('Y-m-d');
        $this->loadAvailabilityData();
    }

    /**
     * Navigation methods for moving through the calendar
     */
    public function navigatePrevious(): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysDiff = $start->diffInDays($end);

        $this->startDate = $start->subDays($daysDiff + 1)->format('Y-m-d');
        $this->endDate = $end->subDays($daysDiff + 1)->format('Y-m-d');
        $this->loadAvailabilityData();
    }

    public function navigateNext(): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysDiff = $start->diffInDays($end);

        $this->startDate = $start->addDays($daysDiff + 1)->format('Y-m-d');
        $this->endDate = $end->addDays($daysDiff + 1)->format('Y-m-d');
        $this->loadAvailabilityData();
    }

    public function navigateToToday(): void
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(30)->format('Y-m-d');
        $this->loadAvailabilityData();
    }

    public function setViewPeriod(string $period): void
    {
        $today = now();

        switch ($period) {
            case 'week':
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->addDays(6)->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->addDays(30)->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->addDays(90)->format('Y-m-d');
                break;
            default:
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->addDays(30)->format('Y-m-d');
        }

        $this->loadAvailabilityData();
    }

    // Form schema for filtering options
    protected function getFilters(): array
    {
        return [
            'selectedHostel' => [
                'label' => 'Hostel',
                'type' => 'select',
                'options' => Hostel::where('status', 'active')->pluck('name', 'id'),
                'onChange' => 'loadAvailabilityData',
            ],
            'selectedRoom' => [
                'label' => 'Room',
                'type' => 'select',
                'options' => $this->selectedHostel
                    ? Room::where('hostel_id', $this->selectedHostel)->pluck('room_number', 'id')
                    : [],
                'onChange' => 'loadAvailabilityData',
            ],
            'viewPeriod' => [
                'label' => 'View Period',
                'type' => 'select',
                'options' => [
                    'week' => 'This Week',
                    'month' => 'This Month',
                    'quarter' => 'This Quarter',
                    'custom' => 'Custom Dates',
                ],
                'onChange' => 'setViewPeriod',
            ],
            'startDate' => [
                'label' => 'Start Date',
                'type' => 'date',
                'required' => true,
                'onChange' => 'loadAvailabilityData',
            ],
            'endDate' => [
                'label' => 'End Date',
                'type' => 'date',
                'required' => true,
                'minDate' => $this->startDate,
                'onChange' => 'loadAvailabilityData',
            ],
            'availabilityStatus' => [
                'label' => 'Availability',
                'type' => 'select',
                'options' => [
                    'all' => 'All Beds',
                    'available' => 'Available Only',
                    'occupied' => 'Occupied Only',
                ],
                'onChange' => 'loadAvailabilityData',
            ],
        ];
    }

    public function loadAvailabilityData(): void
    {
        $this->availabilityData = [];

        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        $query = Bed::with(['room', 'room.hostel', 'bookings'])
            ->when($this->selectedHostel, function (Builder $query, $hostelId) {
                $query->whereHas('room', function (Builder $query) use ($hostelId) {
                    $query->where('hostel_id', $hostelId);
                });
            })
            ->when($this->selectedRoom, function (Builder $query, $roomId) {
                $query->where('room_id', $roomId);
            });

        $beds = $query->get();

        foreach ($beds as $bed) {
            $bedData = [
                'id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'room_number' => $bed->room->room_number,
                'hostel_name' => $bed->room->hostel->name,
                'availability' => [],
            ];

            $currentDate = $start->copy();
            $isBedAvailableForPeriod = false;
            $isBedOccupiedForPeriod = false;

            while ($currentDate->lte($end)) {
                $dateStr = $currentDate->format('Y-m-d');

                // Check if bed is available on this date
                $isAvailable = ! Booking::where('bed_id', $bed->id)
                    ->where(function (Builder $query) use ($dateStr) {
                        $query->where('check_in_date', '<=', $dateStr)
                            ->where('check_out_date', '>=', $dateStr);
                    })
                    ->whereIn('status', ['confirmed', 'checked_in', 'awaiting_payment'])
                    ->exists();

                $bedData['availability'][$dateStr] = [
                    'available' => $isAvailable,
                    'date' => $dateStr,
                    'status' => $isAvailable ? 'available' : 'occupied',
                ];

                // Track overall availability for the period
                if ($isAvailable) {
                    $isBedAvailableForPeriod = true;
                } else {
                    $isBedOccupiedForPeriod = true;
                }

                $currentDate->addDay();
            }

            // Apply availability status filtering
            $shouldIncludeBed = true;

            if ($this->availabilityStatus === 'available') {
                $shouldIncludeBed = $isBedAvailableForPeriod;
            } elseif ($this->availabilityStatus === 'occupied') {
                $shouldIncludeBed = $isBedOccupiedForPeriod;
            }

            if ($shouldIncludeBed) {
                $this->availabilityData[] = $bedData;
            }
        }
    }

    public function getTitle(): string
    {
        return 'Room Availability Calendar';
    }

    public function getHeader(): ?View
    {
        return null;
    }

    /**
     * Create a booking from the availability calendar
     */
    public function createBooking($bedId, $checkInDate, $checkOutDate): void
    {
        // Validate the dates
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);

        if ($checkOut->lte($checkIn)) {
            Notification::make()
                ->danger()
                ->title('Check-out date must be after check-in date')
                ->send();

            return;
        }

        // Check if bed is available for the selected dates
        $isAvailable = ! Booking::where('bed_id', $bedId)
            ->where(function (Builder $query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                    ->orWhere(function (Builder $query) use ($checkInDate, $checkOutDate) {
                        $query->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            })
            ->whereIn('status', ['confirmed', 'checked_in', 'awaiting_payment'])
            ->exists();

        if (! $isAvailable) {
            Notification::make()
                ->danger()
                ->title('Selected bed is not available for the chosen dates')
                ->send();

            return;
        }

        // Get the bed and room information
        $bed = Bed::with('room.hostel')->find($bedId);

        if (! $bed) {
            Notification::make()
                ->danger()
                ->title('Bed not found')
                ->send();

            return;
        }

        // Redirect to the booking creation page with pre-filled data
        $this->redirectRoute('hostels.bookings.create', [
            'hostel' => $bed->room->hostel->id,
            'preSelectedRoomId' => $bed->room_id,
            'preSelectedBedId' => $bedId,
            'preSelectedCheckInDate' => $checkInDate,
            'preSelectedCheckOutDate' => $checkOutDate,
        ]);
    }
}
