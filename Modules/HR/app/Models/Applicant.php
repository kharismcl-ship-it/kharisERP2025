<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    protected $table = 'hr_applicants';

    protected $fillable = [
        'job_vacancy_id', 'first_name', 'last_name', 'email', 'phone',
        'status', 'source', 'resume_path', 'cover_letter_path', 'notes', 'applied_date',
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    const STATUSES = [
        'applied'              => 'Applied',
        'shortlisted'          => 'Shortlisted',
        'interview_scheduled'  => 'Interview Scheduled',
        'interviewed'          => 'Interviewed',
        'offered'              => 'Offered',
        'hired'                => 'Hired',
        'rejected'             => 'Rejected',
        'withdrawn'            => 'Withdrawn',
    ];

    const SOURCES = [
        'direct'       => 'Direct Application',
        'referral'     => 'Employee Referral',
        'job_board'    => 'Job Board',
        'social_media' => 'Social Media',
        'agency'       => 'Recruitment Agency',
        'other'        => 'Other',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
