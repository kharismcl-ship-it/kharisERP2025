<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiScore extends Model
{
    protected $table = 'hr_kpi_scores';

    protected $fillable = [
        'performance_review_id',
        'kpi_definition_id',
        'target_value',
        'actual_value',
        'score',
        'notes',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'score'        => 'decimal:2',
    ];

    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class);
    }

    public function kpiDefinition(): BelongsTo
    {
        return $this->belongsTo(KpiDefinition::class);
    }

    /**
     * Auto-compute normalised score (0-100) when actual/target values are set.
     */
    public function computeScore(): float
    {
        if (! $this->target_value || $this->target_value == 0) {
            return 0;
        }

        return round(min(($this->actual_value / $this->target_value) * 100, 100), 2);
    }
}
