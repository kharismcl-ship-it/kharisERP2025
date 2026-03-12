<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class PosTerminal extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'hostel_id',
        'name',
        'location',
        'is_active',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(\Modules\Hostels\Models\Hostel::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PosSession::class, 'terminal_id');
    }

    public function activeSession(): ?PosSession
    {
        return $this->sessions()->where('status', 'open')->latest()->first();
    }
}