<div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deposit Collection</h2>
                    <p class="text-sm text-gray-500">{{ $hostel->name }}</p>
                </div>
                <div class="w-40">
                    <select
                        wire:model.live="statusFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="collected">Collected</option>
                        <option value="refunded">Refunded</option>
                        <option value="partial_refund">Partial Refund</option>
                        <option value="forfeited">Forfeited</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occupant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($deposits as $deposit)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">
                                        {{ $deposit->booking?->booking_reference ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $deposit->deposit_type ?? 'security' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900">{{ $deposit->occupant?->full_name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ number_format($deposit->amount, 2) }}</div>
                                    @if($deposit->refund_amount)
                                        <div class="text-xs text-green-600">Refund: {{ number_format($deposit->refund_amount, 2) }}</div>
                                    @endif
                                    @if($deposit->deductions)
                                        <div class="text-xs text-red-600">Deductions: {{ number_format($deposit->deductions, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($deposit->status === 'collected') bg-green-100 text-green-800
                                        @elseif($deposit->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($deposit->status === 'refunded') bg-blue-100 text-blue-800
                                        @elseif($deposit->status === 'forfeited') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $deposit->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $deposit->collected_date?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        @if($deposit->status === 'pending')
                                            <button
                                                wire:click="markCollected({{ $deposit->id }})"
                                                wire:confirm="Mark this deposit as collected?"
                                                class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700"
                                            >
                                                Mark Collected
                                            </button>
                                        @endif
                                        @if($deposit->canBeRefunded())
                                            <button
                                                wire:click="openRefundModal({{ $deposit->id }})"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700"
                                            >
                                                Process Refund
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    No deposits found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $deposits->links() }}
            </div>
        </div>

    </div>

    <!-- Refund Modal -->
    @if($showRefundModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Process Deposit Refund</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount</label>
                        <input
                            type="number"
                            wire:model="refundAmount"
                            step="0.01"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                        />
                        @error('refundAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deduction Reason (optional)</label>
                        <textarea
                            wire:model="deductionReason"
                            rows="2"
                            placeholder="Reason for deductions if any..."
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                        ></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 justify-end">
                    <button
                        wire:click="processRefund"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700"
                    >
                        Confirm Refund
                    </button>
                    <button
                        wire:click="$set('showRefundModal', false)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
