<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\User;

class Announcement extends Model
{
    protected $table = 'hr_announcements';

    protected $fillable = [
        'company_id', 'title', 'content', 'priority', 'target_audience',
        'target_department_id', 'target_job_position_id', 'is_published',
        'published_at', 'expires_at', 'send_email', 'send_sms', 'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'send_email'   => 'boolean',
        'send_sms'     => 'boolean',
    ];

    const AUDIENCES  = ['all' => 'All Employees', 'department' => 'Department', 'job_position' => 'Job Position'];
    const PRIORITIES = ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'];

    public function getIsActiveAttribute(): bool
    {
        return $this->is_published && (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function targetDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'target_department_id');
    }

    public function targetJobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'target_job_position_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }
}