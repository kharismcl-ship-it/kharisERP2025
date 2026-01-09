<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Finance\Models\Invoice;

class HostelUtilityCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_id',
        'hostel_occupant_id',
        'utility_type',
        'meter_number',
        'previous_reading',
        'current_reading',
        'consumption',
        'rate_per_unit',
        'fixed_charge',
        'total_amount',
        'billing_period_start',
        'billing_period_end',
        'due_date',
        'status',
        'invoice_id',
        'billing_cycle_id',
    ];

    protected $casts = [
        'previous_reading' => 'decimal:2',
        'current_reading' => 'decimal:2',
        'consumption' => 'decimal:2',
        'rate_per_unit' => 'decimal:4',
        'fixed_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function occupant(): BelongsTo
    {
        return $this->belongsTo(HostelOccupant::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function billingCycle(): BelongsTo
    {
        return $this->belongsTo(HostelBillingCycle::class);
    }

    public function calculateConsumption(): float
    {
        if ($this->previous_reading && $this->current_reading) {
            return $this->current_reading - $this->previous_reading;
        }

        return 0;
    }

    public function calculateTotalAmount(): float
    {
        $consumption = $this->calculateConsumption();
        $variableCharge = $consumption * $this->rate_per_unit;

        return $variableCharge + $this->fixed_charge;
    }

    public function isConsumptionAbnormal(float $thresholdPercentage = 50): bool
    {
        if (! $this->previous_reading || ! $this->current_reading) {
            return false;
        }

        $consumption = $this->calculateConsumption();
        $expectedConsumption = $this->getExpectedConsumption();

        if ($expectedConsumption <= 0) {
            return false;
        }

        $deviation = abs($consumption - $expectedConsumption) / $expectedConsumption * 100;

        return $deviation > $thresholdPercentage;
    }

    public function getExpectedConsumption(): float
    {
        // Get average consumption for similar rooms/periods
        $average = HostelUtilityCharge::where('hostel_id', $this->hostel_id)
            ->where('utility_type', $this->utility_type)
            ->where('room_id', $this->room_id)
            ->whereNotNull('consumption')
            ->avg('consumption');

        return $average ?? 0;
    }

    public function syncWithMeterReading(float $newReading): bool
    {
        if ($newReading <= $this->current_reading) {
            return false;
        }

        $this->update([
            'previous_reading' => $this->current_reading,
            'current_reading' => $newReading,
            'consumption' => $newReading - $this->current_reading,
            'total_amount' => $this->calculateTotalAmount(),
        ]);

        return true;
    }
}
