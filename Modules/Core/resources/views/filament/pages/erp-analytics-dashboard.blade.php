<x-filament::page>
    <div class="space-y-8">

        {{-- Hostels Section --}}
        @if(!empty($stats['hostels']))
        <div>
            <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider mb-3">Hostels</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hostels']['occupancy_rate'] }}%</div>
                    <div class="text-sm text-gray-500 mt-1">Occupancy Rate</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['hostels']['occupied_beds'] }} / {{ $stats['hostels']['total_beds'] }} beds</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hostels']['bookings_month'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Bookings This Month</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hostels']['pending_bookings'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Pending Approvals</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hostels']['pending_deposits'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Pending Deposits</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Finance Section --}}
        @if(!empty($stats['finance']))
        <div>
            <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider mb-3">Finance</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-emerald-500">
                    <div class="text-2xl font-bold text-gray-900">GHS {{ number_format($stats['finance']['revenue_month'], 2) }}</div>
                    <div class="text-sm text-gray-500 mt-1">Revenue This Month</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                    <div class="text-2xl font-bold text-gray-900">GHS {{ number_format($stats['finance']['total_outstanding'], 2) }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Outstanding</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['finance']['outstanding_invoices'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Open Invoices</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['finance']['overdue_invoices'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Overdue Invoices</div>
                </div>
            </div>
        </div>
        @endif

        {{-- HR Section --}}
        @if(!empty($stats['hr']))
        <div>
            <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider mb-3">Human Resources</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hr']['active_employees'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Active Employees</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hr']['pending_leave_requests'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Pending Leave Requests</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['hr']['on_leave_today'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">On Leave Today</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Procurement Section --}}
        @if(!empty($stats['procurement']))
        <div>
            <h2 class="text-base font-semibold text-gray-500 uppercase tracking-wider mb-3">Procurement & Inventory</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['procurement']['total_items'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Catalog Items</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['procurement']['low_stock_items'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Low Stock Alerts</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['procurement']['pending_purchase_orders'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Pending POs</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Last Updated --}}
        <div class="text-xs text-gray-400 text-right">
            Last updated: {{ now()->format('d M Y H:i') }}
            <button
                wire:click="loadStats"
                class="ml-2 text-primary-600 hover:text-primary-800 underline"
            >Refresh</button>
        </div>

    </div>
</x-filament::page>
