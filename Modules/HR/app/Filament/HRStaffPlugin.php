<?php

namespace Modules\HR\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\HR\Filament\Pages\ClockInOutPage;
use Modules\HR\Filament\Pages\MyProfilePage;
use Modules\HR\Filament\Pages\StaffDashboard;
use Modules\HR\Filament\Resources\Staff\MyAnnouncementResource;
use Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource;
use Modules\HR\Filament\Resources\Staff\MyContractResource;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource;
use Modules\HR\Filament\Resources\Staff\MyLeaveBalanceResource;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource;
use Modules\HR\Filament\Resources\Staff\MyLoanResource;
use Modules\HR\Filament\Resources\Staff\MyPayslipResource;
use Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource;
use Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource;
use Modules\HR\Filament\Resources\Staff\MyTrainingResource;

class HRStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'hr-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyLeaveRequestResource::class,
            MyLeaveBalanceResource::class,
            MyPayslipResource::class,
            MyContractResource::class,
            MyAnnouncementResource::class,
            MyGrievanceResource::class,
            MyShiftScheduleResource::class,
            MyTrainingResource::class,
            MyCertificationResource::class,
            MyLoanResource::class,
            MyAttendanceRecordResource::class,
            MyPerformanceReviewResource::class,
            MyEmployeeGoalResource::class,
        ]);

        $panel->pages([
            StaffDashboard::class,
            MyProfilePage::class,
            ClockInOutPage::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
