<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSensorReading extends Model
{
    protected $table = 'farm_sensor_readings';

    // No updated_at — only recorded_at
    public $timestamps = false;

    protected $fillable = [
        'farm_iot_device_id',
        'company_id',
        'farm_id',
        'reading_value',
        'reading_unit',
        'recorded_at',
        'quality_flag',
        'notes',
    ];

    protected $casts = [
        'recorded_at'   => 'datetime',
        'reading_value' => 'float',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FarmIotDevice::class, 'farm_iot_device_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}