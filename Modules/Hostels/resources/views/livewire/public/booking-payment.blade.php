<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                
                <h1 class="mt-4 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Payment for Booking
                </h1>
                
                <p class="mt-4 text-lg text-gray-500">
                    Please complete your payment for booking reference {{ $booking->booking_reference }}.
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
                        </dl>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Payment Summary</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Total Amount</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->total_amount, 2) }} GHS</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Amount Paid</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->amount_paid, 2) }} GHS</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Balance Due</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($booking->balance_amount, 2) }} GHS</dd>
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
                
                @if(count($groupedPaymentMethods) > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Payment Method</h3>
                    
                    <div class="max-w-md mx-auto">
                        <select 
                            wire:model.live="selectedPaymentMethod"
                            class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-cyan-200">
                            <option value="">Choose a payment method</option>
                            @foreach($groupedPaymentMethods as $providerKey => $providerGroup)
                                <optgroup label="{{ $providerGroup['name'] }}">
                                    @foreach($providerGroup['methods'] as $method)
                                        <option value="{{ $method['code'] }}">
                                            {{ $method['name'] }} ({{ strtoupper($method['currency']) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        
                        @if($payIntent && $payIntent->status === 'pending')
                        <div class="mt-4 text-center">
                            <button wire:click="changePaymentMethod" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Change Payment Method
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="mt-8">
                    <div class="rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    No payment methods available
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>
                                        There are currently no payment methods configured. Please contact the system administrator.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="mt-10">
                @if (session()->has('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    {{ session('error') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button wire:click="initiatePayment" 
                            @disabled(count($groupedPaymentMethods) > 0 && (is_null($selectedPaymentMethod) || $selectedPaymentMethod === '')) 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white 
                                @if(count($groupedPaymentMethods) > 0 && (is_null($selectedPaymentMethod) || $selectedPaymentMethod === ''))
                                    bg-gray-400 cursor-not-allowed
                                @else
                                    bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                @endif">
                        Proceed to Payment
                    </button>
                    <a href="{{ route('hostels.public.booking.confirmation', $booking) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View Booking Details
                    </a>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    Need help? <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Contact support</a>
                </p>
            </div>
        </div>
    </div>
</div>