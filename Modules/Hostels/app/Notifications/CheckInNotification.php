<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Models\Booking;

class CheckInNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'check_in_notification';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Check In Notification';
    }

    /**
     * Get the placeholders used in this notification
     */
    public static function getPlaceholders(): array
    {
        return [
            'hostel_occupant_name',
            'hostel_name',
            'room_number',
            'bed_number',
        ];
    }

    /**
     * Send check-in notification to hostel occupant
     *
     * @return void
     */
    public function send(Booking $booking)
    {
        if (! $booking->hostelOccupant || ! $booking->hostelOccupant->isCommEnabled('sms')) {
            return;
        }

        try {
            // First check if template exists
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'check_in_notification')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Check-in notification template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: 'sms',
                templateCode: 'check_in_notification',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'bed_number' => $booking->bed->bed_number ?? 'N/A',
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to send check-in notification: '.$e->getMessage());
        }
    }
}
