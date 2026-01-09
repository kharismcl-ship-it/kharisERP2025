<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayProviderConfig;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * Initialize a payment with the Stripe gateway.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        $config = $this->getProviderConfig($intent, 'stripe');

        if (empty($config['secret_key'])) {
            throw new \Exception('Stripe secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = 'https://api.stripe.com/v1';

        $data = [
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $intent->currency,
                        'product_data' => [
                            'name' => $intent->description,
                        ],
                        'unit_amount' => $intent->amount * 100, // Amount in cents
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $intent->return_url.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $intent->return_url.'?cancelled=true',
            'client_reference_id' => $intent->reference,
        ];

        $response = Http::withToken($secretKey)
            ->asForm()
            ->post("{$baseUrl}/checkout/sessions", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to initialize Stripe payment: '.$response->body());
        }

        $result = $response->json();

        return new PaymentInitResponse(
            redirect_url: $result['url'],
            reference: $intent->reference,
            provider_reference: $result['id'],
            extra_data: $result
        );
    }

    /**
     * Verify the status of a payment with the Stripe gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        $config = $this->getProviderConfig($intent, 'stripe');

        if (empty($config['secret_key'])) {
            throw new \Exception('Stripe secret key is not configured');
        }

        $sessionId = $payload['session_id'] ?? $intent->provider_reference;

        if (! $sessionId) {
            throw new \Exception('Stripe session ID not found for verification');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = 'https://api.stripe.com/v1';

        $response = Http::withToken($secretKey)
            ->get("{$baseUrl}/checkout/sessions/{$sessionId}");

        if (! $response->successful()) {
            throw new \Exception('Failed to verify Stripe payment: '.$response->body());
        }

        $result = $response->json();

        $status = match ($result['payment_status'] ?? null) {
            'paid' => 'successful',
            'unpaid' => 'failed',
            default => 'pending'
        };

        return new PaymentVerifyResult(
            status: $status,
            amount: (float) ($result['amount_total'] / 100),
            currency: $result['currency'],
            provider_transaction_id: $result['payment_intent'],
            raw_payload: $result
        );
    }

    /**
     * Handle webhook payload from the Stripe provider.
     */
    public function handleWebhook(array $payload): PayIntent
    {
        $reference = $payload['data']['object']['client_reference_id'] ?? null;

        if (! $reference) {
            throw new \Exception('Client reference ID not found in Stripe webhook payload');
        }

        $intent = PayIntent::where('reference', $reference)->first();

        if (! $intent) {
            throw new \Exception("PayIntent with reference {$reference} not found");
        }

        $this->validateWebhookSignature($intent);

        $event = $payload['type'] ?? null;

        if ($event === 'checkout.session.completed') {
            $session = $payload['data']['object'];
            $status = $session['payment_status'] === 'paid' ? 'successful' : 'failed';

            $intent->update([
                'status' => $status,
                'provider_reference' => $session['id'],
            ]);
        }

        return $intent;
    }

    /**
     * Validate webhook signature for security.
     */
    protected function validateWebhookSignature(PayIntent $intent): void
    {
        $signature = request()->header('Stripe-Signature');
        $config = $this->getProviderConfig($intent, 'stripe');
        $webhookSecret = $config['webhook_secret'] ?? null;

        if (empty($webhookSecret)) {
            Log::error('Stripe webhook secret not configured for company.', ['company_id' => $intent->company_id]);
            throw new \Exception('Webhook secret not configured, cannot verify payload.');
        }

        try {
            WebhookSignature::verifyHeader(
                request()->getContent(),
                $signature,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            throw new \Exception('Invalid Stripe webhook signature: '.$e->getMessage());
        }
    }

    /**
     * Process a refund for a payment through Stripe gateway.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        $config = $this->getProviderConfig($intent, 'stripe');

        if (empty($config['secret_key'])) {
            throw new \Exception('Stripe secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = 'https://api.stripe.com/v1';

        // Get the payment intent ID for refund
        $paymentIntentId = $intent->provider_reference;

        if (empty($paymentIntentId)) {
            throw new \Exception('Payment intent ID not found for refund');
        }

        // Prepare refund data
        // Note: Stripe amount is in cents, so multiply by 100
        $data = [
            'payment_intent' => $paymentIntentId,
            'amount' => $amount * 100, // Convert to cents
            'reason' => $reason ?: 'requested_by_customer',
        ];

        // Make API request to process refund
        $response = Http::withToken($secretKey)
            ->asForm()
            ->post("{$baseUrl}/refunds", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to process Stripe refund: '.$response->body());
        }

        $result = $response->json();

        if (isset($result['error'])) {
            throw new \Exception('Stripe refund failed: '.($result['error']['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'refund_id' => $result['id'] ?? null,
            'status' => $result['status'] ?? 'succeeded',
            'message' => 'Refund processed successfully',
            'raw_response' => $result,
        ];
    }

    /**
     * Get provider configuration for a company or global fallback.
     */
    protected function getProviderConfig(PayIntent $intent, string $provider): array
    {
        $companyId = $intent->company_id;

        // First try to get company-specific config
        if ($companyId) {
            $config = PayProviderConfig::where('company_id', $companyId)
                ->where('provider', $provider)
                ->where('is_active', true)
                ->first()
                ?->config ?? [];

            if (! empty($config)) {
                return $config;
            }
        }

        // Fallback to global config if no company config found
        return PayProviderConfig::whereNull('company_id')
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first()
            ?->config ?? [];
    }
}
