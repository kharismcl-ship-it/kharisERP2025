<?php

namespace Modules\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCompleted
{
    use Dispatchable, SerializesModels;

    public $paymentResult;

    public $metadata;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $paymentResult
     * @return void
     */
    public function __construct($paymentResult, array $metadata = [])
    {
        $this->paymentResult = $paymentResult;
        $this->metadata = $metadata;
    }
}
