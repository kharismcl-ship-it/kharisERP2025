<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class PaySwitchGateway implements PaymentGatewayInterface
{
    /**
     * Initialize a payment with the PaySwitch gateway.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        $config = $this->getProviderConfig($intent, 'payswitch');

        if (empty($config['merchant_id']) || empty($config['api_key'])) {
            throw new \Exception('PaySwitch credentials are not configured');
        }

        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');
        $apiKey = $config['api_key'];

        $auth = base64_encode($config['merchant_id'].':'.$apiKey);

        $data = [
            'amount' => $intent->amount,
            'transaction_id' => $intent->reference,
            'currency' => $intent->currency,
            'r_url' => $intent->return_url ?? url('/'),
            'c_email' => $intent->customer_email,
            'c_name' => $intent->customer_name,
            'desc' => $intent->description,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$auth,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/v1.1/payment/initiate", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to initialize PaySwitch payment: '.$response->body());
        }

        $result = $response->json();

        if (! isset($result['success']) || ! $result['success']) {
            throw new \Exception('PaySwitch payment initialization failed: '.($result['message'] ?? 'Unknown error'));
        }

        return new PaymentInitResponse(
            redirect_url: $result['redirect_url'] ?? null,
            reference: $intent->reference,
            provider_reference: $result['transaction_id'] ?? null,
            extra_data: $result
        );
    }

    /**
     * Verify the status of a payment with the PaySwitch gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        $config = $this->getProviderConfig($intent, 'payswitch');

        if (empty($config['merchant_id']) || empty($config['api_key'])) {
            throw new \Exception('PaySwitch credentials are not configured');
        }

        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');
        $apiKey = $config['api_key'];

        $auth = base64_encode($config['merchant_id'].':'.$apiKey);

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$auth,
            'Content-Type' => 'application/json',
        ])->get("{$baseUrl}/v1.1/payment/status/{$intent->reference}");

        if (! $response->successful()) {
            throw new \Exception('Failed to verify PaySwitch payment: '.$response->body());
        }

        $result = $response->json();

        $status = match ($result['status'] ?? null) {
            'SUCCESS' => 'successful',
            'FAILED' => 'failed',
            default => 'pending'
        };

        return new PaymentVerifyResult(
            status: $status,
            amount: (float) ($result['amount'] ?? $intent->amount),
            currency: $result['currency'] ?? $intent->currency,
            provider_transaction_id: $result['transaction_id'] ?? null,
            raw_payload: $result
        );
    }

    /**
     * Handle webhook payload from the PaySwitch provider.
     */
    public function handleWebhook(array $payload): PayIntent
    {
        Log::debug('PaySwitch webhook payload', ['payload' => $payload]);

        $reference = $payload['transaction_id'] ?? null;

        if (! $reference) {
            throw new \Exception('Reference not found in PaySwitch webhook payload');
        }

        $intent = PayIntent::where('reference', $reference)->first();

        if (! $intent) {
            throw new \Exception("PayIntent with reference {$reference} not found");
        }

        $this->validateWebhookSignature($payload, $intent);

        $status = $payload['status'] ?? null;

        $intent->update([
            'status' => $status === 'SUCCESS' ? 'successful' : 'failed',
            'provider_reference' => $payload['transaction_id'] ?? $intent->provider_reference,
        ]);

        return $intent;
    }

    /**
     * Validate webhook signature for security.
     */
    protected function validateWebhookSignature(array $payload, PayIntent $intent): void
    {
        $signature = request()->header('PaySwitch-Signature');
        $config = $this->getProviderConfig($intent, 'payswitch');
        $apiSecret = $config['api_secret'] ?? null;

        if (empty($apiSecret)) {
            Log::error('PaySwitch webhook secret not configured for company.', ['company_id' => $intent->company_id]);
            throw new \Exception('Webhook secret not configured, cannot verify payload.');
        }

        $expectedSignature = hash_hmac('sha256', request()->getContent(), $apiSecret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid PaySwitch webhook signature');
        }
    }

    /**
     * Get the API base URL based on the mode.
     */
    protected function getApiBaseUrl(string $mode): string
    {
        return $mode === 'test' ? 'https://test.theteller.net' : 'https://prod.theteller.net';
    }

    /**
     * Process a refund for a payment through PaySwitch gateway.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        $config = $this->getProviderConfig($intent, 'payswitch');

        if (empty($config['merchant_id']) || empty($config['api_key'])) {
            throw new \Exception('PaySwitch credentials are not configured');
        }

        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');
        $apiKey = $config['api_key'];

        $auth = base64_encode($config['merchant_id'].':'.$apiKey);

        $data = [
            'transaction_id' => $intent->provider_reference,
            'amount' => $amount,
            'currency' => $intent->currency,
            'reason' => $reason,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$auth,
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/v1.1/payment/refund", $data);

        if (! $response->successful()) {
            throw new \Exception('Failed to process PaySwitch refund: '.$response->body());
        }

        $result = $response->json();

        if (! isset($result['success']) || ! $result['success']) {
            throw new \Exception('PaySwitch refund failed: '.($result['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'refund_id' => $result['refund_id'] ?? null,
            'status' => $result['status'] ?? 'processed',
            'message' => $result['message'] ?? 'Refund processed successfully',
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
