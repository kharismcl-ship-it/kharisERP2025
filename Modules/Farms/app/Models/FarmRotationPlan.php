<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmRotationPlan extends Model
{
    protected $table = 'farm_rotation_plans';

    protected $fillable = [
        'company_id',
        'farm_id',
        'farm_plot_id',
        'plan_name',
        'start_season',
        'total_years',
        'rotation_sequence',
        'nitrogen_balance_notes',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'rotation_sequence' => 'array',
        'is_active'         => 'boolean',
        'total_years'       => 'integer',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class);
    }

    public function cropCycles(): HasMany
    {
        return $this->hasMany(CropCycle::class);
    }

    /**
     * Get the crop entry for the current year offset from the start of the plan.
     */
    public function getCurrentYearCrop(): ?array
    {
        $sequence = $this->rotation_sequence ?? [];

        if (empty($sequence)) {
            return null;
        }

        // Determine the year offset (1-based)
        $startYear = (int) substr($this->start_season, 0, 4);
        $currentYear = (int) now()->format('Y');
        $yearOffset = ($currentYear - $startYear) % $this->total_years + 1;

        foreach ($sequence as $entry) {
            if (($entry['year'] ?? 0) == $yearOffset) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Validate the rotation sequence for consecutive same-crop violations.
     *
     * @return array{valid: bool, warnings: string[]}
     */
    public function validateRotation(): array
    {
        $sequence = $this->rotation_sequence ?? [];
        $warnings = [];

        if (count($sequence) < 2) {
            return ['valid' => true, 'warnings' => []];
        }

        $sorted = collect($sequence)->sortBy('year')->values();

        for ($i = 1; $i < $sorted->count(); $i++) {
            $prev = $sorted[$i - 1]['crop_name'] ?? null;
            $curr = $sorted[$i]['crop_name'] ?? null;

            if ($prev && $curr && strtolower($prev) === strtolower($curr)) {
                $warnings[] = "Year {$sorted[$i]['year']}: '{$curr}' follows the same crop — consider a rotation break.";
            }
        }

        return ['valid' => empty($warnings), 'warnings' => $warnings];
    }
}