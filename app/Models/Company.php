<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class Company extends Model implements HasAvatar
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'company_logo',
        'company_service_type',
        'company_service_description',
        'company_address',
        'company_country',
        'company_city',
        'company_location',
        'company_latitude',
        'company_longitude',
        'company_ghanapostgps',
        'company_phone',
        'company_email',
        'company_website',
        'is_active',
        'parent_company_id',
    ];

    protected function casts()
    {
        return [
            'is_active' => 'boolean',
            'company_location' => 'array',
            'company_latitude' => 'double',
            'company_longitude' => 'double',

        ];
    }

    /**
     * Users that belong to the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot(['position', 'is_active', 'assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Active users only.
     */
    public function activeUsers()
    {
        return $this->users()->wherePivot('is_active', true);
    }

    /**
     * Get the payment provider configurations for the company.
     */
    public function payProviderConfigs()
    {
        return $this->hasMany(PayProviderConfig::class, 'company_id');
    }

    // /**
    //  * Get employees assigned to this company.
    //  */
    // public function assignedEmployees()
    // {
    //     return $this->belongsToMany(
    //         \Modules\HR\Models\Employee::class,
    //         'employee_company_assignments',
    //         'company_id',
    //         'employee_id'
    //     )->wherePivot('is_active', true);
    // }

    /**
     * Get all employee assignments for this company.
     */
    public function employeeAssignments()
    {
        return $this->hasMany(\Modules\HR\Models\EmployeeCompanyAssignment::class, 'company_id');
    }

    /**
     * Automation settings for the company.
     */
    public function automationSettings()
    {
        return $this->hasMany(\Modules\Core\Models\AutomationSetting::class, 'company_id');
    }

    public function parentCompany()
    {
        return $this->belongsTo(self::class, 'parent_company_id');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! empty($this->company_logo)) {
            return Storage::url($this->company_logo);
        }

        return null;
    }
}
