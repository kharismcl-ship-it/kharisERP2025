<x-filament-panels::page>
    {{-- Summary buckets --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-6">
        <x-filament::card>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Current (0–30 days)</div>
            <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                GHS {{ number_format($total0_30, 2) }}
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">31–60 days</div>
            <div class="mt-1 text-xl font-bold text-yellow-600">
                GHS {{ number_format($total31_60, 2) }}
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">61–90 days</div>
            <div class="mt-1 text-xl font-bold text-orange-600">
                GHS {{ number_format($total61_90, 2) }}
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Over 90 days</div>
            <div class="mt-1 text-xl font-bold text-red-600">
                GHS {{ number_format($total90, 2) }}
            </div>
        </x-filament::card>
    </div>

    {{-- Detail table --}}
    <x-filament::card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Invoice #</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Vendor</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Invoice Date</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Due Date</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right">Amount (GHS)</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-center">Days Overdue</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-center">Bucket</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2 font-mono text-xs">{{ $row['invoice_number'] }}</td>
                        <td class="py-2">{{ $row['vendor'] }}</td>
                        <td class="py-2 text-gray-500">{{ $row['invoice_date'] }}</td>
                        <td class="py-2 text-gray-500">{{ $row['due_date'] }}</td>
                        <td class="py-2 text-right font-medium">{{ number_format($row['total'], 2) }}</td>
                        <td class="py-2 text-center">{{ $row['days_overdue'] }}</td>
                        <td class="py-2 text-center">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $row['bucket'] === '0-30'  ? 'bg-gray-100 text-gray-700'     : '' }}
                                {{ $row['bucket'] === '31-60' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $row['bucket'] === '61-90' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $row['bucket'] === '90+'   ? 'bg-red-100 text-red-700'       : '' }}
                            ">{{ $row['bucket'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">No outstanding vendor invoices.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                <tr>
                    <td colspan="4" class="pt-2">Grand Total</td>
                    <td class="pt-2 text-right">{{ number_format($grandTotal, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </x-filament::card>
</x-filament-panels::page>
