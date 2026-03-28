<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementContract extends Model
{
    use HasFactory;

    protected $table = 'procurement_contracts';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'contract_number',
        'title',
        'contract_type',
        'start_date',
        'end_date',
        'total_value',
        'committed_value',
        'currency',
        'payment_terms',
        'status',
        'auto_renewal',
        'renewal_notice_days',
        'notes',
        'file_path',
        'created_by_user_id',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'total_value'         => 'decimal:2',
        'committed_value'     => 'decimal:2',
        'auto_renewal'        => 'boolean',
        'renewal_notice_days' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $contract) {
            if (empty($contract->contract_number)) {
                $prefix = 'CON-' . now()->format('Ym') . '-';
                $count  = static::where('contract_number', 'like', $prefix . '%')->count() + 1;
                $contract->contract_number = $prefix . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ProcurementContractLine::class, 'contract_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function remainingValue(): float
    {
        if ($this->total_value === null) {
            return PHP_FLOAT_MAX;
        }

        return max(0, (float) $this->total_value - (float) $this->committed_value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}