<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_job_positions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'title',
        'code',
        'description',
        'grade',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the job position.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department the job position belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employees with this job position.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
