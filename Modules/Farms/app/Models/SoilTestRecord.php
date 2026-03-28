<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class SoilTestRecord extends Model
{
    use BelongsToCompany;

    protected $table = 'soil_test_records';

    protected $fillable = [
        'farm_id', 'farm_plot_id', 'company_id',
        'test_date', 'tested_by', 'lab_reference',
        'ph_level', 'nitrogen_pct', 'phosphorus_ppm', 'potassium_ppm',
        'organic_matter_pct', 'texture', 'recommendations', 'notes',
        'lime_recommendation_kg_ha', 'nitrogen_recommendation_kg_ha',
        'phosphorus_recommendation_kg_ha', 'potassium_recommendation_kg_ha',
        'recommendation_notes', 'interpretation',
    ];

    protected $casts = [
        'test_date'                        => 'date',
        'ph_level'                         => 'decimal:2',
        'nitrogen_pct'                     => 'decimal:3',
        'phosphorus_ppm'                   => 'decimal:3',
        'potassium_ppm'                    => 'decimal:3',
        'organic_matter_pct'               => 'decimal:2',
        'lime_recommendation_kg_ha'        => 'decimal:2',
        'nitrogen_recommendation_kg_ha'    => 'decimal:2',
        'phosphorus_recommendation_kg_ha'  => 'decimal:2',
        'potassium_recommendation_kg_ha'   => 'decimal:2',
    ];

    const TEXTURES = ['clay', 'loam', 'sandy', 'silt', 'clay_loam', 'sandy_loam'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function generateRecommendations(): array
    {
        $recs  = [];
        $notes = [];

        if ($this->ph_level !== null) {
            $ph = (float) $this->ph_level;
            if ($ph < 5.5) {
                $lime = round((6.0 - $ph) * 2.5 * 1000, 0);
                $recs['lime_recommendation_kg_ha'] = $lime;
                $notes[] = "pH {$ph} is strongly acidic — apply approx {$lime} kg/ha lime to raise to 6.0.";
            } elseif ($ph < 6.0) {
                $lime = round((6.0 - $ph) * 1.5 * 1000, 0);
                $recs['lime_recommendation_kg_ha'] = $lime;
                $notes[] = "pH {$ph} is mildly acidic — apply approx {$lime} kg/ha lime.";
            } elseif ($ph > 7.5) {
                $notes[] = "pH {$ph} is alkaline — consider sulfur application or acidifying fertilizers.";
            } else {
                $notes[] = "pH {$ph} is in optimal range (6.0–7.5).";
            }
        }

        if ($this->nitrogen_pct !== null && (float) $this->nitrogen_pct < 0.15) {
            $n = (float) $this->nitrogen_pct;
            $rec = round((0.15 - $n) * 10000, 0);
            $recs['nitrogen_recommendation_kg_ha'] = min($rec, 150);
            $notes[] = "Low nitrogen ({$n}%) — apply {$recs['nitrogen_recommendation_kg_ha']} kg N/ha.";
        }

        if ($this->phosphorus_ppm !== null && (float) $this->phosphorus_ppm < 15) {
            $p = (float) $this->phosphorus_ppm;
            $recs['phosphorus_recommendation_kg_ha'] = round((15 - $p) * 5, 0);
            $notes[] = "Low phosphorus ({$p} ppm) — apply {$recs['phosphorus_recommendation_kg_ha']} kg P2O5/ha.";
        }

        if ($this->potassium_ppm !== null && (float) $this->potassium_ppm < 120) {
            $k = (float) $this->potassium_ppm;
            $recs['potassium_recommendation_kg_ha'] = round((120 - $k) * 1.5, 0);
            $notes[] = "Low potassium ({$k} ppm) — apply {$recs['potassium_recommendation_kg_ha']} kg K2O/ha.";
        }

        $recs['recommendation_notes'] = implode(' ', $notes);
        $recs['interpretation']        = $this->buildInterpretation();

        return $recs;
    }

    private function buildInterpretation(): string
    {
        $parts = [];
        if ($this->ph_level !== null) {
            $ph = (float) $this->ph_level;
            $parts[] = 'pH: ' . ($ph < 6.0 ? 'Acidic' : ($ph > 7.5 ? 'Alkaline' : 'Optimal'));
        }
        if ($this->organic_matter_pct !== null) {
            $om = (float) $this->organic_matter_pct;
            $parts[] = 'OM: ' . ($om < 2 ? 'Low' : ($om > 5 ? 'High' : 'Good'));
        }
        if ($this->nitrogen_pct !== null) {
            $parts[] = 'N: ' . ((float) $this->nitrogen_pct < 0.15 ? 'Deficient' : 'Adequate');
        }
        if ($this->phosphorus_ppm !== null) {
            $parts[] = 'P: ' . ((float) $this->phosphorus_ppm < 15 ? 'Deficient' : 'Adequate');
        }
        if ($this->potassium_ppm !== null) {
            $parts[] = 'K: ' . ((float) $this->potassium_ppm < 120 ? 'Deficient' : 'Adequate');
        }

        return implode(' | ', $parts);
    }
}