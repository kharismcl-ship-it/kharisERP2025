<?php

namespace Modules\HR\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\DisciplinaryCase;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeLoan;
use Modules\HR\Models\GrievanceCase;
use Modules\HR\Models\JobVacancy;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\PayrollRun;
use Modules\HR\Models\PerformanceReview;
use Modules\HR\Models\TrainingProgram;

class HrAnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'HR Dashboard';

    protected string $view = 'hr::filament.pages.hr-analytics-dashboard';

    public array   $stats       = [];
    public array   $charts      = [];
    public array   $recentLeave = [];
    public array   $openCases   = [];
    public ?string $companyName = null;

    public function mount(): void
    {
        $tenant            = Filament::getTenant();
        $this->companyName = $tenant?->name;
        $this->stats       = $this->buildStats();
        $this->charts      = $this->buildCharts();
        $this->recentLeave = $this->buildRecentLeave();
        $this->openCases   = $this->buildOpenCases();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    protected function companyId(): ?int
    {
        $tenant = Filament::getTenant();

        return $tenant?->getKey()
            ?? auth()->user()->current_company_id
            ?? null;
    }

    /**
     * Return a query scoped to the current company (or global for HQ).
     *
     * @param  class-string  $model
     */
    protected function scope(string $model): \Illuminate\Database\Eloquent\Builder
    {
        $q   = $model::query();
        $cid = $this->companyId();

        if ($cid) {
            $q->where('company_id', $cid);
        }

        return $q;
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    protected function buildStats(): array
    {
        $today      = today();
        $monthStart = now()->startOfMonth();

        // ── Workforce ─────────────────────────────────────────────────────────
        $empBase  = $this->scope(Employee::class);
        $total    = (clone $empBase)->count();
        $active   = (clone $empBase)->where('employment_status', 'active')->count();
        $inactive = $total - $active;
        $newHires = (clone $empBase)->where('hire_date', '>=', $monthStart)->count();

        $genderCounts = (clone $empBase)
            ->where('employment_status', 'active')
            ->select('gender', DB::raw('count(*) as cnt'))
            ->groupBy('gender')
            ->pluck('cnt', 'gender')
            ->toArray();

        // ── Leave ─────────────────────────────────────────────────────────────
        $leaveBase         = $this->scope(LeaveRequest::class);
        $onLeaveToday      = (clone $leaveBase)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();
        $pendingLeave      = (clone $leaveBase)->where('status', 'pending')->count();
        $approvedThisMonth = (clone $leaveBase)
            ->where('status', 'approved')
            ->where('approved_at', '>=', $monthStart)
            ->count();

        // ── Payroll ───────────────────────────────────────────────────────────
        $lastPayroll = $this->scope(PayrollRun::class)
            ->where('status', 'paid')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();

        $draftPayrolls = $this->scope(PayrollRun::class)
            ->whereIn('status', ['draft', 'processing'])
            ->count();

        // ── Loans ─────────────────────────────────────────────────────────────
        $loanBase         = $this->scope(EmployeeLoan::class);
        $activeLoans      = (clone $loanBase)->where('status', 'active')->count();
        $totalOutstanding = (clone $loanBase)->where('status', 'active')->sum('outstanding_balance');

        // ── Recruitment ───────────────────────────────────────────────────────
        $vacBase       = $this->scope(JobVacancy::class);
        $openVacancies = (clone $vacBase)->where('status', 'open')->count();
        $closingSoon   = (clone $vacBase)
            ->where('status', 'open')
            ->whereBetween('closing_date', [$today, $today->copy()->addDays(7)])
            ->count();

        // ── Training ──────────────────────────────────────────────────────────
        $trainBase         = $this->scope(TrainingProgram::class);
        $ongoingTraining   = (clone $trainBase)->where('status', 'ongoing')->count();
        $plannedTraining   = (clone $trainBase)->where('status', 'planned')->count();
        $completedTraining = (clone $trainBase)
            ->where('status', 'completed')
            ->whereYear('end_date', now()->year)
            ->count();

        // ── Employee Relations ────────────────────────────────────────────────
        $openDisciplinary = $this->scope(DisciplinaryCase::class)
            ->whereIn('status', ['open', 'under_review'])
            ->count();

        $openGrievances = $this->scope(GrievanceCase::class)
            ->whereIn('status', ['filed', 'under_investigation', 'hearing_scheduled'])
            ->count();

        // ── Performance ───────────────────────────────────────────────────────
        $avgRating = round((float) ($this->scope(PerformanceReview::class)->avg('rating') ?? 0), 1);

        return compact(
            'total', 'active', 'inactive', 'newHires', 'genderCounts',
            'onLeaveToday', 'pendingLeave', 'approvedThisMonth',
            'lastPayroll', 'draftPayrolls',
            'activeLoans', 'totalOutstanding',
            'openVacancies', 'closingSoon',
            'ongoingTraining', 'plannedTraining', 'completedTraining',
            'openDisciplinary', 'openGrievances',
            'avgRating',
        );
    }

    // ── Charts ────────────────────────────────────────────────────────────────

    protected function buildCharts(): array
    {
        $cid = $this->companyId();

        // 1. Active headcount by department (top 8)
        $deptData = Employee::query()
            ->when($cid, fn ($q) => $q->where('hr_employees.company_id', $cid))
            ->where('hr_employees.employment_status', 'active')
            ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->select('hr_departments.name', DB::raw('count(*) as cnt'))
            ->groupBy('hr_departments.id', 'hr_departments.name')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get();

        // 2. Payroll gross/net trend — last 6 paid runs
        $payrollData = PayrollRun::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('status', 'paid')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(6)
            ->get(['period_year', 'period_month', 'total_gross', 'total_net', 'total_deductions'])
            ->sortBy(fn ($r) => $r->period_year * 100 + $r->period_month)
            ->values();

        // 3. Attendance this week (Mon → today) by status
        $attendanceData = AttendanceRecord::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('date', '>=', now()->startOfWeek())
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status');

        // 4. Leave requests last 30 days by status
        $leaveStatusData = LeaveRequest::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('created_at', '>=', now()->subDays(30))
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status');

        // 5. Employment type breakdown
        $empTypeData = Employee::query()
            ->when($cid, fn ($q) => $q->where('company_id', $cid))
            ->where('employment_status', 'active')
            ->select('employment_type', DB::raw('count(*) as cnt'))
            ->groupBy('employment_type')
            ->get();

        return [
            'dept_headcount' => [
                'labels' => $deptData->pluck('name')->toArray(),
                'data'   => $deptData->pluck('cnt')->toArray(),
            ],
            'payroll_trend' => [
                'labels'     => $payrollData->map(fn ($r) => Carbon::create($r->period_year, $r->period_month)->format('M Y'))->toArray(),
                'gross'      => $payrollData->pluck('total_gross')->map(fn ($v) => round((float) $v, 2))->toArray(),
                'net'        => $payrollData->pluck('total_net')->map(fn ($v) => round((float) $v, 2))->toArray(),
                'deductions' => $payrollData->pluck('total_deductions')->map(fn ($v) => round((float) $v, 2))->toArray(),
            ],
            'attendance' => [
                'labels' => $attendanceData->keys()->map(fn ($s) => ucfirst($s))->values()->toArray(),
                'data'   => $attendanceData->values()->toArray(),
            ],
            'leave_status' => [
                'labels' => $leaveStatusData->keys()->map(fn ($s) => ucfirst($s))->values()->toArray(),
                'data'   => $leaveStatusData->values()->toArray(),
            ],
            'emp_type' => [
                'labels' => $empTypeData->pluck('employment_type')->map(fn ($s) => ucwords(str_replace('_', ' ', (string) $s)))->toArray(),
                'data'   => $empTypeData->pluck('cnt')->toArray(),
            ],
        ];
    }

    // ── Activity Lists ────────────────────────────────────────────────────────

    protected function buildRecentLeave(): array
    {
        return $this->scope(LeaveRequest::class)
            ->with(['employee:id,first_name,last_name,full_name', 'leaveType:id,name'])
            ->latest()
            ->limit(7)
            ->get()
            ->map(fn ($r) => [
                'employee'   => $r->employee?->full_name ?? ($r->employee ? $r->employee->first_name . ' ' . $r->employee->last_name : '—'),
                'type'       => $r->leaveType?->name ?? '—',
                'start_date' => $r->start_date?->format('M d'),
                'end_date'   => $r->end_date?->format('M d'),
                'status'     => $r->status,
                'days'       => $r->total_days,
            ])
            ->toArray();
    }

    protected function buildOpenCases(): array
    {
        $disciplinary = $this->scope(DisciplinaryCase::class)
            ->with('employee:id,first_name,last_name,full_name')
            ->whereIn('status', ['open', 'under_review'])
            ->latest('incident_date')
            ->limit(4)
            ->get()
            ->map(fn ($c) => [
                'kind'     => 'Disciplinary',
                'employee' => $c->employee?->full_name ?? '—',
                'type'     => ucwords(str_replace('_', ' ', (string) $c->type)),
                'date'     => $c->incident_date?->format('M d, Y') ?? '—',
                'status'   => ucwords(str_replace('_', ' ', $c->status)),
                'color'    => $c->status === 'open' ? 'danger' : 'warning',
            ]);

        $grievances = $this->scope(GrievanceCase::class)
            ->with('employee:id,first_name,last_name,full_name')
            ->whereIn('status', ['filed', 'under_investigation', 'hearing_scheduled'])
            ->latest('filed_date')
            ->limit(4)
            ->get()
            ->map(fn ($c) => [
                'kind'     => 'Grievance',
                'employee' => $c->employee?->full_name ?? '—',
                'type'     => ucwords(str_replace('_', ' ', (string) $c->grievance_type)),
                'date'     => $c->filed_date?->format('M d, Y') ?? '—',
                'status'   => ucwords(str_replace('_', ' ', $c->status)),
                'color'    => $c->status === 'filed' ? 'danger' : 'warning',
            ]);

        return $disciplinary->concat($grievances)
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }
}
