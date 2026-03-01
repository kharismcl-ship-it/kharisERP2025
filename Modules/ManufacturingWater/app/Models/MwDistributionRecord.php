<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MwDistributionRecord extends Model
{
    protected $table = 'mw_distribution_records';

    protected $fillable = [
        'plant_id',
        'company_id',
        'distribution_date',
        'destination',
        'volume_liters',
        'unit_price',
        'total_amount',
        'vehicle_info',
        'distribution_reference',
        'notes',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'volume_liters'     => 'decimal:2',
        'unit_price'        => 'decimal:4',
        'total_amount'      => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record) {
            if (empty($record->distribution_reference)) {
                $record->distribution_reference = 'MW-DIST-' . strtoupper(Str::random(6));
            }
        });

        static::saving(function (self $record) {
            if ($record->volume_liters && $record->unit_price) {
                $record->total_amount = round($record->volume_liters * $record->unit_price, 2);
            }
        });
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MwPlant::class, 'plant_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
