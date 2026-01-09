<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="text-center">
                @if($this->offlinePaymentMessage)
                    <svg class="mx-auto h-16 w-16 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    
                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Booking Received!
                    </h1>
                    
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 font-medium">
                            {{ $this->offlinePaymentMessage['message'] }}
                        </p>
                        <p class="mt-2 text-yellow-700 text-sm">
                            Booking Reference: <strong>{{ $this->offlinePaymentMessage['booking_reference'] }}</strong><br>
                            Payment Method: <strong>{{ ucfirst($this->offlinePaymentMessage['payment_method']) }}</strong>
                        </p>
                        
                        {{-- Show payment instructions if available --}}
                        @if(!empty($this->offlinePaymentMessage['payment_instructions']))
                            <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded-md">
                                <h4 class="text-yellow-900 font-semibold text-sm mb-2">
                                    Payment Instructions:
                                </h4>
                                <div class="text-yellow-800 text-sm whitespace-pre-line">
                                    {!! $this->offlinePaymentMessage['payment_instructions'] !!}
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    
                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Booking Confirmed!
                    </h1>
                    
                    <p class="mt-4 text-lg text-gray-500">
                        Thank you for your booking. A confirmation email has been sent to {{ $booking->hostelOccupant?->email ?? $booking->guest_email ?? 'your email address' }}.
                    </p>
                @endif
            </div>
            
            <div class="mt-10 bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Booking Details</h2>
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
                                <dt class="text-sm text-gray-600">Room</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->room->room_number }}</dd>
                            </div>
                            @if($booking->bed)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Bed</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->bed->bed_number }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Booking Type</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</dd>
                            </div>
                            @if($booking->booking_type === 'academic')
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Academic Year</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->academic_year }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Semester</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->semester }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Dates & Payment</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Check-in Date</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->check_in_date->format('M j, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Check-out Date</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->check_out_date->format('M j, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Total Amount</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->total_amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Amount Paid</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->amount_paid, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Balance</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->balance_amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Payment Status</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $booking->payment_status === 'paid' ? 'green' : ($booking->payment_status === 'partially_paid' ? 'yellow' : 'red') }}-100 text-{{ $booking->payment_status === 'paid' ? 'green' : ($booking->payment_status === 'partially_paid' ? 'yellow' : 'red') }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('hostel_occupant.dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    View My Bookings
                </a>
                <a href="{{ route('hostels.public.booking.change-request', $booking) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Request Room/Bed Change
                </a>
                <a href="{{ route('hostels.public.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Browse More Hostels
                </a>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    Need help? <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Contact support</a>
                </p>
            </div>
        </div>
    </div>
</div>