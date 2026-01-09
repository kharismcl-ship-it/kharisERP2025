<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Book Your Stay at {{ $hostel->name }}</flux:heading>
                <flux:subheading>Complete the following steps to confirm your booking</flux:subheading>
            </div>

            {{-- Progress Bar --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center w-full">
                    <ol class="flex items-center w-full text-sm font-medium text-center text-gray-500">
                        {{-- Step 1 --}}
                        <li class="flex items-center">
                            <div class="flex items-center">
                                <div class="{{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold">
                                    @if ($step > 1)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        1
                                    @endif
                                </div>
                                <span class="{{ $step >= 1 ? 'text-blue-600' : 'text-gray-500' }} ml-2 text-sm font-medium">
                                    Booking Details
                                </span>
                            </div>
                            <div class="flex-auto ml-2 h-0.5 bg-gray-200"></div>
                        </li>

                        {{-- Step 2 --}}
                        <li class="flex items-center">
                            <div class="flex items-center">
                                <div class="{{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold">
                                    @if ($step > 2)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        2
                                    @endif
                                </div>
                                <span class="{{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }} ml-2 text-sm font-medium">
                                    Your Information
                                </span>
                            </div>
                            <div class="flex-auto ml-2 h-0.5 bg-gray-200"></div>
                        </li>

                        {{-- Step 3 --}}
                        <li class="flex items-center">
                            <div class="flex items-center">
                                <div class="{{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold">
                                    @if ($step > 3)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        3
                                    @endif
                                </div>
                                <span class="{{ $step >= 3 ? 'text-blue-600' : 'text-gray-500' }} ml-2 text-sm font-medium">
                                    Room Selection
                                </span>
                            </div>
                            <div class="flex-auto ml-2 h-0.5 bg-gray-200"></div>
                        </li>

                        {{-- Step 4 --}}
                        <li class="flex items-center">
                            <div class="flex items-center">
                                <div class="{{ $step >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold">
                                    @if ($step > 4)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        4
                                    @endif
                                </div>
                                <span class="{{ $step >= 4 ? 'text-blue-600' : 'text-gray-500' }} ml-2 text-sm font-medium">
                                    Confirmation
                                </span>
                            </div>
                            <div class="flex-auto ml-2 h-0.5 bg-gray-200"></div>
                        </li>

                        {{-- Step 5 --}}
                        <li class="flex items-center">
                            <div class="flex items-center">
                                <div class="{{ $step >= 5 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold">
                                    @if ($step > 5)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        5
                                    @endif
                                </div>
                                <span class="{{ $step >= 5 ? 'text-blue-600' : 'text-gray-500' }} ml-2 text-sm font-medium">
                                    Payment
                                </span>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="p-6">
                @error('booking')
                    <div class="mb-4 p-3 rounded-md bg-red-50 text-red-700">{{ $message }}</div>
                @enderror
                {{-- STEP 1 --}}
                @if ($step === 1)
                    <div>
                        <flux:heading size="md">Booking Details</flux:heading>
                        <flux:subheading>Select your booking type and dates</flux:subheading>


                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:select
                                label="Booking Type"
                                wire:model.live="bookingType"
                                required
                                variant="listbox"
                            >
                                <flux:select.option value="academic">Academic Year</flux:select.option>
                                <flux:select.option value="semester">Semester</flux:select.option>
                                <flux:select.option value="short_stay">Short Stay</flux:select.option>
                            </flux:select>

                            @if ($bookingType === 'academic')
                                <flux:input
                                    label="Academic Year"
                                    wire:model="academicYear"
                                    required
                                />
                            @endif

                            @if ($bookingType === 'semester')
                                <flux:input
                                    label="Academic Year"
                                    wire:model="academicYear"
                                    required
                                />

                                <flux:select
                                    label="Semester"
                                    wire:model="semester"
                                    required
                                >
                                    <flux:select.option value="1">Semester 1</flux:select.option>
                                    <flux:select.option value="2">Semester 2</flux:select.option>
                                </flux:select>
                            @endif

                            @if (in_array($bookingType, ['academic', 'semester', 'short_stay']))
                                <flux:input
                                    label="Check-in Date"
                                    type="date"
                                    wire:model="checkInDate"
                                    required
                                />

                                <flux:input
                                    label="Check-out Date"
                                    type="date"
                                    wire:model="checkOutDate"
                                    required
                                />
                            @endif
                        </div>
                    </div>
                @endif

                {{-- STEP 2 --}}
                @if ($step === 2)
                    <div>
                        <flux:heading size="md">Your Information</flux:heading>
                        <flux:subheading>Provide your personal details</flux:subheading>

                        <div class="mt-6 space-y-6">
                            @if (Auth::guard('tenant')->check())
                                <flux:select
                                    label="Are you a new or returning tenant?"
                                    wire:model.live="hostelOccupantType"
                                    disabled
                                    variant="listbox"
                                >
                                    <flux:select.option value="existing" label="Returning Hostel Occupant" />
                                </flux:select>
                            @else
                                <flux:select
                                    label="Are you a new or returning tenant?"
                                    wire:model.live="hostelOccupantType"
                                >
                                    <flux:select.option value="new" label="New Guest" />
                                    <flux:select.option value="existing" label="Returning Hostel Occupant" />
                                </flux:select>
                            @endif

                            @if ($hostelOccupantType === 'existing')
                                @if(Auth::guard('tenant')->check())
                                    <flux:select
                                        label="Select Your Account"
                                        wire:model.live="existingHostelOccupantId"
                                        placeholder="Select your account"
                                    >
                                        <flux:select.option value="">Select your account</flux:select.option>
                                        @foreach ($existingHostelOccupants as $hostelOccupant)
                                            <flux:select.option value="{{ $hostelOccupant->id }}">
                                                 {{ $hostelOccupant->first_name }} {{ $hostelOccupant->last_name }} (ID: {{ $hostelOccupant->id }})
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:subheading class="text-sm text-gray-500">
                                        Can't find your account? Contact support or select "New Guest" above.
                                    </flux:subheading>
                                @else
                                    <flux:input
                                        label="Email or Phone"
                                        wire:model.live="lookupIdentifier"
                                        placeholder="Enter your registered email or phone"
                                    />
                                    @error('lookupIdentifier')
                                    <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                    <flux:button variant="primary" wire:click="sendOtpForExisting">
                                        Send verification code
                                    </flux:button>
                                    @if($otpSent)
                                        <flux:subheading class="text-sm text-gray-500">
                                            Verification code sent to {{ $otpDestination }}
                                        </flux:subheading>
                                        <flux:input
                                            class="mt-3"
                                            label="Verification Code"
                                            wire:model="verificationOtp"
                                            placeholder="Enter 6-digit code"
                                        />
                                        @error('verificationOtp')
                                        <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                        <flux:button class="mt-2" variant="primary" wire:click="verifyOtpForExisting">
                                            Verify and continue
                                        </flux:button>
                                    @endif
                                @endif
                            @endif

                            @php $existingHostelOccupant = (isset($existingHostelOccupants) && $existingHostelOccupantId) ? $existingHostelOccupants->firstWhere('id', $existingHostelOccupantId) : null; @endphp

                            @if ($hostelOccupantType === 'existing' && $existingHostelOccupantId && $existingHostelOccupant)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <flux:heading size="sm">Guest Information</flux:heading>
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <flux:badge>Name</flux:badge>
                                            <div class="mt-1">
                                                {{ $existingHostelOccupant->first_name }} {{ $existingHostelOccupant->last_name }}
                                            </div>
                                        </div>
                                        <div>
                                            <flux:badge>Gender</flux:badge>
                                            <div class="mt-1">{{ ucfirst($existingHostelOccupant->gender) }}</div>
                                        </div>
                                        <div>
                                            <flux:badge>Email</flux:badge>
                                            <div class="mt-1">{{ $existingHostelOccupant->email }}</div>
                                        </div>
                                        <div>
                                            <flux:badge>Phone</flux:badge>
                                            <div class="mt-1">{{ $existingHostelOccupant->phone }}</div>
                                        </div>
                                        @if ($existingHostelOccupant->student_id)
                                            <div>
                                                <flux:badge>Student ID</flux:badge>
                                                <div class="mt-1">{{ $existingHostelOccupant->student_id }}</div>
                                            </div>
                                        @endif
                                        @if ($existingHostelOccupant->institution)
                                            <div>
                                                <flux:badge>Institution</flux:badge>
                                                <div class="mt-1">{{ $existingHostelOccupant->institution }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($hostelOccupantType === 'new' || ($hostelOccupantType === 'existing' && !$existingHostelOccupantId))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <flux:input
                                        label="First Name"
                                        wire:model="firstName"
                                        required
                                    />

                                    <flux:input
                                        label="Last Name"
                                        wire:model="lastName"
                                        required
                                    />

                                    <flux:select
                                        label="Gender"
                                        wire:model="gender"
                                        required
                                    >
                                        @foreach ($this->genderOptions as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>

                                    <flux:input
                                        label="Email Address"
                                        type="email"
                                        wire:model="email"
                                        required
                                    />

                                    <flux:input
                                        label="Phone Number"
                                        type="tel"
                                        wire:model="phone"
                                        required
                                    />

                                    <flux:input
                                        label="Student ID (Optional)"
                                        wire:model="studentId"
                                    />

                                    <flux:input
                                        label="Institution (Optional)"
                                        wire:model="institution"
                                    />

                                    <div class="md:col-span-2">
                                        <flux:textarea
                                            label="Address (Optional)"
                                            wire:model="address"
                                            rows="3"
                                        />
                                    </div>

                                    <flux:input
                                        label="Emergency Contact Name (Optional)"
                                        wire:model="emergencyContactName"
                                    />

                                    <flux:input
                                        label="Emergency Contact Phone (Optional)"
                                        type="tel"
                                        wire:model="emergencyContactPhone"
                                    />

                                    <div class="md:col-span-2">
                                        <flux:heading size="sm">Identification Documents</flux:heading>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                            <div>
                                                <x-hostels::image-upload
                                                    label="ID Card Front Photo"
                                                    model="idCardFront"
                                                    :value="$idCardFront"
                                                    remove="removeIdCardFront"
                                                    accept="image/*"
                                                    promptHeading="Upload ID Front Photo"
                                                    promptText="JPG, PNG, WEBP up to 2MB"
                                                    previewAlt="ID Card Front Preview"
                                                />
                                            </div>

                                            <div>
                                                <x-hostels::image-upload
                                                    label="ID Card Back Photo"
                                                    model="idCardBack"
                                                    :value="$idCardBack"
                                                    remove="removeIdCardBack"
                                                    accept="image/*"
                                                    promptHeading="Upload ID Back Photo"
                                                    promptText="JPG, PNG, WEBP up to 2MB"
                                                    previewAlt="ID Card Back Preview"
                                                />
                                            </div>
                                            
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">

                                        <x-hostels::image-upload
                                                    label="Profile Photo"
                                                    model="profilePhoto"
                                                    :value="$profilePhoto"
                                                    remove="removeProfilePhoto"
                                                    accept="image/*"
                                                    promptHeading="Upload Profile Photo"
                                                    promptText="JPG, PNG, WEBP up to 2MB"
                                                    previewAlt="Profile Photo Preview"
                                        />

                                        
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- STEP 3 --}}
                @if ($step === 3)
                    <div>
                        <flux:heading size="md">Room Selection</flux:heading>
                        <flux:subheading>Choose your room and bed</flux:subheading>

                        <div class="mt-6 space-y-6">
                            <flux:select
                                label="Select Room"
                                wire:model.live="selectedRoom"
                                required
                                placeholder="Select a room"
                            >
                                <flux:select.option value="">Select a room</flux:select.option>
                                @foreach ($this->rooms as $room)
                                    <flux:select.option value="{{ $room->id }}">
                                        Room {{ $room->room_number }}
                                        ({{ ucfirst(str_replace('_', ' ', $room->type)) }})
                                        - {{ number_format($room->getRateForBillingCycle($bookingType), 2) }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            @if ($this->beds->count() > 0)
                                <flux:select
                                    label="Select Bed (Optional)"
                                    wire:model="selectedBed"
                                    placeholder="No specific bed"
                                >
                                    <flux:select.option value="">No specific bed</flux:select.option>
                                    @foreach ($this->beds as $bed)
                                        <flux:select.option value="{{ $bed->id }}">
                                            Bed {{ $bed->bed_number }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            @endif

                            @if ($selectedRoom && $this->bookingSummary)
                                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                                    <flux:heading size="sm">Booking Summary</flux:heading>

                                    <div class="mt-4 space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Room Rate</span>
                                            <span class="font-medium">
                                                {{ number_format($this->bookingSummary['room_rate'], 2) }}
                                            </span>
                                        </div>

                                        @foreach ($this->bookingSummary['mandatory_fees'] as $fee)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">{{ $fee['name'] }}</span>
                                                <span class="font-medium">
                                                    {{ number_format($fee['amount'], 2) }}
                                                </span>
                                            </div>
                                        @endforeach

                                        <div class="border-t border-gray-200 pt-3 flex justify-between font-bold">
                                            <span>Total Amount</span>
                                            <span>{{ number_format($this->bookingSummary['total_amount'], 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- STEP 4 --}}
                @if ($step === 4)
                    <div>
                        <flux:heading size="md">Review and Confirm</flux:heading>
                        <flux:subheading>Review your booking details before confirming</flux:subheading>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:card>
                                <flux:heading size="sm">Booking Details</flux:heading>
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <flux:badge>Booking Type</flux:badge>
                                        <div>{{ ucfirst(str_replace('_', ' ', $bookingType)) }}</div>
                                    </div>

                                    @if ($bookingType === 'academic')
                                        <div class="flex justify-between">
                                            <flux:badge>Academic Year</flux:badge>
                                            <div>{{ $academicYear }}</div>
                                        </div>
                                    @elseif ($bookingType === 'semester')
                                        <div class="flex justify-between">
                                            <flux:badge>Academic Year</flux:badge>
                                            <div>{{ $academicYear }}</div>
                                        </div>
                                        <div class="flex justify-between">
                                            <flux:badge>Semester</flux:badge>
                                            <div>{{ $semester }}</div>
                                        </div>
                                    @endif

                                    <div class="flex justify-between">
                                        <flux:badge>Check-in Date</flux:badge>
                                        <div>{{ $checkInDate }}</div>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:badge>Check-out Date</flux:badge>
                                        <div>{{ $checkOutDate }}</div>
                                    </div>
                                </div>
                            </flux:card>

                            <flux:card>
                                <flux:heading size="sm">Guest Information</flux:heading>
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <flux:badge>Name</flux:badge>
                                        <div>{{ $firstName }} {{ $lastName }}</div>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:badge>Gender</flux:badge>
                                        <div>{{ ucfirst($gender) }}</div>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:badge>Email</flux:badge>
                                        <div>{{ $email }}</div>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:badge>Phone</flux:badge>
                                        <div>{{ $phone }}</div>
                                    </div>
                                    @if ($studentId)
                                        <div class="flex justify-between">
                                            <flux:badge>Student ID</flux:badge>
                                            <div>{{ $studentId }}</div>
                                        </div>
                                    @endif
                                    @if ($institution)
                                        <div class="flex justify-between">
                                            <flux:badge>Institution</flux:badge>
                                            <div>{{ $institution }}</div>
                                        </div>
                                    @endif
                                </div>
                            </flux:card>

                            <flux:card class="md:col-span-2">
                                <flux:heading size="sm">Room Selection</flux:heading>
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <flux:badge>Room</flux:badge>
                                        <div>
                                            Room {{ \Modules\Hostels\Models\Room::find($selectedRoom)->room_number ?? 'N/A' }}
                                        </div>
                                    </div>
                                    @if ($selectedBed)
                                        <div class="flex justify-between">
                                            <flux:badge>Bed</flux:badge>
                                            <div>
                                                Bed {{ \Modules\Hostels\Models\Bed::find($selectedBed)->bed_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </flux:card>

                            @if ($this->bookingSummary)
                                <flux:card class="md:col-span-2">
                                    <flux:heading size="sm">Payment Summary</flux:heading>
                                    <div class="mt-4 space-y-2">
                                        <div class="flex justify-between">
                                            <flux:badge>Room Rate</flux:badge>
                                            <div>{{ number_format($this->bookingSummary['room_rate'], 2) }}</div>
                                        </div>

                                        @foreach ($this->bookingSummary['mandatory_fees'] as $fee)
                                            <div class="flex justify-between">
                                                <flux:badge>{{ $fee['name'] }}</flux:badge>
                                                <div>{{ number_format($fee['amount'], 2) }}</div>
                                            </div>
                                        @endforeach

                                        <div class="border-t border-gray-200 pt-2 flex justify-between font-bold">
                                            <flux:badge>Total Amount</flux:badge>
                                            <div>{{ number_format($this->bookingSummary['total_amount'], 2) }}</div>
                                        </div>
                                    </div>
                                </flux:card>
                            @endif
                        </div>

                        <div class="mt-6">
                            <div class="flex items-start gap-2">
                                <flux:checkbox wire:model="acceptTerms" required />
                                <div class="text-sm text-gray-700">
                                    I agree to the
                                    <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms and Conditions</a>
                                    and
                                    <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                                </div>
                            </div>
                            @error('acceptTerms')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- STEP 5 --}}
                @if ($step === 5)
                    <div>
                        <flux:heading size="md">Payment</flux:heading>
                        <flux:subheading>Complete your booking with secure payment</flux:subheading>

                        <div class="mt-6">
                            <flux:card>
                                <flux:heading size="sm">Payment Summary</flux:heading>
                                <div class="mt-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Amount</span>
                                        <span class="font-medium">
                                            {{ number_format($this->bookingSummary['total_amount'], 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Amount Due</span>
                                        <span class="font-medium">
                                            {{ number_format($this->bookingSummary['total_amount'], 2) }}
                                        </span>
                                    </div>
                                </div>
                            </flux:card>

                            <div class="mt-6">
                                <flux:heading size="sm">Payment Methods</flux:heading>
                                <div class="mt-4 space-y-4">
                                    @foreach($availablePaymentMethods as $method)
                                        <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 cursor-pointer">
                                            <input type="radio" id="payment-{{ $method['code'] }}" 
                                                   name="payment_method" value="{{ $method['code'] }}" 
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1" 
                                                   wire:model="paymentMethod" 
                                                   {{ $loop->first ? 'checked' : '' }}>
                                            <div class="ml-3 flex-1">
                                                <label for="payment-{{ $method['code'] }}" class="block text-sm font-medium text-gray-700">
                                                    {{ $method['name'] }}
                                                    @if($method['payment_mode'] === 'offline')
                                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Offline Payment
                                                        </span>
                                                    @endif
                                                </label>
                                                
                                                {{-- Show offline payment instructions when available --}}
                                                @if($method['payment_mode'] === 'offline' && !empty($method['offline_payment_instruction']))
                                                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
                                                        <div class="flex items-center">
                                                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="font-medium">
                                                                Payment Instructions
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="mt-2 text-blue-700 whitespace-pre-line">
                                                            {!! $method['offline_payment_instruction'] !!}
                                                        </div>
                                                        
                                                        <div class="mt-3 p-2 bg-blue-100 rounded text-blue-900 text-xs">
                                                            💡 Your booking will be confirmed once payment is verified by our team.
                                                        </div>
                                                    </div>
                                                @elseif($method['payment_mode'] === 'offline')
                                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-800">
                                                        <div class="flex items-center">
                                                            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                            </svg>
                                                            <span class="font-medium">
                                                                Payment Instructions Needed
                                                            </span>
                                                        </div>
                                                        <p class="mt-1 text-yellow-700">
                                                            Please contact the administrator to set up payment instructions for this method.
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Show message if no payment methods available --}}
                                @if(empty($availablePaymentMethods))
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mt-4">
                                        <p class="text-yellow-800 text-sm">No payment methods available. Please contact the system administrator.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Payment button removed from here - using navigation button instead --}}
                        </div>
                    </div>
                @endif

                {{-- Navigation Buttons --}}
                <div class="mt-8 flex justify-between">
                    @if ($step > 1)
                        <flux:button wire:click="previousStep" variant="ghost">
                            Back
                        </flux:button>
                    @else
                        <div></div>
                    @endif

                    @if ($step < 4)
                        <flux:button wire:click="nextStep" variant="primary">
                            Continue
                        </flux:button>
                    @elseif ($step === 4)
                        <flux:button wire:click="nextStep" variant="primary" wire:loading.attr="disabled">
                            Continue to Payment
                        </flux:button>
                    @else
                        <flux:button wire:click="processPayment" variant="primary" wire:loading.attr="disabled">
                            Complete Payment
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
