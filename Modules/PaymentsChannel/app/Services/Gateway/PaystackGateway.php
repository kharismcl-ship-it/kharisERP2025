<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class PaystackGateway implements PaymentGatewayInterface
{
    /**
     * Initialize a payment with the Paystack gateway.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'paystack');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Paystack secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Prepare payment data
        // Note: Paystack amount is in kobo (smallest unit), so multiply by 100
        $data = [
            'reference' => $intent->reference,
            'amount' => $intent->amount * 100, // Convert to kobo
            'currency' => $intent->currency,
            'email' => $intent->customer_email,
            'callback_url' => $intent->return_url ?? url('/'),
            'metadata' => [
                'custom_fields' => [
                    [
                        'display_name' => 'Customer Name',
                        'variable_name' => 'customer_name',
                        'value' => $intent->customer_name,
                    ],
                    [
                        'display_name' => 'Customer Phone',
                        'variable_name' => 'customer_phone',
                        'value' => $intent->customer_phone,
                    ],
                ],
            ],
        ];

        // Make API request to initialize payment
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/transaction/initialize", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to initialize Paystack payment: '.$response->body());
        }

        $result = $response->json();

        if (! $result['status']) {
            throw new \Exception('Paystack payment initialization failed: '.($result['message'] ?? 'Unknown error'));
        }

        return new PaymentInitResponse(
            redirect_url: $result['data']['authorization_url'],
            reference: $intent->reference,
            provider_reference: $result['data']['reference'],
            extra_data: $result
        );
    }

    /**
     * Verify the status of a payment with the Paystack gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'paystack');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Paystack secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Get transaction reference from payload or intent
        $reference = $payload['reference'] ?? $intent->reference;

        if (! $reference) {
            throw new \Exception('Transaction reference not found for verification');
        }

        // Make API request to verify payment
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get("{$baseUrl}/transaction/verify/{$reference}");

        if (! $response->successful()) {
            throw new \Exception('Failed to verify Paystack payment: '.$response->body());
        }

        $result = $response->json();

        if (! $result['status']) {
            return new PaymentVerifyResult(
                status: 'failed',
                amount: $intent->amount,
                currency: $intent->currency,
                provider_transaction_id: null,
                raw_payload: $result
            );
        }

        $data = $result['data'];
        $status = match ($data['status']) {
            'success' => 'successful',
            'failed' => 'failed',
            default => 'pending'
        };

        // Convert amount from kobo back to main currency unit
        $amount = (float) $data['amount'] / 100;

        return new PaymentVerifyResult(
            status: $status,
            amount: $amount,
            currency: $data['currency'],
            provider_transaction_id: $data['id'],
            raw_payload: $result
        );
    }

    /**
     * Handle webhook payload from the Paystack provider.
     */
    public function handleWebhook(array $payload): PayIntent
    {
        // Get transaction data
        $data = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;

        if (! $reference) {
            throw new \Exception('Reference not found in Paystack webhook payload');
        }

        // Find the corresponding PayIntent
        $intent = PayIntent::where('reference', $reference)->first();

        if (! $intent) {
            throw new \Exception("PayIntent with reference {$reference} not found");
        }

        // Validate webhook signature for security
        $this->validateWebhookSignature($intent);

        // Extract the event type
        $event = $payload['event'] ?? null;

        if (! $event || $event !== 'charge.success') {
            // We can choose to ignore non-success events or handle them
            // For now, we only care about successful charges
            return $intent;
        }

        // Check if required data exists
        if (! isset($data['status'])) {
            throw new \Exception('Invalid Paystack webhook data: missing status field');
        }

        // Update the intent status based on the webhook data
        $intent->update([
            'status' => $data['status'] === 'success' ? 'successful' : 'failed',
            'provider_reference' => $data['id'] ?? $intent->provider_reference,
        ]);

        return $intent;
    }

    /**
     * Validate webhook signature for security.
     */
    protected function validateWebhookSignature(PayIntent $intent): void
    {
        // Get the signature from headers
        $signature = request()->header('x-paystack-signature');

        // Get the expected signature from the correct company config
        $config = $this->getProviderConfig($intent, 'paystack');
        $secretKey = $config['secret_key'] ?? null;

        // If no secret key is configured, we can't validate
        if (empty($secretKey)) {
            Log::error('Paystack webhook secret key not configured for company.', ['company_id' => $intent->company_id]);
            throw new \Exception('Webhook secret not configured, cannot verify payload.');
        }

        // Get raw body for signature verification
        $rawBody = request()->getContent();

        // Generate the expected signature using HMAC-SHA512
        $expectedSignature = hash_hmac('sha512', $rawBody, $secretKey);

        // Validate the signature using hash_equals for security
        if (! hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid Paystack webhook signature');
        }
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

    /**
     * Process a refund for a payment through Paystack gateway.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'paystack');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Paystack secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Paystack requires the transaction reference for refunds
        $transactionReference = $intent->provider_reference;

        if (empty($transactionReference)) {
            throw new \Exception('Transaction reference not found for refund');
        }

        // Prepare refund data
        // Note: Paystack amount is in kobo (smallest unit), so multiply by 100
        $data = [
            'transaction' => $transactionReference,
            'amount' => $amount * 100, // Convert to kobo
            'currency' => $intent->currency,
        ];

        // Add reason if provided
        if (! empty($reason)) {
            $data['customer_note'] = $reason;
        }

        // Make API request to process refund
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/refund", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to process Paystack refund: '.$response->body());
        }

        $result = $response->json();

        if (! $result['status']) {
            throw new \Exception('Paystack refund failed: '.($result['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'refund_id' => $result['data']['id'] ?? null,
            'status' => $result['data']['status'] ?? 'processed',
            'message' => $result['message'] ?? 'Refund processed successfully',
            'raw_response' => $result,
        ];
    }

    /**
     * Get API base URL based on mode.
     */
    protected function getApiBaseUrl(string $mode): string
    {
        return $mode === 'test'
            ? 'https://api.paystack.co'
            : 'https://api.paystack.co';
    }
}
