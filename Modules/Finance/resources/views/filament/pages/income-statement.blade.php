<x-filament-panels::page>
    {{-- Date range filter --}}
    <div class="flex items-end gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
            <input type="date" wire:model="fromDate" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
            <input type="date" wire:model="toDate" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Revenue --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3">Revenue</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($incomeRows as $row)
                        <tr>
                            <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                            <td class="py-1.5">{{ $row['name'] }}</td>
                            <td class="py-1.5 text-right font-medium">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">No revenue accounts with activity.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gray-300 dark:border-gray-600 font-semibold">
                    <tr>
                        <td colspan="2" class="pt-2 text-green-700 dark:text-green-400">Total Revenue</td>
                        <td class="pt-2 text-right text-green-700 dark:text-green-400">{{ number_format($totalIncome, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>

        {{-- Expenses --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3">Expenses</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($expenseRows as $row)
                        <tr>
                            <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                            <td class="py-1.5">{{ $row['name'] }}</td>
                            <td class="py-1.5 text-right font-medium">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">No expense accounts with activity.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gray-300 dark:border-gray-600 font-semibold">
                    <tr>
                        <td colspan="2" class="pt-2 text-red-700 dark:text-red-400">Total Expenses</td>
                        <td class="pt-2 text-right text-red-700 dark:text-red-400">{{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>
    </div>

    {{-- Net profit summary --}}
    <x-filament::card class="mt-6">
        <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-gray-700 dark:text-gray-200">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
            <span class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                GHS {{ number_format(abs($netProfit), 2) }}
            </span>
        </div>
    </x-filament::card>
</x-filament-panels::page>
