<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Farm Reports</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
        </div>

        <!-- Date range filter -->
        <div class="bg-white rounded-lg shadow p-4 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">From:</label>
                <input type="date" wire:model.live="fromDate"
                       class="rounded-md border-gray-300 text-sm" />
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">To:</label>
                <input type="date" wire:model.live="toDate"
                       class="rounded-md border-gray-300 text-sm" />
            </div>
        </div>

        <!-- Tab nav -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="border-b flex">
                @foreach(['financials' => 'Financials', 'budget' => 'Budget', 'crops' => 'Crop P&L', 'livestock' => 'Livestock', 'tasks' => 'Tasks'] as $tab => $label)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                            class="px-5 py-3 text-sm font-medium border-b-2 -mb-px
                                {{ $activeTab === $tab ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="p-6">

                {{-- Financials --}}
                @if($activeTab === 'financials')
                    @php $fin = $this->financials; @endphp
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Revenue</p>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($fin['revenue'], 2) }}</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Expenses</p>
                            <p class="text-2xl font-bold text-red-700">{{ number_format($fin['expenses'], 2) }}</p>
                        </div>
                        <div class="{{ $fin['profit'] >= 0 ? 'bg-blue-50' : 'bg-orange-50' }} rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Net Profit</p>
                            <p class="text-2xl font-bold {{ $fin['profit'] >= 0 ? 'text-blue-700' : 'text-orange-700' }}">
                                {{ number_format($fin['profit'], 2) }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 text-center">Period: {{ $fromDate }} to {{ $toDate }}</p>
                @endif

                {{-- Budget vs Actual --}}
                @if($activeTab === 'budget')
                    @php $bud = $this->budget; @endphp
                    @if(empty($bud['byCategory']))
                        <p class="text-sm text-gray-400 text-center py-8">No budget data for the selected period.</p>
                    @else
                        <!-- Summary row -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 rounded p-4 text-center">
                                <p class="text-xs text-gray-500 mb-1">Total Budgeted</p>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($bud['totalBudgeted'], 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4 text-center">
                                <p class="text-xs text-gray-500 mb-1">Total Actual</p>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($bud['totalActual'], 2) }}</p>
                            </div>
                            <div class="{{ ($bud['variance'] ?? 0) > 0 ? 'bg-red-50' : 'bg-green-50' }} rounded p-4 text-center">
                                <p class="text-xs text-gray-500 mb-1">Variance</p>
                                <p class="text-xl font-bold {{ ($bud['variance'] ?? 0) > 0 ? 'text-red-700' : 'text-green-700' }}">
                                    {{ number_format($bud['variance'] ?? 0, 2) }}
                                    @if($bud['variancePct'] ?? null)
                                        <span class="text-sm">({{ $bud['variancePct'] }}%)</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Category breakdown -->
                        <table class="w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Budgeted</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actual</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Variance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($bud['byCategory'] as $category => $row)
                                    <tr>
                                        <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $category) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['budgeted'], 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['actual'], 2) }}</td>
                                        <td class="px-4 py-2 text-right {{ $row['variance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($row['variance'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif

                {{-- Crop P&L --}}
                @if($activeTab === 'crops')
                    @if($this->cropPnl->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-8">No crop cycles planted in the selected period.</p>
                    @else
                        <table class="w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Crop Cycle</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Input Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Activity Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net Profit</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Yield%</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($this->cropPnl as $row)
                                    <tr>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('farms.crops.show', [$farm->slug, $row['cycle']]) }}"
                                               class="text-indigo-600 hover:underline">{{ $row['cycle']->crop_name }}</a>
                                            <span class="text-xs text-gray-400 ml-1">{{ $row['cycle']->planting_date?->format('M Y') }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['revenue'], 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['inputCost'], 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['activityCost'], 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['totalCost'], 2) }}</td>
                                        <td class="px-4 py-2 text-right font-medium {{ $row['netProfit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                            {{ number_format($row['netProfit'], 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            {{ $row['yieldPct'] !== null ? $row['yieldPct'] . '%' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif

                {{-- Livestock --}}
                @if($activeTab === 'livestock')
                    @if($this->livestockStats->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-8">No livestock batches found.</p>
                    @else
                        <table class="w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Current Count</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Deaths (period)</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Feed Conv. Ratio</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Growth (kg/day)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($this->livestockStats as $row)
                                    <tr>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('farms.livestock.show', [$farm->slug, $row['batch']]) }}"
                                               class="text-indigo-600 hover:underline">{{ $row['batch']->name }}</a>
                                        </td>
                                        <td class="px-4 py-2 capitalize">{{ $row['batch']->animal_type }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row['batch']->current_count) }}</td>
                                        <td class="px-4 py-2 text-right {{ $row['deaths'] > 0 ? 'text-red-600' : '' }}">
                                            {{ $row['deaths'] ?: '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-right">{{ $row['fcr'] !== null ? $row['fcr'] : '—' }}</td>
                                        <td class="px-4 py-2 text-right">{{ $row['growth'] !== null ? $row['growth'] : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif

                {{-- Tasks --}}
                @if($activeTab === 'tasks')
                    @php $ts = $this->taskStats; @endphp
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 rounded p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Total</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $ts['total'] }}</p>
                        </div>
                        <div class="bg-green-50 rounded p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Completed</p>
                            <p class="text-2xl font-bold text-green-700">{{ $ts['completed'] }}</p>
                        </div>
                        <div class="bg-yellow-50 rounded p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Open</p>
                            <p class="text-2xl font-bold text-yellow-700">{{ $ts['open'] }}</p>
                        </div>
                        <div class="bg-red-50 rounded p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Overdue</p>
                            <p class="text-2xl font-bold text-red-700">{{ $ts['overdue'] }}</p>
                        </div>
                    </div>

                    @if(!empty($ts['byType']))
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Breakdown by Type</h3>
                        <div class="space-y-2">
                            @foreach($ts['byType'] as $type => $count)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-32 capitalize">{{ str_replace('_', ' ', $type ?: 'general') }}</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full"
                                             style="width: {{ $ts['total'] > 0 ? round(($count / $ts['total']) * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 w-8 text-right">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

            </div>
        </div>

    </div>
</div>
