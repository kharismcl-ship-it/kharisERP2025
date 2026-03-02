<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ProcurementInventory\Models\Item;
use App\Models\Concerns\BelongsToCompany;

class MwChemicalUsage extends Model
{
    use BelongsToCompany;

    protected $table = 'mw_chemical_usages';

    protected $fillable = [
        'plant_id',
        'company_id',
        'treatment_stage_id',
        'chemical_name',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'usage_date',
        'purpose',
        'batch_number',
        'notes',
        'item_id',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:4',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $usage) {
            if ($usage->quantity && $usage->unit_cost) {
                $usage->total_cost = round($usage->quantity * $usage->unit_cost, 2);
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MwPlant::class, 'plant_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function treatmentStage(): BelongsTo
    {
        return $this->belongsTo(MwTreatmentStage::class, 'treatment_stage_id');
    }
}
