<x-filament-panels::page>
    @if(! $employee)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm">
            No employee profile is linked to your account. Please contact HR to set up your profile.
        </div>
    @else
        {{-- Welcome Banner --}}
        <div class="bg-teal-600 text-white rounded-xl p-6 flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                {{ strtoupper(substr($employee['first_name'], 0, 1)) }}{{ strtoupper(substr($employee['last_name'] ?? '', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold">Welcome, {{ $employee['first_name'] }}!</h2>
                <p class="text-teal-100 text-sm">
                    {{ $employee['job_position']['title'] ?? '' }}
                    @if(! empty($employee['department']))
                        &mdash; {{ $employee['department']['name'] }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Leave Balances --}}
        @if($leaveBalances)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Leave Balances</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($leaveBalances as $balance)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <p class="text-xs text-gray-500 truncate">{{ $balance['leave_type']['name'] ?? '—' }}</p>
                    <p class="text-2xl font-bold text-teal-600 mt-1">{{ number_format($balance['balance'] ?? 0, 1) }}</p>
                    <p class="text-xs text-gray-400">days available</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Operational Portals --}}
        @if($farmPortals || $hostelPortals)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">My Portals</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($farmPortals as $farm)
                <a href="{{ url('/farms/' . $farm['slug']) }}"
                   class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm hover:bg-green-100 transition flex items-center gap-3">
                    <div class="w-9 h-9 bg-green-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-green-800 truncate">{{ $farm['name'] }}</p>
                        <p class="text-xs text-green-600">Farm Portal</p>
                    </div>
                </a>
                @endforeach

                @foreach($hostelPortals as $hostel)
                <a href="{{ url('/hostels/admin/' . $hostel['slug']) }}"
                   class="bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-sm hover:bg-blue-100 transition flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-blue-800 truncate">{{ $hostel['name'] }}</p>
                        <p class="text-xs text-blue-600">Hostel Portal</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Pending Leave Requests --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Pending Leave Requests</h3>
                @if($pendingLeaves)
                    <div class="space-y-2">
                        @foreach($pendingLeaves as $leave)
                        <div class="flex items-center justify-between text-sm border-b border-gray-100 pb-2">
                            <div>
                                <span class="font-medium">{{ $leave['leave_type']['name'] ?? '—' }}</span>
                                <span class="text-gray-400 ml-2 text-xs">
                                    {{ \Carbon\Carbon::parse($leave['start_date'])->format('M d') }}
                                    – {{ \Carbon\Carbon::parse($leave['end_date'])->format('M d, Y') }}
                                </span>
                            </div>
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pending</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No pending leave requests.</p>
                @endif
                <a href="{{ route('filament.staff.resources.my-leave.index', ['tenant' => Filament::getTenant()]) }}"
                   class="mt-3 inline-block text-xs text-teal-600 hover:underline">View all leave →</a>
            </div>

            {{-- Announcements --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Company Announcements</h3>
                @if($announcements)
                    <div class="space-y-3">
                        @foreach($announcements as $ann)
                        <div class="text-sm border-b border-gray-100 pb-2">
                            <div class="flex items-start gap-2">
                                @if(($ann['priority'] ?? '') === 'urgent')
                                    <span class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded flex-shrink-0">Urgent</span>
                                @elseif(($ann['priority'] ?? '') === 'high')
                                    <span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded flex-shrink-0">High</span>
                                @endif
                                <span class="font-medium">{{ $ann['title'] }}</span>
                            </div>
                            <p class="text-gray-400 text-xs mt-0.5">
                                {{ \Carbon\Carbon::parse($ann['published_at'])->diffForHumans() }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No announcements.</p>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
