<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class SkillCategory extends Model
{
    protected $table = 'hr_skill_categories';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'color',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'skill_category_id');
    }
}