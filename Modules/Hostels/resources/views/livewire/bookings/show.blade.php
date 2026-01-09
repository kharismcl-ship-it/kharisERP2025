<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Booking Details</h1>
                <p class="text-gray-600">Booking Reference: {{ $booking->booking_reference }}</p>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($booking->status === 'awaiting_payment') bg-orange-100 text-orange-800
                    @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($booking->status === 'checked_in') bg-green-100 text-green-800
                    @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Booking Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Booking Type</div>
                        <div class="font-medium capitalize">{{ str_replace('_', ' ', $booking->booking_type) }}</div>
                    </div>
                    
                    @if ($booking->booking_type === 'academic')
                        <div>
                            <div class="text-sm text-gray-500">Academic Year</div>
                            <div class="font-medium">{{ $booking->academic_year }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500">Semester</div>
                            <div class="font-medium">{{ $booking->semester }}</div>
                        </div>
                    @endif
                    
                    <div>
                        <div class="text-sm text-gray-500">Check-in Date</div>
                        <div class="font-medium">{{ $booking->check_in_date->format('M d, Y') }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Check-out Date</div>
                        <div class="font-medium">{{ $booking->check_out_date->format('M d, Y') }}</div>
                    </div>
                    
                    @if ($booking->actual_check_in_at)
                        <div>
                            <div class="text-sm text-gray-500">Actual Check-in</div>
                            <div class="font-medium">{{ $booking->actual_check_in_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif
                    
                    @if ($booking->actual_check_out_at)
                        <div>
                            <div class="text-sm text-gray-500">Actual Check-out</div>
                            <div class="font-medium">{{ $booking->actual_check_out_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif
                    
                    <div>
                        <div class="text-sm text-gray-500">Channel</div>
                        <div class="font-medium capitalize">{{ str_replace('_', ' ', $booking->channel) }}</div>
                    </div>
                </div>
                
                @if ($booking->notes)
                    <div class="mt-4">
                        <div class="text-sm text-gray-500">Notes</div>
                        <div class="font-medium">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>
            
            <!-- Hostel Occupant Information -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Hostel Occupant Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div class="font-medium">{{ $booking->hostelOccupant->first_name }} {{ $booking->hostelOccupant->last_name }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Phone</div>
                        <div class="font-medium">{{ $booking->hostelOccupant->phone }}</div>
                    </div>
                    
                    @if ($booking->tenant->email)
                        <div>
                            <div class="text-sm text-gray-500">Email</div>
                            <div class="font-medium">{{ $booking->tenant->email }}</div>
                        </div>
                    @endif
                    
                    @if ($booking->tenant->student_id)
                        <div>
                            <div class="text-sm text-gray-500">Student ID</div>
                            <div class="font-medium">{{ $booking->tenant->student_id }}</div>
                        </div>
                    @endif
                    
                    @if ($booking->tenant->institution)
                        <div>
                            <div class="text-sm text-gray-500">Institution</div>
                            <div class="font-medium">{{ $booking->tenant->institution }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Room & Bed Information -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Accommodation</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Hostel</div>
                        <div class="font-medium">{{ $booking->hostel->name }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Room</div>
                        <div class="font-medium">Room {{ $booking->room->room_number }} ({{ ucfirst($booking->room->room_type) }})</div>
                    </div>
                    
                    @if ($booking->bed_id)
                        <div>
                            <div class="text-sm text-gray-500">Bed</div>
                            <div class="font-medium">Bed {{ $booking->bed->bed_number }}</div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Charges -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Charges</h2>
                
                <div class="border border-gray-200 rounded-md mb-4">
                    @foreach ($booking->charges as $charge)
                        <div class="flex justify-between p-3 border-b border-gray-200">
                            <div>{{ $charge->description }}</div>
                            <div class="font-medium">{{ number_format($charge->amount, 2) }}</div>
                        </div>
                    @endforeach
                    <div class="flex justify-between p-3 font-bold">
                        <div>Total Amount:</div>
                        <div>{{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                    <div class="flex justify-between p-3">
                        <div>Amount Paid:</div>
                        <div>{{ number_format($booking->amount_paid, 2) }}</div>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 font-bold">
                        <div>Balance:</div>
                        <div>{{ number_format($booking->balance_amount, 2) }}</div>
                    </div>
                </div>
                
                <div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        @if($booking->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($booking->payment_status === 'partially_paid') bg-yellow-100 text-yellow-800
                        @elseif($booking->payment_status === 'unpaid') bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-lg font-semibold mb-4">Actions</h2>
                
                <div class="space-y-3">
                    @if ($this->canCheckIn)
                        <button wire:click="$set('showCheckInModal', true)" 
                                class="w-full px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                            Check In
                        </button>
                    @endif
                    
                    @if ($this->canCheckOut)
                        <button wire:click="$set('showCheckOutModal', true)" 
                                class="w-full px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                            Check Out
                        </button>
                    @endif
                    
                    <button wire:click="$set('showRoomChangeModal', true)" 
                            class="w-full px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700">
                        Change Room/Bed
                    </button>
                    
                    <button class="w-full px-4 py-2 bg-gray-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-700">
                        Edit Booking
                    </button>
                    
                    <button class="w-full px-4 py-2 bg-gray-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-700">
                        Cancel Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Check-in Modal -->
    @if ($showCheckInModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Confirm Check-in
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to check in this booking? This will update the status to "Checked In" and update room/bed availability.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="checkIn" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Check In
                        </button>
                        <button wire:click="$set('showCheckInModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Check-out Modal -->
    @if ($showCheckOutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Check Out Tenant
                                </h3>
                                
                                <div class="mt-4">
                                    <h4 class="text-md font-medium mb-2">Additional Charges</h4>
                                    
                                    @if (count($checkOutCharges) > 0)
                                        <div class="space-y-2">
                                            @foreach ($checkOutCharges as $index => $charge)
                                                <div class="grid grid-cols-12 gap-2 items-center">
                                                    <div class="col-span-6">
                                                        <input type="text" 
                                                               wire:model="checkOutCharges.{{ $index }}.description"
                                                               placeholder="Description"
                                                               class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    </div>
                                                    <div class="col-span-4">
                                                        <input type="number" step="0.01"
                                                               wire:model="checkOutCharges.{{ $index }}.amount"
                                                               placeholder="Amount"
                                                               class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                    </div>
                                                    <div class="col-span-2">
                                                        <button wire:click="removeCheckOutCharge({{ $index }})" 
                                                                class="text-red-500 hover:text-red-700">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-2 text-gray-500 text-sm">
                                            No additional charges added
                                        </div>
                                    @endif
                                    
                                    <button wire:click="addCheckOutCharge" 
                                            class="mt-2 px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                                        Add Charge
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="checkOut" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Check Out
                        </button>
                        <button wire:click="$set('showCheckOutModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Room Change Modal -->
    @if ($showRoomChangeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Change Room/Bed
                                </h3>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select New Room *</label>
                                        <select wire:model="newRoomId" 
                                                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Choose a room</option>
                                            @foreach ($this->availableRooms as $room)
                                                <option value="{{ $room->id }}">
                                                    Room {{ $room->room_number }} ({{ ucfirst($room->room_type) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    @if ($newRoomId)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Bed (Optional)</label>
                                            <select wire:model="newBedId" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">No specific bed</option>
                                                @foreach ($this->bedsForRoom as $bed)
                                                    <option value="{{ $bed->id }}">Bed {{ $bed->bed_number }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="changeRoom" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Change Room/Bed
                        </button>
                        <button wire:click="$set('showRoomChangeModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>