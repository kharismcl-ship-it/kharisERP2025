<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\ManufacturingWater\Events\MwDistributionCompleted;
use App\Models\Concerns\BelongsToCompany;

class MwDistributionRecord extends Model
{
    use BelongsToCompany;

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
        'customer_name',
        'customer_phone',
        'customer_email',
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

        static::created(function (self $record) {
            MwDistributionCompleted::dispatch($record);
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
