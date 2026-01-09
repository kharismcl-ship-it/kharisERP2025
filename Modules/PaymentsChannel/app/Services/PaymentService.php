<?php

namespace Modules\PaymentsChannel\Services;

use Modules\PaymentsChannel\Events\PaymentFailed;
use Modules\PaymentsChannel\Events\PaymentSucceeded;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayMethod;
use Modules\PaymentsChannel\Models\PayProviderConfig;
use Modules\PaymentsChannel\Models\PayTransaction;
use Illuminate\Support\Facades\Auth;
use Modules\PaymentsChannel\Services\Gateway\FlutterwaveGateway;
use Modules\PaymentsChannel\Services\Gateway\ManualGateway;
use Modules\PaymentsChannel\Services\Gateway\PaymentInitResponse;
use Modules\PaymentsChannel\Services\Gateway\PaymentVerifyResult;
use Modules\PaymentsChannel\Services\Gateway\PaystackGateway;
use Modules\PaymentsChannel\Services\Gateway\PaySwitchGateway;
use Modules\PaymentsChannel\Services\Gateway\StripeGateway;

class PaymentService
{
    /**
     * Get available payment methods for a company with optional filtering.
     */
    public function getAvailablePaymentMethods(?int $companyId = null, array $filters = []): array
    {
        // Generate cache key based on parameters
        $cacheKey = $this->generatePaymentMethodsCacheKey($companyId, $filters);

        // Use caching with 1-hour TTL for payment methods
        return cache()->remember($cacheKey, 3600, function () use ($companyId, $filters) {
            $query = PayMethod::where('is_active', true);

            if ($companyId) {
                // Include both company-specific methods AND global methods (null company_id)
                $query->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                });
            } else {
                // If no company specified, show all active methods
                $query->whereNotNull('company_id');
            }

            // Apply filters
            if (!empty($filters['payment_mode'])) {
                $query->where('payment_mode', $filters['payment_mode']);
            }
            
            if (!empty($filters['provider'])) {
                $query->where('provider', $filters['provider']);
            }
            
            if (!empty($filters['channel'])) {
                $query->where('channel', $filters['channel']);
            }
            
            if (!empty($filters['currency'])) {
                $query->where('currency', $filters['currency']);
            }

            return $query->orderBy('sort_order')->get()->toArray();
        });
    }

    /**
     * Generate cache key for payment methods based on parameters.
     */
    protected function generatePaymentMethodsCacheKey(?int $companyId, array $filters): string
    {
        $keyParts = [
            'payment_methods',
            'company_' . ($companyId ?? 'global'),
        ];

        // Add filter parameters to cache key
        foreach (['payment_mode', 'provider', 'channel', 'currency'] as $filter) {
            if (!empty($filters[$filter])) {
                $keyParts[] = $filter . '_' . $filters[$filter];
            }
        }

        return implode(':', $keyParts);
    }

    /**
     * Clear payment methods cache for specific parameters.
     */
    public function clearPaymentMethodsCache(?int $companyId = null, array $filters = []): void
    {
        $cacheKey = $this->generatePaymentMethodsCacheKey($companyId, $filters);
        cache()->forget($cacheKey);
    }

    /**
     * Get payment methods grouped by provider with optional filtering.
     */
    public function getGroupedPaymentMethods(?int $companyId = null, array $filters = []): array
    {
        $methods = $this->getAvailablePaymentMethods($companyId, $filters);
        
        $grouped = [];
        foreach ($methods as $method) {
            $provider = $method['provider'] ?? 'other';
            $grouped[$provider][] = $method;
        }
        
        return $grouped;
    }

    /**
     * Create a payment intent for a model.
     */
    public function createIntentForModel(
        $payable,
        ?string $provider = null,
        array $options = []
    ): PayIntent {
        // Get the company ID from the model or current session
        $companyId = $this->resolveCompanyId($payable);

        // Get method if provided
        $method = null;
        if (isset($options['method_code'])) {
            $method = PayMethod::where('company_id', $companyId)
                ->where('code', $options['method_code'])
                ->where('is_active', true)
                ->first();
        }

        // Use provider from method if available, otherwise use default provider if not explicitly set
        if ($method && $method->provider) {
            $provider = $method->provider;
        } elseif (! $provider) {
            $provider = $this->getDefaultProvider($companyId)?->provider ?? 'manual';
        }

        // Create reference
        $reference = $this->generateReference();

        // Create the payment intent
        $intent = PayIntent::create([
            'company_id' => $companyId,
            'provider' => $provider,
            'pay_method_id' => $method?->id,
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'reference' => $reference,
            'amount' => $options['amount'] ?? $payable->getPaymentAmount(),
            'currency' => $options['currency'] ?? $payable->getPaymentCurrency(),
            'description' => $payable->getPaymentDescription(),
            'customer_name' => $payable->getPaymentCustomerName(),
            'customer_email' => $payable->getPaymentCustomerEmail(),
            'customer_phone' => $payable->getPaymentCustomerPhone(),
            'return_url' => $options['return_url'] ?? null,
            'callback_url' => $options['callback_url'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'status' => 'pending',
        ]);

        return $intent;
    }

    /**
     * Initialize payment (get redirect URL / inline data).
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        // Resolve gateway class from provider
        $gateway = $this->resolveGateway($intent->provider);

        // Call initialize onthe gateway
        $response = $gateway->initialize($intent);

        // Update PayIntent with status = initiated, provider_reference
        $intent->update([
            'status' => 'initiated',
            'provider_reference' => $response->provider_reference,
        ]);

        return $response;
    }

    /**
     * Verify payment (after redirect or manual check).
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        // Call gateway verify
        $gateway = $this->resolveGateway($intent->provider);
        $result = $gateway->verify($intent, $payload);

        // Update PayIntent status
        $intent->update([
            'status' => $result->status,
        ]);

        // Create PayTransaction
        PayTransaction::create([
            'pay_intent_id' => $intent->id,
            'company_id' => $intent->company_id,
            'provider' => $intent->provider,
            'transaction_type' => 'payment',
            'amount' => $result->amount,
            'currency' => $result->currency,
            'provider_transaction_id' => $result->provider_transaction_id,
            'status' => $result->status,
            'raw_payload' => $result->raw_payload,
            'processed_at' => now(),
        ]);

        // Dispatch events based on payment status
        if ($result->status === 'successful') {
            event(new PaymentSucceeded($intent));
        } else {
            event(new PaymentFailed($intent));
        }

        return $result;
    }

    /**
     * Handle webhook from a provider.
     */
    public function handleWebhook(string $provider, array $payload): PayIntent
    {
        // Resolve gateway class from provider
        $gateway = $this->resolveGateway($provider);

        // Handle the webhook and get updated intent
        $intent = $gateway->handleWebhook($payload);

        // Extract transaction attributes safely from payload
        $data = $payload['data'] ?? [];
        $status = ($data['status'] ?? $intent->status) === 'successful' ? 'successful' : (($data['status'] ?? $intent->status) === 'failed' ? 'failed' : 'pending');
        $amount = isset($data['amount']) ? (float) $data['amount'] : (float) ($intent->amount ?? 0);
        $currency = $data['currency'] ?? ($intent->currency ?? 'GHS');
        $providerTransactionId = $data['id'] ?? ($intent->provider_reference ?? null);

        // Create transaction record
        PayTransaction::create([
            'pay_intent_id' => $intent->id,
            'company_id' => $intent->company_id,
            'provider' => $provider,
            'transaction_type' => 'payment',
            'amount' => $amount,
            'currency' => $currency,
            'provider_transaction_id' => $providerTransactionId,
            'status' => $status,
            'raw_payload' => $payload,
            'processed_at' => now(),
        ]);

        // Dispatch events
        if ($status === 'successful') {
            event(new \Modules\PaymentsChannel\Events\PaymentSucceeded($intent));
        } elseif ($status === 'failed') {
            event(new \Modules\PaymentsChannel\Events\PaymentFailed($intent));
        }

        return $intent;
    }

    /**
     * Resolve gateway class from provider name.
     */
    protected function resolveGateway(string $provider): object
    {
        return match ($provider) {
            'flutterwave' => new FlutterwaveGateway,
            'paystack' => new PaystackGateway,
            'payswitch' => new PaySwitchGateway,
            'stripe' => new StripeGateway,
            'manual' => new ManualGateway,
            default => throw new \Exception("Unsupported payment provider: {$provider}"),
        };
    }

    /**
     * Resolve company ID from a model.
     */
    protected function resolveCompanyId($model): ?int
    {
        // Try to get company ID from the model
        if (method_exists($model, 'getAttribute')) {
            // Check if model has company_id directly
            $companyId = $model->getAttribute('company_id');
            if ($companyId) {
                return $companyId;
            }

            // Check if model has current_company_id
            $companyId = $model->getAttribute('current_company_id');
            if ($companyId) {
                return $companyId;
            }

            // For hostel bookings, get company ID from the related hostel
            if (method_exists($model, 'hostel') && $model->hostel) {
                return $model->hostel->company_id;
            }
        }

        return $this->resolveCurrentCompanyId();
    }

    /**
     * Resolve current company ID from session.
     */
    protected function resolveCurrentCompanyId(): ?int
    {
        // Try to get company ID from the authenticated user
        if (auth()->check() && method_exists(auth()->user(), 'getAttribute')) {
            return auth()->user()->getAttribute('current_company_id') ?? null;
        }

        return null;
    }

    /**
     * Get the default provider for a company.
     */
    protected function getDefaultProvider(?int $companyId): ?PayProviderConfig
    {
        return PayProviderConfig::where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->where('is_default', true)
            ->first();
    }

    /**
     * Generate a unique payment reference.
     */
    protected function generateReference(): string
    {
        return 'PMT-'.now()->format('Y').'-'.strtoupper(uniqid());
    }

    /**
     * Process a refund for a payment intent.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        // Validate refund amount
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Refund amount must be greater than zero');
        }

        if ($amount > $intent->amount) {
            throw new \InvalidArgumentException('Refund amount cannot exceed original payment amount');
        }

        // Resolve gateway
        $gateway = $this->resolveGateway($intent->provider);

        try {
            // Process refund through gateway
            $refundResult = $gateway->refund($intent, $amount, $reason);

            // Create refund transaction record
            $refundTransaction = PayTransaction::create([
                'pay_intent_id' => $intent->id,
                'company_id' => $intent->company_id,
                'provider' => $intent->provider,
                'transaction_type' => 'refund',
                'amount' => $amount,
                'currency' => $intent->currency,
                'provider_transaction_id' => $refundResult['transaction_id'] ?? 'REF-'.uniqid(),
                'status' => $refundResult['success'] ? 'successful' : 'failed',
                'raw_payload' => $refundResult,
                'processed_at' => now(),
                'error_message' => $refundResult['error_message'] ?? null,
            ]);

            return [
                'success' => $refundResult['success'],
                'transaction_id' => $refundTransaction->provider_transaction_id,
                'message' => $refundResult['message'] ?? 'Refund processed successfully',
                'refund_transaction' => $refundTransaction,
            ];

        } catch (\Exception $e) {
            // Create failed refund transaction
            PayTransaction::create([
                'pay_intent_id' => $intent->id,
                'company_id' => $intent->company_id,
                'provider' => $intent->provider,
                'transaction_type' => 'refund',
                'amount' => $amount,
                'currency' => $intent->currency,
                'provider_transaction_id' => 'REF-FAILED-'.uniqid(),
                'status' => 'failed',
                'raw_payload' => ['error' => $e->getMessage()],
                'processed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund failed: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }
}
