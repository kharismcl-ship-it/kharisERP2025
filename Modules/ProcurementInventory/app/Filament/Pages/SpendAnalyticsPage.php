<?php

namespace Modules\ProcurementInventory\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class SpendAnalyticsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 79;

    protected static ?string $navigationLabel = 'Spend Analytics';

    protected string $view = 'procurementinventory::filament.pages.spend-analytics';

    public ?string $dateFrom  = null;
    public ?string $dateTo    = null;
    public string  $groupBy   = 'vendor';

    public Collection $rows;
    public float      $totalSpend    = 0.0;
    public int        $totalPos      = 0;
    public int        $totalVendors  = 0;
    public float      $avgPoValue    = 0.0;

    public Collection $topItems;

    // Intelligence metrics
    public float  $maverickSpendAmount  = 0.0;
    public int    $maverickSpendCount   = 0;
    public float  $maverickSpendPct     = 0.0;
    public bool   $hasConcentrationRisk = false;
    public string $topConcentratedVendor = '';
    public float  $topConcentrationPct  = 0.0;
    public float  $spendLastYear        = 0.0;
    public float  $yoyChange            = 0.0;
    public float  $savingsAmount        = 0.0;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfYear()->toDateString();
        $this->dateTo   = now()->toDateString();
        $this->rows     = collect();
        $this->topItems = collect();
        $this->loadData();
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            DatePicker::make('dateFrom')
                ->label('From')
                ->default(now()->startOfYear()->toDateString())
                ->live(),

            DatePicker::make('dateTo')
                ->label('To')
                ->default(now()->toDateString())
                ->live(),

            Select::make('groupBy')
                ->label('Group By')
                ->options([
                    'vendor'   => 'Vendor',
                    'category' => 'Category',
                    'month'    => 'Month',
                ])
                ->default('vendor')
                ->live(),
        ]);
    }

    public function updatedDateFrom(): void { $this->loadData(); }
    public function updatedDateTo(): void   { $this->loadData(); }
    public function updatedGroupBy(): void  { $this->loadData(); }

    public function loadData(): void
    {
        $query = PurchaseOrder::whereIn('status', ['received', 'closed'])
            ->when($this->dateFrom, fn ($q) => $q->where('po_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->where('po_date', '<=', $this->dateTo));

        $pos = $query->with(['vendor', 'lines.item.category'])->get();

        $this->totalSpend   = (float) $pos->sum('total');
        $this->totalPos     = $pos->count();
        $this->totalVendors = $pos->pluck('vendor_id')->unique()->count();
        $this->avgPoValue   = $this->totalPos > 0 ? $this->totalSpend / $this->totalPos : 0;

        $this->rows = match ($this->groupBy) {
            'vendor'   => $this->groupByVendor($pos),
            'category' => $this->groupByCategory($pos),
            'month'    => $this->groupByMonth($pos),
            default    => collect(),
        };

        // ── Intelligence Metrics ─────────────────────────────────────────────

        // 1. Maverick Spend — PO lines where item has a catalog but from a different vendor
        // (catalog items are in procurement_vendor_catalog_items via VendorCatalogItem)
        $maverickAmount = 0.0;
        $maverickCount  = 0;
        foreach ($pos as $po) {
            foreach ($po->lines as $line) {
                if ($line->item_id) {
                    // Check if any catalog entry exists for this item from a DIFFERENT vendor
                    $catalogVendorIds = \Modules\ProcurementInventory\Models\VendorCatalogItem::where('item_id', $line->item_id)
                        ->whereHas('catalog', fn ($q) => $q->where('is_active', true))
                        ->with('catalog')
                        ->get()
                        ->pluck('catalog.vendor_id')
                        ->unique()
                        ->filter()
                        ->values();

                    if ($catalogVendorIds->isNotEmpty() && ! $catalogVendorIds->contains($po->vendor_id)) {
                        $maverickAmount += (float) $line->line_total;
                        $maverickCount++;
                    }
                }
            }
        }
        $this->maverickSpendAmount = $maverickAmount;
        $this->maverickSpendCount  = $maverickCount;
        $this->maverickSpendPct    = $this->totalSpend > 0 ? round($maverickAmount / $this->totalSpend * 100, 1) : 0.0;

        // 2. Vendor Concentration — flag if any single vendor > 30% of total spend
        $this->hasConcentrationRisk    = false;
        $this->topConcentratedVendor   = '';
        $this->topConcentrationPct     = 0.0;
        if ($this->totalSpend > 0) {
            $vendorSpends = $pos->groupBy('vendor_id')->map(fn ($grp) => $grp->sum('total'));
            $topVendorId  = $vendorSpends->keys()->sortByDesc(fn ($id) => $vendorSpends->get($id))->first();
            if ($topVendorId) {
                $topSpend = (float) $vendorSpends->get($topVendorId);
                $pct      = $topSpend / $this->totalSpend * 100;
                $this->topConcentrationPct   = round($pct, 1);
                $this->hasConcentrationRisk  = $pct > 30;
                $topVendor = $pos->firstWhere('vendor_id', $topVendorId);
                $this->topConcentratedVendor = $topVendor?->vendor?->name ?? 'Unknown';
            }
        }

        // 3. YoY Comparison — same date range last year
        $lastYearFrom  = $this->dateFrom ? \Carbon\Carbon::parse($this->dateFrom)->subYear()->toDateString() : null;
        $lastYearTo    = $this->dateTo   ? \Carbon\Carbon::parse($this->dateTo)->subYear()->toDateString()   : null;
        $lastYearTotal = PurchaseOrder::whereIn('status', ['received', 'closed'])
            ->when($lastYearFrom, fn ($q) => $q->where('po_date', '>=', $lastYearFrom))
            ->when($lastYearTo,   fn ($q) => $q->where('po_date', '<=', $lastYearTo))
            ->sum('total');
        $this->spendLastYear = (float) $lastYearTotal;
        $this->yoyChange     = $this->spendLastYear > 0
            ? round(($this->totalSpend - $this->spendLastYear) / $this->spendLastYear * 100, 1)
            : 0.0;

        // 4. Savings Tracking — sum of (catalog_price - actual unit_price) × qty for catalog items
        $savings = 0.0;
        foreach ($pos as $po) {
            foreach ($po->lines as $line) {
                if ($line->item_id) {
                    $catalogPrice = \Modules\ProcurementInventory\Models\VendorCatalogItem::where('item_id', $line->item_id)
                        ->whereHas('catalog', fn ($q) => $q->where('vendor_id', $po->vendor_id)->where('is_active', true))
                        ->value('unit_price');
                    if ($catalogPrice && (float) $line->unit_price < (float) $catalogPrice) {
                        $savings += ((float) $catalogPrice - (float) $line->unit_price) * (float) $line->quantity;
                    }
                }
            }
        }
        $this->savingsAmount = round($savings, 2);

        // Top 10 items by spend
        $itemSpend = collect();
        foreach ($pos as $po) {
            foreach ($po->lines as $line) {
                $itemId   = $line->item_id;
                $itemName = $line->item?->name ?? 'Unknown';
                $spend    = (float) $line->line_total;
                if (! $itemSpend->has($itemId)) {
                    $itemSpend->put($itemId, ['name' => $itemName, 'spend' => 0]);
                }
                $entry          = $itemSpend->get($itemId);
                $entry['spend'] += $spend;
                $itemSpend->put($itemId, $entry);
            }
        }

        $this->topItems = $itemSpend->sortByDesc('spend')->take(10)->values();
    }

    private function groupByVendor(Collection $pos): Collection
    {
        return $pos->groupBy('vendor_id')->map(function ($group, $vendorId) {
            $totalSpend = $group->sum('total');
            return [
                'name'      => $group->first()->vendor?->name ?? 'Unknown',
                'spend'     => (float) $totalSpend,
                'po_count'  => $group->count(),
            ];
        })->sortByDesc('spend')->values();
    }

    private function groupByCategory(Collection $pos): Collection
    {
        $categories = collect();
        foreach ($pos as $po) {
            foreach ($po->lines as $line) {
                $cat   = $line->item?->category?->name ?? 'Uncategorised';
                $spend = (float) $line->line_total;
                if (! $categories->has($cat)) {
                    $categories->put($cat, ['spend' => 0, 'po_count' => 0]);
                }
                $entry           = $categories->get($cat);
                $entry['spend'] += $spend;
                $entry['po_count']++;
                $categories->put($cat, $entry);
            }
        }
        return $categories->map(fn ($v, $k) => array_merge(['name' => $k], $v))
            ->sortByDesc('spend')->values();
    }

    private function groupByMonth(Collection $pos): Collection
    {
        return $pos->groupBy(fn ($po) => \Carbon\Carbon::parse($po->po_date)->format('Y-m'))
            ->map(function ($group, $month) {
                return [
                    'name'     => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                    'spend'    => (float) $group->sum('total'),
                    'po_count' => $group->count(),
                ];
            })->sortKeys()->values();
    }
}
