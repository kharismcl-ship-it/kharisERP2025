<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionLot extends Model
{
    use HasFactory;

    protected $table = 'procurement_inspection_lots';

    protected $fillable = [
        'company_id',
        'goods_receipt_id',
        'lot_number',
        'inspection_date',
        'inspected_by_user_id',
        'status',
        'overall_result',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $lot) {
            if (empty($lot->lot_number)) {
                $prefix = 'INS-' . now()->format('Ym') . '-';
                $count  = static::where('lot_number', 'like', $prefix . '%')->count() + 1;
                $lot->lot_number = $prefix . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InspectionLotLine::class, 'lot_id');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by_user_id');
    }
}