<x-filament-panels::page>
    {{-- Date filter --}}
    <div class="flex items-end gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">As of Date</label>
            <input type="date" wire:model="asOf" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white"
                value="{{ $asOf }}">
        </div>
    </div>

    @if (!$balanced)
        <div class="mb-4 rounded bg-red-50 dark:bg-red-900/30 border border-red-300 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            Warning: Balance sheet is out of balance. Assets: GHS {{ number_format($totalAssets, 2) }} | Liabilities + Equity: GHS {{ number_format($totalLiabEquity, 2) }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- ASSETS --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Assets</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($assetRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                            <td class="py-1.5">{{ $row['name'] }}</td>
                            <td class="py-1.5 text-right font-medium {{ $row['balance'] < 0 ? 'text-red-600' : '' }}">
                                {{ number_format($row['balance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">No asset accounts with balances.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-300 dark:border-gray-600 font-bold">
                    <tr>
                        <td colspan="2" class="pt-2 text-blue-700 dark:text-blue-400">Total Assets</td>
                        <td class="pt-2 text-right text-blue-700 dark:text-blue-400">{{ number_format($totalAssets, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>

        {{-- LIABILITIES + EQUITY --}}
        <div class="space-y-6">
            {{-- Liabilities --}}
            <x-filament::card>
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Liabilities</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($liabilityRows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                                <td class="py-1.5">{{ $row['name'] }}</td>
                                <td class="py-1.5 text-right font-medium">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">No liability accounts with balances.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-300 dark:border-gray-600 font-semibold">
                        <tr>
                            <td colspan="2" class="pt-2 text-orange-700 dark:text-orange-400">Total Liabilities</td>
                            <td class="pt-2 text-right text-orange-700 dark:text-orange-400">{{ number_format($totalLiabilities, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::card>

            {{-- Equity --}}
            <x-filament::card>
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Equity</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($equityRows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                                <td class="py-1.5">{{ $row['name'] }}</td>
                                <td class="py-1.5 text-right font-medium {{ $row['balance'] < 0 ? 'text-red-600' : 'text-green-700 dark:text-green-400' }}">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">No equity accounts with balances.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-300 dark:border-gray-600 font-semibold">
                        <tr>
                            <td colspan="2" class="pt-2 text-green-700 dark:text-green-400">Total Equity</td>
                            <td class="pt-2 text-right text-green-700 dark:text-green-400">{{ number_format($totalEquity, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::card>

            {{-- Total L + E --}}
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-700 dark:text-gray-200">Total Liabilities + Equity</span>
                    <span class="text-xl font-bold {{ $balanced ? 'text-blue-700 dark:text-blue-400' : 'text-red-600' }}">
                        GHS {{ number_format($totalLiabEquity, 2) }}
                    </span>
                </div>
            </x-filament::card>
        </div>
    </div>
</x-filament-panels::page>
