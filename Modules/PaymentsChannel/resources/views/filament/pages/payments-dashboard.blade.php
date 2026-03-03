<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Volume Stats --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Volume Today</div>
                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    GHS {{ number_format($stats['totalVolumeToday'], 2) }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Volume This Month</div>
                <div class="mt-1 text-2xl font-bold text-green-600">
                    GHS {{ number_format($stats['totalVolumeMtd'], 2) }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Success Today</div>
                <div class="mt-1 text-2xl font-bold text-blue-600">{{ $stats['successToday'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Today</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['failedToday'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                    {{ $stats['failedToday'] }}
                </div>
            </x-filament::card>
        </div>

        {{-- Success Rate MTD --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Success Rate (MTD)</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['successRateMtd'] >= 90 ? 'text-green-600' : ($stats['successRateMtd'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $stats['successRateMtd'] }}%
                </div>
            </x-filament::card>

            {{-- By Provider --}}
            @if (!empty($stats['byProvider']))
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Volume by Provider (MTD)</div>
                <div class="space-y-1">
                    @foreach ($stats['byProvider'] as $provider => $volume)
                    <div class="flex justify-between text-sm">
                        <span class="capitalize text-gray-700 dark:text-gray-300">{{ $provider }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">GHS {{ number_format($volume, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::card>
            @endif
        </div>

        {{-- Recent Transactions --}}
        @if ($stats['recentTransactions']->isNotEmpty())
        <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-gray-500 mb-3">Recent Transactions</h2>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Provider</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Type</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Amount</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Processed At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stats['recentTransactions'] as $tx)
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-4 py-2 capitalize text-gray-900 dark:text-white">{{ $tx->provider }}</td>
                            <td class="px-4 py-2 capitalize text-gray-600 dark:text-gray-400">{{ $tx->transaction_type }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">GHS {{ number_format($tx->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $tx->status === 'success' ? 'bg-green-100 text-green-700' : ($tx->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $tx->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $tx->processed_at?->format('d M H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="text-xs text-gray-400 text-right">
            Last updated: {{ now()->format('d M Y H:i') }}
            <button wire:click="mount" class="ml-2 text-primary-600 hover:text-primary-800 underline">Refresh</button>
        </div>

    </div>
</x-filament-panels::page>
