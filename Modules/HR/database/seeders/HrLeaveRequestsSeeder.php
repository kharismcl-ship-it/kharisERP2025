<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrLeaveRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrLeaveRequestsSeeder.');
            return;
        }

        $employees = DB::table('hr_employees')
            ->where('company_id', $company->id)
            ->whereIn('employment_status', ['active', 'probation'])
            ->pluck('id', 'employee_code');

        $leaveTypes = DB::table('hr_leave_types')
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->get()->keyBy('code');

        if ($employees->isEmpty() || $leaveTypes->isEmpty()) {
            $this->command->warn('Missing employees or leave types. Skipping HrLeaveRequestsSeeder.');
            return;
        }

        $annualLeaveId = $leaveTypes['AL']->id ?? null;
        $sickLeaveId   = $leaveTypes['SL']->id ?? null;
        $studyLeaveId  = $leaveTypes['STL']->id ?? null;
        $emergencyId   = $leaveTypes['EL']->id ?? null;

        $approverEmpId = $employees['EMP003'] ?? null; // HR Manager

        $requests = [
            // Approved annual leaves
            [
                'emp' => 'EMP011', 'lt_id' => $annualLeaveId,
                'start' => '2026-01-06', 'end' => '2026-01-10', 'days' => 5,
                'status' => 'approved', 'reason' => 'Family vacation',
                'approved_by' => $approverEmpId, 'approved_at' => '2026-01-03 10:00:00',
            ],
            [
                'emp' => 'EMP014', 'lt_id' => $annualLeaveId,
                'start' => '2026-01-13', 'end' => '2026-01-16', 'days' => 4,
                'status' => 'approved', 'reason' => 'Personal travel',
                'approved_by' => $approverEmpId, 'approved_at' => '2026-01-10 09:30:00',
            ],
            [
                'emp' => 'EMP008', 'lt_id' => $annualLeaveId,
                'start' => '2026-02-02', 'end' => '2026-02-06', 'days' => 5,
                'status' => 'approved', 'reason' => 'Rest and recuperation',
                'approved_by' => $approverEmpId, 'approved_at' => '2026-01-30 14:00:00',
            ],

            // Approved sick leaves
            [
                'emp' => 'EMP010', 'lt_id' => $sickLeaveId,
                'start' => '2026-01-20', 'end' => '2026-01-22', 'days' => 3,
                'status' => 'approved', 'reason' => 'Flu and fever',
                'approved_by' => $approverEmpId, 'approved_at' => '2026-01-20 08:00:00',
            ],
            [
                'emp' => 'EMP017', 'lt_id' => $sickLeaveId,
                'start' => '2026-02-10', 'end' => '2026-02-11', 'days' => 2,
                'status' => 'approved', 'reason' => 'Medical appointment',
                'approved_by' => $approverEmpId, 'approved_at' => '2026-02-09 15:00:00',
            ],

            // Pending requests
            [
                'emp' => 'EMP012', 'lt_id' => $annualLeaveId,
                'start' => '2026-03-09', 'end' => '2026-03-13', 'days' => 5,
                'status' => 'pending', 'reason' => 'Wedding anniversary trip',
                'approved_by' => null, 'approved_at' => null,
            ],
            [
                'emp' => 'EMP015', 'lt_id' => $studyLeaveId,
                'start' => '2026-03-16', 'end' => '2026-03-18', 'days' => 3,
                'status' => 'pending', 'reason' => 'ACCA professional exam preparation',
                'approved_by' => null, 'approved_at' => null,
            ],
            [
                'emp' => 'EMP013', 'lt_id' => $emergencyId,
                'start' => '2026-03-04', 'end' => '2026-03-05', 'days' => 2,
                'status' => 'pending', 'reason' => 'Family bereavement',
                'approved_by' => null, 'approved_at' => null,
            ],
            [
                'emp' => 'EMP019', 'lt_id' => $annualLeaveId,
                'start' => '2026-03-23', 'end' => '2026-03-27', 'days' => 5,
                'status' => 'pending', 'reason' => 'Annual leave',
                'approved_by' => null, 'approved_at' => null,
            ],

            // Rejected
            [
                'emp' => 'EMP015', 'lt_id' => $annualLeaveId,
                'start' => '2026-01-26', 'end' => '2026-01-30', 'days' => 5,
                'status' => 'rejected', 'reason' => 'Planned holiday',
                'approved_by' => null, 'approved_at' => null,
                'rejected_reason' => 'Insufficient cover during peak sales week.',
            ],
            [
                'emp' => 'EMP017', 'lt_id' => $annualLeaveId,
                'start' => '2026-02-16', 'end' => '2026-02-20', 'days' => 5,
                'status' => 'rejected', 'reason' => 'Personal leave',
                'approved_by' => null, 'approved_at' => null,
                'rejected_reason' => 'Campaign launch period — cannot approve.',
            ],

            // Cancelled
            [
                'emp' => 'EMP011', 'lt_id' => $studyLeaveId,
                'start' => '2026-02-23', 'end' => '2026-02-25', 'days' => 3,
                'status' => 'cancelled', 'reason' => 'University workshop',
                'approved_by' => null, 'approved_at' => null,
            ],

            // Draft
            [
                'emp' => 'EMP020', 'lt_id' => $annualLeaveId,
                'start' => '2026-04-07', 'end' => '2026-04-11', 'days' => 5,
                'status' => 'draft', 'reason' => 'Easter break',
                'approved_by' => null, 'approved_at' => null,
            ],
            [
                'emp' => 'EMP016', 'lt_id' => $annualLeaveId,
                'start' => '2026-04-14', 'end' => '2026-04-18', 'days' => 5,
                'status' => 'draft', 'reason' => 'Team retreat follow-up',
                'approved_by' => null, 'approved_at' => null,
            ],
            [
                'emp' => 'EMP010', 'lt_id' => $annualLeaveId,
                'start' => '2026-05-04', 'end' => '2026-05-08', 'days' => 5,
                'status' => 'draft', 'reason' => 'Mid-year break',
                'approved_by' => null, 'approved_at' => null,
            ],
        ];

        $created = 0;

        foreach ($requests as $req) {
            $empId = $employees[$req['emp']] ?? null;

            if (! $empId || ! $req['lt_id']) {
                continue;
            }

            // Skip if already exists (matching employee, leave type, start date)
            $exists = DB::table('hr_leave_requests')
                ->where('employee_id', $empId)
                ->where('leave_type_id', $req['lt_id'])
                ->where('start_date', $req['start'])
                ->exists();

            if (! $exists) {
                DB::table('hr_leave_requests')->insert([
                    'company_id'              => $company->id,
                    'employee_id'             => $empId,
                    'leave_type_id'           => $req['lt_id'],
                    'start_date'              => $req['start'],
                    'end_date'                => $req['end'],
                    'total_days'              => $req['days'],
                    'status'                  => $req['status'],
                    'reason'                  => $req['reason'],
                    'approved_by_employee_id' => $req['approved_by'],
                    'approved_at'             => $req['approved_at'] ?? null,
                    'rejected_reason'         => $req['rejected_reason'] ?? null,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
                $created++;
            }
        }

        $this->command->info("HrLeaveRequestsSeeder: {$created} leave requests seeded.");
    }
}
