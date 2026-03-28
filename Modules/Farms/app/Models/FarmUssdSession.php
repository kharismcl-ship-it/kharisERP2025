<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmUssdSession extends Model
{
    protected $table = 'farm_ussd_sessions';

    protected $fillable = [
        'session_id', 'phone_number', 'farm_worker_id', 'company_id',
        'current_menu', 'session_data', 'status', 'last_activity_at',
    ];

    protected $casts = [
        'session_data'     => 'array',
        'last_activity_at' => 'datetime',
    ];

    public function farmWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class);
    }
}