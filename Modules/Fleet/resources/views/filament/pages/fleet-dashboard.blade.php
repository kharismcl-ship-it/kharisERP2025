<x-filament-panels::page>

    {{-- Fleet Composition --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Fleet</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['totalVehicles'] }}</div>
            <div class="mt-1 text-xs text-gray-400">registered vehicles</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-green-600">Active</div>
            <div class="mt-1 text-3xl font-bold text-green-600">{{ $stats['activeVehicles'] }}</div>
            <div class="mt-1 text-xs text-gray-400">operational</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-amber-600">Under Maintenance</div>
            <div class="mt-1 text-3xl font-bold text-amber-600">{{ $stats['inMaintenance'] }}</div>
            <div class="mt-1 text-xs text-gray-400">off-road</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Service</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['scheduledMaintenance'] }}</div>
            <div class="mt-1 text-xs text-gray-400">pending jobs</div>
        </x-filament::card>
    </div>

    {{-- Financial Summary --}}
    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Fuel Spend (MTD)</div>
            <div class="mt-1 text-2xl font-bold text-blue-600">
                GHS {{ number_format($stats['fuelSpendMtd'], 2) }}
            </div>
            <div class="mt-1 text-xs text-gray-400">this month</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Fuel Spend (YTD)</div>
            <div class="mt-1 text-2xl font-bold text-blue-700">
                GHS {{ number_format($stats['fuelSpendYtd'], 2) }}
            </div>
            <div class="mt-1 text-xs text-gray-400">year to date</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Maintenance Spend (YTD)</div>
            <div class="mt-1 text-2xl font-bold text-orange-600">
                GHS {{ number_format($stats['maintSpendYtd'], 2) }}
            </div>
            <div class="mt-1 text-xs text-gray-400">completed jobs</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-red-600">Expiring Documents</div>
            <div class="mt-1 text-3xl font-bold text-red-600">{{ $stats['expiringDocuments'] }}</div>
            <div class="mt-1 text-xs text-gray-400">within 30 days</div>
        </x-filament::card>
    </div>

    {{-- Trip Activity --}}
    <div class="mt-4 grid grid-cols-3 gap-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Trips</div>
            <div class="mt-1 text-3xl font-bold text-yellow-600">{{ $stats['activeTrips'] }}</div>
            <div class="mt-1 text-xs text-gray-400">currently in progress</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Trips (MTD)</div>
            <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['tripsMtd'] }}</div>
            <div class="mt-1 text-xs text-gray-400">this month</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Distance (MTD)</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ number_format($stats['distanceMtd'], 0) }} km
            </div>
            <div class="mt-1 text-xs text-gray-400">completed trips this month</div>
        </x-filament::card>
    </div>

    {{-- Top Fuel Consumers --}}
    @if($stats['topFuelVehicles']->isNotEmpty())
    <div class="mt-6">
        <x-filament::card>
            <div class="mb-4 text-base font-semibold text-gray-700 dark:text-gray-200">
                Top Fuel Consumers (YTD)
            </div>
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2 font-medium text-gray-500">#</th>
                        <th class="pb-2 font-medium text-gray-500">Vehicle</th>
                        <th class="pb-2 font-medium text-gray-500">Plate</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Fuel Cost (YTD)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['topFuelVehicles'] as $i => $v)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $v->name }}</td>
                        <td class="py-2 text-gray-500">{{ $v->plate }}</td>
                        <td class="py-2 text-right font-semibold text-blue-600">
                            GHS {{ number_format($v->total_fuel_cost, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::card>
    </div>
    @endif

</x-filament-panels::page>
