<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Booking Details</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ $booking->booking_reference }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('hostel_occupant.bookings.receipt', $booking) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Receipt
            </a>
            @if(in_array($booking->status, ['pending', 'awaiting_payment', 'confirmed']))
                <a href="{{ route('hostel_occupant.bookings.cancel', $booking) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                    Cancel
                </a>
            @endif
            <a href="{{ route('hostel_occupant.bookings.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Card with tabs ───────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">

        {{-- Tabs --}}
        <div class="border-b border-gray-200 px-4 sm:px-6 overflow-x-auto">
            <nav class="-mb-px flex gap-4 sm:gap-6 min-w-max">
                @foreach(['details' => 'Details', 'deposit' => 'Deposit', 'charges' => 'Charges', 'payments' => 'Payments'] as $tab => $label)
                    <button
                        wire:click="$set('activeTab', '{{ $tab }}')"
                        class="py-3.5 text-sm font-medium border-b-2 transition-colors
                            {{ $activeTab === $tab
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">

            {{-- ── Details Tab ──────────────────────────────────────────── --}}
            @if($activeTab === 'details')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                    <div class="rounded-lg bg-gray-50 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Booking</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Reference</span>
                                <span class="font-medium text-gray-900">{{ $booking->booking_reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-700
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-700
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type</span>
                                <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</span>
                            </div>
                            @if($booking->booking_type === 'academic')
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Year</span>
                                    <span class="font-medium text-gray-900">{{ $booking->academic_year }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Semester</span>
                                    <span class="font-medium text-gray-900">{{ $booking->semester }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Dates</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Check-in</span>
                                <span class="font-medium text-gray-900">{{ $booking->check_in_date->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Check-out</span>
                                <span class="font-medium text-gray-900">{{ $booking->check_out_date->format('M j, Y') }}</span>
                            </div>
                            @if($booking->actual_check_in_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Actual In</span>
                                    <span class="font-medium text-gray-900">{{ $booking->actual_check_in_at->format('M j, Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Room</h3>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Hostel</span>
                                <span class="font-medium text-gray-900">{{ $booking->hostel?->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Room</span>
                                <span class="font-medium text-gray-900">{{ $booking->room?->room_number ?? '—' }}</span>
                            </div>
                            @if($booking->bed)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Bed</span>
                                    <span class="font-medium text-gray-900">{{ $booking->bed->bed_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-5 sm:col-span-2 lg:col-span-3">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Financials</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Total</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($booking->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Paid</p>
                                <p class="mt-1 text-lg font-semibold text-green-700">{{ number_format($booking->amount_paid, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Balance</p>
                                <p class="mt-1 text-lg font-semibold {{ ($booking->balance_amount ?? 0) > 0 ? 'text-red-600' : 'text-green-700' }}">{{ number_format($booking->balance_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Payment Status</p>
                                <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($booking->payment_status === 'fully_paid') bg-green-100 text-green-700
                                    @elseif(in_array($booking->payment_status, ['partial', 'deposit_paid'])) bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                @if($booking->status === 'pending')
                    <div class="mt-5 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                        Your booking is pending. Please wait for confirmation or contact the hostel administration.
                    </div>
                @elseif($booking->status === 'confirmed')
                    <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        Booking confirmed. Please check in on or after {{ $booking->check_in_date->format('M j, Y') }}.
                    </div>
                @endif
            @endif

            {{-- ── Deposit Tab ───────────────────────────────────────────── --}}
            @if($activeTab === 'deposit')
                @if($deposit)
                    <div class="max-w-sm rounded-lg bg-gray-50 p-5 space-y-3 text-sm">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Deposit Details</h3>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Amount</span>
                            <span class="font-semibold text-gray-900">{{ number_format($deposit->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($deposit->status === 'collected') bg-green-100 text-green-700
                                @elseif($deposit->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($deposit->status === 'refunded') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $deposit->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Collected On</span>
                            <span>{{ $deposit->collected_date?->format('M j, Y') ?? '—' }}</span>
                        </div>
                        @if($deposit->refund_amount)
                            <div class="flex justify-between border-t border-gray-200 pt-3">
                                <span class="text-gray-500">Refund Amount</span>
                                <span class="font-semibold text-green-700">{{ number_format($deposit->refund_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Refunded On</span>
                                <span>{{ $deposit->refunded_date?->format('M j, Y') ?? '—' }}</span>
                            </div>
                        @endif
                        @if($deposit->deductions)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Deductions</span>
                                <span class="font-medium text-red-600">{{ number_format($deposit->deductions, 2) }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="max-w-sm rounded-lg bg-gray-50 p-5 space-y-3 text-sm">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Deposit Information</h3>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Required</span>
                            <span class="font-semibold text-gray-900">{{ number_format($booking->deposit_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Paid</span>
                            <span class="font-semibold text-green-700">{{ number_format($booking->deposit_paid ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Balance</span>
                            <span class="font-semibold text-gray-900">{{ number_format($booking->deposit_balance ?? 0, 2) }}</span>
                        </div>
                        @if(($booking->deposit_refunded ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Refunded</span>
                                <span class="font-semibold text-blue-700">{{ number_format($booking->deposit_refunded, 2) }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            {{-- ── Charges Tab ───────────────────────────────────────────── --}}
            @if($activeTab === 'charges')
                @if($charges->isEmpty())
                    <p class="py-10 text-center text-sm text-gray-400">No charges recorded yet.</p>
                @else
                    <table class="w-full text-sm divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fee</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($charges as $charge)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $charge->feeType?->name ?? $charge->description ?? 'Charge' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ $charge->quantity ?? 1 }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($charge->unit_price ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($charge->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200">
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($charges->sum('amount'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            @endif

            {{-- ── Payments Tab ──────────────────────────────────────────── --}}
            @if($activeTab === 'payments')
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('hostel_occupant.bookings.receipt', $booking) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Print Receipt
                    </a>
                </div>
                @if($payments->isEmpty())
                    <p class="py-10 text-center text-sm text-gray-400">No payment records found.</p>
                @else
                    <table class="w-full text-sm divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-gray-600">{{ $payment->created_at->format('M j, Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $payment->reference ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($payment->status === 'successful') bg-green-100 text-green-700
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

        </div>
    </div>

</div>
