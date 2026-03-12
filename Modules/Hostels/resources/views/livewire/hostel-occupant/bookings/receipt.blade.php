<div class="max-w-2xl mx-auto my-8 px-4">
    <div class="bg-white shadow-sm rounded-lg p-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Booking Receipt</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $booking->hostel->name }}</p>
        </div>

        <!-- Booking Info -->
        <div class="border-b pb-4 mb-4 grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Reference:</span>
                <span class="ml-2 font-semibold">{{ $booking->booking_reference }}</span>
            </div>
            <div>
                <span class="text-gray-500">Status:</span>
                <span class="ml-2 font-semibold capitalize">{{ str_replace('_', ' ', $booking->status) }}</span>
            </div>
            <div>
                <span class="text-gray-500">Guest:</span>
                <span class="ml-2 font-semibold">{{ $booking->guest_full_name }}</span>
            </div>
            <div>
                <span class="text-gray-500">Booking Type:</span>
                <span class="ml-2 capitalize">{{ str_replace('_', ' ', $booking->booking_type) }}</span>
            </div>
            <div>
                <span class="text-gray-500">Check-in:</span>
                <span class="ml-2">{{ $booking->check_in_date->format('M j, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Check-out:</span>
                <span class="ml-2">{{ $booking->check_out_date->format('M j, Y') }}</span>
            </div>
            @if($booking->room)
            <div>
                <span class="text-gray-500">Room:</span>
                <span class="ml-2">{{ $booking->room->room_number }}</span>
            </div>
            @endif
            @if($booking->bed)
            <div>
                <span class="text-gray-500">Bed:</span>
                <span class="ml-2">{{ $booking->bed->bed_number }}</span>
            </div>
            @endif
        </div>

        <!-- Charges -->
        @if($booking->charges->isNotEmpty())
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Charges Breakdown</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b">
                        <th class="text-left py-1">Description</th>
                        <th class="text-right py-1">Qty</th>
                        <th class="text-right py-1">Unit Price</th>
                        <th class="text-right py-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->charges as $charge)
                    <tr class="border-b border-gray-100">
                        <td class="py-1.5">{{ $charge->feeType?->name ?? $charge->description ?? 'Charge' }}</td>
                        <td class="text-right py-1.5">{{ $charge->quantity ?? 1 }}</td>
                        <td class="text-right py-1.5">{{ number_format($charge->unit_price ?? 0, 2) }}</td>
                        <td class="text-right py-1.5">{{ number_format($charge->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Financial Summary -->
        <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Total Amount</span>
                <span class="font-medium">{{ number_format($booking->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Amount Paid</span>
                <span class="font-medium text-green-700">{{ number_format($booking->amount_paid, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Deposit Paid</span>
                <span class="font-medium">{{ number_format($booking->deposit_paid, 2) }}</span>
            </div>
            <div class="flex justify-between border-t pt-2">
                <span class="font-semibold text-gray-700">Balance Due</span>
                <span class="font-bold {{ $booking->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($booking->balance_amount, 2) }}
                </span>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-gray-400">
            Generated {{ now()->format('M j, Y H:i') }} — {{ $booking->hostel->name }}
        </div>

    </div>
</div>
