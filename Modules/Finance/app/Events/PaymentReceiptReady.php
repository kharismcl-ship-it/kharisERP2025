<?php

namespace Modules\Finance\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;

class PaymentReceiptReady
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Payment $payment,
        public readonly Invoice $invoice,
    ) {}
}