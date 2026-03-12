<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                @endphp
                {{ $greeting }}, {{ $occupant->first_name ?? 'Resident' }}
            </h1>
            <p class="mt-0.5 text-sm text-gray-500">
                {{ $occupant->hostel?->name }} · {{ now()->format('l, F j, Y') }}
            </p>
        </div>
        <a href="{{ route('hostel_occupant.bookings.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Booking
        </a>
    </div>

    {{-- ── Active stay card ─────────────────────────────────────────────── --}}
    @if($activeBooking)
    <div class="rounded-xl border border-blue-200 bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide">
                        {{ $activeBooking->status === 'checked_in' ? 'Currently Staying' : 'Confirmed' }}
                    </span>
                    <span class="text-blue-200 text-xs">{{ $activeBooking->booking_reference }}</span>
                </div>
                <div>
                    <p class="text-2xl font-bold">
                        Room {{ $activeBooking->room?->room_number ?? '—' }}
                        @if($activeBooking->bed)
                            <span class="text-lg font-normal text-blue-200">· Bed {{ $activeBooking->bed->bed_number }}</span>
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-blue-100">
                        {{ ucfirst(str_replace('_', ' ', $activeBooking->room?->type ?? '')) }}
                        @if($activeBooking->room?->billing_cycle)
                            · {{ ucfirst($activeBooking->room->billing_cycle) }} billing
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-4 text-sm text-blue-100">
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Check-in: {{ $activeBooking->check_in_date?->format('M j, Y') ?? '—' }}
                    </span>
                    <span class="text-blue-300">→</span>
                    <span class="flex items-center gap-1.5">
                        Check-out: {{ $activeBooking->check_out_date?->format('M j, Y') ?? '—' }}
                    </span>
                </div>
            </div>

            {{-- Financial summary panel --}}
            <div class="shrink-0 rounded-lg bg-white/15 px-5 py-4 text-sm space-y-2 min-w-48">
                <p class="font-semibold text-white text-xs uppercase tracking-wide mb-3">Payment Summary</p>
                <div class="flex justify-between gap-6">
                    <span class="text-blue-200">Total</span>
                    <span class="font-semibold">{{ number_format($activeBooking->total_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between gap-6">
                    <span class="text-blue-200">Paid</span>
                    <span class="font-semibold text-green-300">{{ number_format($activeBooking->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="border-t border-white/20 pt-2 flex justify-between gap-6">
                    <span class="text-blue-200">Balance</span>
                    <span class="font-bold {{ ($activeBooking->balance_amount ?? 0) > 0 ? 'text-yellow-300' : 'text-green-300' }}">
                        {{ number_format($activeBooking->balance_amount ?? 0, 2) }}
                    </span>
                </div>
                @if($deposit)
                <div class="border-t border-white/20 pt-2 flex justify-between gap-6">
                    <span class="text-blue-200">Deposit</span>
                    <span class="font-semibold {{ $deposit->status === 'collected' ? 'text-green-300' : 'text-yellow-300' }}">
                        {{ ucfirst($deposit->status) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <a href="{{ route('hostel_occupant.bookings.show', $activeBooking) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 px-4 py-1.5 text-sm font-medium text-white hover:bg-white/30 transition-colors">
                View Details
            </a>
            @if($activeBooking->canBeCancelled())
            <a href="{{ route('hostel_occupant.bookings.cancel', $activeBooking) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-4 py-1.5 text-sm font-medium text-blue-100 hover:bg-white/20 transition-colors">
                Cancel Booking
            </a>
            @endif
        </div>
    </div>
    @else
    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="mt-3 text-sm font-medium text-gray-700">No active booking</p>
        <p class="mt-1 text-sm text-gray-500">Book a room to get started</p>
        <a href="{{ route('hostel_occupant.bookings.create') }}"
           class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            Book a Room
        </a>
    </div>
    @endif

    {{-- ── 4 stat cards ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Active Bookings</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['active_bookings'] }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-yellow-50">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['pending_bookings'] }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Maintenance</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['open_maintenance'] }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Incidents</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['open_incidents'] }}</p>
        </div>

    </div>

    {{-- ── Quick actions ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold text-gray-700">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <a href="{{ route('hostel_occupant.maintenance.create') }}"
               class="flex flex-col items-center gap-2 rounded-lg border border-gray-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 group-hover:bg-orange-200 transition-colors">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700">Request Maintenance</span>
            </a>

            <a href="{{ route('hostel_occupant.incidents.create') }}"
               class="flex flex-col items-center gap-2 rounded-lg border border-gray-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 group-hover:bg-red-200 transition-colors">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700">Report Incident</span>
            </a>

            <a href="{{ route('hostel_occupant.visitors.create') }}"
               class="flex flex-col items-center gap-2 rounded-lg border border-gray-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 group-hover:bg-green-200 transition-colors">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700">Register Visitor</span>
            </a>

            <a href="{{ route('hostel_occupant.profile.edit') }}"
               class="flex flex-col items-center gap-2 rounded-lg border border-gray-200 p-4 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 group-hover:bg-purple-200 transition-colors">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700">Edit Profile</span>
            </a>
        </div>
    </div>

    {{-- ── Bottom grid: recent activity ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Recent Bookings --}}
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-700">Recent Bookings</h2>
                <a href="{{ route('hostel_occupant.bookings.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            @forelse($recentBookings as $b)
            <div class="flex items-center justify-between px-5 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('hostel_occupant.bookings.show', $b) }}"
                       class="text-sm font-medium text-blue-600 hover:underline">{{ $b->booking_reference }}</a>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Room {{ $b->room?->room_number ?? '—' }}
                        @if($b->bed) · Bed {{ $b->bed->bed_number }} @endif
                        · {{ $b->check_in_date?->format('M j') }} – {{ $b->check_out_date?->format('M j, Y') }}
                    </p>
                </div>
                <span class="ml-3 inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    @if($b->status === 'checked_in') bg-blue-100 text-blue-700
                    @elseif($b->status === 'confirmed') bg-green-100 text-green-700
                    @elseif($b->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($b->status === 'cancelled') bg-gray-100 text-gray-500
                    @else bg-gray-100 text-gray-600
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400">No bookings yet.</div>
            @endforelse
        </div>

        {{-- Side column: maintenance + incidents + visitors --}}
        <div class="space-y-5">

            {{-- Maintenance --}}
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Maintenance</h2>
                    <a href="{{ route('hostel_occupant.maintenance.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                @forelse($recentMaintenance as $m)
                <div class="px-5 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $m->title }}</p>
                    <div class="mt-0.5 flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $m->created_at?->diffForHumans() }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            @if($m->status === 'open') bg-red-50 text-red-700
                            @elseif($m->status === 'in_progress') bg-yellow-50 text-yellow-700
                            @else bg-green-50 text-green-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $m->status)) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-5 text-center text-xs text-gray-400">No requests.</div>
                @endforelse
            </div>

            {{-- Incidents --}}
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Incidents</h2>
                    <a href="{{ route('hostel_occupant.incidents.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                @forelse($recentIncidents as $i)
                <div class="px-5 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $i->title }}</p>
                    <div class="mt-0.5 flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $i->created_at?->diffForHumans() }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            @if($i->severity === 'critical') bg-red-50 text-red-700
                            @elseif($i->severity === 'high') bg-orange-50 text-orange-700
                            @elseif($i->severity === 'medium') bg-yellow-50 text-yellow-700
                            @else bg-gray-50 text-gray-600
                            @endif">
                            {{ ucfirst($i->severity ?? 'low') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-5 text-center text-xs text-gray-400">No incidents.</div>
                @endforelse
            </div>

            {{-- Recent Visitors --}}
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Recent Visitors</h2>
                    <a href="{{ route('hostel_occupant.visitors.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                @forelse($recentVisitors as $v)
                <div class="px-5 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <p class="text-sm font-medium text-gray-800">{{ $v->visitor_name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $v->purpose ?? 'Visit' }} ·
                        @if($v->check_out_at)
                            Checked out {{ $v->check_out_at->diffForHumans() }}
                        @elseif($v->check_in_at)
                            Checked in {{ $v->check_in_at->diffForHumans() }}
                        @else
                            Pre-registered
                        @endif
                    </p>
                </div>
                @empty
                <div class="px-5 py-5 text-center text-xs text-gray-400">No visitor history.</div>
                @endforelse
            </div>

        </div>
    </div>

</div>
