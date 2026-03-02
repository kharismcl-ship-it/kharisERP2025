<?php

namespace Modules\HR\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\HR\Filament\Clusters\HrLearningCluster;
use Modules\HR\Filament\Clusters\HrPerformanceCluster;
use Modules\HR\Filament\Clusters\HrRecordsCluster;
use Modules\HR\Filament\Clusters\HrRecruitmentCluster;
use Modules\HR\Filament\Clusters\HrRelationsCluster;
use Modules\HR\Filament\Clusters\HrSetupCluster;
use Modules\HR\Filament\Pages\HrAnalyticsDashboard;
use Modules\HR\Filament\Resources\AllowanceTypeResource;
use Modules\HR\Filament\Resources\AnnouncementResource;
use Modules\HR\Filament\Resources\AttendanceRecordResource;
use Modules\HR\Filament\Resources\BenefitTypeResource;
use Modules\HR\Filament\Resources\CertificationResource;
use Modules\HR\Filament\Resources\DeductionTypeResource;
use Modules\HR\Filament\Resources\DepartmentResource;
use Modules\HR\Filament\Resources\DisciplinaryCaseResource;
use Modules\HR\Filament\Resources\EmployeeCompanyAssignmentResource;
use Modules\HR\Filament\Resources\EmployeeDocumentResource;
use Modules\HR\Filament\Resources\EmployeeGoalResource;
use Modules\HR\Filament\Resources\EmployeeLoanResource;
use Modules\HR\Filament\Resources\EmployeeResource;
use Modules\HR\Filament\Resources\EmployeeSalaryResource;
use Modules\HR\Filament\Resources\EmploymentContractResource;
use Modules\HR\Filament\Resources\GrievanceCaseResource;
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource;
use Modules\HR\Filament\Resources\JobPositionResource;
use Modules\HR\Filament\Resources\JobVacancyResource;
use Modules\HR\Filament\Resources\KpiDefinitionResource;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource;
use Modules\HR\Filament\Resources\LeaveBalanceResource;
use Modules\HR\Filament\Resources\LeaveRequestResource;
use Modules\HR\Filament\Resources\LeaveTypeResource;
use Modules\HR\Filament\Resources\PayrollRunResource;
use Modules\HR\Filament\Resources\PerformanceCycleResource;
use Modules\HR\Filament\Resources\PerformanceReviewResource;
use Modules\HR\Filament\Resources\PublicHolidayResource;
use Modules\HR\Filament\Resources\SalaryScaleResource;
use Modules\HR\Filament\Resources\ShiftResource;
use Modules\HR\Filament\Resources\TrainingProgramResource;

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
        if ($panel->getId() === 'admin') {
            $panel->resources([
                // Core HR
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

                // Payroll
                PayrollRunResource::class,
                AllowanceTypeResource::class,
                DeductionTypeResource::class,

                // Workforce
                ShiftResource::class,
                PublicHolidayResource::class,

                // Recruitment
                JobVacancyResource::class,

                // Learning & Development
                TrainingProgramResource::class,
                CertificationResource::class,

                // Employee Relations
                DisciplinaryCaseResource::class,
                GrievanceCaseResource::class,

                // Performance
                EmployeeGoalResource::class,
                KpiDefinitionResource::class,

                // Benefits & Loans
                BenefitTypeResource::class,
                EmployeeLoanResource::class,

                // HR Comms
                AnnouncementResource::class,
            ]);
        } elseif ($panel->getId() === 'company-admin') {
            $panel->resources([
                // Core HR (operational)
                DepartmentResource::class,
                JobPositionResource::class,
                EmployeeResource::class,
                EmployeeSalaryResource::class,
                EmploymentContractResource::class,
                EmployeeDocumentResource::class,
                EmployeeCompanyAssignmentResource::class,
                HostelStaffAssignmentResource::class,
                SalaryScaleResource::class,

                // Leave
                LeaveTypeResource::class,
                LeaveRequestResource::class,
                LeaveBalanceResource::class,
                LeaveApprovalWorkflowResource::class,

                // Payroll
                PayrollRunResource::class,
                AllowanceTypeResource::class,
                DeductionTypeResource::class,
                BenefitTypeResource::class,

                // Workforce
                ShiftResource::class,
                PublicHolidayResource::class,

                // Recruitment
                JobVacancyResource::class,

                // Learning & Development
                TrainingProgramResource::class,
                CertificationResource::class,

                // Employee Relations
                DisciplinaryCaseResource::class,
                GrievanceCaseResource::class,

                // Performance
                PerformanceReviewResource::class,
                AttendanceRecordResource::class,
                EmployeeGoalResource::class,
                KpiDefinitionResource::class,

                // Benefits & Loans
                EmployeeLoanResource::class,

                // HR Comms
                AnnouncementResource::class,
            ]);
        }

        // Clusters extend Page — register them via pages() so Filament
        // recognises them as navigatable cluster containers.
        $panel->pages([
            HrSetupCluster::class,
            HrRecruitmentCluster::class,
            HrPerformanceCluster::class,
            HrRelationsCluster::class,
            HrLearningCluster::class,
            HrRecordsCluster::class,
            HrAnalyticsDashboard::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}