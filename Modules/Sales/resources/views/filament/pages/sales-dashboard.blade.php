<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Key Metrics --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Leads</div>
                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['totalLeads'] }}</div>
                <div class="mt-1 text-xs text-gray-400">{{ $stats['newLeadsToday'] }} new today</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pipeline Value</div>
                <div class="mt-1 text-2xl font-bold text-blue-600">
                    GHS {{ number_format($stats['pipelineValue'], 2) }}
                </div>
                <div class="mt-1 text-xs text-gray-400">{{ $stats['openOpportunities'] }} open opportunities</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (MTD)</div>
                <div class="mt-1 text-2xl font-bold text-green-600">
                    GHS {{ number_format($stats['revenueMtd'], 2) }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (YTD)</div>
                <div class="mt-1 text-2xl font-bold text-emerald-600">
                    GHS {{ number_format($stats['revenueYtd'], 2) }}
                </div>
            </x-filament::card>
        </div>

        {{-- Win Rate + Orders by Status --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Win Rate</div>
                <div class="mt-1 text-3xl font-bold {{ $stats['winRate'] >= 50 ? 'text-green-600' : 'text-orange-500' }}">
                    {{ $stats['winRate'] }}%
                </div>
                <div class="mt-1 text-xs text-gray-400">Closed won vs. total closed</div>
            </x-filament::card>

            @if (!empty($stats['ordersByStatus']))
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Orders by Status</div>
                <div class="space-y-1">
                    @foreach ($stats['ordersByStatus'] as $status => $count)
                    <div class="flex justify-between text-sm">
                        <span class="capitalize text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::card>
            @endif
        </div>

        {{-- Top Opportunities --}}
        @if ($stats['topOpportunities']->isNotEmpty())
        <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-gray-500 mb-3">Top Open Opportunities</h2>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Opportunity</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Contact</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Stage</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Value (GHS)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stats['topOpportunities'] as $opp)
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $opp->title }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $opp->contact?->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 capitalize">
                                    {{ str_replace('_', ' ', $opp->stage) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-white">
                                {{ number_format($opp->estimated_value, 2) }}
                            </td>
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
