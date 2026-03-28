<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class Skill extends Model
{
    protected $table = 'hr_skills';

    protected $fillable = [
        'company_id',
        'skill_category_id',
        'name',
        'description',
        'skill_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }

    public function employeeSkills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public static function proficiencyLabel(int $level): string
    {
        return match ($level) {
            1 => 'Beginner',
            2 => 'Basic',
            3 => 'Intermediate',
            4 => 'Advanced',
            5 => 'Expert',
            default => 'Unknown',
        };
    }
}