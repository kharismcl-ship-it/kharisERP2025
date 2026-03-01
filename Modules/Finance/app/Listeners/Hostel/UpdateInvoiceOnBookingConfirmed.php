<?php

namespace Modules\Finance\Listeners\Hostel;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Hostels\Events\BookingConfirmed;

class UpdateInvoiceOnBookingConfirmed
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        $invoice = Invoice::where('hostel_id', $booking->hostel_id)
            ->where(function ($q) use ($booking) {
                $q->where('customer_id', $booking->hostel_occupant_id)
                    ->orWhere('customer_type', 'hostel_occupant');
            })
            ->whereIn('status', ['draft', 'pending'])
            ->latest()
            ->first();

        if (! $invoice) {
            Log::info('UpdateInvoiceOnBookingConfirmed: no draft invoice found', [
                'booking_id' => $booking->id,
                'hostel_id'  => $booking->hostel_id,
            ]);

            return;
        }

        $invoice->update(['status' => 'sent']);

        Log::info('UpdateInvoiceOnBookingConfirmed: invoice marked sent', [
            'invoice_id'     => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'booking_id'     => $booking->id,
        ]);
    }
}
