<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Models\Booking;

class PreArrivalNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'hostel_pre_arrival_welcome';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Pre-Arrival Welcome';
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
            'check_in_date',
            'check_in_time',
            'hostel_address',
            'hostel_phone',
            'hostel_email',
            'directions',
        ];
    }

    /**
     * Send pre-arrival welcome notification to hostel occupant
     *
     * @return void
     */
    public function sendWelcome(Booking $booking)
    {
        if (! $booking->hostelOccupant || ! $booking->hostelOccupant->isCommEnabled('email')) {
            return;
        }

        try {
            // First check if template exists
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'hostel_pre_arrival_welcome')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Pre-arrival welcome template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: 'email',
                templateCode: 'hostel_pre_arrival_welcome',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'bed_number' => $booking->bed->bed_number ?? 'Not assigned',
                    'check_in_date' => $booking->check_in_date->format('F j, Y'),
                    'check_in_time' => '2:00 PM', // Default check-in time
                    'hostel_address' => $booking->hostel->location ?? 'Address not specified',
                    'hostel_phone' => $booking->hostel->contact_phone ?? 'Not available',
                    'hostel_email' => $booking->hostel->contact_email ?? 'Not available',
                    'directions' => $this->getDirections($booking->hostel),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to send pre-arrival welcome: '.$e->getMessage());
        }
    }

    /**
     * Send pre-arrival reminder notification (3 days before)
     *
     * @return void
     */
    public function sendReminder(Booking $booking)
    {
        if (! $booking->tenant || ! $booking->tenant->isCommEnabled('sms')) {
            return;
        }

        try {
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'hostel_pre_arrival_reminder')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Pre-arrival reminder template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: 'sms',
                templateCode: 'hostel_pre_arrival_reminder',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'bed_number' => $booking->bed->bed_number ?? 'Not assigned',
                    'check_in_date' => $booking->check_in_date->format('F j, Y'),
                    'hostel_phone' => $booking->hostel->contact_phone ?? 'Not available',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send pre-arrival reminder: '.$e->getMessage());
        }
    }

    /**
     * Send final pre-arrival reminder notification (1 day before)
     *
     * @return void
     */
    public function sendFinalReminder(Booking $booking)
    {
        if (! $booking->tenant || ! $booking->tenant->isCommEnabled('sms')) {
            return;
        }

        try {
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'hostel_pre_arrival_final')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Pre-arrival final reminder template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: 'sms',
                templateCode: 'hostel_pre_arrival_final',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'check_in_time' => '2:00 PM', // Default check-in time
                    'hostel_phone' => $booking->hostel->contact_phone ?? 'Not available',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send final pre-arrival reminder: '.$e->getMessage());
        }
    }

    /**
     * Generate directions text based on hostel location
     */
    protected function getDirections($hostel): string
    {
        if (! $hostel->location) {
            return 'Please contact the hostel for directions.';
        }

        // Simple directions based on common hostel locations
        $directions = 'From city center: Take a taxi or Uber to '.$hostel->location;

        if ($hostel->latitude && $hostel->longitude) {
            $directions .= "\nGPS Coordinates: ".$hostel->latitude.', '.$hostel->longitude;
        }

        return $directions;
    }
}
