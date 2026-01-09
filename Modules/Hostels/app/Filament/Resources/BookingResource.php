<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Modules\Hostels\Filament\Resources\BookingResource\Pages;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelCharge;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Services\PricingService;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $slug = 'bookings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking Details')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset room and bed when hostel changes
                                $set('room_id', null);
                                $set('bed_id', null);
                                $set('total_amount', null);
                            }),

                        Select::make('room_id')
                            ->label('Room')
                            ->options(function (Get $get): array {
                                $hostelId = $get('hostel_id');

                                if (! $hostelId) {
                                    return [];
                                }

                                return Room::where('hostel_id', $hostelId)
                                    ->pluck('room_number', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->live() // so bed list updates when room changes
                            ->disabled(fn (Get $get) => blank($get('hostel_id')))
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Reset bed when room changes
                                $set('bed_id', null);

                                // Recalculate total amount when room changes
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),

                        Select::make('bed_id')
                            ->label('Bed')
                            ->options(function (Get $get): array {
                                $roomId = $get('room_id');

                                if (! $roomId) {
                                    return [];
                                }

                                return Bed::where('room_id', $roomId)
                                    ->pluck('bed_number', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get) => blank($get('room_id')))
                            ->nullable(),

                        Radio::make('hostel_occupant_type')
                            ->label('Tenant Type')
                            ->options([
                                'new' => 'New Guest',
                                'existing' => 'Returning Guest',
                            ])
                            ->default('new')
                            ->live()
                            ->columnSpanFull(),

                        Select::make('hostel_occupant_id')
                            ->label('Select Tenant')
                            ->options([])
                            ->getSearchResultsUsing(function (string $search) {
                                return \Modules\Hostels\Models\HostelOccupant::where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('student_id', 'like', "%{$search}%")
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(function ($occupant) {
                                        return [
                                            $occupant->id => "{$occupant->first_name} {$occupant->last_name} (ID: {$occupant->id})",
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->visible(fn (Get $get) => $get('hostel_occupant_type') === 'existing')
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $occupant = \Modules\Hostels\Models\HostelOccupant::find($state);
                                    if ($occupant) {
                                        // Pre-fill guest information with occupant data
                                        $set('guest_first_name', $occupant->first_name);
                                        $set('guest_last_name', $occupant->last_name);
                                        $set('guest_other_names', $occupant->other_names);
                                        $set('guest_full_name', $occupant->full_name);
                                        $set('guest_gender', $occupant->gender);
                                        $set('guest_dob', $occupant->dob);
                                        $set('guest_phone', $occupant->phone);
                                        $set('guest_alt_phone', $occupant->alt_phone);
                                        $set('guest_email', $occupant->email);
                                        $set('guest_national_id_number', $occupant->national_id_number);
                                        $set('guest_student_id', $occupant->student_id);
                                        $set('guest_institution', $occupant->institution);
                                        $set('guest_guardian_name', $occupant->guardian_name);
                                        $set('guest_guardian_phone', $occupant->guardian_phone);
                                        $set('guest_guardian_email', $occupant->guardian_email);
                                        $set('guest_address', $occupant->address);
                                        $set('guest_emergency_contact_name', $occupant->emergency_contact_name);
                                        $set('guest_emergency_contact_phone', $occupant->emergency_contact_phone);
                                    }
                                }
                            }),

                        TextInput::make('booking_reference')
                            ->required()
                            ->unique(Booking::class, 'booking_reference', ignoreRecord: true),

                        Select::make('booking_type')
                            ->options([
                                'academic' => 'Academic Year',
                                'semester' => 'Semester',
                                'short_stay' => 'Short Stay',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Recalculate total amount when booking type changes
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),

                        Select::make('academic_year')
                            ->label('Academic Year')
                            ->options([
                                Carbon::now()->year => Carbon::now()->year,
                                Carbon::now()->year + 1 => Carbon::now()->year + 1,
                            ])
                            ->default(Carbon::now()->year)
                            ->required()
                            ->visible(fn (Get $get) => in_array($get('booking_type'), ['academic', 'semester']))
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Recalculate total amount when academic year changes
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),

                        Select::make('semester')
                            ->options([
                                '1' => 'Semester 1',
                                '2' => 'Semester 2',
                            ])
                            ->nullable()
                            ->visible(fn (Get $get) => $get('booking_type') === 'semester')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Recalculate total amount when semester changes
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),

                        TextInput::make('number_of_nights')
                            ->label('Number of Nights')
                            ->numeric()
                            ->visible(fn (Get $get) => $get('booking_type') === 'short_stay')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Recalculate total amount when number of nights changes
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),

                        DatePicker::make('check_in_date')
                            ->required(),

                        DatePicker::make('check_out_date')
                            ->required(),

                        DatePicker::make('expected_check_out_date')
                            ->label('Expected Check Out Date')
                            ->visible(fn (Get $get) => $get('booking_type') === 'per_night'),

                        DateTimePicker::make('actual_check_in_at')
                            ->nullable(),

                        DateTimePicker::make('actual_check_out_at')
                            ->nullable(),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->readOnly()
                            ->live()
                            ->afterStateHydrated(function (callable $get, callable $set) {
                                $totalAmount = self::calculateTotalAmount(
                                    $get('booking_type'),
                                    $get('hostel_id'),
                                    $get('room_id'),
                                    $get('number_of_nights'),
                                    $get('academic_year'),
                                    $get('semester')
                                );
                                $set('total_amount', $totalAmount);
                            }),
                    ]),

                Section::make('Cost Breakdown')
                    ->description('Breakdown of costs used to calculate total amount')
                    ->schema([
                        TextEntry::make('room_rate_breakdown')
                            ->label('Room Rate')
                            ->state(function (Get $get) {
                                $roomId = $get('room_id');
                                if (! $roomId) {
                                    return 'No room selected';
                                }

                                $room = Room::find($roomId);
                                if (! $room) {
                                    return 'Room not found';
                                }

                                // Use the appropriate rate based on booking type
                                $bookingType = $get('booking_type');
                                if (! $bookingType) {
                                    return 'Select a booking type to see room rate';
                                }
                                $rate = $room->getRateForBillingCycle($bookingType);
                                $rateLabel = match ($bookingType) {
                                    'academic' => 'Per Year',
                                    'semester' => 'Per Semester',
                                    'short_stay' => 'Per Night',
                                    default => 'Rate'
                                };

                                return new HtmlString("Room Base Rate ({$rateLabel}): GHS {$rate}");
                            })
                            ->visible(fn (Get $get) => $get('room_id')),

                        TextEntry::make('fees_breakdown')
                            ->label('Applicable Fees')
                            ->state(function (Get $get) {
                                $hostelId = $get('hostel_id');
                                $bookingType = $get('booking_type');
                                $roomId = $get('room_id');

                                if (! $hostelId || ! $bookingType || ! $roomId) {
                                    return 'Select hostel, room and booking type to see fees';
                                }

                                $room = Room::find($roomId);
                                if (! $room) {
                                    return 'Room not found';
                                }

                                if (in_array($bookingType, ['academic', 'semester'])) {
                                    $feeCycle = match ($bookingType) {
                                        'academic' => 'per_year',
                                        'semester' => 'per_semester',
                                        default => null,
                                    };
                                    $mandatoryFees = FeeType::where('hostel_id', $hostelId)
                                        ->where('is_mandatory', true)
                                        ->where('is_active', true)
                                        ->when($feeCycle, function ($q) use ($feeCycle) {
                                            $q->whereIn('billing_cycle', [$feeCycle, 'one_time']);
                                        })
                                        ->get();

                                    $hostelCharges = HostelCharge::where('hostel_id', $hostelId)
                                        ->where('is_active', true)
                                        ->where('charge_type', 'recurring')
                                        ->get();

                                    $items = [];

                                    if (! $mandatoryFees->isEmpty()) {
                                        foreach ($mandatoryFees as $fee) {
                                            $items[] = "{$fee->name}: GHS {$fee->default_amount}";
                                        }
                                    }

                                    if (! $hostelCharges->isEmpty()) {
                                        foreach ($hostelCharges as $charge) {
                                            $items[] = "{$charge->name}: GHS {$charge->amount}";
                                        }
                                    }

                                    if (empty($items)) {
                                        return 'No mandatory fees or hostel charges found for this billing cycle';
                                    }

                                    $feesHtml = '<ul>';
                                    foreach ($items as $item) {
                                        $feesHtml .= "<li>{$item}</li>";
                                    }
                                    $feesHtml .= '</ul>';

                                    return new HtmlString($feesHtml);
                                } elseif ($bookingType === 'short_stay') {
                                    $nightlyCharges = HostelCharge::where('hostel_id', $hostelId)
                                        ->where('charge_type', 'recurring')
                                        ->where('is_active', true)
                                        ->get();

                                    $rate = $room->getRateForBillingCycle('short_stay');
                                    $items = ["Room Nightly Rate: GHS {$rate}"];

                                    if (! $nightlyCharges->isEmpty()) {
                                        foreach ($nightlyCharges as $charge) {
                                            $items[] = "{$charge->name}: GHS {$charge->amount}";
                                            $rate += $charge->amount;
                                        }
                                    }

                                    $items[] = "<strong>Total Nightly Rate: GHS {$rate}</strong>";

                                    $feesHtml = '<ul>';
                                    foreach ($items as $item) {
                                        $feesHtml .= "<li>{$item}</li>";
                                    }
                                    $feesHtml .= '</ul>';

                                    return new HtmlString($feesHtml);
                                }

                                return 'Unknown booking type';
                            })
                            ->html()
                            ->visible(fn (Get $get) => $get('hostel_id') && $get('booking_type') && $get('room_id')),

                        TextEntry::make('calculation_explanation')
                            ->label('Calculation Details')
                            ->state(function (Get $get) {
                                $bookingType = $get('booking_type');
                                $numberOfNights = $get('number_of_nights');

                                if (in_array($bookingType, ['academic', 'semester'])) {
                                    $typeLabel = $bookingType === 'academic' ? 'Academic Year' : 'Semester';

                                    return "Total = Room Rate ({$typeLabel}) + Sum of Mandatory Fees ({$typeLabel}) + Sum of Hostel Charges";
                                } elseif ($bookingType === 'short_stay') {
                                    $nights = $numberOfNights ?? 1;

                                    return "Total = (Room Nightly Rate + Sum of Hostel Charges) × {$nights} nights";
                                }

                                return 'Select a booking type to see calculation details';
                            })
                            ->visible(fn (Get $get) => $get('booking_type')),
                    ])
                    ->visible(fn (Get $get) => $get('hostel_id') && $get('room_id')),

                Section::make('Payment Details')
                    ->schema([
                        TextInput::make('amount_paid')
                            ->numeric()
                            ->default(0),

                        TextInput::make('balance_amount')
                            ->numeric()
                            ->required(),

                        Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'partially_paid' => 'Partially Paid',
                                'paid' => 'Paid',
                                'overpaid' => 'Overpaid',
                            ])
                            ->required()
                            ->default('unpaid'),

                        Select::make('channel')
                            ->options([
                                'walk_in' => 'Walk In',
                                'online' => 'Online',
                                'agent' => 'Agent',
                            ])
                            ->required(),

                        Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->options([
                                'pending_approval' => 'Pending Approval',
                                'pending' => 'Pending',
                                'awaiting_payment' => 'Awaiting Payment',
                                'confirmed' => 'Confirmed',
                                'checked_in' => 'Checked In',
                                'checked_out' => 'Checked Out',
                                'no_show' => 'No Show',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending_approval'),
                    ]),

                Section::make('Guest Information')
                    ->description('Information used to create hostel occupant record upon check-in')
                    ->schema([
                        TextInput::make('guest_first_name')
                            ->label('First Name')
                            ->maxLength(255)
                            ->required()
                            ->visible(fn (Get $get) => $get('hostel_occupant_type') === 'new'),

                        TextInput::make('guest_last_name')
                            ->label('Last Name')
                            ->maxLength(255)
                            ->required()
                            ->visible(fn (Get $get) => $get('hostel_occupant_type') === 'new'),

                        TextInput::make('guest_other_names')
                            ->label('Other Names')
                            ->maxLength(255),

                        TextInput::make('guest_full_name')
                            ->label('Full Name')
                            ->maxLength(255),

                        Select::make('guest_gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),

                        DatePicker::make('guest_dob')
                            ->label('Date of Birth'),

                        TextInput::make('guest_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('guest_alt_phone')
                            ->label('Alternative Phone')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('guest_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('guest_national_id_number')
                            ->label('National ID Number')
                            ->maxLength(255),

                        TextInput::make('guest_student_id')
                            ->label('Student ID')
                            ->maxLength(255),

                        TextInput::make('guest_institution')
                            ->label('Institution')
                            ->maxLength(255),

                        TextInput::make('guest_guardian_name')
                            ->label('Guardian Name')
                            ->maxLength(255),

                        TextInput::make('guest_guardian_phone')
                            ->label('Guardian Phone')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('guest_guardian_email')
                            ->label('Guardian Email')
                            ->email()
                            ->maxLength(255),

                        Textarea::make('guest_address')
                            ->label('Address')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        TextInput::make('guest_emergency_contact_name')
                            ->label('Emergency Contact Name')
                            ->maxLength(255),

                        TextInput::make('guest_emergency_contact_phone')
                            ->label('Emergency Contact Phone')
                            ->tel()
                            ->maxLength(255),

                        FileUpload::make('id_card_front_photo')
                            ->label('ID Card Front Photo')
                            ->image()
                            ->directory('hostel-occupant-id-cards')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048),

                        FileUpload::make('id_card_back_photo')
                            ->label('ID Card Back Photo')
                            ->image()
                            ->directory('hostel-occupant-id-cards')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048),

                        FileUpload::make('profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->directory('hostel-occupant-profiles')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048),
                    ])
                    ->collapsible()
                    ->visible(fn (Get $get) => $get('hostel_occupant_type') === 'new' || ($get('hostel_occupant_type') === 'existing' && ! $get('hostel_occupant_id'))),
            ]);
    }

    public static function calculateTotalAmount($bookingType, $hostelId, $roomId, $numberOfNights = null, $academicYear = null, $semester = null)
    {
        if (! $hostelId || ! $roomId || ! $bookingType) {
            return 0;
        }

        $room = Room::find($roomId);
        if (! $room) {
            return 0;
        }

        if (in_array($bookingType, ['academic', 'semester'])) {
            // Map booking type to fee billing cycle and include one-time fees
            $feeCycle = match ($bookingType) {
                'academic' => 'per_year',
                'semester' => 'per_semester',
                default => null,
            };
            $mandatoryFees = FeeType::where('hostel_id', $hostelId)
                ->where('is_mandatory', true)
                ->where('is_active', true)
                ->when($feeCycle, function ($q) use ($feeCycle) {
                    $q->whereIn('billing_cycle', [$feeCycle, 'one_time']);
                })
                ->get();

            // Also get active hostel charges for academic bookings
            $hostelCharges = HostelCharge::where('hostel_id', $hostelId)
                ->where('is_active', true)
                ->where('charge_type', 'recurring')
                ->get();

            $roomRate = $room->getRateForBillingCycle($bookingType);
            $total = $roomRate;

            foreach ($mandatoryFees as $fee) {
                $total += $fee->default_amount;
            }

            foreach ($hostelCharges as $charge) {
                $total += $charge->amount;
            }

            return $total;
        } elseif ($bookingType === 'short_stay') {
            // For short stay bookings, use hostel charges or room rate
            $nightlyCharges = HostelCharge::where('hostel_id', $hostelId)
                ->where('charge_type', 'recurring')
                ->where('is_active', true)
                ->get();

            $baseRate = $room->getRateForBillingCycle('short_stay');

            // Add all recurring charges to the nightly rate
            foreach ($nightlyCharges as $charge) {
                $baseRate += $charge->amount;
            }

            $nights = $numberOfNights ?? 1;

            // Apply dynamic pricing for short-stay bookings
            $hostel = Hostel::find($hostelId);
            if ($hostel) {
                $pricingService = new PricingService;

                // Calculate check-in and check-out dates for dynamic pricing
                $checkInDate = now()->addDays(1); // Default to tomorrow
                $checkOutDate = $checkInDate->copy()->addDays($nights);

                $dynamicPricing = $pricingService->calculateDynamicPrice(
                    $hostel,
                    $checkInDate,
                    $checkOutDate,
                    $nights,
                    $baseRate
                );

                return $dynamicPricing['final_price'] * $nights;
            }

            return $baseRate * $nights;
        }

        return 0;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('booking_reference')
                    ->label('Booking Ref.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hostel.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('hostelOccupant.first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hostelOccupant.last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room.room_number')
                    ->label('Room No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bed.bed_number')
                    ->label('Bed No.')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                

                TextColumn::make('booking_type')
                    ->label('Booking Type')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'academic' => 'Academic Year',
                        'semester' => 'Semester',
                        'short_stay' => 'Short Stay',
                        default => ucfirst($state),
                    }),

                TextColumn::make('check_in_date')
                    ->label('Check In Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_out_date')
                    ->label('Check Out Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('balance_amount')
                    ->label('Balance Amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    }),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'warning',
                        'pending' => 'gray',
                        'awaiting_payment' => 'info',
                        'confirmed' => 'success',
                        'checked_in' => 'primary',
                        'checked_out' => 'secondary',
                        'no_show' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_approval' => 'Pending Approval',
                        'pending' => 'Pending',
                        'awaiting_payment' => 'Awaiting Payment',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'no_show' => 'No Show',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                //
                TernaryFilter::make('status')
                    ->label('Status')
                    ->trueLabel('Confirmed')
                    ->falseLabel('Pending Approval')
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', 'confirmed'),
                        false: fn (Builder $query) => $query->where('status', 'pending_approval'),
                    ),

            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                
                    \Filament\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Booking $booking) {
                            // Perform the approval process
                            $booking->update([
                                'status' => 'confirmed',
                                'payment_status' => 'pending_payment',
                                'approved_at' => now(),
                                'approved_by' => Auth::id(),
                            ]);

                            // Create hostel occupant from guest info
                            $booking->createHostelOccupantFromGuestInfo();

                            if ($booking->bed_id) {
                                $booking->bed->update(['status' => 'reserved']);
                            }

                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title('Booking Approved')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Booking $booking) => $booking->status === 'pending_approval'),
                    \Filament\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Booking $booking) {
                            // Perform the rejection process
                            $booking->update([
                                'status' => 'cancelled',
                                'cancelled_at' => now(),
                                'cancelled_by' => Auth::id(),
                            ]);

                            if ($booking->bed_id) {
                                $booking->bed->update(['status' => 'available']);
                            }

                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title('Booking Rejected')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Booking $booking) => $booking->status === 'pending_approval'),
                    \Filament\Actions\Action::make('check_in')
                        ->label('Check In')
                        ->icon('heroicon-o-arrow-right-start-on-rectangle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Booking $booking) {
                            // Perform the check-in process
                            $booking->checkIn();

                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title('Guest Checked In')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Booking $booking) => $booking->status === 'confirmed' && ! $booking->hostel_occupant_id),
                    DeleteAction::make(),
                ]),
            
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['hostelOccupant']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['hostelOccupant.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->hostelOccupant) {
            $details['Hostel Occupant'] = $record->hostelOccupant->name;
        }

        return $details;
    }
}
