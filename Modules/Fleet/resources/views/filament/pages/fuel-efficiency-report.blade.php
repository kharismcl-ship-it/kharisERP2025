<x-filament-panels::page>

    {{-- Period Filter --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['last30' => 'Last 30 Days', 'mtd' => 'Month to Date', 'qtd' => 'Quarter to Date', 'ytd' => 'Year to Date'] as $key => $label)
        <button
            wire:click="setPeriod('{{ $key }}')"
            class="px-3 py-1.5 text-sm rounded-lg border transition
                {{ $period === $key
                    ? 'bg-primary-600 text-white border-primary-600'
                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
        >
            {{ $label }}
        </button>
        @endforeach
        <span class="ml-auto text-sm text-gray-400 self-center">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>

    {{-- Fleet Average --}}
    <div class="mt-4 grid grid-cols-3 gap-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Fleet Average Efficiency</div>
            <div class="mt-1 text-2xl font-bold {{ $fleetAverage > 0 ? 'text-green-600' : 'text-gray-400' }}">
                {{ $fleetAverage > 0 ? $fleetAverage . ' L/100km' : 'Insufficient data' }}
            </div>
            <div class="mt-1 text-xs text-gray-400">lower is more efficient</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Vehicles with Data</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ count($rows) }}</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Total Fuel Litres</div>
            <div class="mt-1 text-2xl font-bold text-blue-600">
                {{ number_format(collect($rows)->sum('total_litres'), 1) }} L
            </div>
        </x-filament::card>
    </div>

    {{-- Per-Vehicle Efficiency Table --}}
    <div class="mt-6">
        <x-filament::card>
            <div class="mb-4 text-base font-semibold text-gray-700 dark:text-gray-200">
                Fuel Efficiency by Vehicle
            </div>

            @if(empty($rows))
                <p class="text-sm text-gray-400">No fuel data with mileage readings for the selected period.</p>
            @else
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2 font-medium text-gray-500">#</th>
                        <th class="pb-2 font-medium text-gray-500">Vehicle</th>
                        <th class="pb-2 font-medium text-gray-500">Plate</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Fills</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Litres</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Distance (km)</th>
                        <th class="pb-2 text-right font-medium text-gray-500">L/100km</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Fuel Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php
                        $row = (object)$row;
                        $effClass = $row->efficiency === null
                            ? 'text-gray-400'
                            : ($row->efficiency <= $fleetAverage ? 'text-green-600' : 'text-red-500');
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $row->vehicle_name }}</td>
                        <td class="py-2 text-gray-500">{{ $row->plate }}</td>
                        <td class="py-2 text-right text-gray-500">{{ $row->total_fills }}</td>
                        <td class="py-2 text-right text-gray-700 dark:text-gray-300">{{ number_format($row->total_litres, 1) }}</td>
                        <td class="py-2 text-right text-gray-700 dark:text-gray-300">{{ number_format($row->total_distance, 0) }}</td>
                        <td class="py-2 text-right font-semibold {{ $effClass }}">
                            {{ $row->efficiency !== null ? $row->efficiency : '—' }}
                        </td>
                        <td class="py-2 text-right text-blue-600">GHS {{ number_format($row->total_fuel_cost, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-3 text-xs text-gray-400">
                Green = at or below fleet average. Red = above fleet average. L/100km requires at least 2 fill-ups with odometer readings.
            </p>
            @endif
        </x-filament::card>
    </div>

</x-filament-panels::page>