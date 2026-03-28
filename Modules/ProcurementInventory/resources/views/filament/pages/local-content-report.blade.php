<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Report Period</h3>
            {{ $this->form }}
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Spend ({{ $year }})</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($totalSpend, 0) }}</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Local Vendor Spend</div>
                <div class="text-2xl font-bold text-success-600">GHS {{ number_format($localSpend, 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $localPct }}% of total</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Local Content Target (PPA)</div>
                <div class="text-2xl font-bold {{ $localPct >= $localContentTarget ? 'text-success-600' : 'text-danger-600' }}">
                    {{ $localContentTarget }}%
                </div>
                <div class="text-sm mt-2 {{ $localPct >= $localContentTarget ? 'text-success-600' : 'text-danger-600' }}">
                    @if($localPct >= $localContentTarget)
                        Target met
                    @else
                        {{ round($localContentTarget - $localPct, 1) }}% gap
                    @endif
                </div>
            </div>
        </div>

        {{-- Local Content Progress Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Local Content Progress</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Local spend: {{ $localPct }}%</span>
                    <span>Target: {{ $localContentTarget }}%</span>
                </div>
                <div class="relative w-full bg-gray-200 rounded-full h-6 dark:bg-gray-700 overflow-hidden">
                    <div class="h-6 rounded-full transition-all {{ $localPct >= $localContentTarget ? 'bg-success-500' : 'bg-primary-500' }}"
                         style="width: {{ min($localPct, 100) }}%"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white">
                        {{ $localPct }}%
                    </div>
                    {{-- Target marker --}}
                    <div class="absolute top-0 bottom-0 w-0.5 bg-red-500"
                         style="left: {{ $localContentTarget }}%"></div>
                </div>
                <div class="text-xs text-gray-500">
                    Ghana Public Procurement Act (PPA) — Local content target: {{ $localContentTarget }}% for government contracts
                </div>
            </div>
        </div>

        {{-- Diversity Class Breakdown --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Vendor Diversity Breakdown</h3>
                <a href="#" onclick="window.print()"
                   class="text-xs text-primary-600 hover:underline dark:text-primary-400">
                    Export to PDF
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diversity Class</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Spend</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Vendors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($diversityBreakdown as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $row['label'] }}</td>
                                <td class="px-4 py-3 text-right font-medium">GHS {{ number_format($row['spend'], 0) }}</td>
                                <td class="px-4 py-3 text-right">{{ $row['pct'] }}%</td>
                                <td class="px-4 py-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                        <div class="bg-primary-500 h-2 rounded-full" style="width: {{ min($row['pct'], 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $row['vendors'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>