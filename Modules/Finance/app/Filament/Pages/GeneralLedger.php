<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalLine;

class GeneralLedger extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 55;

    use HasPageShield;

    protected static ?string $navigationLabel = 'General Ledger';

    protected string $view = 'finance::filament.pages.general-ledger';

    public ?string $fromDate   = null;
    public ?string $toDate     = null;
    public ?int    $accountId  = null;

    public array  $accounts    = [];
    public array  $lines       = [];
    public float  $openingBalance = 0;
    public float  $closingBalance = 0;
    public float  $totalDebits    = 0;
    public float  $totalCredits   = 0;

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate   = now()->toDateString();
        $this->loadAccounts();
    }

    public function loadAccounts(): void
    {
        $companyId = auth()->user()?->current_company_id;

        $this->accounts = Account::when(
            $companyId,
            fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id'))
        )
        ->orderBy('code')
        ->get()
        ->map(fn ($a) => ['id' => $a->id, 'label' => "{$a->code} — {$a->name}"])
        ->toArray();
    }

    public function loadReport(): void
    {
        if (! $this->accountId) {
            $this->lines          = [];
            $this->openingBalance = 0;
            $this->closingBalance = 0;
            $this->totalDebits    = 0;
            $this->totalCredits   = 0;
            return;
        }

        $companyId = auth()->user()?->current_company_id;
        $from      = $this->fromDate ?? now()->startOfMonth()->toDateString();
        $to        = $this->toDate   ?? now()->toDateString();

        $account = Account::find($this->accountId);
        if (! $account) return;

        // Opening balance: all lines before fromDate
        $opening = JournalLine::where('account_id', $this->accountId)
            ->whereHas('journalEntry', fn ($je) => $je
                ->whereDate('date', '<', $from)
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            )
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $openDebit  = (float) ($opening->total_debit  ?? 0);
        $openCredit = (float) ($opening->total_credit ?? 0);
        $isDebitNormal = in_array($account->type, ['asset', 'expense']);
        $this->openingBalance = $isDebitNormal
            ? ($openDebit - $openCredit)
            : ($openCredit - $openDebit);

        // Period lines
        $rawLines = JournalLine::where('account_id', $this->accountId)
            ->whereHas('journalEntry', fn ($je) => $je
                ->whereBetween('date', [$from, $to])
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            )
            ->with('journalEntry')
            ->orderBy(fn ($q) => $q->select('date')->from('journal_entries')->whereColumn('id', 'journal_entry_id'))
            ->get();

        // Use a sub-query-free approach: order by join
        $rawLines = JournalLine::where('account_id', $this->accountId)
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->whereBetween('journal_entries.date', [$from, $to])
            ->when($companyId, fn ($q) => $q->where('journal_entries.company_id', $companyId))
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.id')
            ->select('journal_lines.*', 'journal_entries.date as entry_date', 'journal_entries.reference as entry_reference', 'journal_entries.description as entry_description')
            ->get();

        $runningBalance  = $this->openingBalance;
        $this->totalDebits  = 0;
        $this->totalCredits = 0;

        $this->lines = $rawLines->map(function ($line) use ($isDebitNormal, &$runningBalance) {
            $debit  = (float) $line->debit;
            $credit = (float) $line->credit;

            $this->totalDebits  += $debit;
            $this->totalCredits += $credit;

            $runningBalance += $isDebitNormal ? ($debit - $credit) : ($credit - $debit);

            return [
                'date'        => \Carbon\Carbon::parse($line->entry_date)->format('d M Y'),
                'reference'   => $line->entry_reference ?? '—',
                'description' => $line->entry_description ?? '—',
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $runningBalance,
            ];
        })->toArray();

        $this->closingBalance = $runningBalance;
    }
}
