<?php

namespace Modules\Finance\Listeners\Hostel;

use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\Payment;
use Modules\Hostels\Events\BookingPaymentCompleted;

class CreateInvoiceForBooking
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BookingPaymentCompleted $event)
    {
        $booking = $event->booking->load(['hostelOccupant', 'room', 'bed', 'hostel']);

        // Use hostel occupant info if available, otherwise use guest info
        $customerName = $booking->hostelOccupant->full_name ?? $booking->guest_full_name ?? 'Unknown Customer';
        $customerEmail = $booking->hostelOccupant->email ?? $booking->guest_email ?? null;
        $customerPhone = $booking->hostelOccupant->phone ?? $booking->guest_phone ?? null;

        // Create an invoice for the booking
        $invoice = Invoice::create([
            'company_id' => $booking->hostel->company_id ?? null,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'customer_type' => 'hostel_occupant',
            'customer_id' => $booking->hostel_occupant_id,
            'invoice_number' => 'INV-'.date('Y').'-'.strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => $booking->check_in_date,
            'status' => 'pending',
            'sub_total' => $booking->total_amount,
            'tax_total' => 0,
            'total' => $booking->total_amount,
            'hostel_id' => $booking->hostel_id,
            'reference' => $booking->booking_reference,
        ]);

        // Create an invoice line for the booking with detailed information
        $description = 'Room Booking';
        if ($booking->room) {
            $description .= ': Room #'.$booking->room->room_number;
        }
        if ($booking->bed) {
            $description .= ' Bed #'.$booking->bed->bed_number;
        }

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => $description,
            'quantity' => 1,
            'unit_price' => $booking->total_amount,
            'line_total' => $booking->total_amount,
        ]);

        // Link invoice to the latest pay intent and post payment from latest successful transaction
        $payIntent = $booking->payIntents()->with(['transactions', 'payMethod'])->latest()->first();
        if ($payIntent) {
            $meta = $payIntent->metadata ?? [];
            $meta['invoice_id'] = $invoice->id;
            $payIntent->update(['metadata' => $meta]);

            $transaction = $payIntent->transactions()->where('status', 'successful')->latest()->first();
            if ($transaction) {
                $paymentMethod = $payIntent->payMethod?->code ?? $payIntent->provider;
                $reference = $transaction->provider_transaction_id ?? $payIntent->reference;
                $paymentDate = $transaction->processed_at ?? $transaction->created_at ?? now();

                Payment::create([
                    'company_id' => $invoice->company_id,
                    'invoice_id' => $invoice->id,
                    'amount' => $transaction->amount,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentMethod,
                    'reference' => $reference,
                ]);

                $totalPaid = $invoice->payments()->sum('amount');
                if ($totalPaid >= $invoice->total) {
                    $invoice->update(['status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                }
            }
        }
    }
}
