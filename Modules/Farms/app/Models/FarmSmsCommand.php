<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSmsCommand extends Model
{
    protected $table = 'farm_sms_commands';

    protected $fillable = [
        'phone_number', 'farm_worker_id', 'company_id', 'raw_message',
        'command_type', 'parsed_data', 'status', 'response_message', 'processed_at',
    ];

    protected $casts = [
        'parsed_data'  => 'array',
        'processed_at' => 'datetime',
    ];

    public function farmWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class);
    }
}