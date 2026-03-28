<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmIotAlertRule extends Model
{
    protected $table = 'farm_iot_alert_rules';

    protected $fillable = [
        'farm_iot_device_id',
        'company_id',
        'rule_name',
        'condition',
        'threshold_value',
        'alert_message',
        'severity',
        'notification_channel',
        'is_active',
        'last_triggered_at',
    ];

    protected $casts = [
        'threshold_value'   => 'float',
        'last_triggered_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FarmIotDevice::class, 'farm_iot_device_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}