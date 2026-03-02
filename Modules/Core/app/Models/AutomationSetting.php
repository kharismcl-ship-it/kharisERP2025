<?php

namespace Modules\Core\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class AutomationSetting extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'module',
        'action',
        'company_id',
        'is_enabled',
        'schedule_type',
        'schedule_value',
        'last_run_at',
        'next_run_at',
        'config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'config' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class);
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function shouldRun(): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        if (! $this->next_run) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->next_run);
    }

    public function markAsRun()
    {
        $this->update([
            'last_run' => now(),
            'next_run' => $this->calculateNextRun(),
        ]);
    }

    protected function calculateNextRun()
    {
        return match ($this->schedule_type) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            'custom' => now()->add($this->schedule_value),
            default => null
        };
    }
}
