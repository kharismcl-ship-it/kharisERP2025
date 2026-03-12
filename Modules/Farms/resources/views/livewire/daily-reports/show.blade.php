<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6 space-y-5">

            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Daily Report</h1>
                    <p class="text-sm text-gray-500">
                        {{ $farm->name }} &bull; {{ $report->report_date?->format('M j, Y') }}
                    </p>
                </div>
                <div class="flex gap-3 items-center">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($report->status === 'reviewed') bg-green-100 text-green-800
                        @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucfirst($report->status) }}
                    </span>
                    <a href="{{ route('farms.daily-reports.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline">
                        Back
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm border-b pb-4">
                <div>
                    <span class="text-gray-500">Reported By:</span>
                    <span class="ml-2 font-medium">{{ $report->worker?->name ?? 'Staff' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Weather:</span>
                    <span class="ml-2">{{ $report->weather_observation ?? '—' }}</span>
                </div>
                @if($report->reviewed_at)
                    <div>
                        <span class="text-gray-500">Reviewed:</span>
                        <span class="ml-2">{{ $report->reviewed_at->format('M j, Y H:i') }}</span>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Summary</h3>
                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $report->summary }}</p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Activities Done</h3>
                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $report->activities_done }}</p>
            </div>

            @if($report->issues_noted)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Issues Noted</h3>
                    <p class="text-sm text-gray-800 whitespace-pre-line">{{ $report->issues_noted }}</p>
                </div>
            @endif

            @if($report->recommendations)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Recommendations</h3>
                    <p class="text-sm text-gray-800 whitespace-pre-line">{{ $report->recommendations }}</p>
                </div>
            @endif

        </div>
    </div>
</div>
