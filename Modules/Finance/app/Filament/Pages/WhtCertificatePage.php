<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Invoice;

class WhtCertificatePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 63;

    protected static ?string $navigationLabel = 'WHT Certificates';

    protected string $view = 'finance::filament.pages.wht-certificates';

    public string  $vendorName    = '';
    public int     $periodMonth;
    public int     $periodYear;
    public Collection $certificates;
    public bool    $generated = false;

    public function mount(): void
    {
        $this->periodMonth    = now()->month;
        $this->periodYear     = now()->year;
        $this->certificates   = collect();
    }

    public function generate(): void
    {
        $this->generated = false;

        $companyId = auth()->user()?->current_company_id;

        $query = Invoice::with(['lines'])
            ->where('type', 'vendor')
            ->whereMonth('invoice_date', $this->periodMonth)
            ->whereYear('invoice_date', $this->periodYear)
            ->where('tax_total', '>', 0)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($this->vendorName, fn ($q) => $q->where('customer_name', 'like', '%' . $this->vendorName . '%'));

        $invoices = $query->get();

        // Group by vendor name
        $this->certificates = $invoices
            ->groupBy('customer_name')
            ->map(function (Collection $group, string $vendor) {
                $grossAmount = $group->sum('total');
                $whtAmount   = $group->sum('tax_total');
                $whtRate     = $grossAmount > 0 ? round($whtAmount / $grossAmount * 100, 2) : 0;
                return [
                    'vendor'        => $vendor,
                    'invoice_count' => $group->count(),
                    'gross_amount'  => round((float) $grossAmount, 2),
                    'wht_rate'      => $whtRate,
                    'wht_amount'    => round((float) $whtAmount, 2),
                    'period'        => sprintf('%s %d', now()->month($this->periodMonth)->format('F'), $this->periodYear),
                    'invoices'      => $group->pluck('invoice_number')->implode(', '),
                ];
            })
            ->values();

        $this->generated = true;
    }
}