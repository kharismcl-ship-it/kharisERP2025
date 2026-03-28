<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class OnboardingTask extends Model
{
    protected $table = 'hr_onboarding_tasks';

    protected $fillable = [
        'company_id',
        'employee_id',
        'title',
        'description',
        'assignee_type',
        'due_days_from_hire',
        'status',
        'is_template',
        'sort_order',
        'notes',
        'completed_by_employee_id',
        'completed_at',
    ];

    protected $casts = [
        'is_template'  => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'completed_by_employee_id');
    }

    public static function scopeTemplates($query)
    {
        return $query->where('is_template', true)->whereNull('employee_id');
    }

    public static function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId)->where('is_template', false);
    }

    public static function createFromTemplates(Employee $employee): void
    {
        $templates = static::scopeTemplates(
            static::where('company_id', $employee->company_id)
        )->orderBy('sort_order')->get();

        foreach ($templates as $template) {
            static::create([
                'company_id'       => $employee->company_id,
                'employee_id'      => $employee->id,
                'title'            => $template->title,
                'description'      => $template->description,
                'assignee_type'    => $template->assignee_type,
                'due_days_from_hire' => $template->due_days_from_hire,
                'status'           => 'pending',
                'is_template'      => false,
                'sort_order'       => $template->sort_order,
            ]);
        }
    }
}