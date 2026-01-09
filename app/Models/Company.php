<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active',
        'parent_company_id',
    ];

    /**
     * Users that belong to the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')->withPivot('position')->withTimestamps();
    }

    /**
     * Get the payment provider configurations for the company.
     */
    public function payProviderConfigs()
    {
        return $this->hasMany(PayProviderConfig::class, 'company_id');
    }

    /**
     * Get the parent company.
     */
    public function parentCompany()
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    /**
     * Get the child companies.
     */
    public function childCompanies()
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    /**
     * Get employees assigned to this company.
     */
    public function assignedEmployees()
    {
        return $this->belongsToMany(
            \Modules\HR\Models\Employee::class,
            'employee_company_assignments',
            'company_id',
            'employee_id'
        )->wherePivot('is_active', true);
    }

    /**
     * Get all employee assignments for this company.
     */
    public function employeeAssignments()
    {
        return $this->hasMany(\Modules\HR\Models\EmployeeCompanyAssignment::class, 'company_id');
    }

    /**
     * Roles that belong to the company.
     */
    public function roles()
    {
        return $this->hasMany(\App\Models\Role::class, 'company_id');
    }
}
