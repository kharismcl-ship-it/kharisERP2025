<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTerminal extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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