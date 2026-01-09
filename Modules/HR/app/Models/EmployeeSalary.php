<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_employee_salaries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'company_id',
        'salary_scale_id',
        'basic_salary',
        'currency',
        'effective_from',
        'effective_to',
        'is_current',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_current' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the employee salary.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee for this salary record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the salary scale for this salary record.
     */
    public function salaryScale()
    {
        return $this->belongsTo(SalaryScale::class);
    }
}
