<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use Modules\ProcurementInventory\Models\Item;
use App\Models\Concerns\BelongsToCompany;

class MaterialUsage extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'construction_project_id',
        'project_phase_id',
        'company_id',
        'item_id',
        'material_name',
        'unit',
        'quantity',
        'unit_cost',
        'total_cost',
        'usage_date',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:4',
        'total_cost' => 'decimal:2',
        'usage_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $usage) {
            if ($usage->quantity && $usage->unit_cost && ! $usage->isDirty('total_cost')) {
                $usage->total_cost = round($usage->quantity * $usage->unit_cost, 2);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Optional link to a ProcurementInventory item for stock tracking.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
