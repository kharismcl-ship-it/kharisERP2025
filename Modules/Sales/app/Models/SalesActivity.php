<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalesActivity extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'subject',
        'body',
        'scheduled_at',
        'completed_at',
        'outcome',
        'assigned_to',
        'related_id',
        'related_type',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    const TYPES = ['call', 'email', 'meeting', 'demo', 'task', 'note'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}