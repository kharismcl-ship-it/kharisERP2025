<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Modules\PaymentsChannel\Models\PayIntent;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment with the gateway.
     * Return data needed for redirect/inline checkout.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse;

    /**
     * Verify the status of a payment with the gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult;

    /**
     * Handle webhook payload from the provider.
     */
    public function handleWebhook(array $payload): PayIntent;

    /**
     * Process a refund for a payment.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array;
}
