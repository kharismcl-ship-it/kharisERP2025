<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds staff-portal module-access permissions AND the model-level Shield permissions
 * required for each staff role's HR resources (My Leave, My Payslip, etc.).
 *
 * Run once after migration, then again whenever companies are added:
 *   php artisan db:seed --class=StaffPortalPermissionsSeeder
 */
class StaffPortalPermissionsSeeder extends Seeder
{
    /** Module-access guard permissions for the staff portal. */
    private const ACCESS_PERMISSIONS = [
        'access_staff_hr',
        'access_staff_construction',
        'access_staff_fleet',
        'access_staff_sales',
        'access_staff_manufacturing',
        'access_staff_clientservice',
        'access_staff_finance',
        'access_staff_itsupport',
        'access_staff_requisition',
        'access_staff_farms',
        'access_staff_hostels',
    ];

    /**
     * Model-level Shield permissions granted automatically when a role has the
     * corresponding module-access permission.  These map 1-to-1 with the policies
     * that each staff resource delegates to, using the PascalCase:Model format
     * defined in config/filament-shield.php.
     */
    private const MODULE_MODEL_PERMISSIONS = [
        'access_staff_requisition' => [
            // Requisition — staff submit and can delete their own pending requisitions
            'ViewAny:Requisition', 'View:Requisition', 'Create:Requisition', 'Delete:Requisition',
        ],
        'access_staff_itsupport' => [
            // ItRequest — staff submit and can delete their own open requests
            'ViewAny:ItRequest', 'View:ItRequest', 'Create:ItRequest', 'Delete:ItRequest',
        ],
        'access_staff_construction' => [
            // WorkerAttendance — read-only for staff
            'ViewAny:WorkerAttendance', 'View:WorkerAttendance',
        ],
        'access_staff_fleet' => [
            // TripLog — read-only for drivers
            'ViewAny:TripLog', 'View:TripLog',
        ],
        'access_staff_sales' => [
            // SalesOpportunity + SalesActivity — read-only for sales reps
            'ViewAny:SalesOpportunity', 'View:SalesOpportunity',
            'ViewAny:SalesActivity', 'View:SalesActivity',
        ],
        'access_staff_manufacturing' => [
            // MpProductionBatch + MwWaterTestRecord — read-only for plant operators
            'ViewAny:MpProductionBatch', 'View:MpProductionBatch',
            'ViewAny:MwWaterTestRecord', 'View:MwWaterTestRecord',
        ],
        'access_staff_clientservice' => [
            // CsVisitor — read-only for receptionists
            'ViewAny:CsVisitor', 'View:CsVisitor',
        ],
        'access_staff_finance' => [
            // FixedAsset — read-only for finance staff
            'ViewAny:FixedAsset', 'View:FixedAsset',
        ],
        'access_staff_hostels' => [
            // VisitorLog — staff create/edit (log visitors in/out)
            'ViewAny:VisitorLog', 'View:VisitorLog', 'Create:VisitorLog', 'Update:VisitorLog',
            // Incident — staff create/update open incidents
            'ViewAny:Incident', 'View:Incident', 'Create:Incident', 'Update:Incident',
            // MaintenanceRequest — staff create/view only
            'ViewAny:MaintenanceRequest', 'View:MaintenanceRequest', 'Create:MaintenanceRequest',
            // HostelHousekeepingSchedule — read-only (start/complete via table actions)
            'ViewAny:HostelHousekeepingSchedule', 'View:HostelHousekeepingSchedule',
            'Update:HostelHousekeepingSchedule',
        ],
        'access_staff_farms' => [
            // FarmTask — read-only (assigned tasks); mark-done is a table action, not a policy gate
            'ViewAny:FarmTask', 'View:FarmTask',
            // FarmDailyReport — staff create/edit/delete draft reports
            'ViewAny:FarmDailyReport', 'View:FarmDailyReport',
            'Create:FarmDailyReport', 'Update:FarmDailyReport', 'Delete:FarmDailyReport',
            // FarmWorkerAttendance — read-only
            'ViewAny:FarmWorkerAttendance', 'View:FarmWorkerAttendance',
            // FarmRequest — staff create/edit/delete draft requests
            'ViewAny:FarmRequest', 'View:FarmRequest',
            'Create:FarmRequest', 'Update:FarmRequest', 'Delete:FarmRequest',
        ],
        'access_staff_hr' => [
            // LeaveRequest — staff create/edit/delete their own; Policy guards pending-only for edit/delete
            'ViewAny:LeaveRequest', 'View:LeaveRequest', 'Create:LeaveRequest',
            'Update:LeaveRequest', 'Delete:LeaveRequest',
            // GrievanceCase — staff can file and view (no update/delete for staff)
            'ViewAny:GrievanceCase', 'View:GrievanceCase', 'Create:GrievanceCase',
            // TrainingNomination — read-only for staff
            'ViewAny:TrainingNomination', 'View:TrainingNomination',
            // Certification — fully self-managed by staff
            'ViewAny:Certification', 'View:Certification', 'Create:Certification',
            'Update:Certification', 'Delete:Certification',
            // Announcement — read-only
            'ViewAny:Announcement', 'View:Announcement',
            // EmploymentContract — read-only
            'ViewAny:EmploymentContract', 'View:EmploymentContract',
            // EmployeeLoan — staff can apply, view, and cancel pending loans
            'ViewAny:EmployeeLoan', 'View:EmployeeLoan', 'Create:EmployeeLoan', 'Delete:EmployeeLoan',
            // PayrollLine (payslips) — read-only
            'ViewAny:PayrollLine', 'View:PayrollLine',
            // ShiftAssignment — read-only
            'ViewAny:ShiftAssignment', 'View:ShiftAssignment',
            // LeaveBalance — read-only (staff see their own balances)
            'ViewAny:LeaveBalance', 'View:LeaveBalance',
            // AttendanceRecord — read-only (clock-in/out via dedicated page)
            'ViewAny:AttendanceRecord', 'View:AttendanceRecord',
            // PerformanceReview — read-only (staff see their reviews)
            'ViewAny:PerformanceReview', 'View:PerformanceReview',
            // EmployeeGoal — fully self-managed by staff
            'ViewAny:EmployeeGoal', 'View:EmployeeGoal', 'Create:EmployeeGoal',
            'Update:EmployeeGoal', 'Delete:EmployeeGoal',
        ],
    ];

    /**
     * Role → module-access permission matrix.
     * HR resources are always accessible (access_staff_hr granted to all roles).
     */
    private const ROLES = [
        'office_staff' => [
            'access_staff_hr',
            'access_staff_itsupport',
            'access_staff_requisition',
            'access_staff_finance',
        ],
        'construction_worker' => [
            'access_staff_hr',
            'access_staff_construction',
            'access_staff_requisition',
        ],
        'farm_worker' => [
            'access_staff_hr',
            'access_staff_farms',
            'access_staff_requisition',
        ],
        'hostel_staff' => [
            'access_staff_hr',
            'access_staff_hostels',
            'access_staff_itsupport',
            'access_staff_requisition',
        ],
        'driver' => [
            'access_staff_hr',
            'access_staff_fleet',
        ],
        'sales_rep' => [
            'access_staff_hr',
            'access_staff_sales',
            'access_staff_itsupport',
            'access_staff_requisition',
        ],
        'plant_operator' => [
            'access_staff_hr',
            'access_staff_manufacturing',
            'access_staff_itsupport',
            'access_staff_requisition',
        ],
        'receptionist' => [
            'access_staff_hr',
            'access_staff_clientservice',
            'access_staff_itsupport',
        ],
        'finance_staff' => [
            'access_staff_hr',
            'access_staff_finance',
            'access_staff_itsupport',
            'access_staff_requisition',
        ],
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Ensure all access permissions exist (guard_name = web, no company_id) ──
        foreach (self::ACCESS_PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ── 2. Ensure all model-level Shield permissions exist ───────────────────
        $allModelPerms = array_unique(array_merge(...array_values(self::MODULE_MODEL_PERMISSIONS)));
        foreach ($allModelPerms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->command->info('Staff portal permissions created/verified.');

        // ── 3. Seed staff roles per company ─────────────────────────────────────
        $teamKey   = config('permission.column_names.team_foreign_key', 'company_id');
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found — skipping role seeding.');
            return;
        }

        foreach ($companies as $company) {
            $this->seedRolesForCompany($company, $teamKey);
        }

        $this->command->info("Staff roles seeded for {$companies->count()} company(ies).");
    }

    private function seedRolesForCompany(Company $company, string $teamKey): void
    {
        foreach (self::ROLES as $roleName => $accessPermNames) {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->where($teamKey, $company->id)
                ->first();

            if (! $role) {
                $role = Role::create([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                    $teamKey     => $company->id,
                ]);
            }

            // Collect all permissions to give: module-access + model-level Shield permissions
            $allPermNames = $accessPermNames;
            foreach ($accessPermNames as $accessPerm) {
                if (isset(self::MODULE_MODEL_PERMISSIONS[$accessPerm])) {
                    $allPermNames = array_merge($allPermNames, self::MODULE_MODEL_PERMISSIONS[$accessPerm]);
                }
            }
            $allPermNames = array_unique($allPermNames);

            $perms = Permission::whereIn('name', $allPermNames)
                ->where('guard_name', 'web')
                ->get();

            // givePermissionTo (not sync) — won't strip permissions assigned elsewhere
            foreach ($perms as $perm) {
                if (! $role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                }
            }
        }

        $this->command->line("  · {$company->name}: " . implode(', ', array_keys(self::ROLES)));
    }
}
