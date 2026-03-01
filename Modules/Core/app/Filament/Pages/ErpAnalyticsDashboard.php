<?php

namespace Modules\Core\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;

class ErpAnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected string $view = 'core::filament.pages.erp-analytics-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->stats = [
            'hostels'     => $this->getHostelsStats(),
            'finance'     => $this->getFinanceStats(),
            'hr'          => $this->getHrStats(),
            'procurement' => $this->getProcurementStats(),
        ];
    }

    protected function getHostelsStats(): array
    {
        if (! class_exists(\Modules\Hostels\Models\Booking::class)) {
            return [];
        }

        $totalBeds     = \Modules\Hostels\Models\Bed::count();
        $occupiedBeds  = \Modules\Hostels\Models\Booking::whereIn('status', ['confirmed', 'checked_in'])->distinct('bed_id')->count('bed_id');
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

        $bookingsThisMonth = \Modules\Hostels\Models\Booking::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->count();

        $pendingDeposits = class_exists(\Modules\Hostels\Models\Deposit::class)
            ? \Modules\Hostels\Models\Deposit::where('status', \Modules\Hostels\Models\Deposit::STATUS_PENDING)->count()
            : 0;

        $pendingBookings = \Modules\Hostels\Models\Booking::where('status', 'pending')->count();

        return [
            'occupancy_rate'    => $occupancyRate,
            'total_beds'        => $totalBeds,
            'occupied_beds'     => $occupiedBeds,
            'bookings_month'    => $bookingsThisMonth,
            'pending_deposits'  => $pendingDeposits,
            'pending_bookings'  => $pendingBookings,
        ];
    }

    protected function getFinanceStats(): array
    {
        if (! class_exists(\Modules\Finance\Models\Invoice::class)) {
            return [];
        }

        $revenueThisMonth = \Modules\Finance\Models\Receipt::whereBetween('receipt_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->sum('amount');

        $outstandingInvoices = \Modules\Finance\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])->count();

        $overdueInvoices = \Modules\Finance\Models\Invoice::where('due_date', '<', now())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->count();

        $totalOutstanding = \Modules\Finance\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])
            ->sum('total');

        return [
            'revenue_month'       => $revenueThisMonth,
            'outstanding_invoices' => $outstandingInvoices,
            'overdue_invoices'    => $overdueInvoices,
            'total_outstanding'   => $totalOutstanding,
        ];
    }

    protected function getHrStats(): array
    {
        if (! class_exists(\Modules\HR\Models\Employee::class)) {
            return [];
        }

        $activeEmployees = \Modules\HR\Models\Employee::where('employment_status', 'active')->count();

        $pendingLeaveRequests = \Modules\HR\Models\LeaveRequest::where('status', 'pending')->count();

        $onLeaveToday = \Modules\HR\Models\LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        return [
            'active_employees'      => $activeEmployees,
            'pending_leave_requests' => $pendingLeaveRequests,
            'on_leave_today'        => $onLeaveToday,
        ];
    }

    protected function getProcurementStats(): array
    {
        if (! class_exists(\Modules\ProcurementInventory\Models\Item::class)) {
            return [];
        }

        $totalItems = \Modules\ProcurementInventory\Models\Item::count();

        $lowStockItems = class_exists(\Modules\ProcurementInventory\Models\StockLevel::class)
            ? \Modules\ProcurementInventory\Models\StockLevel::whereColumn('quantity_on_hand', '<=', 'reorder_level')->count()
            : 0;

        $pendingPurchaseOrders = class_exists(\Modules\ProcurementInventory\Models\PurchaseOrder::class)
            ? \Modules\ProcurementInventory\Models\PurchaseOrder::whereIn('status', ['draft', 'submitted'])->count()
            : 0;

        return [
            'total_items'           => $totalItems,
            'low_stock_items'       => $lowStockItems,
            'pending_purchase_orders' => $pendingPurchaseOrders,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'ERP Analytics';
    }

    public function getTitle(): string
    {
        return 'ERP Analytics Dashboard';
    }
}