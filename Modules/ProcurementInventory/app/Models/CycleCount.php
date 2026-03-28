<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CycleCount extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_cycle_counts';

    protected $fillable = [
        'company_id',
        'count_number',
        'warehouse_id',
        'count_type',
        'scheduled_date',
        'counted_date',
        'status',
        'counted_by_user_id',
        'approved_by_user_id',
        'variance_threshold_pct',
        'notes',
    ];

    protected $casts = [
        'scheduled_date'        => 'date',
        'counted_date'          => 'date',
        'variance_threshold_pct'=> 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $count) {
            if (empty($count->count_number)) {
                $count->count_number = static::generateCountNumber();
            }
        });
    }

    public static function generateCountNumber(): string
    {
        $prefix = 'CC';
        $year   = date('Y');
        $month  = date('m');
        $n      = static::where('count_number', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $n);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function countedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counted_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(CycleCountLine::class, 'count_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }
}