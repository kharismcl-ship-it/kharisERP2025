<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Models\Booking;

class PaymentReceiptNotification
{
    /**
     * Get the template code for this notification
     */
    public static function getTemplateCode(): string
    {
        return 'payment_receipt';
    }

    /**
     * Get the display name for this notification template
     */
    public static function getTemplateName(): string
    {
        return 'Payment Receipt';
    }

    /**
     * Get the placeholders used in this notification
     */
    public static function getPlaceholders(): array
    {
        return [
            'hostel_occupant_name',
            'booking_reference',
            'amount',
            'date',
        ];
    }

    /**
     * Send payment receipt to hostel occupant
     *
     * @return void
     */
    public function send(Booking $booking, float $amount)
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
            $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'payment_receipt')
                ->where(function ($query) use ($booking) {
                    $query->where('company_id', $booking->tenant->company_id)
                        ->orWhereNull('company_id');
                })
                ->first();

            if (! $template) {
                throw new \Exception('Payment receipt template not found');
            }

            Communication::sendToModel(
                notifiable: $booking->tenant,
                channel: $channel,
                templateCode: 'payment_receipt',
                data: [
                    'tenant_name' => $booking->tenant->full_name,
                    'booking_reference' => $booking->booking_reference,
                    'amount' => number_format($amount, 2),
                    'date' => now()->format('F j, Y'),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Failed to send payment receipt: '.$e->getMessage());
        }
    }
}
