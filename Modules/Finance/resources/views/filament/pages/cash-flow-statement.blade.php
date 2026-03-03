<x-filament-panels::page>
    {{-- Date filter --}}
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

    <div class="space-y-6">

        {{-- Operating Activities --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                Operating Activities
            </h3>
            <table class="w-full text-sm">
                <tbody>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-1.5">Net Income / (Loss) for period</td>
                        <td class="py-1.5 text-right font-medium {{ $netIncome < 0 ? 'text-red-600' : '' }}">
                            {{ number_format($netIncome, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="border-t border-gray-200 dark:border-gray-700 font-semibold">
                    <tr>
                        <td class="pt-2 text-blue-700 dark:text-blue-400">Net Cash from Operating Activities</td>
                        <td class="pt-2 text-right text-blue-700 dark:text-blue-400 {{ $netCashFromOperating < 0 ? '!text-red-600' : '' }}">
                            GHS {{ number_format($netCashFromOperating, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>

        {{-- Investing Activities --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                Investing Activities
            </h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($investingRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                            <td class="py-1.5">{{ $row['name'] }}</td>
                            <td class="py-1.5 text-right font-medium {{ $row['cashImpact'] < 0 ? 'text-red-600' : '' }}">
                                {{ number_format($row['cashImpact'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-center text-gray-400 text-xs">No asset movements in period.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gray-200 dark:border-gray-700 font-semibold">
                    <tr>
                        <td colspan="2" class="pt-2 text-orange-700 dark:text-orange-400">Net Cash from Investing Activities</td>
                        <td class="pt-2 text-right text-orange-700 dark:text-orange-400 {{ $netCashFromInvesting < 0 ? '!text-red-600' : '' }}">
                            GHS {{ number_format($netCashFromInvesting, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>

        {{-- Financing Activities --}}
        <x-filament::card>
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                Financing Activities
            </h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($financingRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-1.5 font-mono text-gray-400 w-20">{{ $row['code'] }}</td>
                            <td class="py-1.5">{{ $row['name'] }}</td>
                            <td class="py-1.5 text-right font-medium {{ $row['cashImpact'] < 0 ? 'text-red-600' : '' }}">
                                {{ number_format($row['cashImpact'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-center text-gray-400 text-xs">No liability or equity movements in period.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gray-200 dark:border-gray-700 font-semibold">
                    <tr>
                        <td colspan="2" class="pt-2 text-green-700 dark:text-green-400">Net Cash from Financing Activities</td>
                        <td class="pt-2 text-right text-green-700 dark:text-green-400 {{ $netCashFromFinancing < 0 ? '!text-red-600' : '' }}">
                            GHS {{ number_format($netCashFromFinancing, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>

        {{-- Net Cash Change Summary --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <span class="font-bold text-gray-700 dark:text-gray-200">Net Increase / (Decrease) in Cash</span>
                <span class="text-2xl font-bold {{ $netCashChange < 0 ? 'text-red-600' : 'text-blue-700 dark:text-blue-400' }}">
                    GHS {{ number_format($netCashChange, 2) }}
                </span>
            </div>
        </x-filament::card>

    </div>
</x-filament-panels::page>
