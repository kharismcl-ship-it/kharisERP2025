<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class Survey extends Model
{
    protected $table = 'hr_surveys';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'survey_type',
        'status',
        'is_anonymous',
        'starts_at',
        'ends_at',
        'created_by_employee_id',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->ends_at || $this->ends_at->isFuture());
    }
}