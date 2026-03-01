<?php

namespace Modules\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Sales\Models\SalesQuotation;

class QuotationSent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly SalesQuotation $quotation) {}
}