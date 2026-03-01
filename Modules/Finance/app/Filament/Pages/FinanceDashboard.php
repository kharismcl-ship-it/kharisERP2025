<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Models\Payment;

class FinanceDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Finance Dashboard';

    protected string $view = 'finance::filament.pages.finance-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $invoiceQ = Invoice::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $paymentQ = Payment::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        // AR — open customer invoices
        $arTotal = (clone $invoiceQ)
            ->where('type', 'customer')
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('total');

        // AP — open vendor invoices
        $apTotal = (clone $invoiceQ)
            ->where('type', 'vendor')
            ->whereIn('status', ['draft', 'sent'])
            ->sum('total');

        // Revenue this month
        $revenueMtd = (clone $invoiceQ)
            ->where('type', 'customer')
            ->where('status', 'paid')
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total');

        // Cash received this month
        $cashMtd = (clone $paymentQ)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Overdue invoices count
        $overdueCount = (clone $invoiceQ)
            ->where('type', 'customer')
            ->where('status', 'overdue')
            ->count();

        // Unpaid invoices count
        $unpaidCount = (clone $invoiceQ)
            ->where('type', 'customer')
            ->whereIn('status', ['sent', 'overdue'])
            ->count();

        // Bank balance — sum of debit - credit on account code 1120
        $bankAccount = Account::where('code', '1120')
            ->when($companyId, fn ($q) => $q->where(fn ($q2) => $q2->where('company_id', $companyId)->orWhereNull('company_id')))
            ->first();

        $bankBalance = 0;
        if ($bankAccount) {
            $bankBalance = JournalLine::where('account_id', $bankAccount->id)
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->value('balance') ?? 0;
        }

        return compact('arTotal', 'apTotal', 'revenueMtd', 'cashMtd', 'overdueCount', 'unpaidCount', 'bankBalance');
    }
}
