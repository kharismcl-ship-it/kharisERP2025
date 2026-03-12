<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $farm->name }}</h1>
                <p class="text-sm text-gray-500">{{ $farm->location }} &bull; {{ ucfirst($farm->type ?? 'mixed') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('farms.tasks.index', $farm->slug) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    View Tasks
                </a>
                <a href="{{ route('farms.daily-reports.create', $farm->slug) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                    Daily Report
                </a>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Active Crop Cycles</p>
                <p class="text-3xl font-bold text-green-600">{{ $this->activeCropCycles->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Open Tasks</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $this->openTasks->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Overdue Tasks</p>
                <p class="text-3xl font-bold {{ $this->overdueTasks->count() > 0 ? 'text-red-600' : 'text-gray-400' }}">
                    {{ $this->overdueTasks->count() }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Net Profit (This Month)</p>
                <p class="text-2xl font-bold {{ $this->netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($this->netProfit, 2) }}
                </p>
            </div>
        </div>

        <!-- Three Column Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Active Crops -->
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-800">Active Crops</h2>
                    <a href="{{ route('farms.crops.index', $farm->slug) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
                </div>
                @if($this->activeCropCycles->isEmpty())
                    <p class="text-sm text-gray-400">No active crop cycles.</p>
                @else
                    <div class="space-y-3">
                        @foreach($this->activeCropCycles as $cycle)
                            <div class="flex justify-between items-center text-sm border-b pb-2 last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $cycle->crop_name ?? $cycle->variety?->name ?? 'Crop Cycle' }}</p>
                                    <p class="text-xs text-gray-400">
                                        Planted: {{ $cycle->planting_date?->format('M j') ?? '—' }}
                                    </p>
                                </div>
                                <a href="{{ route('farms.crops.show', [$farm->slug, $cycle]) }}"
                                   class="text-xs text-indigo-600 hover:underline">Details</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Livestock Summary -->
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-800">Livestock</h2>
                    <a href="{{ route('farms.livestock.index', $farm->slug) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
                </div>
                @if($this->livestockSummary->isEmpty())
                    <p class="text-sm text-gray-400">No active livestock batches.</p>
                @else
                    <div class="space-y-2">
                        @foreach($this->livestockSummary as $row)
                            <div class="flex justify-between items-center text-sm">
                                <span class="capitalize text-gray-700">{{ str_replace('_', ' ', $row->animal_type) }}</span>
                                <span class="font-semibold text-gray-900">{{ number_format($row->total_count) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Reports -->
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-800">Recent Reports</h2>
                    <a href="{{ route('farms.daily-reports.index', $farm->slug) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
                </div>
                @if($this->recentReports->isEmpty())
                    <p class="text-sm text-gray-400">No reports yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($this->recentReports as $report)
                            <div class="flex justify-between items-center text-sm border-b pb-2 last:border-0">
                                <div>
                                    <p class="text-gray-800">{{ $report->report_date?->format('M j, Y') ?? $report->created_at->format('M j') }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $report->worker?->name ?? 'Staff' }}
                                    </p>
                                </div>
                                <span class="inline-flex px-2 py-0.5 rounded text-xs
                                    @if($report->status === 'reviewed') bg-green-100 text-green-800
                                    @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($report->status ?? 'draft') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Overdue Tasks Alert -->
        @if($this->overdueTasks->count() > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-red-800 mb-2">
                    {{ $this->overdueTasks->count() }} Overdue Task(s)
                </h3>
                <div class="space-y-1">
                    @foreach($this->overdueTasks->take(3) as $task)
                        <p class="text-sm text-red-700">
                            &bull; {{ $task->title }}
                            <span class="text-xs text-red-500 ml-1">
                                (due {{ $task->due_date?->format('M j') ?? '?' }})
                            </span>
                        </p>
                    @endforeach
                </div>
                <a href="{{ route('farms.tasks.index', $farm->slug) }}"
                   class="mt-3 inline-block text-sm text-red-700 font-medium hover:underline">
                    View all tasks
                </a>
            </div>
        @endif

    </div>
</div>
