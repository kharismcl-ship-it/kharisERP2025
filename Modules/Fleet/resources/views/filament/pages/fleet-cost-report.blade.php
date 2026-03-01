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

    {{-- Summary Totals --}}
    @php
        $totalFuel  = collect($rows)->sum('fuel_cost');
        $totalMaint = collect($rows)->sum('maintenance_cost');
        $grandTotal = collect($rows)->sum('total_cost');
    @endphp

    <div class="mt-4 grid grid-cols-3 gap-4">
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Total Fuel Cost</div>
            <div class="mt-1 text-2xl font-bold text-blue-600">GHS {{ number_format($totalFuel, 2) }}</div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Total Maintenance Cost</div>
            <div class="mt-1 text-2xl font-bold text-orange-600">GHS {{ number_format($totalMaint, 2) }}</div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500">Grand Total Fleet Cost</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($grandTotal, 2) }}</div>
        </x-filament::card>
    </div>

    {{-- Per-Vehicle Breakdown --}}
    <div class="mt-6">
        <x-filament::card>
            <div class="mb-4 text-base font-semibold text-gray-700 dark:text-gray-200">
                Per-Vehicle Cost Breakdown
            </div>

            @if(empty($rows))
                <p class="text-sm text-gray-400">No cost data for the selected period.</p>
            @else
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-2 font-medium text-gray-500">#</th>
                        <th class="pb-2 font-medium text-gray-500">Vehicle</th>
                        <th class="pb-2 font-medium text-gray-500">Plate</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Fuel Cost</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Maintenance</th>
                        <th class="pb-2 text-right font-medium text-gray-500">Total</th>
                        <th class="pb-2 text-right font-medium text-gray-500">% of Fleet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php $row = (object)$row; @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $row->vehicle_name }}</td>
                        <td class="py-2 text-gray-500">{{ $row->plate }}</td>
                        <td class="py-2 text-right text-blue-600">GHS {{ number_format($row->fuel_cost, 2) }}</td>
                        <td class="py-2 text-right text-orange-600">GHS {{ number_format($row->maintenance_cost, 2) }}</td>
                        <td class="py-2 text-right font-semibold text-gray-800 dark:text-gray-200">GHS {{ number_format($row->total_cost, 2) }}</td>
                        <td class="py-2 text-right text-gray-500">
                            {{ $grandTotal > 0 ? number_format(($row->total_cost / $grandTotal) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </x-filament::card>
    </div>

</x-filament-panels::page>