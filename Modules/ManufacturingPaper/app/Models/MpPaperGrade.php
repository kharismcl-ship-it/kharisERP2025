<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class MpPaperGrade extends Model
{
    use BelongsToCompany;

    protected $table = 'mp_paper_grades';

    protected $fillable = [
        'company_id',
        'name',
        'gsm',
        'width_mm',
        'color',
        'category',
        'description',
        'unit_selling_price',
        'min_order_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'gsm'                => 'decimal:2',
        'width_mm'           => 'decimal:2',
        'unit_selling_price' => 'decimal:4',
        'min_order_quantity' => 'decimal:3',
    ];

    const CATEGORIES = ['printing', 'writing', 'packaging', 'tissue', 'specialty', 'newsprint', 'kraft'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(MpProductionBatch::class, 'paper_grade_id');
    }
}