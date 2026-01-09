<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                
                <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Payment Failed
                </h1>
                
                <p class="mt-4 text-lg text-gray-500">
                    Unfortunately, your payment for booking {{ $booking->booking_reference }} was not successful.
                </p>
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
                                <dt class="text-sm text-gray-600">Amount</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->total_amount, 2) }} GHS</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Failure Details</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Status</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                </dd>
                            </div>
                            @if($failureReason)
                            <div>
                                <dt class="text-sm text-gray-600">Reason</dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-red-50 p-3 rounded">
                                    {{ $failureReason }}
                                </dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Booking Status</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Reserved
                                    </span>
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
                                <strong>Important:</strong> Your booking is still reserved for a limited time. Please complete your payment as soon as possible to secure your booking.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(count($availablePaymentMethods) > 0)
            <div class="mt-10">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Try Another Payment Method</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($availablePaymentMethods as $method)
                    <div class="border rounded-lg p-4 cursor-pointer hover:border-indigo-500 transition-colors duration-200"
                         wire:click="retryWithMethod('{{ $method['code'] }}')">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                @if($method['channel'] === 'momo')
                                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif($method['channel'] === 'card')
                                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @elseif($method['channel'] === 'bank')
                                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $method['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ strtoupper($method['currency']) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('hostels.public.booking.payment', $booking) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Retry Payment
                </a>
                <a href="{{ route('hostels.public.booking.confirmation', $booking) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    View Booking Details
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