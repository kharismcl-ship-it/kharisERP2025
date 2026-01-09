<?php

namespace Modules\PaymentsChannel\Services\Gateway;

class PaymentVerifyResult
{
    public function __construct(
        public readonly string $status,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?string $provider_transaction_id,
        public readonly array $raw_payload = []
    ) {}
}
