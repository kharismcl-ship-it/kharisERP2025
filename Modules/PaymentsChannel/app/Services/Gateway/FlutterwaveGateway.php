<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class FlutterwaveGateway implements PaymentGatewayInterface
{
    /**
     * Initialize a payment with the Flutterwave gateway.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'flutterwave');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Flutterwave secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Prepare payment data
        $data = [
            'tx_ref' => $intent->reference,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
            'redirect_url' => $intent->return_url ?? url('/'),
            'customer' => [
                'email' => $intent->customer_email,
                'name' => $intent->customer_name,
                'phone_number' => $intent->customer_phone,
            ],
            'customizations' => [
                'title' => $intent->description ?? 'Payment',
                'description' => $intent->description ?? 'Payment for services',
            ],
        ];

        try {
            $channel = $intent->payMethod?->channel;
            if ($channel) {
                $option = match ($channel) {
                    'momo' => 'mobilemoneyghana',
                    'card' => 'card',
                    'bank' => 'banktransfer',
                    'ussd' => 'ussd',
                    default => null,
                };
                if ($option) {
                    $data['payment_options'] = $option;
                }
            }
        } catch (\Throwable $e) {
        }

        // Make API request to initialize payment
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/payments", $data);

        Log::debug('Flutterwave initialize request', ['data' => $data]);

        $status = $response->status();
        $body = $response->body();
        Log::debug('Flutterwave initialize raw', ['status' => $status, 'body' => $body]);

        if (! $response->successful()) {
            throw new \Exception('Failed to initialize Flutterwave payment (HTTP '.$status.'): '.$body);
        }

        $result = $response->json();

        // Log the parsed response for debugging
        Log::debug('Flutterwave initialize response', ['response' => $result]);

        if (! is_array($result) || ! isset($result['status'])) {
            throw new \Exception('Invalid Flutterwave response (HTTP '.$status.'): '.$body);
        }

        if ($result['status'] !== 'success') {
            $message = isset($result['message']) ? $result['message'] : ('HTTP '.$status.' - '.$body);
            throw new \Exception('Flutterwave payment initialization failed: '.$message);
        }

        // Check if required data exists
        if (! isset($result['data'])) {
            throw new \Exception('Invalid Flutterwave response: missing data field');
        }

        $responseData = $result['data'];

        // Check if required fields exist in data
        if (! isset($responseData['link'])) {
            throw new \Exception('Invalid Flutterwave response: missing payment link');
        }

        // Safely extract the ID field, which might not always be present
        $providerReference = null;
        if (isset($responseData['id'])) {
            $providerReference = $responseData['id'];
        }

        return new PaymentInitResponse(
            redirect_url: $responseData['link'],
            reference: $intent->reference,
            provider_reference: $providerReference,
            extra_data: $result
        );
    }

    /**
     * Verify the status of a payment with the Flutterwave gateway.
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'flutterwave');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Flutterwave secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Get transaction ID from payload or intent
        $transactionId = $payload['transaction_id'] ?? $intent->provider_reference;

        if (! $transactionId) {
            throw new \Exception('Transaction ID not found for verification');
        }

        // Make API request to verify payment
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get("{$baseUrl}/transactions/{$transactionId}/verify");

        if (! $response->successful()) {
            throw new \Exception('Failed to verify Flutterwave payment: '.$response->body());
        }

        $result = $response->json();

        // Log the response for debugging
        Log::debug('Flutterwave verify response', ['response' => $result]);

        if (! isset($result['status']) || $result['status'] !== 'success') {
            return new PaymentVerifyResult(
                status: 'failed',
                amount: $intent->amount,
                currency: $intent->currency,
                provider_transaction_id: null,
                raw_payload: $result
            );
        }

        $data = $result['data'];

        // Check if required fields exist
        if (! isset($data['status'])) {
            throw new \Exception('Invalid Flutterwave verification response: missing status field');
        }

        $status = match ($data['status']) {
            'successful' => 'successful',
            'failed' => 'failed',
            default => 'pending'
        };

        // Safely extract fields that might not be present
        $amount = isset($data['amount']) ? (float) $data['amount'] : $intent->amount;
        $currency = $data['currency'] ?? $intent->currency;
        $providerTransactionId = $data['id'] ?? null;

        return new PaymentVerifyResult(
            status: $status,
            amount: $amount,
            currency: $currency,
            provider_transaction_id: $providerTransactionId,
            raw_payload: $result
        );
    }

    /**
     * Handle webhook payload from the Flutterwave provider.
     */
    public function handleWebhook(array $payload): PayIntent
    {
        // Log the webhook payload for debugging
        Log::debug('Flutterwave webhook payload', ['payload' => $payload]);

        // Get transaction data to find the intent first
        $data = $payload['data'] ?? [];
        $reference = $data['tx_ref'] ?? null;

        if (! $reference) {
            throw new \Exception('Reference not found in Flutterwave webhook payload');
        }

        // Find the corresponding PayIntent
        $intent = PayIntent::where('reference', $reference)->first();

        if (! $intent) {
            throw new \Exception("PayIntent with reference {$reference} not found. Cannot process webhook.");
        }

        // Validate webhook signature for security using the intent's configuration
        $this->validateWebhookSignature($payload, $intent);

        // Extract the event type
        $event = $payload['event'] ?? null;

        if (! $event || ! in_array($event, ['charge.completed', 'transfer.completed'])) {
            Log::warning('Ignoring Flutterwave webhook with unhandled event type.', ['event' => $event]);

            return $intent; // Return intent without change for unhandled but valid events
        }

        // Check if required data exists
        if (! isset($data['status'])) {
            throw new \Exception('Invalid Flutterwave webhook data: missing status field');
        }

        // Safely extract the ID field
        $providerReference = $data['id'] ?? $intent->provider_reference;

        // Update the intent status based on the webhook data
        $intent->update([
            'status' => $data['status'] === 'successful' ? 'successful' : 'failed',
            'provider_reference' => $providerReference,
        ]);

        return $intent;
    }

    /**
     * Process a refund for a payment.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        // Get the config for the company
        $config = $this->getProviderConfig($intent, 'flutterwave');

        // Validate required config
        if (empty($config['secret_key'])) {
            throw new \Exception('Flutterwave secret key is not configured');
        }

        $secretKey = $config['secret_key'];
        $baseUrl = $this->getApiBaseUrl($config['mode'] ?? 'live');

        // Get the transaction ID from the intent
        $transactionId = $intent->provider_reference;

        if (! $transactionId) {
            throw new \Exception('Transaction ID not found for refund');
        }

        // Prepare refund data
        $data = [
            'amount' => $amount,
            'comment' => $reason ?: 'Refund requested',
        ];

        // Make API request to process refund
        $response = Http::withToken($secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$baseUrl}/transactions/{$transactionId}/refund", $data);

        Log::debug('Flutterwave refund request', ['data' => $data, 'transaction_id' => $transactionId]);

        if (! $response->successful()) {
            throw new \Exception('Failed to process Flutterwave refund: '.$response->body());
        }

        $result = $response->json();

        // Log the response for debugging
        Log::debug('Flutterwave refund response', ['response' => $result]);

        if (! isset($result['status']) || $result['status'] !== 'success') {
            $message = $result['message'] ?? 'Refund processing failed';
            throw new \Exception('Flutterwave refund failed: '.$message);
        }

        return [
            'success' => true,
            'refund_reference' => $result['data']['id'] ?? null,
            'message' => $result['message'] ?? 'Refund processed successfully',
            'raw_response' => $result,
        ];
    }

    /**
     * Validate webhook signature for security.
     */
    protected function validateWebhookSignature(array $payload, PayIntent $intent): void
    {
        // Get the signature from headers
        $signature = request()->header('verif-hash'); // Flutterwave docs mention 'verif-hash'

        if (! $signature) {
            // Fallback for older or different versions
            $signature = request()->header('flutterwave-signature');
        }

        // Get the config for the company from the intent
        $config = $this->getProviderConfig($intent, 'flutterwave');
        $secretHash = $config['secret_hash'] ?? null;

        // If no secret hash is configured, we can't validate. This is a security risk.
        if (empty($secretHash)) {
            Log::error('Flutterwave webhook secret hash not configured for company.', ['company_id' => $intent->company_id]);
            throw new \Exception('Webhook secret hash not configured, cannot verify payload.');
        }

        // In Flutterwave's case, the signature is simply the secret hash.
        // The webhook is considered authentic if the `verif-hash` header matches the secret hash.
        if (! hash_equals($secretHash, $signature)) {
            throw new \Exception('Invalid Flutterwave webhook signature.');
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
     * Get API base URL based on mode.
     */
    protected function getApiBaseUrl(string $mode): string
    {
        $normalized = strtolower($mode);

        return in_array($normalized, ['test', 'sandbox'])
            ? 'https://api.flutterwave.com/v3'
            : 'https://api.flutterwave.com/v3';
    }
}
