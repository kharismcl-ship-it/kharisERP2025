<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create New Booking</h1>
        <p class="text-gray-600">Hostel: {{ $hostel->name }}</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <!-- Progress line -->
            <div class="absolute top-4 left-0 right-0 h-1 bg-gray-200 -z-10">
                <div class="h-full bg-blue-500 transition-all duration-300 ease-in-out" 
                     style="width: {{ ($step - 1) * 25 }}%"></div>
            </div>
            
            @for ($i = 1; $i <= 5; $i++)
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                        {{ $step >= $i ? 'bg-blue-500 text-white' : 'bg-white border-2 border-gray-300 text-gray-400' }}">
                        {{ $i }}
                    </div>
                    <div class="mt-2 text-sm font-medium 
                        {{ $step >= $i ? 'text-blue-600' : 'text-gray-500' }}">
                        @if ($i == 1) Hostel Occupant @endif
                        @if ($i == 2) Type @endif
                        @if ($i == 3) Room @endif
                        @if ($i == 4) Charges @endif
                        @if ($i == 5) Review @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Step 1: Hostel Occupant Selection -->
    @if ($step == 1)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Select Hostel Occupant</h2>
            
            @if ($showCreateHostelOccupantForm)
                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-3">Create New Hostel Occupant</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" wire:model="newHostelOccupant.first_name" 
                                   class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newHostelOccupant.first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" wire:model="newHostelOccupant.last_name" 
                                   class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newHostelOccupant.last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select wire:model="newHostelOccupant.gender" 
                                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('newHostelOccupant.gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                            <input type="text" wire:model="newHostelOccupant.phone" 
                                   class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newHostelOccupant.phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="newHostelOccupant.email" 
                                   class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newHostelOccupant.email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                            <input type="text" wire:model="newHostelOccupant.student_id" 
                                   class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-4">
                        <button wire:click="$set('showCreateHostelOccupantForm', false)" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="createHostelOccupant" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                            Create Hostel Occupant
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Hostel Occupant</label>
                    <div class="flex">
                        <input type="text" wire:model.debounce.300ms="hostelOccupantSearch" 
                               class="flex-1 rounded-l border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Search by name, phone, or student ID">
                        <button wire:click="$set('showCreateHostelOccupantForm', true)" 
                                class="px-4 py-2 bg-green-600 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                            Create New
                        </button>
                    </div>
                </div>
                
                @if (!empty($hostelOccupantSearch))
                    <div class="border border-gray-200 rounded-md max-h-60 overflow-y-auto">
                        @forelse ($this->hostelOccupants as $hostelOccupantItem)
                            <div wire:click="selectHostelOccupant({{ $hostelOccupantItem->id }})" 
                                 class="p-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                                <div class="font-medium">{{ $hostelOccupantItem->first_name }} {{ $hostelOccupantItem->last_name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $hostelOccupantItem->phone }} @if($hostelOccupantItem->student_id) • {{ $hostelOccupantItem->student_id }} @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-gray-500">
                                No hostel occupants found. Create a new hostel occupant.
                            </div>
                        @endforelse
                    </div>
                @endif
                
                @if ($hostelOccupant)
                    <div class="mt-4 p-4 bg-blue-50 rounded-md">
                        <div class="font-medium">{{ $hostelOccupant->first_name }} {{ $hostelOccupant->last_name }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $hostelOccupant->phone }} @if($hostelOccupant->student_id) • {{ $hostelOccupant->student_id }} @endif
                        </div>
                    </div>
                @endif
            @endif
            
            <div class="flex justify-end mt-6">
                <button wire:click="nextStep" 
                        @if (!$hostelOccupant) disabled @endif
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 @if (!$hostelOccupant) opacity-50 cursor-not-allowed @endif">
                    Next: Booking Type
                </button>
            </div>
        </div>
    @endif

    <!-- Step 2: Booking Type -->
    @if ($step == 2)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Booking Type & Period</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type *</label>
                    <select wire:model="bookingType" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="academic">Academic (Semester/Year)</option>
                        <option value="short_stay">Short Stay (Daily)</option>
                    </select>
                </div>
                
                @if ($bookingType === 'academic')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year *</label>
                        <input type="text" wire:model="academicYear" 
                               class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., 2025/2026">
                        @error('academicYear') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                        <input type="text" wire:model="semester" 
                               class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., Semester 1">
                        @error('semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Date *</label>
                    <input type="date" wire:model="checkInDate" 
                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('checkInDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date *</label>
                    <input type="date" wire:model="checkOutDate" 
                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('checkOutDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="flex justify-between mt-6">
                <button wire:click="previousStep" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </button>
                <button wire:click="nextStep" 
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Next: Room Selection
                </button>
            </div>
        </div>
    @endif

    <!-- Step 3: Room & Bed Selection -->
    @if ($step == 3)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Room & Bed Selection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($this->rooms as $room)
                    <div wire:click="selectRoom({{ $room->id }})" 
                         class="border rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow
                         {{ $selectedRoom == $room->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium">Room {{ $room->room_number }}</div>
                                <div class="text-sm text-gray-500 capitalize">{{ $room->room_type }}</div>
                            </div>
                            <div class="text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($room->status === 'available') bg-green-100 text-green-800
                                    @elseif($room->status === 'partially_occupied') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-sm">
                            <div>Occupancy: {{ $room->current_occupancy }}/{{ $room->max_occupancy }}</div>
                            <div>Rate: {{ number_format($room->base_rate, 2) }} 
                                /{{ str_replace('_', ' ', $room->billing_cycle) }}</div>
                        </div>
                        
                        @if ($selectedRoom == $room->id)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-sm font-medium mb-2">Beds:</div>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($room->beds as $bed)
                                        <div wire:click.stop="selectBed({{ $bed->id }})" 
                                             class="border rounded p-2 text-center text-xs cursor-pointer
                                             {{ $selectedBed == $bed->id ? 'border-blue-500 bg-blue-100' : 'border-gray-200' }}
                                             @if($bed->status !== 'available') opacity-50 cursor-not-allowed @endif">
                                            <div>Bed {{ $bed->bed_number }}</div>
                                            <div class="capitalize mt-1
                                                @if($bed->status === 'available') text-green-600
                                                @elseif($bed->status === 'occupied') text-red-600
                                                @else text-yellow-600 @endif">
                                                {{ $bed->status }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            @if ($this->selectedRoom)
                <div class="mt-4 p-4 bg-blue-50 rounded-md">
                    <div class="font-medium">Selected: Room {{ $this->selectedRoom->room_number }}</div>
                    @if ($this->selectedBed)
                        <div class="text-sm">Bed: {{ $this->selectedBed->bed_number }}</div>
                    @endif
                </div>
            @endif
            
            <div class="flex justify-between mt-6">
                <button wire:click="previousStep" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </button>
                <button wire:click="nextStep" 
                        @if (!$selectedRoom) disabled @endif
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 @if (!$selectedRoom) opacity-50 cursor-not-allowed @endif">
                    Next: Charges
                </button>
            </div>
        </div>
    @endif

    <!-- Step 4: Charges -->
    @if ($step == 4)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Charges & Pricing</h2>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-3">Base Charges</h3>
                <div class="border border-gray-200 rounded-md">
                    @php
                        $this->calculateCharges();
                    @endphp
                    
                    @foreach ($charges as $index => $charge)
                        <div class="flex justify-between p-3 border-b border-gray-200">
                            <div>
                                <div>{{ $charge['description'] }}</div>
                                @if (isset($charge['quantity']) && $charge['quantity'] > 1)
                                    <div class="text-sm text-gray-500">{{ $charge['quantity'] }} × {{ number_format($charge['unit_price'], 2) }}</div>
                                @endif
                            </div>
                            <div class="font-medium">{{ number_format($charge['amount'], 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium">Additional Charges</h3>
                    <button wire:click="addAdditionalCharge" 
                            class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                        Add Charge
                    </button>
                </div>
                
                @if (count($additionalCharges) > 0)
                    <div class="border border-gray-200 rounded-md">
                        @foreach ($additionalCharges as $index => $charge)
                            <div class="p-3 border-b border-gray-200 grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-4">
                                    <input type="text" 
                                           wire:model="additionalCharges.{{ $index }}.description"
                                           placeholder="Description"
                                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" step="0.01"
                                           wire:model="additionalCharges.{{ $index }}.quantity"
                                           placeholder="Qty"
                                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" step="0.01"
                                           wire:model="additionalCharges.{{ $index }}.unit_price"
                                           placeholder="Unit Price"
                                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                                <div class="col-span-2 text-right">
                                    {{ number_format($charge['amount'] ?? 0, 2) }}
                                </div>
                                <div class="col-span-1 text-right">
                                    <button wire:click="removeAdditionalCharge({{ $index }})" 
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
                    <div class="text-center py-4 text-gray-500">
                        No additional charges added
                    </div>
                @endif
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <div>Total Amount:</div>
                    <div>{{ number_format($this->totalAmount, 2) }}</div>
                </div>
            </div>
            
            <div class="flex justify-between mt-6">
                <button wire:click="previousStep" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </button>
                <button wire:click="nextStep" 
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Next: Review & Confirm
                </button>
            </div>
        </div>
    @endif

    <!-- Step 5: Review & Confirm -->
    @if ($step == 5)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Review & Confirm</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-medium mb-3">Hostel Occupant Details</h3>
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="font-medium">{{ $hostelOccupant->first_name }} {{ $hostelOccupant->last_name }}</div>
                        <div class="text-sm text-gray-600">
                            <div>Phone: {{ $hostelOccupant->phone }}</div>
                            @if($hostelOccupant->email) <div>Email: {{ $hostelOccupant->email }}</div> @endif
                            @if($hostelOccupant->student_id) <div>Student ID: {{ $hostelOccupant->student_id }}</div> @endif
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-3">Booking Details</h3>
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="text-gray-500">Type:</div>
                            <div class="capitalize">{{ str_replace('_', ' ', $bookingType) }}</div>
                            
                            @if ($bookingType === 'academic')
                                <div class="text-gray-500">Academic Year:</div>
                                <div>{{ $academicYear }}</div>
                                
                                <div class="text-gray-500">Semester:</div>
                                <div>{{ $semester }}</div>
                            @endif
                            
                            <div class="text-gray-500">Check-in:</div>
                            <div>{{ \Carbon\Carbon::parse($checkInDate)->format('M d, Y') }}</div>
                            
                            <div class="text-gray-500">Check-out:</div>
                            <div>{{ \Carbon\Carbon::parse($checkOutDate)->format('M d, Y') }}</div>
                            
                            <div class="text-gray-500">Room:</div>
                            <div>{{ $this->selectedRoom->room_number }}</div>
                            
                            @if ($this->selectedBed)
                                <div class="text-gray-500">Bed:</div>
                                <div>{{ $this->selectedBed->bed_number }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-3">Charges Summary</h3>
                <div class="border border-gray-200 rounded-md">
                    @foreach ($charges as $charge)
                        <div class="flex justify-between p-3 border-b border-gray-200">
                            <div>{{ $charge['description'] }}</div>
                            <div class="font-medium">{{ number_format($charge['amount'], 2) }}</div>
                        </div>
                    @endforeach
                    <div class="flex justify-between p-3 font-bold text-lg">
                        <div>Total Amount:</div>
                        <div>{{ number_format($this->totalAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between mt-6">
                <button wire:click="previousStep" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Previous
                </button>
                <button wire:click="saveBooking" 
                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                    Confirm & Create Booking
                </button>
            </div>
        </div>
    @endif
</div>