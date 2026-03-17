<x-filament-panels::page>
    @if(! $employee)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm">
            No employee profile is linked to your account. Please contact HR.
        </div>
    @else
        <div class="max-w-2xl mx-auto space-y-6">
            {{-- Today's Status Card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-800">Today — {{ now()->format('l, F j, Y') }}</h2>
                    @if($todayRecord)
                        @if($todayRecord->check_out_time)
                            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">Completed</span>
                        @elseif($todayRecord->check_in_time)
                            <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">Clocked In</span>
                        @endif
                    @else
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-medium">Not Clocked In</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 border border-green-100 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 mb-1">Clock In</p>
                        <p class="text-2xl font-bold text-green-700">
                            {{ $todayRecord?->check_in_time ? $todayRecord->check_in_time->format('g:i A') : '—' }}
                        </p>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 mb-1">Clock Out</p>
                        <p class="text-2xl font-bold text-orange-700">
                            {{ $todayRecord?->check_out_time ? $todayRecord->check_out_time->format('g:i A') : '—' }}
                        </p>
                    </div>
                </div>

                @if($todayRecord?->check_in_time && $todayRecord?->check_out_time)
                    @php
                        $hours = $todayRecord->check_in_time->diffInMinutes($todayRecord->check_out_time);
                        $h = intdiv($hours, 60);
                        $m = $hours % 60;
                    @endphp
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">Total Hours Worked:
                            <span class="font-semibold text-teal-700">{{ $h }}h {{ $m }}m</span>
                        </p>
                    </div>
                @endif
            </div>

            {{-- Weekly Summary --}}
            @php
                $companyId = \Filament\Facades\Filament::getTenant()?->id;
                $weekRecords = \Modules\HR\Models\AttendanceRecord::where('employee_id', $employee->id)
                    ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->toDateString()])
                    ->orderBy('date')
                    ->get();
            @endphp
            @if($weekRecords->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">This Week</h3>
                    <div class="divide-y divide-gray-100">
                        @foreach($weekRecords as $record)
                            <div class="flex items-center justify-between py-2 text-sm">
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($record->date)->format('D, M j') }}</span>
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-500">
                                        In: <span class="font-medium text-gray-800">{{ $record->check_in_time?->format('g:i A') ?? '—' }}</span>
                                    </span>
                                    <span class="text-gray-500">
                                        Out: <span class="font-medium text-gray-800">{{ $record->check_out_time?->format('g:i A') ?? '—' }}</span>
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $record->status === 'present' ? 'bg-green-100 text-green-700' : ($record->status === 'absent' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
