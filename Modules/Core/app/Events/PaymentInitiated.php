<?php

namespace Modules\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentInitiated
{
    use Dispatchable, SerializesModels;

    public $paymentIntent;

    public $metadata;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $paymentIntent
     * @return void
     */
    public function __construct($paymentIntent, array $metadata = [])
    {
        $this->paymentIntent = $paymentIntent;
        $this->metadata = $metadata;
    }
}
