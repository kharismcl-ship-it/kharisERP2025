<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmProduceLot extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_produce_lots';

    protected $fillable = [
        'company_id',
        'farm_id',
        'harvest_record_id',
        'lot_number',
        'produce_inventory_id',
        'quantity_kg',
        'unit',
        'harvest_date',
        'expiry_date',
        'storage_location',
        'quality_grade',
        'moisture_content_pct',
        'aflatoxin_ppb',
        'is_recalled',
        'recall_reason',
        'qr_code',
        'notes',
    ];

    protected $casts = [
        'harvest_date'         => 'date',
        'expiry_date'          => 'date',
        'is_recalled'          => 'boolean',
        'quantity_kg'          => 'float',
        'moisture_content_pct' => 'float',
        'aflatoxin_ppb'        => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $lot) {
            if (empty($lot->lot_number)) {
                $ym      = now()->format('Ym');
                $count   = static::where('company_id', $lot->company_id)
                    ->whereRaw("lot_number LIKE ?", ["LOT-{$ym}-%"])
                    ->count();
                $lot->lot_number = 'LOT-' . $ym . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function harvestRecord(): BelongsTo
    {
        return $this->belongsTo(HarvestRecord::class);
    }

    public function produceInventory(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'produce_inventory_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(FarmOrderItem::class, 'farm_produce_lot_id');
    }

    public function traceabilityChain(): array
    {
        $harvestRecord = $this->harvestRecord;
        $cropCycle     = $harvestRecord?->cropCycle;

        return [
            'lot'        => $this,
            'harvest'    => $harvestRecord,
            'crop_cycle' => $cropCycle,
            'farm'       => $this->farm,
            'inputs'     => $cropCycle?->inputApplications ?? collect(),
            'orders'     => $this->orderItems()->with('farmOrder')->get()->pluck('farmOrder')->filter(),
        ];
    }
}