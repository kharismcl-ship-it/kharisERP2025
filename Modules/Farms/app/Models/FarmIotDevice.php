<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmIotDevice extends Model
{
    protected $table = 'farm_iot_devices';

    protected $fillable = [
        'company_id',
        'farm_id',
        'farm_plot_id',
        'device_name',
        'device_type',
        'manufacturer',
        'model_number',
        'serial_number',
        'latitude',
        'longitude',
        'api_endpoint',
        'api_key',
        'reading_interval_minutes',
        'last_reading_at',
        'last_reading_value',
        'battery_pct',
        'status',
    ];

    protected $casts = [
        'last_reading_at'          => 'datetime',
        'battery_pct'              => 'float',
        'last_reading_value'       => 'float',
        'reading_interval_minutes' => 'integer',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(FarmSensorReading::class);
    }

    public function alertRules(): HasMany
    {
        return $this->hasMany(FarmIotAlertRule::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Determine if the device is considered online (last reading within 2× interval).
     */
    public function isOnline(): bool
    {
        if (! $this->last_reading_at) {
            return false;
        }

        return $this->last_reading_at->gt(now()->subMinutes($this->reading_interval_minutes * 2));
    }
}