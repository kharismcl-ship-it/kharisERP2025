<?php

namespace Modules\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public $paymentReference;

    public $metadata;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(?string $paymentReference, array $metadata = [])
    {
        $this->paymentReference = $paymentReference;
        $this->metadata = $metadata;
    }
}
