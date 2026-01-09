<?php

namespace Modules\Hostels\Services;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Services\IntegrationService as FinanceService;
use Modules\Hostels\Models\Booking;

class FinanceIntegrationAdapter
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Create an invoice for a hostel booking
     *
     * @return \Modules\Finance\Models\Invoice
     */
    public function createBookingInvoice(Booking $booking)
    {
        try {
            $customerName = $booking->hostelOccupant->full_name ?? $booking->guest_full_name ?? 'Unknown Customer';
            $customerEmail = $booking->hostelOccupant->email ?? $booking->guest_email ?? null;
            $customerPhone = $booking->hostelOccupant->phone ?? $booking->guest_phone ?? null;

            $invoice = $this->financeService->createInvoice([
                'company_id' => $booking->hostel->company_id ?? null,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'customer_type' => 'hostel_occupant',
                'customer_id' => $booking->hostel_occupant_id,
                'due_date' => $booking->check_in_date,
                'sub_total' => $booking->total_amount,
                'total' => $booking->total_amount,
                'hostel_id' => $booking->hostel_id,
                'module' => 'hostels',
                'entity_type' => 'booking',
                'entity_id' => $booking->id,
                'reference' => $booking->booking_reference,
            ]);

            // Create invoice line item
            $description = 'Room Booking';
            if ($booking->room) {
                $description .= ': Room #'.$booking->room->room_number;
            }
            if ($booking->bed) {
                $description .= ', Bed #'.$booking->bed->bed_number;
            }

            \Modules\Finance\Models\InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $booking->total_amount,
                'line_total' => $booking->total_amount,
            ]);

            return $invoice;

        } catch (\Exception $e) {
            Log::error('Failed to create booking invoice: '.$e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);

            throw new \Exception('Failed to create invoice for booking: '.$e->getMessage());
        }
    }

    /**
     * Get payment status for a booking
     *
     * @return array
     */
    public function getBookingPaymentStatus(Booking $booking)
    {
        try {
            $invoice = $this->financeService->findInvoiceByModuleEntity('hostels', 'booking', $booking->id);

            if (! $invoice) {
                return [
                    'status' => 'no_invoice',
                    'message' => 'No invoice found for this booking',
                    'amount_due' => $booking->total_amount,
                ];
            }

            $paidAmount = $invoice->payments()->sum('amount');
            $dueAmount = $invoice->total - $paidAmount;

            return [
                'status' => $invoice->status,
                'invoice' => $invoice,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'is_fully_paid' => $paidAmount >= $invoice->total,
                'payments' => $invoice->payments,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get booking payment status: '.$e->getMessage(), [
                'booking_id' => $booking->id,
                'exception' => $e,
            ]);

            return [
                'status' => 'error',
                'error' => 'Failed to retrieve payment status',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mark booking as paid
     *
     * @return bool
     */
    public function markBookingAsPaid(Booking $booking, array $paymentData)
    {
        try {
            $invoice = $this->financeService->findInvoiceByModuleEntity('hostels', 'booking', $booking->id);

            if (! $invoice) {
                Log::warning('Cannot mark booking as paid - no invoice found', [
                    'booking_id' => $booking->id,
                ]);

                return false;
            }

            // Process payment through finance service
            $payment = $this->financeService->processPaymentFromEvent([
                'module' => 'hostels',
                'entity_type' => 'booking',
                'entity_id' => $booking->id,
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'transaction_id' => $paymentData['transaction_id'] ?? null,
            ]);

            if ($payment) {
                // Update booking status if fully paid
                $paidAmount = $invoice->payments()->sum('amount');
                if ($paidAmount >= $invoice->total) {
                    $booking->update(['payment_status' => 'paid']);
                }

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to mark booking as paid: '.$e->getMessage(), [
                'booking_id' => $booking->id,
                'payment_data' => $paymentData,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Check if booking requires payment
     */
    public function requiresPayment(Booking $booking): bool
    {
        $status = $this->getBookingPaymentStatus($booking);

        return in_array($status['status'], ['no_invoice', 'pending', 'partial']) &&
               ($status['due_amount'] ?? $booking->total_amount) > 0;
    }
}
