<?php

namespace Modules\HR\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class EmployeeDocument extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_employee_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'company_id',
        'document_type',
        'file_path',
        'uploaded_by_user_id',
        'description',
        'expires_at',
        'version',
        'requires_acknowledgment',
        'acknowledged_at',
    ];

    protected $casts = [
        'expires_at'              => 'date',
        'acknowledged_at'         => 'datetime',
        'requires_acknowledgment' => 'boolean',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at
            && $this->expires_at->isFuture()
            && $this->expires_at->diffInDays(now()) <= $days;
    }

    /**
     * Get the company that owns the employee document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee for this document.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
