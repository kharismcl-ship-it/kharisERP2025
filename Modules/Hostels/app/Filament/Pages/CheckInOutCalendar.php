<?php

namespace Modules\Hostels\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\Booking;

class CheckInOutCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected string $view = 'hostels::filament.pages.check-in-out-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 6;

    public ?string $selectedHostel = null;

    public ?string $selectedRoom = null;

    public ?string $selectedDate = null;

    public array $checkInData = [];

    public array $checkOutData = [];

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadCheckInOutData();
    }

    public function loadCheckInOutData(): void
    {
        $this->checkInData = [];
        $this->checkOutData = [];

        if (! $this->selectedDate) {
            return;
        }

        $date = Carbon::parse($this->selectedDate);

        // Load check-ins for the selected date
        $checkInQuery = Booking::with(['bed.room.hostel', 'tenant'])
            ->where('check_in_date', $this->selectedDate)
            ->whereIn('status', ['confirmed', 'awaiting_payment'])
            ->when($this->selectedHostel, function (Builder $query, $hostelId) {
                $query->whereHas('bed.room', function (Builder $query) use ($hostelId) {
                    $query->where('hostel_id', $hostelId);
                });
            })
            ->when($this->selectedRoom, function (Builder $query, $roomId) {
                $query->whereHas('bed.room', function (Builder $query) use ($roomId) {
                    $query->where('id', $roomId);
                });
            });

        $this->checkInData = $checkInQuery->get()->toArray();

        // Load check-outs for the selected date
        $checkOutQuery = Booking::with(['bed.room.hostel', 'tenant'])
            ->where('check_out_date', $this->selectedDate)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->when($this->selectedHostel, function (Builder $query, $hostelId) {
                $query->whereHas('bed.room', function (Builder $query) use ($hostelId) {
                    $query->where('hostel_id', $hostelId);
                });
            })
            ->when($this->selectedRoom, function (Builder $query, $roomId) {
                $query->whereHas('bed.room', function (Builder $query) use ($roomId) {
                    $query->where('id', $roomId);
                });
            });

        $this->checkOutData = $checkOutQuery->get()->toArray();
    }

    public function getTitle(): string
    {
        return 'Check-In/Out Calendar';
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function navigateToDate(string $direction): void
    {
        $date = Carbon::parse($this->selectedDate);

        if ($direction === 'previous') {
            $date->subDay();
        } else {
            $date->addDay();
        }

        $this->selectedDate = $date->format('Y-m-d');
        $this->loadCheckInOutData();
    }
}
