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
    <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
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
    </div>

    {{-- Top vendors table --}}
    @if($stats['topVendors']->isNotEmpty())
    <div class="mt-6">
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
