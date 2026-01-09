<x-filament::page>
    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Options</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Hostel Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <select 
                        wire:model="selectedHostel" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        wire:change="loadAvailabilityData"
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
                        wire:change="loadAvailabilityData"
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

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input 
                        type="date" 
                        wire:model="startDate" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        wire:change="loadAvailabilityData"
                    >
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input 
                        type="date" 
                        wire:model="endDate" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        wire:change="loadAvailabilityData"
                        min="{{ $startDate }}"
                    >
                </div>
            </div>
        </div>

        <!-- Availability Calendar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Room Availability Calendar</h2>
                <div class="flex items-center space-x-2">
                    <!-- Navigation Buttons -->
                    <div class="flex space-x-1">
                        <button 
                            type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            wire:click="navigatePrevious"
                            title="Previous Period"
                        >
                            ←
                        </button>
                        <button 
                            type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            wire:click="navigateToToday"
                            title="Today"
                        >
                            Today
                        </button>
                        <button 
                            type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            wire:click="navigateNext"
                            title="Next Period"
                        >
                            →
                        </button>
                    </div>
                    
                    <!-- View Period Selector -->
                    <select 
                        wire:model="viewPeriod" 
                        class="border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        wire:change="setViewPeriod"
                    >
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="custom">Custom Dates</option>
                    </select>
                    
                    <!-- Availability Status Filter -->
                    <select 
                        wire:model="availabilityStatus" 
                        class="border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        wire:change="loadAvailabilityData"
                    >
                        <option value="all">All Beds</option>
                        <option value="available">Available Only</option>
                        <option value="occupied">Occupied Only</option>
                    </select>
                    
                    <button 
                        type="button" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        wire:click="$toggle('showBookingModal')"
                    >
                        Create Booking
                    </button>
                </div>
            </div>
            
            @if(count($availabilityData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                @php
                                    $currentDate = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                @endphp
                                @while($currentDate->lte($end))
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $currentDate->format('M j') }}
                                    </th>
                                    @php $currentDate->addDay(); @endphp
                                @endwhile
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($availabilityData as $bedData)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $bedData['bed_number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $bedData['room_number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $bedData['hostel_name'] }}
                                    </td>
                                    @php
                                        $currentDate = \Carbon\Carbon::parse($startDate);
                                        $end = \Carbon\Carbon::parse($endDate);
                                    @endphp
                                    @while($currentDate->lte($end))
                                        @php
                                            $dateStr = $currentDate->format('Y-m-d');
                                            $availability = $bedData['availability'][$dateStr] ?? ['available' => false, 'status' => 'unknown'];
                                        @endphp
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $availability['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $availability['status'] === 'available' ? '✓' : '✗' }}
                                            </span>
                                        </td>
                                        @php $currentDate->addDay(); @endphp
                                    @endwhile
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button 
                                            type="button" 
                                            class="text-primary-600 hover:text-primary-900"
                                            wire:click="$set('selectedBedId', {{ $bedData['id'] }})"
                                            wire:click="$toggle('showBookingModal')"
                                        >
                                            Book Now
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No availability data found for the selected filters.</p>
                    <p class="text-sm mt-2">Please adjust your filters and try again.</p>
                </div>
            @endif
        </div>

        <!-- Booking Modal -->
        <x-filament::modal 
            id="booking-modal" 
            :heading="'Create Booking for Bed #' . ($selectedBedId ? \Modules\Hostels\Models\Bed::find($selectedBedId)->bed_number : 'N/A')"
            :subheading="'Select dates for booking'"
            wire:model="showBookingModal"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                    <input 
                        type="date" 
                        wire:model="bookingCheckInDate" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        min="{{ now()->format('Y-m-d') }}"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                    <input 
                        type="date" 
                        wire:model="bookingCheckOutDate" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        min="{{ $bookingCheckInDate ?? now()->format('Y-m-d') }}"
                    >
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-filament::button 
                        color="gray" 
                        wire:click="$set('showBookingModal', false)"
                    >
                        Cancel
                    </x-filament::button>
                    
                    <x-filament::button 
                        wire:click="createBooking({{ $selectedBedId }}, '{{ $bookingCheckInDate }}', '{{ $bookingCheckOutDate }}')"
                        wire:loading.attr="disabled"
                    >
                        Create Booking
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</x-filament::page>