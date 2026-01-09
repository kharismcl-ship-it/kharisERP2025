<?php

namespace Modules\Hostels\Filament\Actions;

use Filament\Actions\Action;
use Modules\Hostels\Models\Booking;

class CheckInHostelOccupantAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'check_in';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Check In')
            ->icon('heroicon-o-arrow-right-start-on-rectangle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Check In Guest')
            ->modalDescription('This will create a hostel occupant record and mark the booking as checked in.')
            ->modalSubmitActionLabel('Check In')
            ->action(function (Booking $booking) {
                // Perform the check-in process
                $booking->checkIn();

                // Show success notification
                $this->successNotificationTitle('Guest Checked In')
                    ->success();
            })
            ->visible(function (Booking $booking) {
                // Only show for bookings that are confirmed but not yet checked in
                return $booking->status === 'confirmed' && ! $booking->hostel_occupant_id;
            });
    }
}
