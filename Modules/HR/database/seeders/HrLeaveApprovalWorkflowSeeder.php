<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrLeaveApprovalWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrLeaveApprovalWorkflowSeeder.');
            return;
        }

        $hrDept = DB::table('hr_departments')
            ->where('company_id', $company->id)
            ->where('code', 'HR')
            ->first();

        // Workflow 1: Standard (Manager → HR)
        $standardId = DB::table('hr_leave_approval_workflows')
            ->where('company_id', $company->id)
            ->where('name', 'Standard Approval')
            ->value('id');

        if (! $standardId) {
            $standardId = DB::table('hr_leave_approval_workflows')->insertGetId([
                'company_id'            => $company->id,
                'name'                  => 'Standard Approval',
                'description'           => 'Two-level approval: direct manager followed by HR department head.',
                'is_active'             => true,
                'requires_all_approvals'=> true,
                'timeout_days'          => 3,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::table('hr_leave_approval_levels')->insert([
                [
                    'workflow_id'           => $standardId,
                    'level_number'          => 1,
                    'approver_type'         => 'manager',
                    'approver_employee_id'  => null,
                    'approver_department_id'=> null,
                    'approver_role'         => null,
                    'is_required'           => true,
                    'approval_order'        => 1,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ],
                [
                    'workflow_id'           => $standardId,
                    'level_number'          => 2,
                    'approver_type'         => 'department_head',
                    'approver_employee_id'  => null,
                    'approver_department_id'=> $hrDept?->id,
                    'approver_role'         => 'HR Manager',
                    'is_required'           => true,
                    'approval_order'        => 2,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ],
            ]);
        }

        // Workflow 2: Simple (Manager only)
        $simpleId = DB::table('hr_leave_approval_workflows')
            ->where('company_id', $company->id)
            ->where('name', 'Simple Approval')
            ->value('id');

        if (! $simpleId) {
            $simpleId = DB::table('hr_leave_approval_workflows')->insertGetId([
                'company_id'            => $company->id,
                'name'                  => 'Simple Approval',
                'description'           => 'Single-level approval by the direct manager.',
                'is_active'             => true,
                'requires_all_approvals'=> false,
                'timeout_days'          => 2,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::table('hr_leave_approval_levels')->insert([
                [
                    'workflow_id'           => $simpleId,
                    'level_number'          => 1,
                    'approver_type'         => 'manager',
                    'approver_employee_id'  => null,
                    'approver_department_id'=> null,
                    'approver_role'         => null,
                    'is_required'           => true,
                    'approval_order'        => 1,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ],
            ]);
        }

        $this->command->info('HrLeaveApprovalWorkflowSeeder: 2 workflows seeded (Standard + Simple).');
    }
}
