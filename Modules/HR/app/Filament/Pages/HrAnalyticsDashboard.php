<?php

namespace Modules\HR\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeLoan;
use Modules\HR\Models\JobVacancy;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\PayrollRun;
use Modules\HR\Models\TrainingProgram;

class HrAnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Comms';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'HR Analytics';

    protected string $view = 'hr::filament.pages.hr-analytics-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()->current_company_id ?? null;

        $employeeQuery = Employee::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        return [
            'total_employees'       => $employeeQuery->count(),
            'active_employees'      => $employeeQuery->where('employment_status', 'active')->count(),
            'on_leave_today'        => LeaveRequest::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'approved')
                ->where('start_date', '<=', today())
                ->where('end_date', '>=', today())
                ->count(),
            'open_vacancies'        => JobVacancy::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'open')->count(),
            'active_loans'          => EmployeeLoan::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')->count(),
            'active_training'       => TrainingProgram::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'active')->count(),
            'last_payroll'          => PayrollRun::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'paid')
                ->latest('period_year')->latest('period_month')
                ->first(),
            'pending_leave_count'   => LeaveRequest::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('status', 'pending')->count(),
        ];
    }
}
