<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Invoice;

class VatReturnReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 58;

    protected static ?string $navigationLabel = 'VAT Return';

    protected string $view = 'finance::filament.pages.vat-return';

    public ?string $from_date = null;

    public ?string $to_date = null;

    public string $tax_type = 'vat';

    public float $output_tax = 0;

    public float $input_tax = 0;

    public float $net_payable = 0;

    public bool $generated = false;

    public function mount(): void
    {
        $this->from_date = now()->startOfMonth()->toDateString();
        $this->to_date   = now()->endOfMonth()->toDateString();
    }

    public function generate(): void
    {
        $companyId = auth()->user()?->current_company_id;

        $this->output_tax = (float) Invoice::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('type', 'customer')
            ->whereIn('status', ['sent', 'paid'])
            ->whereBetween('invoice_date', [$this->from_date, $this->to_date])
            ->sum('tax_total');

        $this->input_tax = (float) Invoice::when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('type', 'vendor')
            ->whereIn('status', ['sent', 'paid'])
            ->whereBetween('invoice_date', [$this->from_date, $this->to_date])
            ->sum('tax_total');

        $this->net_payable = $this->output_tax - $this->input_tax;
        $this->generated = true;
    }
}