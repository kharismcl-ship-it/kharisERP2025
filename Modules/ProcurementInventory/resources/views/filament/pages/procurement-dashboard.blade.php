<x-filament-panels::page>
    {{-- KPI row 1 --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Approval</div>
            <div class="mt-1 text-2xl font-bold {{ $stats['pendingApproval'] > 0 ? 'text-yellow-600' : 'text-gray-900 dark:text-white' }}">
                {{ $stats['pendingApproval'] }}
            </div>
            <div class="mt-1 text-xs text-gray-400">POs awaiting approval</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">In Transit</div>
            <div class="mt-1 text-2xl font-bold text-blue-600">
                {{ $stats['inTransit'] }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Ordered / partially received</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Items</div>
            <div class="mt-1 text-2xl font-bold {{ $stats['lowStockCount'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $stats['lowStockCount'] }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Below reorder level</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Vendors</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $stats['activeVendors'] }}
            </div>
        </x-filament::card>
    </div>

    {{-- KPI row 2 --}}
    <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Spend This Month</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
                GHS {{ number_format($stats['spendMtd'], 2) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Spend This Year</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
                GHS {{ number_format($stats['spendYtd'], 2) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cancelled POs (MTD)</div>
            <div class="mt-1 text-xl font-semibold {{ $stats['cancelledMtd'] > 0 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">
                {{ $stats['cancelledMtd'] }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg PO Processing</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
                {{ $stats['avgPoProcessingDays'] }} days
            </div>
            <div class="mt-1 text-xs text-gray-400">Draft → Approved</div>
        </x-filament::card>
    </div>

    {{-- Analytics row --}}
    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-2">

        {{-- Spend by Category --}}
        <x-filament::card>
            <div class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Spend by Category (YTD)</div>
            @php $maxCat = $stats['spendByCategory']->max('total_spend') ?: 1; @endphp
            @forelse($stats['spendByCategory'] as $i => $row)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $row->category_name }}</span>
                    <span class="text-gray-500 dark:text-gray-400">GHS {{ number_format($row->total_spend, 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($row->total_spend / $maxCat) * 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">No spend data yet this year.</p>
            @endforelse
        </x-filament::card>

        {{-- Spend by Vendor --}}
        <x-filament::card>
            <div class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Spend by Vendor (YTD)</div>
            @php $maxVen = $stats['spendByVendor']->max('total_spend') ?: 1; @endphp
            @forelse($stats['spendByVendor'] as $i => $vendor)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $vendor->name }}</span>
                    <span class="text-gray-500 dark:text-gray-400">GHS {{ number_format($vendor->total_spend, 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ min(100, ($vendor->total_spend / $maxVen) * 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">No spend data yet this year.</p>
            @endforelse
        </x-filament::card>
    </div>

    {{-- PO Aging + Low Stock --}}
    <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">

        {{-- PO Aging --}}
        <x-filament::card>
            <div class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">PO Aging (Open POs)</div>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                    <div class="text-xl font-bold text-green-700 dark:text-green-400">{{ $stats['poAgingBuckets']['lt7'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">&lt; 7 days</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                    <div class="text-xl font-bold text-blue-700 dark:text-blue-400">{{ $stats['poAgingBuckets']['7_30'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">7–30 days</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3">
                    <div class="text-xl font-bold text-yellow-700 dark:text-yellow-400">{{ $stats['poAgingBuckets']['30_60'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">30–60 days</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                    <div class="text-xl font-bold text-red-700 dark:text-red-400">{{ $stats['poAgingBuckets']['gt60'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">&gt; 60 days</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Low Stock Items --}}
        <x-filament::card>
            <div class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Critical Low Stock Items</div>
            @if($stats['lowStockItems']->isEmpty())
                <p class="text-sm text-green-600 dark:text-green-400">All items are adequately stocked.</p>
            @else
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                        <th class="pb-2">Item</th>
                        <th class="pb-2 text-right">On Hand</th>
                        <th class="pb-2 text-right">Reorder At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['lowStockItems'] as $sl)
                    <tr class="border-b border-gray-50 dark:border-gray-800/50">
                        <td class="py-1.5 text-gray-800 dark:text-gray-200 font-medium">{{ $sl->item->name }}</td>
                        <td class="py-1.5 text-right text-red-600 dark:text-red-400">{{ number_format((float)$sl->quantity_on_hand, 2) }}</td>
                        <td class="py-1.5 text-right text-gray-500 dark:text-gray-400">{{ number_format((float)$sl->item->reorder_level, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </x-filament::card>
    </div>

    {{-- Top vendors (legacy) --}}
    @if($stats['topVendors']->isNotEmpty())
    <div class="mt-4">
        <x-filament::card>
            <div class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Top Vendors by Spend (YTD)</div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2">#</th>
                        <th class="pb-2">Vendor</th>
                        <th class="pb-2 text-right">Total Spend (GHS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['topVendors'] as $i => $vendor)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 font-medium text-gray-900 dark:text-white">{{ $vendor->name }}</td>
                        <td class="py-2 text-right">{{ number_format($vendor->total_spend, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::card>
    </div>
    @endif
</x-filament-panels::page>