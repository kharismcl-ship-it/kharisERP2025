<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockLot extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_stock_lots';

    protected $fillable = [
        'company_id',
        'item_id',
        'warehouse_id',
        'lot_number',
        'batch_number',
        'quantity_received',
        'quantity_on_hand',
        'unit_cost',
        'manufacture_date',
        'expiry_date',
        'goods_receipt_id',
        'goods_receipt_line_id',
        'status',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:4',
        'quantity_on_hand'  => 'decimal:4',
        'unit_cost'         => 'decimal:4',
        'manufacture_date'  => 'date',
        'expiry_date'       => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function goodsReceiptLine(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptLine::class);
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class, 'lot_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if ($this->expiry_date === null) {
            return false;
        }

        return $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }
}