<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class FarmWeatherLog extends Model
{
    protected $table = 'farm_weather_logs';

    protected $fillable = [
        'farm_id', 'company_id',
        'log_date', 'rainfall_mm',
        'min_temp_c', 'max_temp_c', 'humidity_pct',
        'wind_speed_kmh', 'weather_condition', 'notes',
    ];

    protected $casts = [
        'log_date'       => 'date',
        'rainfall_mm'    => 'decimal:2',
        'min_temp_c'     => 'decimal:2',
        'max_temp_c'     => 'decimal:2',
        'wind_speed_kmh' => 'decimal:2',
        'humidity_pct'   => 'integer',
    ];

    const CONDITIONS = [
        'sunny', 'partly_cloudy', 'cloudy', 'rainy', 'stormy', 'dry', 'foggy',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}