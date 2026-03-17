<?php

namespace Modules\HR\Services;

use App\Models\Company;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeCompanyAssignment;

class CompanyAssignmentService
{
    /**
     * Assign an employee to a company.
     *
     * @return EmployeeCompanyAssignment
     */
    public function assignEmployeeToCompany(Employee $employee, Company $company, array $attributes = [])
    {
        $defaults = [
            'start_date' => now(),
            'is_active' => true,
        ];

        return EmployeeCompanyAssignment::create(array_merge($defaults, $attributes, [
            'employee_id' => $employee->id,
            'company_id' => $company->id,
        ]));
    }

    /**
     * Assign an employee to a hostel's company.
     *
     * @param  mixed  $hostel
     * @return EmployeeCompanyAssignment
     */
    public function assignEmployeeToHostelCompany(Employee $employee, $hostel, array $attributes = [])
    {
        // Add hostel-specific information to the assignment reason
        $attributes['assignment_reason'] = ($attributes['assignment_reason'] ?? '').' (Hostel: '.$hostel->name.')';

        return $this->assignEmployeeToCompany($employee, $hostel->company, $attributes);
    }

    /**
     * Get all active company assignments for an employee.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAssignmentsForEmployee(Employee $employee)
    {
        return $employee->activeCompanyAssignments;
    }

    /**
     * End an employee's assignment to a company and revoke their scoped roles.
     *
     * @param  string|null  $endDate
     * @return bool
     */
    public function endAssignment(EmployeeCompanyAssignment $assignment, $endDate = null)
    {
        $assignment->is_active = false;
        $assignment->end_date  = $endDate ?? now();
        $saved = $assignment->save();

        // Revoke Spatie roles scoped to the company being ended
        $employee = $assignment->employee()->with('user')->first();
        if ($employee && $employee->user) {
            $companyId = $assignment->company_id;
            // Remove all roles the user holds for this company (team context)
            $rolesForCompany = $employee->user->roles()
                ->where('company_id', $companyId)
                ->get();

            foreach ($rolesForCompany as $role) {
                try {
                    $employee->user->removeRole($role);
                } catch (\Throwable) {
                    // Role may already be removed or not exist — safe to skip
                }
            }
        }

        return $saved;
    }
}
