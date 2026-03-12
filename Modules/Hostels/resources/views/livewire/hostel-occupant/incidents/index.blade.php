<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Incidents</h1>
            <p class="mt-0.5 text-sm text-gray-500">Incidents and safety reports you have submitted.</p>
        </div>
        <a href="{{ route('hostel_occupant.incidents.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Report Incident
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Incidents list ────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($incidents->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No incidents reported yet</p>
                <p class="mt-1 text-sm text-gray-500">Report an issue and our team will follow up promptly.</p>
                <a href="{{ route('hostel_occupant.incidents.create') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Report an Incident
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($incidents as $incident)
                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $incident->title }}</p>
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $incident->description }}</p>
                                <p class="mt-1.5 text-xs text-gray-400">
                                    Reported {{ $incident->reported_at?->diffForHumans() ?? $incident->created_at->diffForHumans() }}
                                    @if($incident->hostel)
                                        &bull; {{ $incident->hostel->name }}
                                    @endif
                                    @if($incident->room)
                                        &bull; Room {{ $incident->room->room_number }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($incident->severity === 'critical') bg-red-100 text-red-700
                                    @elseif($incident->severity === 'high') bg-orange-100 text-orange-700
                                    @elseif($incident->severity === 'medium') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($incident->status === 'resolved') bg-green-100 text-green-700
                                    @elseif($incident->status === 'closed') bg-gray-100 text-gray-500
                                    @elseif($incident->status === 'in_progress') bg-blue-100 text-blue-700
                                    @else bg-yellow-100 text-yellow-700
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($incidents->hasPages())
                <div class="border-t border-gray-100 px-5 py-3">
                    {{ $incidents->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
