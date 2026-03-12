<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Maintenance Requests</h1>
            <p class="mt-0.5 text-sm text-gray-500">Track the status of your maintenance requests.</p>
        </div>
        <a href="{{ route('hostel_occupant.maintenance.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Request
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Requests list ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No maintenance requests</p>
                <p class="mt-1 text-sm text-gray-500">Submit a request if something needs attention in your room.</p>
                <a href="{{ route('hostel_occupant.maintenance.create') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Submit a Request
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reported</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Resolved</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-medium text-gray-900">{{ $request->title }}</p>
                                    @if($request->description)
                                        <p class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">{{ $request->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm text-gray-700">{{ $request->hostel?->name ?? '—' }}</p>
                                    @if($request->room)
                                        <p class="text-xs text-gray-400 mt-0.5">Room {{ $request->room->room_number }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($request->priority === 'urgent') bg-red-100 text-red-700
                                        @elseif($request->priority === 'high') bg-orange-100 text-orange-700
                                        @elseif($request->priority === 'medium') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($request->status === 'open') bg-yellow-100 text-yellow-700
                                        @elseif($request->status === 'in_progress') bg-blue-100 text-blue-700
                                        @elseif($request->status === 'completed') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">
                                        {{ $request->reported_at?->format('M j, Y') ?? $request->created_at->format('M j, Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">
                                        {{ $request->resolved_at?->format('M j, Y') ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
