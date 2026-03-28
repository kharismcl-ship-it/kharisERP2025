<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionCustomField extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_custom_fields';

    protected $fillable = [
        'company_id',
        'request_type',
        'field_key',
        'field_label',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'field_options' => 'array',
            'is_required'   => 'boolean',
            'is_active'     => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForType($query, string $type)
    {
        return $query->whereIn('request_type', [$type, 'all']);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function values()
    {
        return $this->hasMany(RequisitionCustomFieldValue::class, 'custom_field_id');
    }
}