<x-filament-panels::page>
    {{-- Alerts section --}}
    @if(count($alerts))
    <div class="space-y-2 mb-6">
        @foreach($alerts as $alert)
        <div class="rounded-lg px-4 py-3 flex items-center gap-3
            @if($alert['type'] === 'danger') bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300
            @elseif($alert['type'] === 'warning') bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300
            @else bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 @endif">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0"/>
            <span class="text-sm font-medium">{{ $alert['message'] }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- KPI Stats Grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 mb-6">

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Farms</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_farms'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active Crop Cycles</div>
            <div class="text-3xl font-bold text-info-600 dark:text-info-400 mt-1">{{ $stats['active_crops'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Livestock (Active)</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                {{ number_format($stats['livestock_count']) }}
                <span class="text-sm font-normal text-gray-500">in {{ $stats['livestock_batches'] }} batches</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">YTD Revenue</div>
            <div class="text-3xl font-bold text-success-600 dark:text-success-400 mt-1">
                GHS {{ number_format($stats['ytd_revenue'], 2) }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">YTD Expenses</div>
            <div class="text-3xl font-bold text-danger-600 dark:text-danger-400 mt-1">
                GHS {{ number_format($stats['ytd_expenses'], 2) }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Net Profit (YTD)</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['net_profit'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                GHS {{ number_format($stats['net_profit'], 2) }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Budget Utilisation</div>
            @if($stats['budget_utilisation'] !== null)
            <div class="text-3xl font-bold mt-1 {{ $stats['budget_utilisation'] > 100 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                {{ $stats['budget_utilisation'] }}%
            </div>
            <div class="text-xs text-gray-400">of GHS {{ number_format($stats['total_budgeted'], 2) }} budgeted</div>
            @else
            <div class="text-2xl font-bold text-gray-400 mt-1">No budget set</div>
            @endif
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Open Tasks</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['overdue_tasks'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-900 dark:text-white' }}">
                {{ $stats['open_tasks'] }}
            </div>
            @if($stats['overdue_tasks'] > 0)
            <div class="text-xs text-danger-500">{{ $stats['overdue_tasks'] }} overdue</div>
            @endif
        </x-filament::section>

    </div>

    <p class="text-xs text-gray-400 text-right">Data for {{ now()->year }}. Dashboard refreshes on page load.</p>
</x-filament-panels::page>