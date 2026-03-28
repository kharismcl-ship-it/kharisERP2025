<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementApprovalRule extends Model
{
    use HasFactory;

    protected $table = 'procurement_approval_rules';

    protected $fillable = [
        'company_id',
        'name',
        'min_amount',
        'max_amount',
        'approver_user_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function matchingRules(PurchaseOrder $po): Collection
    {
        return static::active()
            ->where('company_id', $po->company_id)
            ->where(function ($q) use ($po) {
                $q->whereNull('min_amount')
                    ->orWhere('min_amount', '<=', $po->total);
            })
            ->where(function ($q) use ($po) {
                $q->whereNull('max_amount')
                    ->orWhere('max_amount', '>', $po->total);
            })
            ->orderBy('sort_order')
            ->get();
    }
}