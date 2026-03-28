<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Filters</h3>
            {{ $this->form }}
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="text-2xl font-bold text-primary-600">GHS {{ number_format($totalSpend, 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">Total Spend</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="text-2xl font-bold text-info-600">{{ number_format($totalPos) }}</div>
                <div class="text-sm text-gray-500 mt-1">Total POs</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="text-2xl font-bold text-warning-600">{{ number_format($totalVendors) }}</div>
                <div class="text-sm text-gray-500 mt-1">Vendors Used</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="text-2xl font-bold text-success-600">GHS {{ number_format($avgPoValue, 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">Avg PO Value</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Spend Breakdown Table --}}
            <div class="lg:col-span-2 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        Spend by {{ ucfirst($groupBy) }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Spend</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">POs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($rows as $row)
                                @php $pct = $totalSpend > 0 ? round($row['spend'] / $totalSpend * 100, 1) : 0; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-right font-medium">GHS {{ number_format($row['spend'], 0) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                <div class="bg-primary-500 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400 w-10 text-right">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $row['po_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($rows->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">No data for the selected period.</div>
                @endif
            </div>

            {{-- Top Items --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Top 10 Items by Spend</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach($topItems as $i => $item)
                        <div class="px-4 py-3 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs text-gray-400 w-5 flex-shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm text-gray-900 dark:text-white truncate">{{ $item['name'] }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex-shrink-0">
                                GHS {{ number_format($item['spend'], 0) }}
                            </span>
                        </div>
                    @endforeach
                    @if($topItems->isEmpty())
                        <div class="px-4 py-8 text-center text-gray-500 text-sm">No item data.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>