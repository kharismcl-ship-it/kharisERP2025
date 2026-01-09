<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="text-center">
                @if($messageType === 'success')
                    <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($messageType === 'error')
                    <svg class="mx-auto h-16 w-16 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="mx-auto h-16 w-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                @endif
                
                <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    @if($messageType === 'success')
                        Payment Successful!
                    @elseif($messageType === 'error')
                        Payment Failed
                    @else
                        Payment Status
                    @endif
                </h1>
                
                <p class="mt-4 text-lg text-gray-500">
                    {{ $message }}
                </p>
            </div>
            
            @if($paymentStatus === 'successful')
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
                            </dl>
                        </div>
                        
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Payment Details</h2>
                            <dl class="mt-4 space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Amount Paid</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ number_format($paymentAmount, 2) }} {{ $paymentCurrency }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Transaction ID</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $providerTransactionId }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Status</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Successful
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            @elseif($messageType === 'error')
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
                            </dl>
                        </div>
                        
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Payment Information</h2>
                            <dl class="mt-4 space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Booking Status</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending Payment
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Next Steps</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        <ul class="list-disc pl-5 text-left space-y-1">
                                            <li>Try a different payment method</li>
                                            <li>Contact support for assistance</li>
                                            <li>Retry the payment</li>
                                        </ul>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Note:</strong> Your booking is still reserved for a limited time. Please complete your payment as soon as possible to secure your booking.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                @if($messageType === 'error')
                    <a href="{{ route('hostels.public.booking.payment', $booking) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Retry Payment
                    </a>
                @elseif($paymentStatus === 'successful')
                    <a href="{{ route('hostels.public.booking.confirmation', $booking) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View Booking Details
                    </a>
                @endif
                <a href="{{ route('hostels.public.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Browse More Hostels
                </a>
            </div>
        </div>
    </div>
</div>