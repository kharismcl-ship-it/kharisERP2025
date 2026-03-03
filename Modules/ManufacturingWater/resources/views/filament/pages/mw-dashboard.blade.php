<x-filament-panels::page>
    {{-- Alerts --}}
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
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-6">

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active Plants</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_plants'] }}</div>
            <div class="text-xs text-gray-400">of {{ $stats['total_plants'] }} total</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Tests Today</div>
            <div class="text-3xl font-bold text-info-600 dark:text-info-400 mt-1">{{ $stats['tests_today'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Quality Pass Rate (MTD)</div>
            @if($stats['quality_pass_rate'] !== null)
            <div class="text-3xl font-bold mt-1 {{ $stats['quality_pass_rate'] >= 95 ? 'text-success-600 dark:text-success-400' : ($stats['quality_pass_rate'] >= 80 ? 'text-warning-600 dark:text-warning-400' : 'text-danger-600 dark:text-danger-400') }}">
                {{ $stats['quality_pass_rate'] }}%
            </div>
            <div class="text-xs text-gray-400">{{ $stats['failed_tests_mtd'] }} failed of {{ $stats['tests_mtd'] }}</div>
            @else
            <div class="text-2xl font-bold text-gray-400 mt-1">No tests</div>
            @endif
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Distribution (MTD)</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                {{ number_format((float) $stats['distribution_volume_mtd'], 0) }} L
            </div>
            <div class="text-xs text-gray-400">GHS {{ number_format((float) $stats['distribution_revenue_mtd'], 2) }} revenue</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Low Tanks (&lt;20%)</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['low_tanks'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                {{ $stats['low_tanks'] }}
            </div>
        </x-filament::section>

    </div>

    <p class="text-xs text-gray-400 text-right">MTD = Month to Date. Dashboard refreshes on page load.</p>
</x-filament-panels::page>
