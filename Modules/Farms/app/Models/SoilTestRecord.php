<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class SoilTestRecord extends Model
{
    protected $table = 'soil_test_records';

    protected $fillable = [
        'farm_id', 'farm_plot_id', 'company_id',
        'test_date', 'tested_by', 'lab_reference',
        'ph_level', 'nitrogen_pct', 'phosphorus_ppm', 'potassium_ppm',
        'organic_matter_pct', 'texture', 'recommendations', 'notes',
    ];

    protected $casts = [
        'test_date'          => 'date',
        'ph_level'           => 'decimal:2',
        'nitrogen_pct'       => 'decimal:3',
        'phosphorus_ppm'     => 'decimal:3',
        'potassium_ppm'      => 'decimal:3',
        'organic_matter_pct' => 'decimal:2',
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
}