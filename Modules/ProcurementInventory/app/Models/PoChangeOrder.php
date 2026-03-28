<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoChangeOrder extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_po_change_orders';

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'change_order_number',
        'change_type',
        'description',
        'previous_total',
        'new_total',
        'amount_change',
        'status',
        'requested_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'previous_total' => 'decimal:2',
        'new_total'      => 'decimal:2',
        'amount_change'  => 'decimal:2',
        'approved_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $co) {
            if (empty($co->change_order_number)) {
                $co->change_order_number = static::generateChangeOrderNumber();
            }
        });
    }

    public static function generateChangeOrderNumber(): string
    {
        $prefix = 'CO';
        $year   = date('Y');
        $month  = date('m');
        $n      = static::where('change_order_number', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $n);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}