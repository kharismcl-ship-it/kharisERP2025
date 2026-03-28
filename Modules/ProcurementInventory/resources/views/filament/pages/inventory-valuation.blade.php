<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Filters</h3>
            <form wire:change="loadData">
                {{ $this->form }}
            </form>
        </div>

        {{-- Report Table --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    Inventory Valuation
                    @if($dateAsAt)
                        <span class="text-sm font-normal text-gray-500 ml-2">as at {{ \Carbon\Carbon::parse($dateAsAt)->format('d M Y') }}</span>
                    @endif
                </h3>
                <span class="text-sm text-gray-500">
                    Grand Total: <strong class="text-gray-900 dark:text-white">GHS {{ number_format($grandTotal, 2) }}</strong>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UOM</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty on Hand</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Unit Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($this->getGroupedRows() as $category => $categoryRows)
                            @foreach($categoryRows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $row['sku'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $row['item_name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row['category'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row['warehouse'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row['uom'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($row['qty_on_hand'], 4) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($row['avg_unit_cost'], 4) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($row['total_value'], 2) }}</td>
                                </tr>
                            @endforeach
                            {{-- Category Subtotal --}}
                            <tr class="bg-blue-50 dark:bg-blue-900/20 border-t-2 border-blue-200 dark:border-blue-700">
                                <td colspan="7" class="px-4 py-2 text-sm font-semibold text-blue-700 dark:text-blue-300">
                                    {{ $category }} Subtotal
                                </td>
                                <td class="px-4 py-2 text-right font-semibold text-blue-700 dark:text-blue-300">
                                    GHS {{ number_format($categoryRows->sum('total_value'), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-400 dark:border-gray-600">
                            <td colspan="7" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">
                                Grand Total
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white text-base">
                                GHS {{ number_format($grandTotal, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($rows->isEmpty())
                <div class="px-6 py-12 text-center text-gray-500">
                    No inventory records found for the selected filters.
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>