<x-filament::page>
    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Daily Check-In/Out Schedule</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Date Navigation -->
                <div class="flex items-center space-x-2">
                    <button 
                        wire:click="navigateToDate('previous')"
                        class="p-2 rounded-md bg-gray-100 hover:bg-gray-200"
                        title="Previous Day"
                    >
                        ←
                    </button>
                    
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input 
                            type="date" 
                            wire:model="selectedDate" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            wire:change="loadCheckInOutData"
                        >
                    </div>
                    
                    <button 
                        wire:click="navigateToDate('next')"
                        class="p-2 rounded-md bg-gray-100 hover:bg-gray-200"
                        title="Next Day"
                    >
                        →
                    </button>
                </div>

                <!-- Hostel Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <select 
                        wire:model="selectedHostel" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        wire:change="loadCheckInOutData"
                    >
                        <option value="">All Hostels</option>
                        @foreach(\Modules\Hostels\Models\Hostel::where('status', 'active')->get() as $hostel)
                            <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Room Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <select 
                        wire:model="selectedRoom" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        wire:change="loadCheckInOutData"
                        @if(!$selectedHostel) disabled @endif
                    >
                        <option value="">All Rooms</option>
                        @if($selectedHostel)
                            @foreach(\Modules\Hostels\Models\Room::where('hostel_id', $selectedHostel)->get() as $room)
                                <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Summary Stats -->
                <div class="bg-blue-50 rounded-md p-3">
                    <div class="text-sm text-blue-800">
                        <div class="font-medium">Check-ins: {{ count($checkInData) }}</div>
                        <div class="font-medium">Check-outs: {{ count($checkOutData) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-Ins Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Check-Ins for {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                </h3>
            </div>
            
            @if(count($checkInData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel Occupant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($checkInData as $booking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $booking['tenant']['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['bed_number'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['room']['room_number'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['room']['hostel']['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $booking['status'] === 'confirmed' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($booking['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking['check_in_date'])->diffInDays($booking['check_out_date']) }} nights
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No check-ins scheduled for this date.</p>
                </div>
            @endif
        </div>

        <!-- Check-Outs Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Check-Outs for {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                </h3>
            </div>
            
            @if(count($checkOutData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel Occupant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($checkOutData as $booking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $booking['tenant']['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['bed_number'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['room']['room_number'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['bed']['room']['hostel']['name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $booking['status'] === 'checked_in' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($booking['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking['check_out_time'] ?? '12:00 PM' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No check-outs scheduled for this date.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament::page>