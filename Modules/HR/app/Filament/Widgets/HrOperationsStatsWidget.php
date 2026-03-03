<?php

namespace Modules\HR\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\HR\Models\DisciplinaryCase;
use Modules\HR\Models\EmployeeLoan;
use Modules\HR\Models\GrievanceCase;
use Modules\HR\Models\JobVacancy;
use Modules\HR\Models\PayrollRun;
use Modules\HR\Models\TrainingProgram;

class HrOperationsStatsWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function companyId(): ?int
    {
        $tenant = Filament::getTenant();

        return $tenant?->getKey()
            ?? auth()->user()->current_company_id
            ?? null;
    }

    protected function scope(string $model): \Illuminate\Database\Eloquent\Builder
    {
        $q   = $model::query();
        $cid = $this->companyId();

        if ($cid) {
            $q->where('company_id', $cid);
        }

        return $q;
    }

    protected function getStats(): array
    {
        $today = today();

        // Payroll
        $lastPayroll   = $this->scope(PayrollRun::class)
            ->where('status', 'paid')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();
        $draftPayrolls = $this->scope(PayrollRun::class)
            ->whereIn('status', ['draft', 'processing'])
            ->count();

        $payrollValue       = $lastPayroll ? $lastPayroll->period_label : 'None yet';
        $payrollDescription = $lastPayroll
            ? 'Net: '.number_format($lastPayroll->total_net, 0)
            : 'No paid payroll';
        if ($draftPayrolls > 0) {
            $payrollDescription .= " · {$draftPayrolls} draft";
        }

        // Loans
        $loanBase         = $this->scope(EmployeeLoan::class);
        $activeLoans      = (clone $loanBase)->where('status', 'active')->count();
        $totalOutstanding = (clone $loanBase)->where('status', 'active')->sum('outstanding_balance');

        // Recruitment
        $vacBase       = $this->scope(JobVacancy::class);
        $openVacancies = (clone $vacBase)->where('status', 'open')->count();
        $closingSoon   = (clone $vacBase)
            ->where('status', 'open')
            ->whereBetween('closing_date', [$today, $today->copy()->addDays(7)])
            ->count();

        // Training
        $trainBase       = $this->scope(TrainingProgram::class);
        $ongoingTraining = (clone $trainBase)->where('status', 'ongoing')->count();
        $plannedTraining = (clone $trainBase)->where('status', 'planned')->count();

        // Cases
        $openDisciplinary = $this->scope(DisciplinaryCase::class)
            ->whereIn('status', ['open', 'under_review'])
            ->count();
        $openGrievances   = $this->scope(GrievanceCase::class)
            ->whereIn('status', ['filed', 'under_investigation', 'hearing_scheduled'])
            ->count();
        $totalCases       = $openDisciplinary + $openGrievances;

        return [
            Stat::make('Last Paid Payroll', $payrollValue)
                ->description($payrollDescription)
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Active Loans', $activeLoans)
                ->description(number_format($totalOutstanding, 0).' outstanding')
                ->descriptionIcon('heroicon-m-credit-card')
                ->icon('heroicon-o-credit-card')
                ->color('danger'),

            Stat::make('Open Vacancies', $openVacancies)
                ->description($closingSoon > 0 ? "{$closingSoon} closing this week" : 'None closing soon')
                ->descriptionIcon('heroicon-m-briefcase')
                ->icon('heroicon-o-briefcase')
                ->color('info'),

            Stat::make('Ongoing Training', $ongoingTraining)
                ->description("{$plannedTraining} planned")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Open Cases', $totalCases)
                ->description("{$openDisciplinary} disciplinary · {$openGrievances} grievance")
                ->descriptionIcon('heroicon-m-scale')
                ->icon('heroicon-o-scale')
                ->color($totalCases > 0 ? 'danger' : 'success'),
        ];
    }
}
