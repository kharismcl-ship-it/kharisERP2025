<?php

namespace Modules\Hostels\Services\Automation;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Core\Models\AutomationSetting;
use Modules\Hostels\Models\Booking;

class OverdueChargeReminderHandler
{
    public function execute(AutomationSetting $setting): array
    {
        // Target bookings that are checked-in or confirmed with unpaid/partial payment balances
        $overdueBookings = Booking::query()
            ->with(['hostelOccupant', 'hostel'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereIn('payment_status', ['pending', 'partial'])
            ->where('check_in_date', '<=', now())
            ->when($setting->company_id, fn ($q) => $q->where('company_id', $setting->company_id))
            ->get();

        $sent = 0;

        foreach ($overdueBookings as $booking) {
            $occupant = $booking->hostelOccupant;

            if (! $occupant || ! $occupant->phone) {
                continue;
            }

            $balance = method_exists($booking, 'getOutstandingAmount')
                ? $booking->getOutstandingAmount()
                : ($booking->total_amount - $booking->amount_paid);

            if ($balance <= 0) {
                continue;
            }

            try {
                app(CommunicationService::class)->sendToContact(
                    channel: 'sms',
                    toEmail: null,
                    toPhone: $occupant->phone,
                    subject: null,
                    templateCode: 'hostel_overdue_charge_reminder',
                    data: [
                        'name'              => $occupant->full_name ?? $occupant->first_name,
                        'hostel_name'       => $booking->hostel?->name ?? 'Hostel',
                        'outstanding_amount' => number_format($balance, 2),
                        'booking_reference' => $booking->booking_reference ?? '',
                    ]
                );

                $sent++;
            } catch (\Exception $e) {
                Log::warning('Overdue charge reminder SMS failed', [
                    'booking_id'  => $booking->id,
                    'occupant_id' => $occupant->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return [
            'success'          => true,
            'records_processed' => $sent,
            'details'          => [
                'overdue_bookings_found' => $overdueBookings->count(),
                'reminders_sent'         => $sent,
            ],
        ];
    }
}