<x-filament-panels::page>
    <div class="space-y-4">
        @if(empty($devices))
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No IoT devices registered. Add devices via the IoT Devices resource.</p>
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($devices as $device)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {{ $device['type'] === 'soil_moisture' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
                         : ($device['type'] === 'weather_station' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300'
                         : ($device['type'] === 'temperature' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300'
                         : ($device['type'] === 'ph_sensor' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'
                         : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'))) }}">
                        {{ str_replace('_', ' ', ucfirst($device['type'])) }}
                    </span>
                    <span class="w-2.5 h-2.5 rounded-full {{ $device['is_online'] ? 'bg-green-500' : 'bg-red-400' }}" title="{{ $device['is_online'] ? 'Online' : 'Offline' }}"></span>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $device['name'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $device['farm'] ?? '—' }}</p>
                <div class="mt-3">
                    <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $device['last_value'] !== null ? $device['last_value'] : '—' }}
                    </span>
                    @if($device['last_unit'])
                    <span class="text-sm text-gray-400 ml-1">{{ $device['last_unit'] }}</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ $device['last_reading_at'] ?? 'No readings yet' }}
                </p>
                @if($device['battery_pct'] !== null)
                <div class="mt-2">
                    <div class="flex items-center gap-1.5">
                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $device['battery_pct'] < 20 ? 'bg-red-500' : ($device['battery_pct'] < 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                 style="width: {{ min(100, max(0, $device['battery_pct'])) }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 w-8 text-right">{{ round($device['battery_pct']) }}%</span>
                    </div>
                </div>
                @endif
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-xs px-1.5 py-0.5 rounded font-medium
                        {{ $device['status'] === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                         : ($device['status'] === 'offline' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'
                         : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300') }}">
                        {{ ucfirst($device['status']) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-filament-panels::page>