<?php

namespace Modules\PaymentsChannel\Traits;

use Modules\PaymentsChannel\Models\PayIntent;

trait HasPayments
{
    /**
     * Get all payment intents for this model.
     */
    public function payIntents()
    {
        return $this->morphMany(PayIntent::class, 'payable');
    }

    /**
     * Get the payment description for this model.
     */
    public function getPaymentDescription(): ?string
    {
        // E.g. "Hostel Booking #REF" or "Invoice #INV-0001"
        return $this->payment_description ?? (string) $this->id;
    }

    /**
     * Get the payment amount for this model.
     */
    public function getPaymentAmount(): float
    {
        // Return the amount to be paid (or outstanding balance)
        return $this->amount ?? $this->total ?? 0;
    }

    /**
     * Get the payment currency for this model.
     */
    public function getPaymentCurrency(): string
    {
        return $this->currency ?? 'GHS';
    }

    /**
     * Get the customer name for payment purposes.
     */
    public function getPaymentCustomerName(): ?string
    {
        // Try to fetch related tenant/customer name if available
        return $this->customer_name ?? null;
    }

    /**
     * Get the customer email for payment purposes.
     */
    public function getPaymentCustomerEmail(): ?string
    {
        return $this->customer_email ?? null;
    }

    /**
     * Get the customer phone for payment purposes.
     */
    public function getPaymentCustomerPhone(): ?string
    {
        return $this->customer_phone ?? null;
    }
}
