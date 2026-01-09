<?php

namespace Modules\PaymentsChannel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\PaymentsChannel\Models\PayIntent;

class PaymentSucceeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PayIntent $payIntent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PayIntent $payIntent)
    {
        $this->payIntent = $payIntent;
    }
}
