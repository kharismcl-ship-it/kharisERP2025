<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmWeatherAlert extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_weather_alerts';

    protected $fillable = [
        'company_id',
        'farm_id',
        'alert_type',
        'severity',
        'title',
        'message',
        'temperature_c',
        'humidity_pct',
        'wind_speed_kmh',
        'rainfall_mm',
        'triggered_at',
        'resolved_at',
        'is_read',
    ];

    protected $casts = [
        'triggered_at'  => 'datetime',
        'resolved_at'   => 'datetime',
        'is_read'       => 'boolean',
        'temperature_c' => 'float',
        'humidity_pct'  => 'float',
        'wind_speed_kmh' => 'float',
        'rainfall_mm'   => 'float',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('resolved_at');
    }
}