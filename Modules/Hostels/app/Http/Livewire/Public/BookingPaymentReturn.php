<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Hostels\Events\BookingPaymentCompleted;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Facades\Payment;

class BookingPaymentReturn extends Component
{
    public Booking $booking;

    public ?string $paymentStatus = null;

    public ?float $paymentAmount = null;

    public ?string $paymentCurrency = null;

    public ?string $providerTransactionId = null;

    public string $message = '';

    public string $messageType = 'info';

    public function mount(Booking $booking)
    {
        $this->booking = $booking;

        // Get the latest payment intent with company relationship
        $payIntent = $booking->payIntents()->with('company')->latest()->first();

        if (! $payIntent) {
            $this->message = 'No payment intent found for this booking.';
            $this->messageType = 'error';

            return;
        }

        try {
            // Verify the payment
            $paymentResult = Payment::verify($payIntent, request()->all());

            // Store only the needed information, not the entire object
            $this->paymentStatus = $paymentResult->status;
            $this->paymentAmount = $paymentResult->amount;
            $this->paymentCurrency = $paymentResult->currency;
            $this->providerTransactionId = $paymentResult->provider_transaction_id;

            if ($this->paymentStatus === 'successful') {
                $this->handleSuccessfulPayment();
                $this->message = 'Payment successful! Your booking has been confirmed.';
                $this->messageType = 'success';
            } else {
                // Redirect to dedicated failed payment page
                return redirect()->route('hostels.public.booking.payment-failed', $this->booking);
            }
        } catch (\Exception $e) {
            Log::error('Payment verification failed: '.$e->getMessage(), [
                'exception' => $e,
                'booking_id' => $this->booking->id,
                'pay_intent_id' => $payIntent->id ?? null,
            ]);

            // Redirect to dedicated failed payment page on exception
            return redirect()->route('hostels.public.booking.payment-failed', $this->booking);
        }
    }

    protected function handleSuccessfulPayment()
    {
        // Check if booking hold has expired
        if ($this->booking->isHoldExpired()) {
            // Release the bed and cancel the booking
            $this->booking->releaseBedIfHoldExpired();

            // Show error message to user
            session()->flash('error', 'Your booking hold has expired. Please start a new booking.');

            return redirect()->route('hostels.public.index');
        }

        // Update booking status to confirmed and payment status to paid
        $this->booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'amount_paid' => $this->booking->total_amount,
            'balance_amount' => 0,
        ]);

        // Update bed status if selected
        if ($this->booking->bed_id) {
            $this->booking->bed->update(['status' => 'occupied']);
        }

        // Fire event for invoice creation
        event(new BookingPaymentCompleted($this->booking));
    }

    public function render()
    {
        return view('hostels::livewire.public.booking-payment-return')
            ->layout('hostels::layouts.public');
    }
}
