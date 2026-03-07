<?php

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Finance\Models\CostCentre;

class RequisitionTemplate extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'request_type',
        'urgency',
        'default_title',
        'cost_centre_id',
        'default_items',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_items' => 'array',
            'is_active'     => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}