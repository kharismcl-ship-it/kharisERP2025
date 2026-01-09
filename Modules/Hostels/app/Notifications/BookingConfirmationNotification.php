<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Models\Booking;

class BookingConfirmationNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'booking_confirmation';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Booking Confirmation';
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
            'check_out_date',
            'booking_reference',
            'total_amount',
        ];
    }

    /**
     * Send booking confirmation to hostel occupant
     *
     * @return void
     */
    public function send(Booking $booking)
    {
        if (! $booking->hostelOccupant || ! $booking->hostelOccupant->isCommEnabled('email')) {
            return;
        }

        try {
            // First check if template exists
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'booking_confirmation')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Booking confirmation template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: 'email',
                templateCode: 'booking_confirmation',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'hostel_name' => $booking->hostel->name,
                    'room_number' => $booking->room->room_number,
                    'bed_number' => $booking->bed->bed_number ?? 'Not assigned',
                    'check_in_date' => $booking->check_in_date->format('F j, Y'),
                    'check_out_date' => $booking->check_out_date->format('F j, Y'),
                    'booking_reference' => $booking->booking_reference,
                    'total_amount' => number_format($booking->total_amount, 2),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to send booking confirmation: '.$e->getMessage());
        }
    }
}
