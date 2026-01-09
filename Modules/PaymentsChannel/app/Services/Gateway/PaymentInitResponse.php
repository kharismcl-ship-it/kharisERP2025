<?php

namespace Modules\PaymentsChannel\Services\Gateway;

class PaymentInitResponse
{
    public function __construct(
        public readonly ?string $redirect_url,
        public readonly string $reference,
        public readonly ?string $provider_reference,
        public readonly array $extra_data = []
    ) {}
}
