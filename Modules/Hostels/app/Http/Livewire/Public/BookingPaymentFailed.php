<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Models\PayIntent;

class BookingPaymentFailed extends Component
{
    public Booking $booking;

    public ?PayIntent $payIntent = null;

    public string $failureReason = '';

    public array $availablePaymentMethods = [];

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['hostel', 'tenant']);

        // Get the latest payment intent
        $this->payIntent = $booking->payIntents()->latest()->first();

        if ($this->payIntent) {
            // Get failure reason from the intent if available
            $this->failureReason = $this->getFailureReasonFromIntent($this->payIntent);
        }

        // Get available payment methods for retry
        $companyId = $this->booking->hostel->company_id ?? null;
        $this->availablePaymentMethods = \Modules\PaymentsChannel\Facades\Payment::getAvailablePaymentMethods($companyId);
    }

    protected function getFailureReasonFromIntent(PayIntent $intent): string
    {
        // Try to get failure reason from the latest transaction
        $latestTransaction = $intent->transactions()->latest()->first();

        if ($latestTransaction && ! empty($latestTransaction->error_message)) {
            return $latestTransaction->error_message;
        }

        if ($latestTransaction && ! empty($latestTransaction->raw_payload)) {
            $payload = $latestTransaction->raw_payload;
            if (! empty($payload['error'])) {
                return is_array($payload['error'])
                    ? json_encode($payload['error'])
                    : $payload['error'];
            }
        }

        return 'The payment was declined by the payment provider. Please try another payment method or contact support.';
    }

    public function retryWithMethod($methodCode)
    {
        // Redirect to payment page with selected method
        return redirect()->route('hostels.public.booking.payment', [
            'booking' => $this->booking,
            'method' => $methodCode,
        ]);
    }

    public function render()
    {
        return view('hostels::livewire.public.booking-payment-failed')
            ->layout('hostels::layouts.public');
    }
}
