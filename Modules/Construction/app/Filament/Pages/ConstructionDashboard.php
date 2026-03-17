<?php

namespace Modules\Construction\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Construction\Models\ConstructionProject;

class ConstructionDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 1;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'construction::filament.pages.construction-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $base = ConstructionProject::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $activeProjects    = (clone $base)->where('status', 'active')->count();
        $completedProjects = (clone $base)->where('status', 'completed')->count();
        $onHoldProjects    = (clone $base)->where('status', 'on_hold')->count();

        $nonCancelled = (clone $base)->whereNotIn('status', ['cancelled']);

        $totalBudget = (clone $nonCancelled)->sum('budget');
        $totalSpent  = (clone $nonCancelled)->sum('total_spent');

        $budgetUtilisation = $totalBudget > 0
            ? round(($totalSpent / $totalBudget) * 100, 1)
            : 0;

        $overBudgetCount = (clone $nonCancelled)
            ->whereColumn('total_spent', '>', 'budget')
            ->count();

        $projectRows = (clone $nonCancelled)
            ->orderBy('name')
            ->get(['id', 'name', 'status', 'budget', 'total_spent'])
            ->map(function ($project) {
                $budget   = (float) $project->budget;
                $spent    = (float) $project->total_spent;
                $variance = $budget - $spent;
                $util     = $budget > 0 ? round(($spent / $budget) * 100, 1) : 0;
                $utilColour = $util >= 90 ? 'danger' : ($util >= 75 ? 'warning' : 'success');

                return [
                    'name'        => $project->name,
                    'status'      => $project->status,
                    'budget'      => $budget,
                    'total_spent' => $spent,
                    'variance'    => $variance,
                    'util'        => $util,
                    'util_colour' => $utilColour,
                ];
            })
            ->toArray();

        return compact(
            'activeProjects',
            'completedProjects',
            'onHoldProjects',
            'totalBudget',
            'totalSpent',
            'budgetUtilisation',
            'overBudgetCount',
            'projectRows'
        );
    }

    public function getTitle(): string
    {
        return 'Construction Dashboard';
    }
}
