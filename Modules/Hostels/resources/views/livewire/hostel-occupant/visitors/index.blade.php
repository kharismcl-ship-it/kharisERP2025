<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Visitor History</h1>
            <p class="mt-0.5 text-sm text-gray-500">All visitors you have pre-registered or who have visited you.</p>
        </div>
        <a href="{{ route('hostel_occupant.visitors.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Register Visitor
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Visitors list ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($visitors->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No visitor records yet</p>
                <p class="mt-1 text-sm text-gray-500">Pre-register your first visitor to get started.</p>
                <a href="{{ route('hostel_occupant.visitors.create') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Register a Visitor
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Visitor</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Expected / Checked In</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked Out</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($visitors as $visitor)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-medium text-gray-900">{{ $visitor->visitor_name }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">{{ $visitor->visitor_phone ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">{{ $visitor->purpose ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">
                                        {{ $visitor->check_in_at?->format('M j, Y H:i') ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-gray-600">
                                        {{ $visitor->check_out_at?->format('M j, Y H:i') ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($visitor->check_out_at)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                            Checked Out
                                        </span>
                                    @elseif($visitor->check_in_at && $visitor->check_in_at->isPast())
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                            Checked In
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">
                                            Pre-Registered
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($visitors->hasPages())
                <div class="border-t border-gray-100 px-5 py-3">
                    {{ $visitors->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
