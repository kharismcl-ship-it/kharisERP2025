<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Core\Services\PaymentOrchestrationService;
use Modules\Hostels\Events\HostelOccupantOtpRequested;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelCharge;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Services\PricingService;
use Modules\PaymentsChannel\Facades\Payment;

class BookingWizard extends Component
{
    use WithFileUploads;

    public Hostel $hostel;

    public $step = 1;

    // Step 1: Booking type and dates
    public $bookingType = 'academic';

    public $academicYear;

    public $semester = '1';

    public $checkInDate;

    public $checkOutDate;

    // Step 2: Guest information
    public $hostelOccupantType = 'new'; // new or existing

    public $existingHostelOccupantId;

    public $firstName;

    public $lastName;

    public $email;

    public $phone;

    public $studentId;

    public $institution;

    public $address;

    public $emergencyContactName;

    public $emergencyContactPhone;

    public $gender = 'male'; // Add gender field with default value

    public $dateOfBirth;

    public $idCardFront;

    public $idCardBack;

    public $profilePhoto;

    // Returning guest verification (unauthenticated)
    public $lookupIdentifier; // email or phone

    public $verificationOtp;

    public $otpSent = false;

    public $otpDestination;

    public $otpHostelOccupantCandidateId;

    // Step 3: Room selection
    public $selectedRoom;

    public $selectedBed;

    // Pre-selected room ID
    public $preSelectedRoomId = null;

    // Step 4: Review and confirmation
    public $acceptTerms = false;

    // Step 5: Payment
    public $paymentMethod = 'momo';

    public $availablePaymentMethods = [];

    public $paymentMethodOptions = [];

    protected $rules = [
        // Step 1
        'bookingType' => 'required|in:academic,semester,short_stay',
        'academicYear' => 'nullable|required_if:bookingType,academic,semester',
        'semester' => 'nullable|required_if:bookingType,semester',
        'checkInDate' => 'required|date',
        'checkOutDate' => 'required|date|after:checkInDate',

        // Step 2
        'hostelOccupantType' => 'required|in:new,existing',
        'existingHostelOccupantId' => 'nullable|required_if:hostelOccupantType,existing',
        'lookupIdentifier' => 'nullable|string|max:255',
        'verificationOtp' => 'nullable|string|max:10',
        'firstName' => 'required_if:hostelOccupantType,new|string|max:255',
        'lastName' => 'required_if:hostelOccupantType,new|string|max:255',
        'email' => 'required_if:hostelOccupantType,new|email|max:255',
        'phone' => 'required_if:hostelOccupantType,new|string|max:20',
        'studentId' => 'nullable|string|max:50',
        'institution' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'emergencyContactName' => 'nullable|string|max:255',
        'emergencyContactPhone' => 'nullable|string|max:20',
        'gender' => 'required_if:hostelOccupantType,new|in:male,female,other',
        'idCardFront' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'idCardBack' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        // Step 3
        'selectedRoom' => 'required',

        // Step 4
        'acceptTerms' => 'accepted',

        // Step 5 - Validation will be handled dynamically in validationRules() method
        'paymentMethod' => 'required',
    ];

    protected function validationRules(): array
    {
        $rules = $this->rules;
        if ($this->hostelOccupantType === 'existing') {
            $rules['existingHostelOccupantId'] = 'required';
        } else {
            $rules['firstName'] = 'required|string|max:255';
            $rules['lastName'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
            $rules['phone'] = 'required|string|max:20';
            $rules['gender'] = 'required|in:male,female,other';
            // $rules['idCardFront'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
            // $rules['profilePhoto'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        }
        if ($this->bookingType === 'academic' || $this->bookingType === 'semester') {
            $rules['academicYear'] = 'required|string';
        }
        if ($this->bookingType === 'semester') {
            $rules['semester'] = 'required|in:1,2';
        }
        $rules['selectedRoom'] = 'required';
        $rules['checkInDate'] = 'required|date';
        $rules['checkOutDate'] = 'required|date|after:checkInDate';
        $rules['acceptTerms'] = 'accepted';

        // Add payment method validation for step 5
        if ($this->step === 5) {
            $rules['paymentMethod'] = ['required', Rule::in($this->paymentMethodOptions)];
        }

        return $rules;
    }

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
        $this->academicYear = date('Y').'/'.(date('Y') + 1);
        $this->checkInDate = date('Y-m-d');

        if (Auth::guard('hostel_occupant')->check()) {
            $hostelOccupantUser = Auth::guard('hostel_occupant')->user();
            $hostelOccupant = $hostelOccupantUser->hostelOccupant;

            if ($hostelOccupant) {
                $this->hostelOccupantType = 'existing';
                $this->existingHostelOccupantId = $hostelOccupant->id;
                $this->firstName = $hostelOccupant->first_name;
                $this->lastName = $hostelOccupant->last_name;
                $this->email = $hostelOccupant->email;
                $this->phone = $hostelOccupant->phone;
                $this->gender = $hostelOccupant->gender;
                $this->dateOfBirth = $hostelOccupant->date_of_birth;
                $this->address = $hostelOccupant->address;

            }
        }
        $this->checkOutDate = date('Y-m-d', strtotime('+1 year'));

        // Check if a room was pre-selected
        $roomId = request()->query('room');
        if ($roomId) {
            $room = Room::where('id', $roomId)
                ->where('hostel_id', $this->hostel->id)
                ->where('status', 'available')
                ->first();

            if ($room) {
                $this->preSelectedRoomId = $room->id;
                // Store the pre-selected room but don't skip steps
                $this->selectedRoom = $room->id;
            }
        }

        // Load available payment methods dynamically
        $companyId = $this->hostel->company_id ?? null;
        $this->availablePaymentMethods = Payment::getAvailablePaymentMethods($companyId);

        // Build validation options from dynamic methods
        $this->paymentMethodOptions = collect($this->availablePaymentMethods)
            ->pluck('code')
            ->toArray();

        // Set default payment method to first available method if current default doesn't exist
        if (! empty($this->paymentMethodOptions) && ! in_array($this->paymentMethod, $this->paymentMethodOptions)) {
            $this->paymentMethod = $this->paymentMethodOptions[0];
        }
    }

    public function getRoomsProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)
            ->where('status', 'available')
            ->get();
    }

    public function getBedsProperty()
    {
        if (! $this->selectedRoom) {
            return collect();
        }

        return Bed::where('room_id', $this->selectedRoom)
            ->where('status', 'available')
            ->get();
    }

    public function getBookingSummaryProperty()
    {
        if (! $this->selectedRoom) {
            return null;
        }

        $room = Room::find($this->selectedRoom);
        // Get the rate based on the booking type
        $roomRate = $room->getRateForBillingCycle($this->bookingType);
        // Convert BigDecimal to float if necessary
        $roomRate = is_object($roomRate) && method_exists($roomRate, 'toFloat') ? $roomRate->toFloat() : (float) $roomRate;
        $totalAmount = $roomRate;

        // Map booking type to fee billing cycles
        $feeCycle = match ($this->bookingType) {
            'academic' => 'per_year',
            'semester' => 'per_semester',
            'short_stay' => 'per_night',
            default => null,
        };

        // Build a unified list of summary items (fees + charges)
        $summaryItems = [];

        // Add mandatory fee types based on booking type, include one-time fees
        $mandatoryFees = FeeType::where('hostel_id', $this->hostel->id)
            ->where('is_mandatory', true)
            ->where('is_active', true)
            ->when($feeCycle, function ($q) use ($feeCycle) {
                $q->whereIn('billing_cycle', [$feeCycle, 'one_time']);
            })
            ->get();

        foreach ($mandatoryFees as $fee) {
            $amount = $fee->default_amount;
            // Convert BigDecimal to float if necessary
            $amount = is_object($amount) && method_exists($amount, 'toFloat') ? $amount->toFloat() : (float) $amount;
            $summaryItems[] = [
                'name' => $fee->name,
                'amount' => $amount,
                'type' => 'mandatory_fee',
            ];
            $totalAmount += $amount;
        }

        // Add active recurring hostel charges
        $hostelCharges = HostelCharge::where('hostel_id', $this->hostel->id)
            ->where('is_active', true)
            ->where('charge_type', 'recurring')
            ->get();

        foreach ($hostelCharges as $charge) {
            $amount = $charge->amount;
            // Convert BigDecimal to float if necessary
            $amount = is_object($amount) && method_exists($amount, 'toFloat') ? $amount->toFloat() : (float) $amount;
            $summaryItems[] = [
                'name' => $charge->name,
                'amount' => $amount,
                'type' => 'hostel_charge',
            ];
            $totalAmount += $amount;
        }

        // For short stay, multiply by number of nights and apply dynamic pricing
        if ($this->bookingType === 'short_stay') {
            $checkIn = new \DateTime($this->checkInDate);
            $checkOut = new \DateTime($this->checkOutDate);
            $interval = $checkIn->diff($checkOut);
            $numberOfNights = $interval->days ?: 1;

            // Apply dynamic pricing for short-stay bookings
            $pricingService = new PricingService;
            $checkInCarbon = \Illuminate\Support\Carbon::parse($this->checkInDate);
            $checkOutCarbon = \Illuminate\Support\Carbon::parse($this->checkOutDate);

            // Calculate base price for dynamic pricing (room rate × number of nights)
            $basePriceForDynamicPricing = $roomRate * $numberOfNights;

            $dynamicPricing = $pricingService->calculateDynamicPrice(
                $this->hostel,
                $checkInCarbon,
                $checkOutCarbon,
                $numberOfNights,
                $basePriceForDynamicPricing
            );

            $totalAmount = $dynamicPricing['final_price'];
        }

        return [
            'room_rate' => $roomRate,
            'mandatory_fees' => $summaryItems,
            'total_amount' => $totalAmount,
        ];
    }

    // Get gender options based on selected room's policy
    public function getGenderOptionsProperty()
    {
        if (! $this->selectedRoom) {
            return [
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other',
            ];
        }

        $room = Room::find($this->selectedRoom);

        if ($room && $room->gender_policy !== 'mixed') {
            // If room has a specific gender policy, only allow that gender
            return [
                $room->gender_policy => ucfirst($room->gender_policy),
            ];
        }

        // For mixed gender rooms or when no room is selected, allow all genders
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
    }

    public function getExistingHostelOccupantsProperty()
    {
        if (! Auth::guard('hostel_occupant')->check()) {
            return collect();
        }

        return HostelOccupant::where('hostel_id', $this->hostel->id)
            ->whereIn('status', ['active', 'prospect'])
            ->limit(50)
            ->get();
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            // Validate step 1 fields individually
            $this->validateOnly('bookingType');
            $this->validateOnly('academicYear');
            $this->validateOnly('semester');
            $this->validateOnly('checkInDate');
            $this->validateOnly('checkOutDate');

            // Check booking limit for academic bookings
            if (in_array($this->bookingType, ['academic', 'semester'])) {
                // Check if hostel occupant already exists
                $existingHostelOccupant = HostelOccupant::where('email', $this->email)
                    ->orWhere('phone', $this->phone)
                    ->first();

                if ($existingHostelOccupant) {
                    // Check if existing hostel occupant has reached booking limit
                    if ($existingHostelOccupant->hasReachedSemesterBookingLimit($this->academicYear, $this->semester)) {
                        $this->addError('booking', 'You have reached the maximum booking limit (3) for this semester. Please contact support if you need further assistance.');

                        return;
                    }
                }
            }
        } elseif ($this->step === 2) {
            // Validate step 2 fields individually
            $this->validateOnly('hostelOccupantType');
            if ($this->hostelOccupantType === 'existing') {
                $this->validateOnly('existingHostelOccupantId');
            } else {
                $this->validateOnly('firstName');
                $this->validateOnly('lastName');
                $this->validateOnly('email');
                $this->validateOnly('phone');
                $this->validateOnly('studentId');
                $this->validateOnly('institution');
                $this->validateOnly('address');
                $this->validateOnly('emergencyContactName');
                $this->validateOnly('emergencyContactPhone');
                $this->validateOnly('gender');

                // Validate file uploads if new hostel occupant
                $this->validateOnly('idCardFront');
                $this->validateOnly('idCardBack');
                $this->validateOnly('profilePhoto');
            }

            // Set gender based on room policy if needed
            if ($this->preSelectedRoomId && ! $this->gender) {
                $room = Room::find($this->preSelectedRoomId);
                if ($room && $room->gender_policy !== 'mixed') {
                    $this->gender = $room->gender_policy;
                }
            }
        } elseif ($this->step === 3) {
            $this->validateOnly('selectedRoom');
        } elseif ($this->step === 4) {
            // Validate step 4 fields (confirmation)
            $this->validateOnly('acceptTerms');
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function updatedSelectedRoom($roomId)
    {
        $this->selectedBed = null;

        // Update gender based on room policy
        if ($roomId) {
            $room = Room::find($roomId);
            if ($room && $room->gender_policy !== 'mixed') {
                $this->gender = $room->gender_policy;
            }
        }
    }

    public function updatedBookingType()
    {
        // Refresh the booking summary when booking type changes
        unset($this->bookingSummary);
    }

    public function updatedCheckInDate()
    {
        // Refresh the booking summary when dates change (important for short stay calculations)
        unset($this->bookingSummary);
    }

    public function updatedCheckOutDate()
    {
        // Refresh the booking summary when dates change (important for short stay calculations)
        unset($this->bookingSummary);
    }

    public function updatedExistingHostelOccupantId($hostelOccupantId)
    {
        if ($hostelOccupantId && $this->hostelOccupantType === 'existing') {
            $hostelOccupant = HostelOccupant::find($hostelOccupantId);
            if ($hostelOccupant) {
                if ($hostelOccupant->status === 'blocked') {
                    $this->addError('existingHostelOccupantId', 'This hostel occupant account is currently blocked. Please contact support.');
                    $this->reset(['existingHostelOccupantId', 'firstName', 'lastName', 'email', 'phone', 'studentId', 'institution', 'address', 'emergencyContactName', 'emergencyContactPhone', 'gender']);

                    return;
                }
                // Populate form fields with hostel occupant data
                $this->firstName = $hostelOccupant->first_name;
                $this->lastName = $hostelOccupant->last_name;
                $this->gender = $hostelOccupant->gender;
                $this->email = $hostelOccupant->email;
                $this->phone = $hostelOccupant->phone;
                $this->studentId = $hostelOccupant->student_id;
                $this->institution = $hostelOccupant->institution;
                $this->address = $hostelOccupant->address;
                $this->emergencyContactName = $hostelOccupant->emergency_contact_name;
                $this->emergencyContactPhone = $hostelOccupant->emergency_contact_phone;
            }
        }
    }

    public function updatedHostelOccupantType($value)
    {
        if ($value === 'existing') {
            $this->resetValidation(['firstName', 'lastName', 'email', 'phone', 'gender']);
        } else {
            $this->reset(['existingHostelOccupantId', 'lookupIdentifier', 'verificationOtp', 'otpSent', 'otpDestination', 'otpHostelOccupantCandidateId']);
            $this->resetValidation(['existingHostelOccupantId', 'lookupIdentifier', 'verificationOtp']);
        }
    }

    public function removeIdCardFront()
    {
        $this->idCardFront = null;
        $this->resetValidation(['idCardFront']);
    }

    public function removeIdCardBack()
    {
        $this->idCardBack = null;
        $this->resetValidation(['idCardBack']);
    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->resetValidation(['profilePhoto']);
    }

    public function sendOtpForExisting()
    {
        $this->resetErrorBag(['lookupIdentifier']);
        $identifier = trim((string) $this->lookupIdentifier);
        if ($identifier === '') {
            $this->addError('lookupIdentifier', 'Enter the email or phone linked to your account.');

            return;
        }
        $hostelOccupant = HostelOccupant::where('hostel_id', $this->hostel->id)
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)->orWhere('phone', $identifier);
            })->first();
        if (! $hostelOccupant) {
            $this->addError('lookupIdentifier', 'We could not find an account with that email or phone.');

            return;
        }
        $code = (string) random_int(100000, 999999);
        // Store in session for simplicity; production can use cache/DB
        session(['booking_otp' => [
            'code' => $code,
            'hostel_occupant_id' => $hostelOccupant->id,
            'expires_at' => now()->addMinutes(10),
        ]]);
        $this->otpSent = true;
        $this->otpHostelOccupantCandidateId = $hostelOccupant->id;
        $this->otpDestination = $hostelOccupant->phone ?: $hostelOccupant->email ?: null;

        event(new HostelOccupantOtpRequested($hostelOccupant, $code, $this->hostel));
    }

    public function verifyOtpForExisting()
    {
        $this->resetErrorBag(['verificationOtp']);
        $payload = session('booking_otp');
        if (! $payload) {
            $this->addError('verificationOtp', 'No verification code requested yet.');

            return;
        }
        if (now()->gt($payload['expires_at'])) {
            $this->addError('verificationOtp', 'The code has expired. Request a new one.');

            return;
        }
        if (trim((string) $this->verificationOtp) !== (string) $payload['code']) {
            $this->addError('verificationOtp', 'Invalid verification code.');

            return;
        }
        $hostelOccupantId = $payload['hostel_occupant_id'];
        $this->existingHostelOccupantId = $hostelOccupantId;
        $this->hostelOccupantType = 'existing';
        // Populate fields
        $this->updatedExistingHostelOccupantId($hostelOccupantId);
        // Clear OTP state
        session()->forget('booking_otp');
        $this->otpSent = false;
        $this->verificationOtp = null;
    }

    public function createBooking()
    {
        Log::info('hostels.booking_wizard.create.start', [
            'hostel_id' => $this->hostel->id,
            'step' => $this->step,
            'hostel_occupant_type' => $this->hostelOccupantType,
            'selected_room' => $this->selectedRoom,
            'selected_bed' => $this->selectedBed,
            'booking_type' => $this->bookingType,
            'accept_terms' => (bool) $this->acceptTerms,
        ]);

        $this->validate($this->validationRules());
        Log::info('hostels.booking_wizard.create.validated');

        $booking = DB::transaction(function () {
            try {
                $hostelOccupant = null;
                $bed = null;
                $existingBooking = null;

                // Handle hostel occupant based on hostel occupant type - only for existing hostel occupants
                if ($this->hostelOccupantType === 'existing' && $this->existingHostelOccupantId) {
                    // Use existing hostel occupant
                    $hostelOccupant = HostelOccupant::find($this->existingHostelOccupantId);
                    $hostelOccupantUserId = $hostelOccupant->hostelOccupantUser->id ?? null;

                    // Check booking limit for academic bookings for existing hostel occupants
                    if (in_array($this->bookingType, ['academic', 'semester']) && $hostelOccupant) {
                        if ($hostelOccupant->hasReachedSemesterBookingLimit($this->academicYear, $this->semester)) {
                            throw new Exception('You have reached the maximum booking limit (3) for this semester. Please contact support if you need further assistance.');
                        }
                    }
                } else {
                    $hostelOccupantUserId = null;

                    // For new hostel occupants, check if they already exist in the system
                    $existingHostelOccupant = HostelOccupant::where('email', $this->email)
                        ->orWhere('phone', $this->phone)
                        ->first();

                    if ($existingHostelOccupant) {
                        throw new Exception('An account already exists with this email or phone. Please select "Existing Hostel Occupant" and verify your account.');
                    }

                    // For new hostel occupants, we don't create a hostel occupant record yet
                    // Hostel occupant will be created later during admin check-in via Booking::checkIn()
                    $hostelOccupant = null;

                    Log::info('hostels.booking_wizard.new_hostel_occupant_detected', [
                        'email' => $this->email,
                        'phone' => $this->phone,
                    ]);
                }

                Log::info('hostels.booking_wizard.hostel_occupant', [
                    'existing_hostel_occupant_id' => $this->existingHostelOccupantId,
                    'hostel_occupant_id' => $hostelOccupant->id ?? null,
                    'hostel_occupant_user_id' => $hostelOccupantUserId,
                ]);

                // If a bed is selected, check its availability with row locking
                if ($this->selectedBed) {
                    // Lock the bed record for update to prevent concurrent bookings
                    $bed = Bed::where('id', $this->selectedBed)
                        ->where('status', 'available')
                        ->lockForUpdate()
                        ->first();

                    // If bed is not available or doesn't exist, throw an exception
                    if (! $bed) {
                        throw new Exception('Selected bed is no longer available. Please select another bed.');
                    }

                    // Check if there are any active bookings for this bed using the new scope
                    $existingBooking = Booking::where('bed_id', $this->selectedBed)
                        ->active()
                        ->lockForUpdate()
                        ->first();

                    if ($existingBooking) {
                        throw new Exception('This bed has already been booked by another user. Please select another bed.');
                    }
                }
                Log::info('hostels.booking_wizard.bed_check', [
                    'selected_bed' => $this->selectedBed,
                    'bed_locked' => (bool) $bed,
                    'existing_booking_id' => $existingBooking->id ?? null,
                ]);

                // Handle file uploads for booking if hostel occupant is new and files were uploaded
                $idCardFrontPath = null;
                $idCardBackPath = null;
                $profilePhotoPath = null;

                if ($this->hostelOccupantType === 'new') {
                    if ($this->idCardFront) {
                        $idCardFrontPath = $this->idCardFront->store('hostel-occupant-id-cards', 'public');
                    }

                    if ($this->idCardBack) {
                        $idCardBackPath = $this->idCardBack->store('hostel-occupant-id-cards', 'public');
                    }

                    if ($this->profilePhoto) {
                        $profilePhotoPath = $this->profilePhoto->store('hostel-occupant-profiles', 'public');
                    }
                }

                // Create booking
                $booking = Booking::create([
                    'hostel_id' => $this->hostel->id,
                    'room_id' => $this->selectedRoom,
                    'bed_id' => $this->selectedBed,
                    'hostel_occupant_id' => $hostelOccupant->id ?? null,
                    'hostel_occupant_user_id' => $hostelOccupantUserId,
                    'booking_reference' => 'BK-'.time().'-'.rand(1000, 9999),
                    'booking_type' => $this->bookingType,
                    'academic_year' => $this->academicYear ?? null,
                    'semester' => $this->semester ?? null,
                    'check_in_date' => $this->checkInDate,
                    'check_out_date' => $this->checkOutDate,
                    'status' => 'pending_approval',
                    'total_amount' => (float) ($this->bookingSummary['total_amount'] ?? 0),
                    'amount_paid' => 0.0,
                    'balance_amount' => (float) ($this->bookingSummary['total_amount'] ?? 0),
                    'payment_status' => 'unpaid',
                    'channel' => 'online',
                    'accepted_terms_at' => now(),
                    // Guest information for hostel occupant creation (only for new hostel occupants)
                    'guest_first_name' => $this->hostelOccupantType === 'new' ? $this->firstName : null,
                    'guest_last_name' => $this->hostelOccupantType === 'new' ? $this->lastName : null,
                    'guest_full_name' => $this->hostelOccupantType === 'new' ? ($this->firstName.' '.$this->lastName) : null,
                    'guest_gender' => $this->hostelOccupantType === 'new' ? $this->gender : null,
                    'guest_phone' => $this->hostelOccupantType === 'new' ? $this->phone : null,
                    'guest_email' => $this->hostelOccupantType === 'new' ? $this->email : null,
                    'guest_student_id' => $this->hostelOccupantType === 'new' ? $this->studentId : null,
                    'guest_institution' => $this->hostelOccupantType === 'new' ? $this->institution : null,
                    'guest_address' => $this->hostelOccupantType === 'new' ? $this->address : null,
                    'guest_emergency_contact_name' => $this->hostelOccupantType === 'new' ? $this->emergencyContactName : null,
                    'guest_emergency_contact_phone' => $this->hostelOccupantType === 'new' ? $this->emergencyContactPhone : null,
                    'id_card_front_photo' => $idCardFrontPath,
                    'id_card_back_photo' => $idCardBackPath,
                    'profile_photo' => $profilePhotoPath,
                ]);
                Log::info('hostels.booking_wizard.booking_created', ['booking_id' => $booking->id]);

                // Update bed status if selected
                if ($this->selectedBed) {
                    $bed = Bed::find($this->selectedBed);
                    $bed->update(['status' => 'reserved_pending_approval']);
                }

                // Set hold expiry for the booking
                $booking->setHoldExpiry();

                return $booking;
            } catch (Exception $e) {
                Log::error('hostels.booking_wizard.error', ['message' => $e->getMessage()]);
                // Handle the exception and show an error message
                $this->addError('booking', $e->getMessage());

                return null;
            }
        });

        return $booking;
    }

    public function processPayment()
    {
        // Debug: check if method is being called
        Log::debug('processPayment method called - START', ['timestamp' => now(), 'step' => $this->step]);

        try {
            $this->validateOnly('paymentMethod');

            Log::info('hostels.booking_wizard.payment.start', [
                'payment_method' => $this->paymentMethod,
                'step' => $this->step,
                'hostel_id' => $this->hostel->id,
                'selected_room' => $this->selectedRoom,
                'selected_bed' => $this->selectedBed,
            ]);
            // Create the booking first (this will set status to pending_approval)
            $booking = $this->createBooking();

            if (! $booking) {
                throw new Exception('Failed to create booking. Please try again.');
            }

            Log::info('hostels.booking_wizard.payment.booking_created', [
                'booking_id' => $booking->id,
                'payment_method' => $this->paymentMethod,
            ]);

            // Process payment based on selected method
            $paymentResult = $this->processPaymentGateway($booking);

            if ($paymentResult['success']) {
                // Update booking with payment intent reference (payment is not complete yet)
                $booking->update([
                    'payment_status' => 'unpaid',
                    'payment_method' => $this->paymentMethod,
                    'payment_reference' => $paymentResult['reference'],
                ]);

                Log::info('hostels.booking_wizard.payment.intent_created', [
                    'booking_id' => $booking->id,
                    'payment_reference' => $paymentResult['reference'],
                    'redirect_url' => $paymentResult['redirect_url'],
                ]);

                // Handle offline payments - show success message instead of redirecting
                if (empty($paymentResult['redirect_url'])) {
                    // This is an offline payment method (like bank transfer)
                    $paymentInstructions = '';

                    // Get the selected payment method details to find offline payment instructions
                    $selectedMethod = collect($this->availablePaymentMethods)
                        ->firstWhere('code', $this->paymentMethod);

                    if ($selectedMethod && ! empty($selectedMethod['offline_payment_instruction'])) {
                        $paymentInstructions = $selectedMethod['offline_payment_instruction'];
                    }

                    session()->flash('offline_payment_success', [
                        'booking_reference' => $booking->booking_reference,
                        'payment_method' => $this->getReadablePaymentMethodName($this->paymentMethod),
                        'message' => 'Your booking has been created successfully! Please complete your bank transfer payment. Your booking will be confirmed once the payment is verified by our team.',
                        'payment_instructions' => $paymentInstructions,
                    ]);

                    return redirect()->route('hostels.public.booking.confirmation', ['booking' => $booking->id]);
                }

                // Redirect to payment gateway for online payment processing
                return $this->redirect($paymentResult['redirect_url']);
            } else {
                throw new Exception($paymentResult['message'] ?? 'Payment processing failed. Please try again.');
            }

        } catch (Exception $e) {
            Log::error('hostels.booking_wizard.payment.error', [
                'message' => $e->getMessage(),
                'payment_method' => $this->paymentMethod,
            ]);

            $this->addError('payment', $e->getMessage());

            // Release the bed if payment fails and booking was created
            if (isset($booking) && $booking->bed_id) {
                try {
                    // Update bed status back to available
                    $bed = \Modules\Hostels\Models\Bed::find($booking->bed_id);
                    if ($bed && $bed->status === 'reserved_pending_approval') {
                        $bed->update(['status' => 'available']);
                        Log::info('hostels.booking_wizard.payment.bed_released', [
                            'booking_id' => $booking->id,
                            'bed_id' => $booking->bed_id,
                            'reason' => 'Payment failed',
                        ]);
                    }

                    // Update booking status to cancelled due to payment failure
                    $booking->update([
                        'status' => 'cancelled',
                        'notes' => 'Booking cancelled due to payment failure: '.$e->getMessage(),
                    ]);

                } catch (Exception $releaseError) {
                    Log::error('hostels.booking_wizard.payment.bed_release_failed', [
                        'booking_id' => $booking->id,
                        'bed_id' => $booking->bed_id,
                        'error' => $releaseError->getMessage(),
                    ]);
                }
            }

            // Show a more user-friendly error message
            session()->flash('error', 'Payment processing failed: '.$e->getMessage());
        }
    }

    protected function processPaymentGateway(Booking $booking)
    {
        try {
            // Use unified payment orchestration service instead of direct PaymentsChannel calls
            $paymentOrchestrationService = app(PaymentOrchestrationService::class);

            // Process booking payment through unified orchestration
            $paymentResult = $paymentOrchestrationService->processBookingPayment($booking, [
                'payment_method' => $this->paymentMethod,
                'return_url' => route('hostels.public.booking.payment-return', $booking),
                'callback_url' => route('hostels.public.booking.payment', $booking),
            ]);

            return [
                'success' => true,
                'reference' => $paymentResult['payment_intent']->reference ?? ($paymentResult['payment_intent']->id ?? uniqid()),
                'message' => 'Payment intent created successfully',
                'redirect_url' => $paymentResult['checkout_url'],
                'invoice_id' => $paymentResult['invoice']->id ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: '.$e->getMessage(),
            ];
        }
    }

    protected function getReadablePaymentMethodName(string $paymentMethodCode): string
    {
        $paymentMethodMap = [
            'manual_bank' => 'Manual Bank Transfer',
            'manual_cash' => 'Manual Cash Payment',
            'momo' => 'Mobile Money',
            'card' => 'Credit/Debit Card',
            'bank' => 'Online Bank Transfer',
            'flutterwave_online' => 'Flutterwave Online Payment',
            'paystack_online' => 'Paystack Online Payment',
        ];

        return $paymentMethodMap[$paymentMethodCode] ?? ucwords(str_replace('_', ' ', $paymentMethodCode));
    }

    public function render()
    {
        return view('hostels::livewire.public.booking-wizard', [
            'existingHostelOccupants' => $this->existingHostelOccupants,
        ])->layout('hostels::layouts.public');
    }
}
