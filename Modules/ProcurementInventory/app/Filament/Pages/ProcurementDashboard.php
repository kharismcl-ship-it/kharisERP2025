<?php

namespace Modules\ProcurementInventory\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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

        return compact(
            'pendingApproval',
            'inTransit',
            'spendMtd',
            'spendYtd',
            'activeVendors',
            'lowStockCount',
            'cancelledMtd',
            'topVendors'
        );
    }
}
