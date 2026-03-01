<div class="container mx-auto p-6 bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Team Leave Calendar</h2>
        
        <div class="flex items-center space-x-4">
            <!-- View Mode Toggle -->
            <div class="flex bg-gray-100 rounded-md p-1">
                <button 
                    wire:click="changeView('month')" 
                    class="px-3 py-1 text-sm rounded-md {{ $viewMode === 'month' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-200' }}"
                >
                    Month
                </button>
                <button 
                    wire:click="changeView('week')" 
                    class="px-3 py-1 text-sm rounded-md {{ $viewMode === 'week' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-200' }}"
                >
                    Week
                </button>
                <button 
                    wire:click="changeView('day')" 
                    class="px-3 py-1 text-sm rounded-md {{ $viewMode === 'day' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-200' }}"
                >
                    Day
                </button>
            </div>

            <!-- Navigation -->
            <div class="flex items-center space-x-2">
                <button 
                    wire:click="previousPeriod" 
                    class="p-2 rounded-md hover:bg-gray-100 text-gray-600"
                    title="Previous"
                >
                    ←
                </button>
                
                <button 
                    wire:click="today" 
                    class="px-3 py-1 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600"
                >
                    Today
                </button>
                
                <button 
                    wire:click="nextPeriod" 
                    class="p-2 rounded-md hover:bg-gray-100 text-gray-600"
                    title="Next"
                >
                    →
                </button>
            </div>

            <!-- Period Label -->
            <span class="text-lg font-semibold text-gray-700">{{ $periodLabel }}</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
        <!-- Department Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <select 
                wire:model="selectedDepartment" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Employee Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
            <select 
                wire:model="selectedEmployee" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="flex items-end">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    wire:model="showApprovedOnly" 
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Show Approved Only</span>
            </label>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white rounded-lg border border-gray-200">
        @if($viewMode === 'month')
            <!-- Month View -->
            <div class="grid grid-cols-7 gap-1 p-2 bg-gray-50 border-b border-gray-200">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center text-sm font-medium text-gray-500 py-2">{{ $day }}</div>
                @endforeach
            </div>
            
            <div class="grid grid-cols-7 gap-1 p-2">
                @php
                    $startOfMonth = \Carbon\Carbon::parse($currentDate)->startOfMonth()->startOfWeek();
                    $endOfMonth = \Carbon\Carbon::parse($currentDate)->endOfMonth()->endOfWeek();
                    $currentDay = $startOfMonth->copy();
                @endphp
                
                @while($currentDay->lte($endOfMonth))
                    @php
                        $isCurrentMonth = $currentDay->month == \Carbon\Carbon::parse($currentDate)->month;
                        $isToday = $currentDay->isToday();
                        $dayEvents = array_filter($events, function($event) use ($currentDay) {
                            $eventStart = \Carbon\Carbon::parse($event['start']);
                            $eventEnd = \Carbon\Carbon::parse($event['end'])->subDay();
                            return $currentDay->between($eventStart, $eventEnd);
                        });
                    @endphp
                    
                    <div class="min-h-24 p-1 border border-gray-100 {{ !$isCurrentMonth ? 'bg-gray-50' : '' }}">
                        <div class="text-right">
                            <span class="inline-block w-6 h-6 text-sm rounded-full {{ $isToday ? 'bg-blue-500 text-white' : 'text-gray-700' }} text-center leading-6">
                                {{ $currentDay->day }}
                            </span>
                        </div>
                        
                        <div class="mt-1 space-y-1">
                            @foreach(array_slice($dayEvents, 0, 3) as $event)
                                <div 
                                    class="text-xs p-1 rounded cursor-pointer" 
                                    style="background-color: {{ $event['color'] }}; color: white;"
                                    title="{{ $event['extendedProps']['employee'] }} - {{ $event['extendedProps']['leave_type'] }}"
                                >
                                    {{ $event['extendedProps']['employee'] }}
                                </div>
                            @endforeach
                            
                            @if(count($dayEvents) > 3)
                                <div class="text-xs text-gray-500 text-center">
                                    +{{ count($dayEvents) - 3 }} more
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @php $currentDay->addDay(); @endphp
                @endwhile
            </div>
        
        @elseif($viewMode === 'week')
            <!-- Week View -->
            <div class="grid grid-cols-8 gap-1 p-2 bg-gray-50 border-b border-gray-200">
                <div class="text-sm font-medium text-gray-500 py-2">Time</div>
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <div class="text-center text-sm font-medium text-gray-500 py-2">{{ $day }}</div>
                @endforeach
            </div>
            
            <div class="grid grid-cols-8 gap-1 p-2">
                <!-- Time slots -->
                <div class="space-y-1">
                    @foreach(range(8, 17) as $hour)
                        <div class="h-12 text-xs text-gray-500 text-right pr-2 pt-1">
                            {{ sprintf('%02d:00', $hour) }}
                        </div>
                    @endforeach
                </div>
                
                <!-- Day columns -->
                @php
                    $startOfWeek = \Carbon\Carbon::parse($currentDate)->startOfWeek();
                @endphp
                
                @foreach(range(0, 6) as $dayOffset)
                    @php
                        $currentDay = $startOfWeek->copy()->addDays($dayOffset);
                        $dayEvents = array_filter($events, function($event) use ($currentDay) {
                            $eventStart = \Carbon\Carbon::parse($event['start']);
                            $eventEnd = \Carbon\Carbon::parse($event['end'])->subDay();
                            return $currentDay->between($eventStart, $eventEnd);
                        });
                    @endphp
                    
                    <div class="space-y-1">
                        @foreach(range(8, 17) as $hour)
                            <div class="h-12 border border-gray-100 p-1">
                                <!-- Hour slot content -->
                            </div>
                        @endforeach
                        
                        <!-- All-day events -->
                        @if(count($dayEvents) > 0)
                            <div class="mt-2 p-2 bg-blue-50 rounded border border-blue-100">
                                <div class="text-xs font-medium text-blue-800 mb-1">Leave Today:</div>
                                @foreach($dayEvents as $event)
                                    <div 
                                        class="text-xs p-1 rounded mb-1 cursor-pointer" 
                                        style="background-color: {{ $event['color'] }}; color: white;"
                                        title="{{ $event['extendedProps']['employee'] }} - {{ $event['extendedProps']['leave_type'] }}"
                                    >
                                        {{ $event['extendedProps']['employee'] }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        
        @else
            <!-- Day View -->
            <div class="p-4">
                @php
                    $currentDay = \Carbon\Carbon::parse($currentDate);
                    $dayEvents = array_filter($events, function($event) use ($currentDay) {
                        $eventStart = \Carbon\Carbon::parse($event['start']);
                        $eventEnd = \Carbon\Carbon::parse($event['end'])->subDay();
                        return $currentDay->between($eventStart, $eventEnd);
                    });
                @endphp
                
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Leave on {{ $currentDay->format('l, F j, Y') }}
                </h3>
                
                @if(count($dayEvents) > 0)
                    <div class="space-y-2">
                        @foreach($dayEvents as $event)
                            <div class="p-3 rounded-lg border-l-4" style="border-left-color: {{ $event['color'] }}; background-color: rgba({{ hexdec(substr($event['color'], 1, 2)) }}, {{ hexdec(substr($event['color'], 3, 2)) }}, {{ hexdec(substr($event['color'], 5, 2)) }}, 0.1);">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $event['extendedProps']['employee'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $event['extendedProps']['leave_type'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $event['extendedProps']['days'] }} days</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        {{ ucfirst($event['extendedProps']['status']) }}
                                    </span>
                                </div>
                                
                                @if($event['extendedProps']['reason'])
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Reason:</strong> {{ $event['extendedProps']['reason'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2">No leave scheduled for this day</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Legend -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h4 class="text-sm font-medium text-gray-700 mb-2">Leave Type Legend</h4>
        <div class="flex flex-wrap gap-2">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-1"></div>
                <span class="text-xs text-gray-600">Annual Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-red-500 mr-1"></div>
                <span class="text-xs text-gray-600">Sick Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-purple-500 mr-1"></div>
                <span class="text-xs text-gray-600">Maternity Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-blue-500 mr-1"></div>
                <span class="text-xs text-gray-600">Paternity Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-amber-500 mr-1"></div>
                <span class="text-xs text-gray-600">Emergency Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-gray-500 mr-1"></div>
                <span class="text-xs text-gray-600">Unpaid Leave</span>
            </div>
        </div>
    </div>
</div>