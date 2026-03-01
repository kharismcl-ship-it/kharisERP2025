<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class Shift extends Model
{
    protected $table = 'hr_shifts';

    protected $fillable = [
        'company_id', 'name', 'start_time', 'end_time', 'days_of_week',
        'break_duration_minutes', 'description', 'is_active',
    ];

    protected $casts = [
        'days_of_week'           => 'array',
        'is_active'              => 'boolean',
        'break_duration_minutes' => 'integer',
    ];

    const DAY_NAMES = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

    public function getDayNamesAttribute(): string
    {
        $days = $this->days_of_week ?? [];
        return implode(', ', array_map(fn ($d) => self::DAY_NAMES[$d] ?? $d, $days));
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
