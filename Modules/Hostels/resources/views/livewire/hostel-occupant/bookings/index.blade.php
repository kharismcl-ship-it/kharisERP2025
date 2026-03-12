<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Bookings</h1>
            <p class="mt-0.5 text-sm text-gray-500">All your hostel booking history.</p>
        </div>
        <a href="{{ route('hostel_occupant.bookings.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Booking
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Bookings table ────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($bookings->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No bookings yet</p>
                <p class="mt-1 text-sm text-gray-500">Create your first booking to get started.</p>
                <a href="{{ route('hostel_occupant.bookings.create') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Book a Room
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hostel / Room</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('hostel_occupant.bookings.show', $booking) }}"
                                       class="text-sm font-medium text-blue-600 hover:underline">
                                        {{ $booking->booking_reference }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm text-gray-900">{{ $booking->hostel?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Room {{ $booking->room?->room_number ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm text-gray-700">{{ $booking->check_in_date?->format('M j, Y') ?? '—' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">to {{ $booking->check_out_date?->format('M j, Y') ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ number_format($booking->total_amount, 2) }}</p>
                                    @if(($booking->balance_amount ?? 0) > 0)
                                        <p class="text-xs text-red-500 mt-0.5">Bal: {{ number_format($booking->balance_amount, 2) }}</p>
                                    @else
                                        <p class="text-xs text-green-600 mt-0.5">Fully paid</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($booking->status === 'checked_in') bg-blue-100 text-blue-700
                                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-700
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($booking->status === 'cancelled') bg-gray-100 text-gray-500
                                        @elseif($booking->status === 'checked_out') bg-purple-100 text-purple-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('hostel_occupant.bookings.show', $booking) }}"
                                       class="text-xs font-medium text-blue-600 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
