<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionDocument extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_project_id',
        'project_phase_id',
        'uploaded_by',
        'title',
        'description',
        'document_type',
        'file_paths',
        'version',
        'tags',
    ];

    protected $casts = [
        'file_paths' => 'array',
        'tags'       => 'array',
    ];

    const DOCUMENT_TYPES = [
        'site_plan', 'drawing', 'permit', 'contract', 'report',
        'photo', 'video', 'specification', 'other',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
