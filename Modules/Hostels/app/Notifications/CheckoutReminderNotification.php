<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Models\Booking;

class CheckoutReminderNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'checkout_reminder';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Checkout Reminder';
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
            'checkout_date',
        ];
    }

    /**
     * Send checkout reminder to hostel occupant
     *
     * @return void
     */
    public function send(Booking $booking)
    {
        if (! $booking->hostelOccupant) {
            return;
        }

        // Try email first, then SMS if email is disabled
        $channel = $booking->tenant->isCommEnabled('email') ? 'email' :
                  ($booking->tenant->isCommEnabled('sms') ? 'sms' : null);

        if (! $channel) {
            return;
        }

        try {
            // First check if template exists
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'checkout_reminder')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Checkout reminder template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: $channel,
                templateCode: 'checkout_reminder',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'checkout_date' => $booking->check_out_date->format('F j, Y'),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to send checkout reminder: '.$e->getMessage());
        }
    }
}
