<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ProcurementInventory\Events\WarehouseTransferCompleted;
use App\Models\Concerns\BelongsToCompany;

class WarehouseTransfer extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'warehouse_transfers';

    protected $fillable = [
        'company_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'reference',
        'status',
        'requested_by',
        'approved_by',
        'notes',
        'transferred_at',
        'completed_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'completed_at'   => 'datetime',
    ];

    const STATUSES = ['draft', 'in_transit', 'completed', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (self $transfer) {
            if (empty($transfer->reference)) {
                $transfer->reference = static::generateReference();
            }
        });

        static::updated(function (self $transfer) {
            if ($transfer->wasChanged('status') && $transfer->status === 'completed') {
                WarehouseTransferCompleted::dispatch($transfer);
            }
        });
    }

    public static function generateReference(): string
    {
        $prefix = 'WT';
        $year   = date('Y');
        $month  = date('m');
        $count  = static::where('reference', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $count);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(WarehouseTransferLine::class);
    }

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isInTransit(): bool { return $this->status === 'in_transit'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
}