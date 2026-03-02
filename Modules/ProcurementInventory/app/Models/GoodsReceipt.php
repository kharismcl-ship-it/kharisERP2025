<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class GoodsReceipt extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'goods_receipts';

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'vendor_id',
        'grn_number',
        'receipt_date',
        'received_by',
        'warehouse_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $grn) {
            if (empty($grn->grn_number)) {
                $grn->grn_number = static::generateGrnNumber();
            }
        });
    }

    public static function generateGrnNumber(): string
    {
        $prefix = 'GRN';
        $year   = date('Y');
        $month  = date('m');
        $count  = static::where('grn_number', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%s-%06d', $prefix, $year, $month, $count);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(GoodsReceiptLine::class);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}