<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommProviderConfig;

class RateLimitingService
{
    /**
     * Check if rate limit is exceeded for a provider
     */
    public function checkRateLimit(string $provider, string $channel, ?int $companyId = null): bool
    {
        $providerConfig = $this->getProviderConfig($provider, $channel, $companyId);

        if (! $providerConfig || ! $providerConfig->rate_limit_enabled) {
            return true; // No rate limiting configured
        }

        $key = $this->getRateLimitKey($provider, $channel, $companyId);
        $currentCount = Cache::get($key, 0);

        if ($currentCount >= $providerConfig->rate_limit) {
            Log::warning('Rate limit exceeded', [
                'provider' => $provider,
                'channel' => $channel,
                'company_id' => $companyId,
                'current_count' => $currentCount,
                'rate_limit' => $providerConfig->rate_limit,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Increment rate limit counter
     */
    public function incrementRateLimit(string $provider, string $channel, ?int $companyId = null): void
    {
        $providerConfig = $this->getProviderConfig($provider, $channel, $companyId);

        if (! $providerConfig || ! $providerConfig->rate_limit_enabled) {
            return;
        }

        $key = $this->getRateLimitKey($provider, $channel, $companyId);
        $expiresAt = now()->addSeconds($providerConfig->rate_limit_period);

        Cache::increment($key);
        Cache::put($key, Cache::get($key), $expiresAt);
    }

    /**
     * Get current rate limit usage
     */
    public function getRateLimitUsage(string $provider, string $channel, ?int $companyId = null): array
    {
        $providerConfig = $this->getProviderConfig($provider, $channel, $companyId);

        if (! $providerConfig || ! $providerConfig->rate_limit_enabled) {
            return [
                'enabled' => false,
                'current' => 0,
                'limit' => 0,
                'remaining' => 0,
                'percentage' => 0,
            ];
        }

        $key = $this->getRateLimitKey($provider, $channel, $companyId);
        $currentCount = Cache::get($key, 0);

        return [
            'enabled' => true,
            'current' => $currentCount,
            'limit' => $providerConfig->rate_limit,
            'remaining' => max(0, $providerConfig->rate_limit - $currentCount),
            'percentage' => $providerConfig->rate_limit > 0
                ? round(($currentCount / $providerConfig->rate_limit) * 100, 2)
                : 0,
            'period' => $providerConfig->rate_limit_period,
            'reset_in' => $this->getTimeUntilReset($key),
        ];
    }

    /**
     * Reset rate limit for a provider
     */
    public function resetRateLimit(string $provider, string $channel, ?int $companyId = null): void
    {
        $key = $this->getRateLimitKey($provider, $channel, $companyId);
        Cache::forget($key);
    }

    /**
     * Get provider configuration with rate limiting settings
     */
    protected function getProviderConfig(string $provider, string $channel, ?int $companyId = null): ?CommProviderConfig
    {
        return CommProviderConfig::where('provider', $provider)
            ->where('channel', $channel)
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->first();
    }

    /**
     * Generate rate limit cache key
     */
    protected function getRateLimitKey(string $provider, string $channel, ?int $companyId = null): string
    {
        $parts = [
            'comm_rate_limit',
            $provider,
            $channel,
            $companyId ?? 'global',
        ];

        return implode(':', $parts);
    }

    /**
     * Get time until rate limit reset
     */
    protected function getTimeUntilReset(string $key): ?int
    {
        $ttl = Cache::get($key.'_ttl');
        if ($ttl) {
            return $ttl - time();
        }

        return null;
    }

    /**
     * Check global system-wide rate limits
     */
    public function checkGlobalRateLimit(string $channel): bool
    {
        $globalLimits = config('communicationcentre.rate_limits.global', []);

        if (! isset($globalLimits[$channel])) {
            return true;
        }

        $limit = $globalLimits[$channel];
        $key = "comm_global_rate_limit:{$channel}";
        $currentCount = Cache::get($key, 0);

        if ($currentCount >= $limit) {
            Log::warning('Global rate limit exceeded', [
                'channel' => $channel,
                'current_count' => $currentCount,
                'limit' => $limit,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Increment global rate limit counter
     */
    public function incrementGlobalRateLimit(string $channel): void
    {
        $globalLimits = config('communicationcentre.rate_limits.global', []);

        if (! isset($globalLimits[$channel])) {
            return;
        }

        $key = "comm_global_rate_limit:{$channel}";
        $expiresAt = now()->addHour(); // Global limits reset hourly

        Cache::increment($key);
        Cache::put($key, Cache::get($key), $expiresAt);
    }

    /**
     * Get all active rate limits
     */
    public function getAllRateLimits(?int $companyId = null): array
    {
        $providers = CommProviderConfig::where('rate_limit_enabled', true)
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->get();

        $result = [];
        foreach ($providers as $provider) {
            $result[] = $this->getRateLimitUsage(
                $provider->provider,
                $provider->channel,
                $provider->company_id
            );
        }

        return $result;
    }

    /**
     * Clean up expired rate limit counters
     */
    public function cleanupExpiredCounters(): int
    {
        // This would typically be handled by cache expiration
        // For Redis, counters expire automatically
        return 0;
    }

    /**
     * Get recommended wait time if rate limited
     */
    public function getWaitTime(string $provider, string $channel, ?int $companyId = null): ?int
    {
        $usage = $this->getRateLimitUsage($provider, $channel, $companyId);

        if (! $usage['enabled'] || $usage['remaining'] > 0) {
            return null;
        }

        return $usage['reset_in'] ?? 60; // Default 60 seconds wait
    }
}
