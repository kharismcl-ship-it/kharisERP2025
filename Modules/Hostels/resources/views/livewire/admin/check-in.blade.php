<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Guest Check-In</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $hostel->name }}</p>

            <div class="flex gap-3">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    wire:keydown.enter="searchBooking"
                    placeholder="Search by booking ref, name or phone..."
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
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->guest_full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $booking->booking_reference }}</p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Check-in Date:</span>
                        <span class="ml-2 font-medium">{{ $booking->check_in_date->format('M j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Check-out Date:</span>
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
                    <div>
                        <span class="text-gray-500">Total Amount:</span>
                        <span class="ml-2 font-medium">{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Amount Paid:</span>
                        <span class="ml-2 font-medium">{{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    @if($booking->guest_phone)
                    <div>
                        <span class="text-gray-500">Phone:</span>
                        <span class="ml-2 font-medium">{{ $booking->guest_phone }}</span>
                    </div>
                    @endif
                    @if($booking->guest_email)
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <span class="ml-2 font-medium">{{ $booking->guest_email }}</span>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Guest Signature (optional)
                        <span class="text-xs text-gray-400 font-normal ml-1">Paste base64 or leave blank</span>
                    </label>
                    <textarea
                        wire:model="signature"
                        rows="2"
                        placeholder="Base64 signature data..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono"
                    ></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        wire:click="confirmCheckIn"
                        wire:confirm="Confirm check-in for {{ $booking->guest_full_name }}?"
                        class="px-6 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700"
                    >
                        Confirm Check-In
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
                No confirmed booking found matching "{{ $search }}".
            </div>
        @endif

    </div>
</div>
