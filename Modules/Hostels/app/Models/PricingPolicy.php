<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hostels\Database\factories\PricingPolicyFactory;

class PricingPolicy extends Model
{
    use HasFactory;

    protected $table = 'pricing_policies';

    protected $fillable = [
        'hostel_id',
        'name',
        'description',
        'policy_type',
        'adjustment_type',
        'adjustment_value',
        'is_active',
        'conditions',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'conditions' => 'array',
        'adjustment_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function newFactory(): PricingPolicyFactory
    {
        return PricingPolicyFactory::new();
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidForDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_to')
                ->orWhere('valid_to', '>=', $date);
        });
    }

    public function appliesToBooking($checkInDate, $checkOutDate, $numberOfNights): bool
    {
        $today = now();

        // Check if policy is active and valid for the booking period
        if (! $this->is_active) {
            return false;
        }

        // Check date validity
        if ($this->valid_from && $this->valid_from > $checkInDate) {
            return false;
        }

        if ($this->valid_to && $this->valid_to < $checkOutDate) {
            return false;
        }

        // Check policy-specific conditions
        return $this->checkConditions($checkInDate, $checkOutDate, $numberOfNights);
    }

    protected function checkConditions($checkInDate, $checkOutDate, $numberOfNights): bool
    {
        $conditions = $this->conditions ?? [];

        foreach ($conditions as $condition) {
            if (! $this->checkCondition($condition, $checkInDate, $checkOutDate, $numberOfNights)) {
                return false;
            }
        }

        return true;
    }

    protected function checkCondition($condition, $checkInDate, $checkOutDate, $numberOfNights): bool
    {
        switch ($condition['type'] ?? '') {
            case 'min_nights':
                return $numberOfNights >= ($condition['value'] ?? 0);
            case 'max_nights':
                return $numberOfNights <= ($condition['value'] ?? PHP_INT_MAX);
            case 'day_of_week':
                $checkInDay = $checkInDate->dayOfWeek;

                return in_array($checkInDay, $condition['values'] ?? []);
            case 'month':
                $checkInMonth = $checkInDate->month;

                return in_array($checkInMonth, $condition['values'] ?? []);
            case 'advance_booking':
                $daysInAdvance = $checkInDate->diffInDays(now());

                return $daysInAdvance <= ($condition['value'] ?? PHP_INT_MAX);
            default:
                return true;
        }
    }

    public function calculateAdjustment($basePrice): float
    {
        if ($this->adjustment_type === 'percentage') {
            return $basePrice * ($this->adjustment_value / 100);
        }

        return $this->adjustment_value;
    }
}
