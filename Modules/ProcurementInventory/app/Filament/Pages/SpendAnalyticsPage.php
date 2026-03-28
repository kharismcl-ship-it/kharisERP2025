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
