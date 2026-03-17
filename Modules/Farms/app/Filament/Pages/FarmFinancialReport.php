<?php

namespace Modules\Farms\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Widgets\FarmFinancialReportStatsWidget;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\HarvestRecord;

class FarmFinancialReport extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 93;

    protected static ?string $navigationLabel = 'Farm Financial Report';

    protected string $view = 'farms::filament.pages.farm-financial-report';

    public string $selectedYear = '';

    public array $farmRows    = [];
    public array $expenseRows = [];
    public array $totals      = [];

    public function mount(): void
    {
        $this->selectedYear = (string) now()->year;
        $this->loadReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('selectedYear')
                ->label('Year')
                ->options($this->getYearOptions())
                ->live()
                ->afterStateUpdated(function () {
                    $this->loadReport();
                    $this->dispatch('financial-report-year-changed', year: (int) $this->selectedYear);
                }),
        ]);
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;
        $year      = (int) ($this->selectedYear ?: now()->year);

        $farms = Farm::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        $this->farmRows = $farms->map(function ($farm) use ($year) {
            $harvestRevenue = HarvestRecord::where('farm_id', $farm->id)
                ->whereYear('harvest_date', $year)
                ->sum('total_revenue');

            $saleRevenue = FarmSale::where('farm_id', $farm->id)
                ->whereYear('sale_date', $year)
                ->sum('total_amount');

            $totalRevenue = $harvestRevenue + $saleRevenue;

            $expenses = FarmExpense::where('farm_id', $farm->id)
                ->whereYear('expense_date', $year)
                ->sum('amount');

            $budgeted = FarmBudget::where('farm_id', $farm->id)
                ->where('budget_year', $year)
                ->sum('budgeted_amount');

            return [
                'farm'               => $farm->name,
                'harvest_revenue'    => $harvestRevenue,
                'sale_revenue'       => $saleRevenue,
                'total_revenue'      => $totalRevenue,
                'total_expenses'     => $expenses,
                'net_profit'         => $totalRevenue - $expenses,
                'budgeted'           => $budgeted,
                'budget_variance'    => $budgeted > 0 ? $budgeted - $expenses : null,
                'budget_utilisation' => $budgeted > 0 ? round(($expenses / $budgeted) * 100, 1) : null,
            ];
        })->toArray();

        $this->expenseRows = FarmExpense::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'category' => ucwords(str_replace('_', ' ', $r->category)),
                'total'    => $r->total,
            ])->toArray();

        $totalRevenue        = collect($this->farmRows)->sum('total_revenue');
        $totalExpenses       = collect($this->farmRows)->sum('total_expenses');
        $this->totals = [
            'total_revenue'  => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit'     => $totalRevenue - $totalExpenses,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [FarmFinancialReportStatsWidget::class];
    }

    public function getYearOptions(): array
    {
        $options = [];
        for ($y = now()->year; $y >= 2022; $y--) {
            $options[(string) $y] = (string) $y;
        }

        return $options;
    }
}
