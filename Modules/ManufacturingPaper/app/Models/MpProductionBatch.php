<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MpProductionBatch extends Model
{
    protected $table = 'mp_production_batches';

    protected $fillable = [
        'plant_id',
        'production_line_id',
        'paper_grade_id',
        'company_id',
        'batch_number',
        'quantity_planned',
        'quantity_produced',
        'unit',
        'waste_quantity',
        'raw_material_used',
        'production_cost',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity_planned'  => 'decimal:3',
        'quantity_produced' => 'decimal:3',
        'waste_quantity'    => 'decimal:3',
        'raw_material_used' => 'decimal:3',
        'production_cost'   => 'decimal:2',
        'start_time'        => 'datetime',
        'end_time'          => 'datetime',
    ];

    const STATUSES = ['planned', 'in_progress', 'completed', 'cancelled', 'on_hold'];

    protected static function booted(): void
    {
        static::creating(function (self $batch) {
            if (empty($batch->batch_number)) {
                $batch->batch_number = 'MP-' . strtoupper(Str::random(8));
            }
        });
    }

    public function getEfficiencyPercentAttribute(): ?float
    {
        if (! $this->quantity_planned || $this->quantity_planned == 0) {
            return null;
        }
        return round(($this->quantity_produced / $this->quantity_planned) * 100, 1);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MpPlant::class, 'plant_id');
    }

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(MpProductionLine::class, 'production_line_id');
    }

    public function paperGrade(): BelongsTo
    {
        return $this->belongsTo(MpPaperGrade::class, 'paper_grade_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function qualityRecords(): HasMany
    {
        return $this->hasMany(MpQualityRecord::class, 'production_batch_id');
    }
}