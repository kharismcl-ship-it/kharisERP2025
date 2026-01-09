<?php

namespace Modules\PaymentsChannel\Services\Gateway;

use Illuminate\Support\Facades\Log;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class ManualGateway implements PaymentGatewayInterface
{
    /**
     * Initialize a manual payment.
     */
    public function initialize(PayIntent $intent): PaymentInitResponse
    {
        // For manual payments, we don't redirect. We just set the status to pending.
        $intent->update(['status' => 'pending']);

        return new PaymentInitResponse(
            redirect_url: null,
            reference: $intent->reference,
            provider_reference: $intent->provider_reference ?? uniqid('manual_'),
            extra_data: ['message' => 'Manual payment initialized. Awaiting confirmation.']
        );
    }

    /**
     * Verify a manual payment (admin confirmation).
     */
    public function verify(PayIntent $intent, array $payload = []): PaymentVerifyResult
    {
        // Manual verification should only be done by an authenticated admin.
        // The actual status change should be an explicit action in the admin panel.
        // This method just reports the current status.

        return new PaymentVerifyResult(
            status: $intent->status,
            amount: $intent->amount,
            currency: $intent->currency,
            provider_transaction_id: $intent->provider_reference,
            raw_payload: $intent->toArray()
        );
    }

    /**
     * Handle webhook payload from the manual provider.
     */
    public function handleWebhook(array $payload): PayIntent
    {
        // Manual payments don't have webhooks.
        // All status changes should be driven by admin actions.

        Log::warning('Attempted to call webhook for ManualGateway, which is not supported.');
        throw new \Exception('Webhook handling not applicable for Manual payments');
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
     * Process a manual refund.
     */
    public function refund(PayIntent $intent, int $amount, string $reason = ''): array
    {
        // For manual refunds, we just record the refund request
        // Actual processing would be done by an admin manually

        return [
            'success' => true,
            'transaction_id' => 'MANUAL-REF-'.uniqid(),
            'message' => 'Manual refund request recorded. Admin approval required.',
            'refund_amount' => $amount,
            'reason' => $reason,
            'requires_approval' => true,
        ];
    }
}
