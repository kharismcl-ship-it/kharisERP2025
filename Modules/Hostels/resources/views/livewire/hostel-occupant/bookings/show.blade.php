<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Booking Details</h2>
                    <a href="{{ route('hostel_occupant.bookings.index') }}" class="text-blue-600 hover:underline">
                        Back to Bookings
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium mb-4">Booking Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Reference:</span>
                                <span class="font-medium">{{ $booking->booking_reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking Type:</span>
                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</span>
                            </div>
                            @if($booking->booking_type === 'academic')
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Academic Year:</span>
                                    <span class="font-medium">{{ $booking->academic_year }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Semester:</span>
                                    <span class="font-medium">{{ $booking->semester }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-in Date:</span>
                                <span class="font-medium">{{ $booking->check_in_date->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-out Date:</span>
                                <span class="font-medium">{{ $booking->check_out_date->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium mb-4">Financial Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="font-medium">{{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span class="font-medium">{{ number_format($booking->amount_paid, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Balance:</span>
                                <span class="font-medium">{{ number_format($booking->balance_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($booking->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($booking->payment_status == 'partially_paid') bg-yellow-100 text-yellow-800
                                    @elseif($booking->payment_status == 'unpaid') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium mb-4">Hostel Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Hostel:</span>
                                <span class="font-medium">{{ $booking->hostel->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Room Number:</span>
                                <span class="font-medium">{{ $booking->room->room_number }}</span>
                            </div>
                            @if($booking->bed)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bed:</span>
                                    <span class="font-medium">{{ $booking->bed->bed_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($booking->status === 'pending')
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Pending Booking</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Your booking is currently pending. Please wait for confirmation or contact the hostel administration.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($booking->status === 'confirmed')
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Booking Confirmed</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>Your booking has been confirmed. Please check in on or after {{ $booking->check_in_date->format('M j, Y') }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>