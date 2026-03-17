<x-filament-panels::page>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Batches</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $summary['total_batches'] ?? 0 }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active Batches</div>
            <div class="text-3xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $summary['active_batches'] ?? 0 }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Animals (Active)</div>
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ number_format($summary['total_animals'] ?? 0) }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Health Alerts (14 days)</div>
            <div class="text-3xl font-bold mt-1 {{ ($summary['health_alerts'] ?? 0) > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-400' }}">
                {{ $summary['health_alerts'] ?? 0 }}
            </div>
        </x-filament::section>
    </div>

    {{-- Batches Filament Table --}}
    {{ $this->table }}

    {{-- Health Treatments Due --}}
    @if(count($healthRows))
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <span class="text-warning-600 dark:text-warning-400">Health Treatments Due (within 14 days)</span>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                        <th class="pb-3 pr-4">Farm</th>
                        <th class="pb-3 pr-4">Batch</th>
                        <th class="pb-3 pr-4">Treatment</th>
                        <th class="pb-3">Due Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($healthRows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $row['overdue'] ? 'bg-danger-50 dark:bg-danger-900/10' : '' }}">
                        <td class="py-2.5 pr-4 font-medium text-gray-900 dark:text-white">{{ $row['farm'] }}</td>
                        <td class="py-2.5 pr-4 text-gray-600 dark:text-gray-400">{{ $row['batch'] }}</td>
                        <td class="py-2.5 pr-4 capitalize text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', $row['treatment']) }}</td>
                        <td class="py-2.5 font-medium {{ $row['overdue'] ? 'text-danger-600 dark:text-danger-400' : 'text-warning-600 dark:text-warning-400' }}">
                            {{ $row['next_due'] }}
                            @if($row['overdue']) <span class="text-xs ml-1 font-bold">(OVERDUE)</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif

</x-filament-panels::page>
