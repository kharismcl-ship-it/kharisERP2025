<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryScale extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_salary_scales';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'min_basic',
        'max_basic',
        'currency',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'min_basic' => 'decimal:2',
            'max_basic' => 'decimal:2',
        ];
    }

    /**
     * Get the company that owns the salary scale.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee salaries using this scale.
     */
    public function employeeSalaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }
}
