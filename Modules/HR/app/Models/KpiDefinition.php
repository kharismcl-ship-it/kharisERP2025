<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class KpiDefinition extends Model
{
    protected $table = 'hr_kpi_definitions';

    protected $fillable = [
        'company_id', 'department_id', 'job_position_id', 'name', 'description',
        'unit_of_measure', 'target_value', 'frequency', 'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    const FREQUENCIES = ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'annually' => 'Annually'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }
}
