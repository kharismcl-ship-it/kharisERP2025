<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;

class ErrorHandlerService
{
    /**
     * Maximum number of retry attempts for failed messages
     */
    const MAX_RETRY_ATTEMPTS = 3;

    /**
     * Retry delay in seconds (exponential backoff)
     */
    const RETRY_DELAY_BASE = 60; // 1 minute

    /**
     * Handle provider failure with retry logic
     */
    public function handleProviderFailure(CommMessage $message, \Exception $e, string $providerClass): void
    {
        $retryCount = $message->retry_count ?? 0;

        if ($retryCount < self::MAX_RETRY_ATTEMPTS) {
            $this->scheduleRetry($message, $retryCount + 1, $e->getMessage());
        } else {
            $this->markAsPermanentlyFailed($message, $e->getMessage(), $providerClass);
        }
    }

    /**
     * Schedule a retry for the failed message
     */
    protected function scheduleRetry(CommMessage $message, int $retryCount, string $errorMessage): void
    {
        $retryDelay = $this->calculateRetryDelay($retryCount);

        $message->update([
            'status' => 'retrying',
            'retry_count' => $retryCount,
            'error_message' => $errorMessage,
            'next_retry_at' => now()->addSeconds($retryDelay),
        ]);

        Log::warning("Scheduled retry #{$retryCount} for message {$message->id} in {$retryDelay} seconds", [
            'message_id' => $message->id,
            'retry_count' => $retryCount,
            'retry_delay' => $retryDelay,
            'error' => $errorMessage,
        ]);

        // Dispatch retry job (you can implement this with Laravel queues)
        // RetryMessageJob::dispatch($message)->delay($retryDelay);
    }

    /**
     * Mark message as permanently failed
     */
    protected function markAsPermanentlyFailed(CommMessage $message, string $errorMessage, string $providerClass): void
    {
        $message->update([
            'status' => 'failed_permanently',
            'error_message' => $errorMessage,
            'failed_at' => now(),
        ]);

        Log::error("Message {$message->id} permanently failed after ".self::MAX_RETRY_ATTEMPTS.' attempts', [
            'message_id' => $message->id,
            'provider' => $providerClass,
            'error' => $errorMessage,
        ]);

        // Optional: Trigger alert or notification for permanent failures
        $this->triggerPermanentFailureAlert($message, $providerClass);
    }

    /**
     * Calculate exponential backoff delay
     */
    protected function calculateRetryDelay(int $retryCount): int
    {
        return self::RETRY_DELAY_BASE * pow(2, $retryCount - 1);
    }

    /**
     * Trigger alert for permanent failures
     */
    protected function triggerPermanentFailureAlert(CommMessage $message, string $providerClass): void
    {
        // Implement your alerting mechanism here
        // This could be:
        // - Email to administrators
        // - Slack notification
        // - SMS alert
        // - Log to monitoring system

        Log::critical("PERMANENT COMMUNICATION FAILURE: Message {$message->id} failed permanently", [
            'message_id' => $message->id,
            'provider' => $providerClass,
            'to_email' => $message->to_email,
            'to_phone' => $message->to_phone,
            'subject' => $message->subject,
        ]);
    }

    /**
     * Check if a provider should be temporarily disabled (circuit breaker)
     */
    public function shouldDisableProvider(string $providerClass, int $failureThreshold = 5, int $timeout = 300): bool
    {
        $failureKey = "communication:provider_failures:{$providerClass}";
        $disableKey = "communication:provider_disabled:{$providerClass}";

        // Check if already disabled
        if (Cache::has($disableKey)) {
            return true;
        }

        // Get failure count
        $failureCount = Cache::get($failureKey, 0);

        // If failures exceed threshold, disable provider temporarily
        if ($failureCount >= $failureThreshold) {
            Cache::put($disableKey, true, $timeout);
            Cache::forget($failureKey);

            Log::warning("Circuit breaker triggered for provider: {$providerClass}", [
                'provider' => $providerClass,
                'failure_count' => $failureCount,
                'timeout_seconds' => $timeout,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Record provider failure for circuit breaker
     */
    public function recordProviderFailure(string $providerClass, int $ttl = 3600): void
    {
        $failureKey = "communication:provider_failures:{$providerClass}";

        Cache::increment($failureKey);
        Cache::put($failureKey, Cache::get($failureKey, 0), $ttl);
    }

    /**
     * Reset provider failure count
     */
    public function resetProviderFailures(string $providerClass): void
    {
        $failureKey = "communication:provider_failures:{$providerClass}";
        $disableKey = "communication:provider_disabled:{$providerClass}";

        Cache::forget($failureKey);
        Cache::forget($disableKey);

        Log::info("Provider failure count reset for: {$providerClass}", [
            'provider' => $providerClass,
        ]);
    }
}
