<?php

namespace Modules\ProcurementInventory\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Modules\ProcurementInventory\Models\Item;
use Modules\ProcurementInventory\Models\ItemCategory;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Models\Vendor;

class ProcurementDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 1;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'procurementinventory::filament.pages.procurement-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $poQ = PurchaseOrder::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        // Open POs awaiting approval
        $pendingApproval = (clone $poQ)->where('status', 'submitted')->count();

        // POs in transit (ordered / partially received)
        $inTransit = (clone $poQ)->whereIn('status', ['ordered', 'partially_received'])->count();

        // Spend this month
        $spendMtd = (clone $poQ)
            ->whereIn('status', ['received', 'closed'])
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->sum('total');

        // Spend this year
        $spendYtd = (clone $poQ)
            ->whereIn('status', ['received', 'closed'])
            ->whereYear('po_date', now()->year)
            ->sum('total');

        // Active vendor count
        $activeVendors = Vendor::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->count();

        // Items below reorder level
        $lowStockCount = StockLevel::with('item')
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get()
            ->filter(fn (StockLevel $sl) => $sl->needsReorder())
            ->count();

        // Cancelled POs this month
        $cancelledMtd = (clone $poQ)
            ->where('status', 'cancelled')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Top 5 vendors by spend this year
        $topVendors = Vendor::query()
            ->when($companyId, fn ($q) => $q->where('vendors.company_id', $companyId))
            ->join('purchase_orders', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->whereIn('purchase_orders.status', ['received', 'closed'])
            ->whereYear('purchase_orders.po_date', now()->year)
            ->selectRaw('vendors.name, SUM(purchase_orders.total) as total_spend')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        // Spend by category (top 5, YTD, received/closed POs)
        $spendByCategory = DB::table('purchase_order_lines as pol')
            ->join('purchase_orders as po', 'pol.purchase_order_id', '=', 'po.id')
            ->join('items as i', 'pol.item_id', '=', 'i.id')
            ->join('item_categories as ic', 'i.item_category_id', '=', 'ic.id')
            ->when($companyId, fn ($q) => $q->where('po.company_id', $companyId))
            ->whereIn('po.status', ['received', 'closed'])
            ->whereYear('po.po_date', now()->year)
            ->selectRaw('ic.name as category_name, SUM(pol.line_total) as total_spend')
            ->groupBy('ic.id', 'ic.name')
            ->orderByDesc('total_spend')
            ->limit(5)
            ->get();

        // Spend by vendor (top 5, YTD)
        $spendByVendor = Vendor::query()
            ->when($companyId, fn ($q) => $q->where('vendors.company_id', $companyId))
            ->join('purchase_orders', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->whereIn('purchase_orders.status', ['received', 'closed'])
            ->whereYear('purchase_orders.po_date', now()->year)
            ->selectRaw('vendors.name, SUM(purchase_orders.total) as total_spend')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        // PO aging buckets (open POs by age)
        $now = now();
        $openPos = (clone $poQ)
            ->whereIn('status', ['submitted', 'approved', 'ordered'])
            ->select('created_at')
            ->get();

        $poAgingBuckets = [
            'lt7'    => 0,
            '7_30'   => 0,
            '30_60'  => 0,
            'gt60'   => 0,
        ];
        foreach ($openPos as $po) {
            $age = $po->created_at->diffInDays($now);
            if ($age < 7) {
                $poAgingBuckets['lt7']++;
            } elseif ($age < 30) {
                $poAgingBuckets['7_30']++;
            } elseif ($age < 60) {
                $poAgingBuckets['30_60']++;
            } else {
                $poAgingBuckets['gt60']++;
            }
        }

        // Avg PO processing days (created_at to approved_at)
        $avgPoProcessingDays = (clone $poQ)
            ->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, approved_at)) as avg_days')
            ->value('avg_days');
        $avgPoProcessingDays = round((float) $avgPoProcessingDays, 1);

        // Top 10 most critical low-stock items (lowest on_hand/reorder_level ratio)
        $lowStockItems = StockLevel::with('item')
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get()
            ->filter(fn (StockLevel $sl) => $sl->needsReorder() && $sl->item && (float) $sl->item->reorder_level > 0)
            ->sortBy(fn (StockLevel $sl) => (float) $sl->quantity_on_hand / max((float) $sl->item->reorder_level, 0.0001))
            ->take(10)
            ->values();

        return compact(
            'pendingApproval',
            'inTransit',
            'spendMtd',
            'spendYtd',
            'activeVendors',
            'lowStockCount',
            'cancelledMtd',
            'topVendors',
            'spendByCategory',
            'spendByVendor',
            'poAgingBuckets',
            'avgPoProcessingDays',
            'lowStockItems'
        );
    }
}
