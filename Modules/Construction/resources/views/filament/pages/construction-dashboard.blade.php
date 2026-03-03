<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Project Status Summary --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Projects</div>
                <div class="mt-1 text-2xl font-bold text-blue-600">{{ $stats['activeProjects'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</div>
                <div class="mt-1 text-2xl font-bold text-green-600">{{ $stats['completedProjects'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">On Hold</div>
                <div class="mt-1 text-2xl font-bold text-yellow-600">{{ $stats['onHoldProjects'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Over Budget</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['overBudgetCount'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                    {{ $stats['overBudgetCount'] }}
                </div>
            </x-filament::card>
        </div>

        {{-- Financial Summary --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Budget</div>
                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    GHS {{ number_format($stats['totalBudget'], 2) }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</div>
                <div class="mt-1 text-2xl font-bold text-orange-600">
                    GHS {{ number_format($stats['totalSpent'], 2) }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Utilisation</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['budgetUtilisation'] >= 90 ? 'text-red-600' : ($stats['budgetUtilisation'] >= 75 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $stats['budgetUtilisation'] }}%
                </div>
            </x-filament::card>
        </div>

        {{-- Per-Project Breakdown --}}
        @if (!empty($stats['projectRows']))
        <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-gray-500 mb-3">Project Financial Breakdown</h2>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Project</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Budget (GHS)</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Spent (GHS)</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Variance (GHS)</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-500">Utilisation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stats['projectRows'] as $row)
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $row['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-gray-900 dark:text-white">{{ number_format($row['budget'], 2) }}</td>
                            <td class="px-4 py-2 text-right text-gray-900 dark:text-white">{{ number_format($row['total_spent'], 2) }}</td>
                            <td class="px-4 py-2 text-right {{ $row['variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($row['variance'], 2) }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $row['util_colour'] === 'danger' ? 'bg-red-100 text-red-700' : ($row['util_colour'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $row['util'] }}%
                                </span>
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
