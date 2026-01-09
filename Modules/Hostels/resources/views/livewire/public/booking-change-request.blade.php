<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                
                <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Request Room/Bed Change
                </h1>
                
                <p class="mt-4 text-lg text-gray-500">
                    Submit a request to change your room or bed for booking {{ $booking->booking_reference }}.
                </p>
            </div>
            
            <div class="mt-10 bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Current Booking</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Booking Reference</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Hostel</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->hostel->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Current Room</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->room->room_number ?? 'Not assigned' }}</dd>
                            </div>
                            @if($booking->bed)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Current Bed</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->bed->bed_number }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Check-in Date</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->check_in_date->format('M d, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Check-out Date</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->check_out_date->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <form wire:submit.prevent="submit" class="mt-8">
                    @if (session()->has('message'))
                        <div class="rounded-md bg-green-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">
                                        {{ session('message') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if (session()->has('error'))
                        <div class="rounded-md bg-red-50 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="space-y-6">
                        <div>
                            <label for="requestedRoomId" class="block text-sm font-medium text-gray-700">
                                Requested Room
                            </label>
                            <select 
                                wire:model.live="requestedRoomId"
                                id="requestedRoomId"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select a room</option>
                                @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->room_number }} ({{ $room->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('requestedRoomId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="requestedBedId" class="block text-sm font-medium text-gray-700">
                                Requested Bed (Optional)
                            </label>
                            <select 
                                wire:model="requestedBedId"
                                id="requestedBedId"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">No preference</option>
                                @foreach($availableBeds as $bed)
                                    <option value="{{ $bed->id }}">
                                        {{ $bed->bed_number }} 
                                        @if($bed->is_upper_bunk) (Upper Bunk) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('requestedBedId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">
                                Reason for Change
                            </label>
                            <textarea 
                                wire:model="reason"
                                id="reason"
                                rows="4"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end">
                        <button 
                            type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('hostels.public.booking.confirmation', $booking) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Back to Booking Details
                </a>
            </div>
        </div>
    </div>
</div>