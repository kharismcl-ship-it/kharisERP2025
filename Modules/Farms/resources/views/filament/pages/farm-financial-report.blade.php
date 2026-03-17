<x-filament-panels::page>

    {{-- Year filter (Filament Select) --}}
    {{ $this->form }}

    {{-- Per-Farm P&L --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">Profit & Loss by Farm — {{ $selectedYear }}</x-slot>

        @if(count($farmRows))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                        <th class="pb-3 pr-4">Farm</th>
                        <th class="pb-3 pr-4 text-right">Harvest Rev.</th>
                        <th class="pb-3 pr-4 text-right">Sales Rev.</th>
                        <th class="pb-3 pr-4 text-right">Total Rev.</th>
                        <th class="pb-3 pr-4 text-right">Expenses</th>
                        <th class="pb-3 pr-4 text-right">Net Profit</th>
                        <th class="pb-3 pr-4 text-right">Budgeted</th>
                        <th class="pb-3 text-right">Budget %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($farmRows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2.5 pr-4 font-medium text-gray-900 dark:text-white">{{ $row['farm'] }}</td>
                        <td class="py-2.5 pr-4 text-right font-mono text-gray-600 dark:text-gray-400">{{ number_format($row['harvest_revenue'], 2) }}</td>
                        <td class="py-2.5 pr-4 text-right font-mono text-gray-600 dark:text-gray-400">{{ number_format($row['sale_revenue'], 2) }}</td>
                        <td class="py-2.5 pr-4 text-right font-mono font-semibold text-success-700 dark:text-success-400">{{ number_format($row['total_revenue'], 2) }}</td>
                        <td class="py-2.5 pr-4 text-right font-mono text-danger-600 dark:text-danger-400">{{ number_format($row['total_expenses'], 2) }}</td>
                        <td class="py-2.5 pr-4 text-right font-mono font-bold {{ $row['net_profit'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            {{ number_format($row['net_profit'], 2) }}
                        </td>
                        <td class="py-2.5 pr-4 text-right font-mono text-gray-500">
                            {{ $row['budgeted'] ? number_format($row['budgeted'], 2) : '—' }}
                        </td>
                        <td class="py-2.5 text-right font-mono {{ ($row['budget_utilisation'] ?? 0) > 100 ? 'text-danger-600' : 'text-gray-500' }}">
                            {{ $row['budget_utilisation'] !== null ? $row['budget_utilisation'] . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold text-sm">
                        <td class="pt-3 text-gray-700 dark:text-gray-300">Totals</td>
                        <td colspan="2"></td>
                        <td class="pt-3 text-right font-mono text-success-700 dark:text-success-400">GHS {{ number_format($totals['total_revenue'], 2) }}</td>
                        <td class="pt-3 text-right font-mono text-danger-600 dark:text-danger-400">GHS {{ number_format($totals['total_expenses'], 2) }}</td>
                        <td class="pt-3 text-right font-mono {{ $totals['net_profit'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            GHS {{ number_format($totals['net_profit'], 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <p class="text-gray-400 text-sm text-center py-8">No farm data found for {{ $selectedYear }}.</p>
        @endif
    </x-filament::section>

    {{-- Expense Breakdown --}}
    @if(count($expenseRows))
    <x-filament::section>
        <x-slot name="heading">Expense Breakdown by Category — {{ $selectedYear }}</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                        <th class="pb-3 pr-4">Category</th>
                        <th class="pb-3 text-right">Total (GHS)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($expenseRows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-300">{{ $row['category'] }}</td>
                        <td class="py-2.5 text-right font-mono text-danger-600 dark:text-danger-400">{{ number_format($row['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif

</x-filament-panels::page>
