<?php

namespace Modules\CommunicationCentre\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCompany;

class Webhook extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'url',
        'secret',
        'events',
        'is_active',
        'provider',
        'headers',
        'timeout',
        'retry_attempts',
        'last_called_at',
        'last_response_status',
        'last_response_body',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'last_called_at' => 'datetime',
        'timeout' => 'integer',
        'retry_attempts' => 'integer',
    ];

    protected $attributes = [
        'events' => '[]',
        'headers' => '[]',
        'is_active' => true,
        'timeout' => 30,
        'retry_attempts' => 3,
    ];

    /**
     * Get the company that owns the webhook.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Check if webhook is configured for a specific event.
     */
    public function hasEvent(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    /**
     * Get the webhook signature for verification.
     */
    public function generateSignature(array $payload): string
    {
        $payloadString = json_encode($payload);

        return hash_hmac('sha256', $payloadString, $this->secret);
    }

    /**
     * Verify webhook signature.
     */
    public function verifySignature(string $signature, array $payload): bool
    {
        $expectedSignature = $this->generateSignature($payload);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Scope active webhooks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope webhooks for specific event.
     */
    public function scopeForEvent($query, string $event)
    {
        return $query->whereJsonContains('events', $event);
    }

    /**
     * Scope webhooks for specific provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}
