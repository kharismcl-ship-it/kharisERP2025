<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class JobVacancy extends Model
{
    use BelongsToCompany;

    protected $table = 'hr_job_vacancies';

    protected $fillable = [
        'company_id', 'department_id', 'job_position_id', 'title', 'description',
        'requirements', 'employment_type', 'status', 'posted_date', 'closing_date',
        'vacancies_count', 'salary_min', 'salary_max', 'posted_by_employee_id',
    ];

    protected $casts = [
        'posted_date'     => 'date',
        'closing_date'    => 'date',
        'salary_min'      => 'decimal:2',
        'salary_max'      => 'decimal:2',
        'vacancies_count' => 'integer',
    ];

    const STATUSES = ['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed', 'filled' => 'Filled'];
    const EMPLOYMENT_TYPES = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'internship' => 'Internship'];

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

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'posted_by_employee_id');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_vacancy_id');
    }
}