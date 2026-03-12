<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Cancel Booking</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ $booking->booking_reference }}</p>
        </div>
        <a href="{{ route('hostel_occupant.bookings.show', $booking) }}"
           class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:self-auto">
            &larr; Back to Booking
        </a>
    </div>

    {{-- ── Card ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="p-5 sm:p-6 space-y-5">

            {{-- Booking Summary --}}
            <div class="rounded-lg border border-gray-100 bg-gray-50 divide-y divide-gray-100 text-sm">
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-gray-500">Booking Reference</span>
                    <span class="font-medium text-gray-900">{{ $booking->booking_reference }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-gray-500">Hostel</span>
                    <span class="font-medium text-gray-900">{{ $booking->hostel->name }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-gray-500">Check-in</span>
                    <span class="font-medium text-gray-900">{{ $booking->check_in_date->format('M j, Y') }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-gray-500">Check-out</span>
                    <span class="font-medium text-gray-900">{{ $booking->check_out_date->format('M j, Y') }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-gray-500">Amount Paid</span>
                    <span class="font-medium text-gray-900">{{ number_format($booking->getTotalPaidAmount(), 2) }}</span>
                </div>
            </div>

            {{-- Cancellation Policy --}}
            @if($policyName)
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm">
                    <p class="font-medium text-blue-800">Policy Applied: {{ $policyName }}</p>
                </div>
            @endif

            @if(!$cancellationAllowed)
                {{-- Not allowed --}}
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-4 text-sm">
                    <p class="font-semibold text-red-800">Cancellation Not Allowed</p>
                    <p class="mt-1 text-red-700">The cancellation window has passed. Please contact the hostel directly.</p>
                </div>

                <a href="{{ route('hostel_occupant.bookings.show', $booking) }}"
                   class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:w-auto">
                    &larr; Return to Booking
                </a>

            @else
                {{-- Refund Estimate --}}
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-4 text-sm space-y-2">
                    <p class="font-semibold text-yellow-800">Estimated Refund</p>
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-700">Refund Amount</span>
                        <span class="text-lg font-bold text-yellow-900">{{ number_format($estimatedRefund, 2) }}</span>
                    </div>
                    @if($estimatedRefund == 0)
                        <p class="text-yellow-700">No refund will be issued per the cancellation policy.</p>
                    @endif
                </div>

                {{-- Confirmation checkbox --}}
                <div class="rounded-lg border border-gray-200 px-4 py-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="confirmed"
                            class="mt-0.5 rounded border-gray-300"
                        />
                        <span class="text-sm text-gray-700 leading-snug">
                            I understand that cancelling this booking is irreversible and I accept the refund terms above.
                        </span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button
                        wire:click="cancel"
                        @if(!$confirmed) disabled @endif
                        class="w-full inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors sm:w-auto"
                    >
                        Cancel Booking
                    </button>
                    <a
                        href="{{ route('hostel_occupant.bookings.show', $booking) }}"
                        class="w-full inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:w-auto"
                    >
                        Keep My Booking
                    </a>
                </div>

            @endif

        </div>
    </div>

</div>
