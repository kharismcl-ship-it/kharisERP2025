<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentContract extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_employment_contracts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'company_id',
        'contract_number',
        'start_date',
        'end_date',
        'contract_type',
        'probation_end_date',
        'is_current',
        'basic_salary',
        'currency',
        'working_hours_per_week',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'probation_end_date' => 'date',
            'is_current' => 'boolean',
            'basic_salary' => 'decimal:2',
        ];
    }

    /**
     * Get the company that owns the employment contract.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee for this employment contract.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
