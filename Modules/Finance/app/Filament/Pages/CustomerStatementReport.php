<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Invoice;

class CustomerStatementReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 59;

    protected static ?string $navigationLabel = 'Customer Statement';

    protected string $view = 'finance::filament.pages.customer-statement';

    public string $customer_name = '';

    public ?string $date_from = null;

    public ?string $date_to = null;

    public array $rows = [];

    public float $closing_balance = 0;

    public bool $generated = false;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to   = now()->toDateString();
    }

    public function generate(): void
    {
        $companyId = auth()->user()?->current_company_id;

        $invoices = Invoice::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('type', 'customer')
            ->where('customer_name', 'like', '%' . $this->customer_name . '%')
            ->when($this->date_from, fn ($q) => $q->whereDate('invoice_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('invoice_date', '<=', $this->date_to))
            ->orderBy('invoice_date')
            ->get();

        $runningBalance = 0;
        $this->rows = $invoices->map(function ($invoice) use (&$runningBalance) {
            $debit  = (float) $invoice->total;
            $credit = (float) $invoice->payments()->sum('amount');
            $runningBalance += ($debit - $credit);

            return [
                'invoice_number' => $invoice->invoice_number,
                'invoice_date'   => $invoice->invoice_date?->format('Y-m-d'),
                'due_date'       => $invoice->due_date?->format('Y-m-d'),
                'type'           => 'Invoice',
                'debit'          => $debit,
                'credit'         => $credit,
                'balance'        => $runningBalance,
            ];
        })->values()->toArray();

        $this->closing_balance = $runningBalance;
        $this->generated = true;
    }
}