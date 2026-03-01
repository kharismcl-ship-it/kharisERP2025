<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesLead extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'company_name',
        'source',
        'status',
        'assigned_to',
        'notes',
        'contacted_at',
        'converted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    const STATUSES  = ['new', 'contacted', 'qualified', 'lost', 'converted'];
    const SOURCES   = ['web', 'referral', 'cold-call', 'social', 'email', 'event', 'other'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SalesActivity::class, 'related_id')->where('related_type', self::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }
}