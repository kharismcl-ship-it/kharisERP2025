<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Hostels\Events\BookingPaymentCompleted;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Facades\Payment;
use Modules\PaymentsChannel\Models\PayIntent;

class BookingPayment extends Component
{
    public Booking $booking;

    public ?PayIntent $payIntent = null;

    public $selectedPaymentMethod = '';

    public $availablePaymentMethods = [];

    public $groupedPaymentMethods = [];

    public function mount(Booking $booking)
    {
        // Load the hostel and hostelOccupant relationships to ensure we can get the company_id and customer info
        $this->booking = $booking->load(['hostel', 'hostelOccupant']);

        // Get available payment methods for this company
        $companyId = $this->booking->hostel->company_id ?? null;
        
        // Get online payment methods for immediate payment
        $this->availablePaymentMethods = Payment::getAvailablePaymentMethods($companyId, [
            'payment_mode' => 'online'
        ]);

        // Group payment methods by provider using the built-in method
        $this->groupedPaymentMethods = Payment::getGroupedPaymentMethods($companyId, [
            'payment_mode' => 'online'
        ]);

        // Check if there's already a payment intent for this booking
        $this->payIntent = $booking->payIntents()->with('company')->latest()->first();

        // If no payment intent exists, create one
        if (! $this->payIntent) {
            $this->createPaymentIntent();

            // Reload with company relationship
            if ($this->payIntent) {
                $this->payIntent->load('company');
            }
        } else {
            // If there's an existing payment intent, pre-select its method
            if ($this->payIntent->payMethod) {
                $this->selectedPaymentMethod = $this->payIntent->payMethod->code;
            }
        }
    }

    protected function groupPaymentMethodsByProvider($methods)
    {
        $grouped = [];

        foreach ($methods as $method) {
            $provider = $method['provider'];

            // Initialize provider group if not exists
            if (! isset($grouped[$provider])) {
                $grouped[$provider] = [
                    'name' => $this->getProviderDisplayName($provider),
                    'methods' => [],
                ];
            }

            // Add method to provider group
            $grouped[$provider]['methods'][] = $method;
        }

        return $grouped;
    }

    protected function getProviderDisplayName($provider)
    {
        $providerNames = [
            'flutterwave' => 'Flutterwave',
            'paystack' => 'Paystack',
            'payswitch' => 'PaySwitch',
            'stripe' => 'Stripe',
            'ghanapay' => 'GhanaPay',
            'manual' => 'Manual Payment',
        ];

        return $providerNames[$provider] ?? ucfirst($provider);
    }

    protected function createPaymentIntent()
    {
        // Calculate minimum payment amount based on hostel's deposit and partial payment settings
        $minPaymentAmount = $this->booking->hostel->getMinimumPaymentAmount($this->booking->total_amount);

        // Use the minimum payment amount or the remaining balance, whichever is applicable
        $paymentAmount = $minPaymentAmount > 0 ? $minPaymentAmount : $this->booking->balance_amount;

        // Prepare options for payment intent
        $options = [
            'amount' => $paymentAmount,
            'currency' => 'GHS', // You might want to make this dynamic based on hostel settings
            'return_url' => route('hostels.public.booking.payment-return', $this->booking),
            'metadata' => [
                'booking_id' => $this->booking->id,
                'hostel_occupant_id' => $this->booking->hostel_occupant_id,
                'payment_type' => $minPaymentAmount > 0 ? 'deposit_or_partial' : 'full_payment',
                'min_payment_amount' => $minPaymentAmount,
                'total_balance' => $this->booking->balance_amount,
            ],
        ];

        // Add method code if selected
        if ($this->selectedPaymentMethod) {
            $options['method_code'] = $this->selectedPaymentMethod;
        }

        $this->payIntent = Payment::createIntentForModel(
            payable: $this->booking,
            options: $options
        );
    }

    public function initiatePayment()
    {
        try {
            // If payment method was selected, update the intent
            if ($this->selectedPaymentMethod && (! $this->payIntent->pay_method_id ||
                ($this->payIntent->payMethod && $this->payIntent->payMethod->code !== $this->selectedPaymentMethod))) {
                // Recreate the payment intent with the selected method
                $this->payIntent->delete();
                $this->createPaymentIntent();

                // Reload with company relationship
                if ($this->payIntent) {
                    $this->payIntent->load('company');
                }
            }

            if ($this->payIntent && $this->payIntent->status !== 'pending') {
                $this->payIntent->delete();
                $this->createPaymentIntent();
                if ($this->payIntent) {
                    $this->payIntent->load('company');
                }
            }

            $init = Payment::initialize($this->payIntent);

            if ($init->redirect_url) {
                return redirect()->away($init->redirect_url);
            }

            // For manual payments or when no redirect is needed
            $this->updateBookingStatus();

            return redirect()->route('hostels.public.booking.confirmation', $this->booking);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: '.$e->getMessage(), [
                'exception' => $e,
                'booking_id' => $this->booking->id,
                'pay_intent_id' => $this->payIntent->id ?? null,
                'selected_method' => $this->selectedPaymentMethod,
            ]);
            session()->flash('error', 'Failed to initiate payment. Please try again. Error: '.$e->getMessage());
        }
    }

    public function changePaymentMethod()
    {
        try {
            // Cancel the existing payment intent
            if ($this->payIntent && $this->payIntent->status === 'pending') {
                $this->payIntent->update(['status' => 'cancelled']);

                // Create a new payment intent
                $this->createPaymentIntent();

                // Reload with company relationship
                if ($this->payIntent) {
                    $this->payIntent->load('company');
                }

                session()->flash('message', 'Payment method changed successfully. Please select a new payment method.');
            }
        } catch (\Exception $e) {
            Log::error('Payment method change failed: '.$e->getMessage(), [
                'exception' => $e,
                'booking_id' => $this->booking->id,
                'pay_intent_id' => $this->payIntent->id ?? null,
            ]);
            session()->flash('error', 'Failed to change payment method. Please try again.');
        }
    }

    protected function updateBookingStatus()
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
        return view('hostels::livewire.public.booking-payment')
            ->layout('hostels::layouts.public');
    }
}
