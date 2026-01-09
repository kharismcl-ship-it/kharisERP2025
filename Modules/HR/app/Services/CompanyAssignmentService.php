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
     * End an employee's assignment to a company.
     *
     * @param  string|null  $endDate
     * @return bool
     */
    public function endAssignment(EmployeeCompanyAssignment $assignment, $endDate = null)
    {
        $assignment->is_active = false;
        $assignment->end_date = $endDate ?? now();

        return $assignment->save();
    }
}
