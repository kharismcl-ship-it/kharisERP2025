<?php

namespace Modules\HR\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\HR\Filament\Resources\AttendanceRecordResource;
use Modules\HR\Filament\Resources\DepartmentResource;
use Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource;
use Modules\HR\Filament\Resources\EmployeeDocumentResource;
use Modules\HR\Filament\Resources\EmployeeResource;
use Modules\HR\Filament\Resources\EmployeeSalaryResource;
use Modules\HR\Filament\Resources\EmploymentContractResource;
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource;
use Modules\HR\Filament\Resources\JobPositionResource;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource;
use Modules\HR\Filament\Resources\LeaveBalanceResource;
use Modules\HR\Filament\Resources\LeaveRequestResource;
use Modules\HR\Filament\Resources\LeaveTypeResource;
use Modules\HR\Filament\Resources\PerformanceCycleResource;
use Modules\HR\Filament\Resources\PerformanceReviewResource;
use Modules\HR\Filament\Resources\SalaryScaleResource;

class HRFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'hr';
    }

    public function register(Panel $panel): void
    {
        // Split resources between admin and company panels
        if ($panel->getId() === 'admin') {
            // Admin panel resources (HQ/management)
            $panel->resources([
                DepartmentResource::class,
                JobPositionResource::class,
                SalaryScaleResource::class,
                LeaveTypeResource::class,
                PerformanceCycleResource::class,
                EmployeeResource::class,
                EmployeeSalaryResource::class,
                EmploymentContractResource::class,
                EmployeeDocumentResource::class,
                LeaveRequestResource::class,
                LeaveApprovalWorkflowResource::class,
                LeaveBalanceResource::class,
                PerformanceReviewResource::class,
                AttendanceRecordResource::class,
                EmployeeCompanyAssignmentResource::class,
                HostelStaffAssignmentResource::class,
            ]);
        } elseif ($panel->getId() === 'company-admin') {
            // Company panel resources (operational/tenant-specific)
            $panel->resources([
                EmployeeResource::class,
                EmployeeSalaryResource::class,
                EmploymentContractResource::class,
                EmployeeDocumentResource::class,
                LeaveRequestResource::class,
                PerformanceReviewResource::class,
                AttendanceRecordResource::class,
                EmployeeCompanyAssignmentResource::class,
                HostelStaffAssignmentResource::class,
            ]);
        }

        // Register pages for both panels
        $panel->pages([
            // Add HR-specific pages here when created
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot logic here
    }
}
