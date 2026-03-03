<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;

class HRDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Dependency order:
     *  1. Departments        — no HR deps
     *  2. JobPositions       — needs departments
     *  3. Employees          — needs departments + positions
     *  4. LeaveTypes         — no HR deps
     *  5. LeaveApprovalWorkflow — needs departments + employees
     *  6. LeaveBalances      — needs employees + leave types
     *  7. LeaveRequests      — needs employees + leave types + balances
     *  8. SalaryScales       — no HR deps
     *  9. EmployeeSalaries   — needs employees + salary scales
     */
    public function run(): void
    {
        $this->call([
            HrDepartmentsSeeder::class,
            HrJobPositionsSeeder::class,
            HrEmployeesSeeder::class,
            HrLeaveTypesSeeder::class,
            HrLeaveApprovalWorkflowSeeder::class,
            HrLeaveBalancesSeeder::class,
            HrLeaveRequestsSeeder::class,
            HrSalaryScaleSeeder::class,
            HrEmployeeSalariesSeeder::class,
        ]);
    }
}
