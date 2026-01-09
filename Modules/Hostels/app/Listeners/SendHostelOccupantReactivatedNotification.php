<?php

namespace Modules\Hostels\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Hostels\Events\HostelOccupantUserReactivated;
use Modules\Hostels\Mail\HostelOccupantReactivatedMail;

class SendHostelOccupantReactivatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(HostelOccupantUserReactivated $event): void
    {
        // Send reactivation email
        if ($event->hostelOccupantUser->email) {
            Mail::to($event->hostelOccupantUser->email)
                ->send(new HostelOccupantReactivatedMail($event->hostelOccupantUser));
        }

        // TODO: Add SMS notification for phone number if needed
    }

    /**
     * Handle a job failure.
     */
    public function failed(HostelOccupantUserReactivated $event, \Throwable $exception): void
    {
        // Log the failure but don't prevent the check-in process
        \Illuminate\Support\Facades\Log::error('Failed to send hostel occupant reactivation notification', [
            'hostel_occupant_user_id' => $event->hostelOccupantUser->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
