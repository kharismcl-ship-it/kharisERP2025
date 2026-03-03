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

    {{-- KPI Row 1: Occupancy --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-4">

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active Hostels</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_hostels'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Rooms</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_rooms'] }}</div>
            <div class="text-xs text-gray-400">{{ $stats['available_rooms'] }} available</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Occupancy Rate</div>
            @if($stats['occupancy_rate'] !== null)
            <div class="text-3xl font-bold mt-1 {{ $stats['occupancy_rate'] >= 90 ? 'text-danger-600 dark:text-danger-400' : ($stats['occupancy_rate'] >= 70 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400') }}">
                {{ $stats['occupancy_rate'] }}%
            </div>
            <div class="text-xs text-gray-400">{{ $stats['current_occupancy'] }} / {{ $stats['max_occupancy'] }} beds</div>
            @else
            <div class="text-2xl font-bold text-gray-400 mt-1">—</div>
            @endif
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active Guests</div>
            <div class="text-3xl font-bold text-info-600 dark:text-info-400 mt-1">{{ $stats['active_bookings'] }}</div>
            <div class="text-xs text-gray-400">currently checked in</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">In Maintenance</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['maintenance_rooms'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-400' }}">
                {{ $stats['maintenance_rooms'] }}
            </div>
            <div class="text-xs text-gray-400">rooms under maintenance</div>
        </x-filament::section>

    </div>

    {{-- KPI Row 2: Operations --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Check-ins Today</div>
            <div class="text-3xl font-bold text-success-600 dark:text-success-400 mt-1">{{ $stats['checkins_today'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Check-outs Today</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['checkouts_today'] }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Open Maintenance</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['open_maintenance'] > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}">
                {{ $stats['open_maintenance'] }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 dark:text-gray-400">Open Incidents</div>
            <div class="text-3xl font-bold mt-1 {{ $stats['open_incidents'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                {{ $stats['open_incidents'] }}
            </div>
        </x-filament::section>

    </div>

    <p class="text-xs text-gray-400 text-right">Dashboard refreshes on page load.</p>
</x-filament-panels::page>
