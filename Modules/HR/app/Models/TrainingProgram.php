<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class TrainingProgram extends Model
{
    protected $table = 'hr_training_programs';

    protected $fillable = [
        'company_id', 'title', 'type', 'description', 'provider',
        'start_date', 'end_date', 'cost', 'max_participants',
        'status', 'certificate_template_path',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'cost'             => 'decimal:2',
        'max_participants' => 'integer',
    ];

    const TYPES    = ['internal' => 'Internal', 'external' => 'External', 'online' => 'Online', 'conference' => 'Conference'];
    const STATUSES = ['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(TrainingNomination::class, 'training_program_id');
    }
}