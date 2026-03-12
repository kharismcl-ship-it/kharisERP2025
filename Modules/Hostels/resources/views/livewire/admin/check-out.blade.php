<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Guest Check-Out</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $hostel->name }}</p>

            <div class="flex gap-3">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    wire:keydown.enter="searchBooking"
                    placeholder="Search checked-in guest by booking ref, name or phone..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                />
                <button
                    wire:click="searchBooking"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700"
                >
                    Search
                </button>
            </div>
        </div>

        @if($booking)
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->guest_full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $booking->booking_reference }}</p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Checked In
                    </span>
                </div>

                <!-- Financial Summary -->
                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between col-span-2">
                        <span class="text-gray-600">Total Charged</span>
                        <span class="font-semibold">{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between col-span-2">
                        <span class="text-gray-600">Amount Paid</span>
                        <span class="font-semibold text-green-700">{{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between col-span-2 border-t pt-2">
                        <span class="text-gray-600">Balance Due</span>
                        <span class="font-semibold {{ $booking->balance_amount > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ number_format($booking->balance_amount, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between col-span-2">
                        <span class="text-gray-600">Deposit Held</span>
                        <span class="font-semibold">{{ number_format($booking->deposit_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between col-span-2">
                        <span class="text-gray-600">Deposit Refunded</span>
                        <span class="font-semibold">{{ number_format($booking->deposit_refunded, 2) }}</span>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Actual Check-In:</span>
                        <span class="ml-2 font-medium">
                            {{ $booking->actual_check_in_at ? $booking->actual_check_in_at->format('M j, Y H:i') : '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">Expected Check-Out:</span>
                        <span class="ml-2 font-medium">{{ $booking->check_out_date->format('M j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Room:</span>
                        <span class="ml-2 font-medium">{{ $booking->room?->room_number ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Bed:</span>
                        <span class="ml-2 font-medium">{{ $booking->bed?->bed_number ?? '—' }}</span>
                    </div>
                </div>

                <!-- Deposit Refund -->
                @if($booking->deposit_paid > $booking->deposit_refunded)
                    <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4 space-y-3">
                        <h4 class="text-sm font-medium text-yellow-800">Deposit Refund</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Refund Amount</label>
                                <input
                                    type="number"
                                    wire:model="refundAmount"
                                    step="0.01"
                                    min="0"
                                    max="{{ $booking->deposit_paid - $booking->deposit_refunded }}"
                                    class="w-full rounded-md border-gray-300 text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Deduction Reason (optional)</label>
                                <input
                                    type="text"
                                    wire:model="deductionReason"
                                    placeholder="e.g. Damage to property"
                                    class="w-full rounded-md border-gray-300 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button
                        wire:click="confirmCheckOut"
                        wire:confirm="Confirm check-out for {{ $booking->guest_full_name }}?"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700"
                    >
                        Confirm Check-Out
                    </button>
                    <button
                        wire:click="$set('booking', null)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        @elseif(strlen($search) >= 3)
            <div class="bg-white shadow sm:rounded-lg p-6 text-center text-gray-500 text-sm">
                No checked-in booking found matching "{{ $search }}".
            </div>
        @endif

    </div>
</div>
