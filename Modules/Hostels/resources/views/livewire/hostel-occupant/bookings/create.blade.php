<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Create New Booking</h2>
                
                <form wire:submit.prevent="createBooking" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="selectedHostel" class="block text-sm font-medium text-gray-700">Hostel</label>
                            <select 
                                id="selectedHostel" 
                                wire:model.live="selectedHostel"
                                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="">Select a hostel</option>
                                @foreach($hostels as $hostel)
                                    <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedHostel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="bookingType" class="block text-sm font-medium text-gray-700">Booking Type</label>
                            <select 
                                id="bookingType" 
                                wire:model="bookingType"
                                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="academic">Academic Year</option>
                                <option value="short_stay">Short Stay</option>
                            </select>
                            @error('bookingType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        @if($bookingType === 'academic')
                            <div>
                                <label for="academicYear" class="block text-sm font-medium text-gray-700">Academic Year</label>
                                <input 
                                    type="text" 
                                    id="academicYear" 
                                    wire:model="academicYear"
                                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                @error('academicYear') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                                <select 
                                    id="semester" 
                                    wire:model="semester"
                                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        
                        <div>
                            <label for="checkInDate" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                            <input 
                                type="date" 
                                id="checkInDate" 
                                wire:model="checkInDate"
                                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            @error('checkInDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="checkOutDate" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                            <input 
                                type="date" 
                                id="checkOutDate" 
                                wire:model="checkOutDate"
                                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            @error('checkOutDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    @if($selectedHostel)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="selectedRoom" class="block text-sm font-medium text-gray-700">Room</label>
                                <select 
                                    id="selectedRoom" 
                                    wire:model.live="selectedRoom"
                                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Select a room</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->room_number }} ({{ ucfirst($room->room_type) }})</option>
                                    @endforeach
                                </select>
                                @error('selectedRoom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            @if($selectedRoom && count($beds) > 0)
                                <div>
                                    <label for="selectedBed" class="block text-sm font-medium text-gray-700">Bed (Optional)</label>
                                    <select 
                                        id="selectedBed" 
                                        wire:model="selectedBed"
                                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">Select a bed (optional)</option>
                                        @foreach($beds as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->bed_number }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedBed') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="flex justify-end">
                        <a href="{{ route('hostel_occupant.bookings.index') }}" class="mr-4 px-4 py-2 text-gray-600 hover:underline">
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            @if(!$selectedHostel || !$selectedRoom) disabled @endif
                        >
                            Create Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>