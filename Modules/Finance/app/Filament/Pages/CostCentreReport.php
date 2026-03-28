<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\CostCentre;
use Modules\Finance\Models\JournalLine;

class CostCentreReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Cost Centre Report';

    protected string $view = 'finance::filament.pages.cost-centre-report';

    public ?int $cost_centre_id = null;

    public ?string $date_from = null;

    public ?string $date_to = null;

    public array $incomeRows = [];

    public array $expenseRows = [];

    public float $totalIncome = 0;

    public float $totalExpenses = 0;

    public array $costCentres = [];

    public function mount(): void
    {
        $this->date_from = now()->startOfYear()->toDateString();
        $this->date_to   = now()->toDateString();

        $companyId = auth()->user()?->current_company_id;
        $this->costCentres = CostCentre::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->pluck('name', 'id')
            ->toArray();
    }

    public function generate(): void
    {
        if (!$this->cost_centre_id) {
            return;
        }

        $lines = JournalLine::with('account')
            ->where('cost_centre_id', $this->cost_centre_id)
            ->whereHas('journalEntry', function ($q) {
                $q->when($this->date_from, fn ($q2) => $q2->whereDate('date', '>=', $this->date_from))
                  ->when($this->date_to, fn ($q2) => $q2->whereDate('date', '<=', $this->date_to));
            })
            ->get()
            ->groupBy('account_id');

        $incomeRows   = [];
        $expenseRows  = [];
        $totalIncome  = 0;
        $totalExpenses = 0;

        foreach ($lines as $accountId => $accountLines) {
            $account = $accountLines->first()->account;
            $debit   = (float) $accountLines->sum('debit');
            $credit  = (float) $accountLines->sum('credit');
            $net     = $credit - $debit;

            $row = [
                'account' => $account ? $account->name : 'Unknown',
                'debit'   => $debit,
                'credit'  => $credit,
                'net'     => abs($net),
            ];

            if ($account && in_array($account->type, ['income', 'revenue'])) {
                $incomeRows[]  = $row;
                $totalIncome  += abs($net);
            } else {
                $row['net']     = abs($net);
                $expenseRows[]  = $row;
                $totalExpenses += abs($net);
            }
        }

        $this->incomeRows    = $incomeRows;
        $this->expenseRows   = $expenseRows;
        $this->totalIncome   = $totalIncome;
        $this->totalExpenses = $totalExpenses;
    }
}