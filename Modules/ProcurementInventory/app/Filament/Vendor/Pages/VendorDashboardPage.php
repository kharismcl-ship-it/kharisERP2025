<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class VendorDashboardPage extends Page
{
    protected string $view = 'procurementinventory::filament.vendor.pages.vendor-dashboard';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public int $openPos       = 0;
    public int $pendingPos    = 0;
    public int $receivedPos   = 0;
    public float $totalValue  = 0.0;

    public function mount(): void
    {
        $vendorId = auth('vendor')->user()->vendor_id;

        $this->openPos    = PurchaseOrder::where('vendor_id', $vendorId)
            ->whereIn('status', ['submitted', 'approved', 'ordered'])
            ->count();

        $this->pendingPos = PurchaseOrder::where('vendor_id', $vendorId)
            ->where('status', 'submitted')
            ->count();

        $this->receivedPos = PurchaseOrder::where('vendor_id', $vendorId)
            ->whereIn('status', ['received', 'partially_received'])
            ->count();

        $this->totalValue = PurchaseOrder::where('vendor_id', $vendorId)
            ->whereIn('status', ['ordered', 'partially_received', 'received'])
            ->sum('total');
    }
}
