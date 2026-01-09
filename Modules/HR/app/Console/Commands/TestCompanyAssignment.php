<?php

namespace Modules\HR\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeCompanyAssignment;

class TestCompanyAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:test-assignment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the company assignment functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing company assignment functionality...');

        // Get the first employee and company
        $employee = Employee::first();
        $company = Company::where('slug', 'kharis-farms')->first();

        if (! $employee || ! $company) {
            $this->error('No employee or company found for testing');

            return 1;
        }

        $this->info("Testing with employee: {$employee->full_name}");
        $this->info("Testing with company: {$company->name}");

        // Test direct creation with unique date
        $assignment = EmployeeCompanyAssignment::create([
            'employee_id' => $employee->id,
            'company_id' => $company->id,
            'start_date' => now()->subDays(5), // Use 5 days ago to avoid duplicate
            'assignment_reason' => 'Test assignment',
            'is_active' => true,
        ]);

        $this->info("Created assignment with ID: {$assignment->id}");

        // Test employee method with unique date
        $assignment2 = $employee->assignToCompany($company, [
            'assignment_reason' => 'Test assignment via employee method',
            'start_date' => now()->subDays(10), // Use 10 days ago to avoid duplicate
        ]);

        $this->info("Created assignment via employee method with ID: {$assignment2->id}");

        // Test if employee is assigned to company
        $isAssigned = $employee->isAssignedToCompany($company);
        $this->info('Employee is assigned to company: '.($isAssigned ? 'Yes' : 'No'));

        // Show all assignments
        $assignments = $employee->activeCompanyAssignments;
        $this->info("Employee has {$assignments->count()} active assignments");

        $this->info('Test completed successfully!');

        return 0;
    }
}
