<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Active Crop Cycles', 'value' => $stats['active_cycles'], 'icon' => '🌱', 'color' => 'bg-green-50 dark:bg-green-900/20'],
                ['label' => 'Harvest This Year (kg)', 'value' => $stats['harvest_kg'], 'icon' => '🌾', 'color' => 'bg-yellow-50 dark:bg-yellow-900/20'],
                ['label' => 'Revenue (YTD)', 'value' => $stats['revenue'], 'icon' => '💰', 'color' => 'bg-blue-50 dark:bg-blue-900/20'],
                ['label' => 'Net Profit (YTD)', 'value' => $stats['net_profit'], 'icon' => '📈', 'color' => 'bg-indigo-50 dark:bg-indigo-900/20'],
                ['label' => 'Livestock Count', 'value' => $stats['livestock'], 'icon' => '🐄', 'color' => 'bg-orange-50 dark:bg-orange-900/20'],
                ['label' => 'Workers Present Today', 'value' => $stats['workers_today'], 'icon' => '👷', 'color' => 'bg-teal-50 dark:bg-teal-900/20'],
                ['label' => 'Yield Efficiency', 'value' => $stats['yield_efficiency'], 'icon' => '🎯', 'color' => 'bg-purple-50 dark:bg-purple-900/20'],
                ['label' => 'Weather Alerts', 'value' => $stats['weather_alerts'], 'icon' => '🌩️', 'color' => $stats['weather_alerts'] > 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-gray-900/20'],
            ] as $stat)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 {{ $stat['color'] }}">
                <div class="text-2xl mb-1">{{ $stat['icon'] }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Expense breakdown --}}
        @if(!empty($financialData))
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Expenses (Last 6 Months)</h3>
            <div class="flex items-end gap-2 h-32">
                @php $maxVal = max(array_values($financialData)) ?: 1; @endphp
                @foreach($financialData as $month => $amount)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs text-gray-500">GHS {{ number_format($amount/1000,1) }}k</span>
                    <div class="w-full bg-primary-500 rounded-t" style="height: {{ max(4, round(($amount/$maxVal)*100)) }}px;"></div>
                    <span class="text-xs text-gray-400">{{ substr($month, 5) }}/{{ substr($month, 2, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>