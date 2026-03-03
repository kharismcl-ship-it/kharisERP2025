<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Top Stats --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sent Today</div>
                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['totalToday'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sent This Month</div>
                <div class="mt-1 text-2xl font-bold text-blue-600">{{ $stats['totalMtd'] }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Today</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['failedToday'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                    {{ $stats['failedToday'] }}
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Rate (MTD)</div>
                <div class="mt-1 text-2xl font-bold {{ $stats['deliveryRateMtd'] >= 90 ? 'text-green-600' : ($stats['deliveryRateMtd'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $stats['deliveryRateMtd'] }}%
                </div>
            </x-filament::card>
        </div>

        {{-- By Channel MTD --}}
        @if (!empty($stats['byChannel']))
        <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-gray-500 mb-3">Messages by Channel (MTD)</h2>
            <div class="grid grid-cols-3 gap-4">
                @foreach ($stats['byChannel'] as $channel => $count)
                <x-filament::card>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">{{ $channel }}</div>
                    <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $count }}</div>
                </x-filament::card>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent Failures --}}
        @if ($stats['recentFailed']->isNotEmpty())
        <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-gray-500 mb-3">Recent Failures</h2>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Recipient</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Channel</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Subject</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Error</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stats['recentFailed'] as $msg)
                        <tr class="bg-white dark:bg-gray-900">
                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $msg->to_name }}</td>
                            <td class="px-4 py-2 capitalize text-gray-600 dark:text-gray-400">{{ $msg->channel }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $msg->subject }}</td>
                            <td class="px-4 py-2 text-red-600 truncate max-w-xs">{{ $msg->error_message }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $msg->sent_at?->format('d M H:i') }}</td>
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
