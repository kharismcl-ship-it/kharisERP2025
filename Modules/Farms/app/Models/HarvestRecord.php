<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class HarvestRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'farm_id',
        'crop_cycle_id',
        'company_id',
        'harvest_date',
        'quantity',
        'unit',
        'unit_price',
        'total_revenue',
        'buyer_name',
        'storage_location',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'harvest_date'  => 'date',
        'quantity'      => 'decimal:3',
        'unit_price'    => 'decimal:4',
        'total_revenue' => 'decimal:2',
        'attachments'   => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $harvest) {
            if ($harvest->quantity && $harvest->unit_price && ! $harvest->isDirty('total_revenue')) {
                $harvest->total_revenue = round($harvest->quantity * $harvest->unit_price, 2);
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
