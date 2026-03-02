<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class LeaveType extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_leave_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'max_days_per_year',
        'requires_approval',
        'is_paid',
        'is_active',
        'has_accrual',
        'accrual_rate',
        'accrual_frequency',
        'carryover_limit',
        'max_balance',
        'pro_rata_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'max_days_per_year' => 'integer',
            'requires_approval' => 'boolean',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'has_accrual' => 'boolean',
            'accrual_rate' => 'decimal:2',
            'carryover_limit' => 'decimal:2',
            'max_balance' => 'decimal:2',
            'pro_rata_enabled' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the leave type.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave requests for this leave type.
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
