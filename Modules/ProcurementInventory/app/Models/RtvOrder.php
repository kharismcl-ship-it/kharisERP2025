<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RtvOrder extends Model
{
    use HasFactory;

    protected $table = 'procurement_rtv_orders';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'goods_receipt_id',
        'rtv_number',
        'return_date',
        'reason',
        'status',
        'debit_note_raised',
    ];

    protected $casts = [
        'return_date'       => 'date',
        'debit_note_raised' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $rtv) {
            if (empty($rtv->rtv_number)) {
                $prefix = 'RTV-' . now()->format('Ym') . '-';
                $count  = static::where('rtv_number', 'like', $prefix . '%')->count() + 1;
                $rtv->rtv_number = $prefix . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
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

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }
}