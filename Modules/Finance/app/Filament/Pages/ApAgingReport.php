<?php

namespace Modules\Finance\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Finance\Models\Invoice;

class ApAgingReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 54;

    protected static ?string $navigationLabel = 'AP Aging';

    protected string $view = 'finance::filament.pages.ap-aging-report';

    public array $rows = [];

    public float $total0_30  = 0;
    public float $total31_60 = 0;
    public float $total61_90 = 0;
    public float $total90    = 0;
    public float $grandTotal = 0;

    public function mount(): void
    {
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $companyId = auth()->user()?->current_company_id;

        $invoices = Invoice::query()
            ->where('type', 'vendor')
            ->whereIn('status', ['sent', 'overdue'])
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->with('vendor')
            ->get();

        $this->rows = $invoices->map(function (Invoice $inv) {
            $daysOverdue = $inv->due_date ? max(0, (int) (now()->diffInDays($inv->due_date, false) * -1)) : 0;

            return [
                'invoice_number' => $inv->invoice_number,
                'vendor'         => $inv->vendor?->name ?? $inv->customer_name ?? '—',
                'invoice_date'   => $inv->invoice_date?->format('d M Y'),
                'due_date'       => $inv->due_date?->format('d M Y') ?? '—',
                'total'          => (float) $inv->total,
                'days_overdue'   => $daysOverdue,
                'bucket'         => $this->bucket($daysOverdue),
            ];
        })->values()->toArray();

        $this->total0_30  = collect($this->rows)->where('bucket', '0-30')->sum('total');
        $this->total31_60 = collect($this->rows)->where('bucket', '31-60')->sum('total');
        $this->total61_90 = collect($this->rows)->where('bucket', '61-90')->sum('total');
        $this->total90    = collect($this->rows)->where('bucket', '90+')->sum('total');
        $this->grandTotal = $this->total0_30 + $this->total31_60 + $this->total61_90 + $this->total90;
    }

    private function bucket(int $days): string
    {
        if ($days <= 30) return '0-30';
        if ($days <= 60) return '31-60';
        if ($days <= 90) return '61-90';

        return '90+';
    }
}
