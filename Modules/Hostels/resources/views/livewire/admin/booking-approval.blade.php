<div>
    <x-filament::section>
        <x-slot name="heading">
            Booking Approval Management
        </x-slot>

        <x-slot name="description">
            Review and approve pending hostel booking requests
        </x-slot>

        <div class="space-y-6">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model.live="search"
                            placeholder="Search by booking number, guest name, email or phone..."
                        />
                    </x-filament::input.wrapper>
                </div>
                
                <div class="w-full sm:w-48">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="statusFilter">
                            <option value="pending_approval">Pending Approval</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="w-full sm:w-32">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="perPage">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <!-- Bookings Table -->
            <x-filament::card>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guest
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hostel & Bed
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dates
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bookings as $booking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $booking->booking_reference }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->status }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $booking->guest_first_name }} {{ $booking->guest_last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->guest_email }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->guest_phone }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $booking->hostel->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($booking->bed)
                                                Bed: {{ $booking->bed->name }}
                                                @if($booking->bed->room)
                                                    (Room: {{ $booking->bed->room->name }})
                                                @endif
                                            @else
                                                No bed assigned
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            Check-in: {{ $booking->check_in_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Check-out: {{ $booking->check_out_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->number_of_nights }} nights
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $booking->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($booking->status === 'pending_approval')
                                            <div class="flex space-x-2">
                                                <x-filament::button
                                                    size="sm"
                                                    color="success"
                                                    wire:click="openApprovalModal('{{ $booking->id }}', 'approve')"
                                                >
                                                    Approve
                                                </x-filament::button>
                                                <x-filament::button
                                                    size="sm"
                                                    color="danger"
                                                    wire:click="openApprovalModal('{{ $booking->id }}', 'reject')"
                                                >
                                                    Reject
                                                </x-filament::button>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No bookings found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $bookings->links() }}
                </div>
            </x-filament::card>
        </div>
    </x-filament::section>

    <!-- Approval Modal -->
    <x-filament::modal
        id="approval-modal"
        wire:model="showApprovalModal"
        :title="$selectedBooking ? 'Review Booking #' . Booking::find($selectedBooking)->booking_reference : 'Review Booking'"
    >
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                Are you sure you want to approve this booking? This will create a tenant record and reserve the bed.
            </p>
            
            <div class="flex space-x-4 justify-end">
                <x-filament::button
                    color="success"
                    wire:click="approveBooking('{{ $selectedBooking }}')"
                >
                    Confirm Approval
                </x-filament::button>
                
                <x-filament::button
                    color="gray"
                    wire:click="$set('showApprovalModal', false)"
                >
                    Cancel
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>

    <!-- Rejection Modal -->
    <x-filament::modal
        id="rejection-modal"
        wire:model="showApprovalModal"
        title="Reject Booking Request"
    >
        <div class="space-y-4">
            <x-filament-forms::field-wrapper>
                <x-filament-forms::textarea
                    wire:model="rejectionReason"
                    placeholder="Please provide a reason for rejecting this booking request..."
                    rows="4"
                />
                <x-filament-forms::error for="rejectionReason" />
            </x-filament-forms::field-wrapper>
            
            <div class="flex space-x-4 justify-end">
                <x-filament::button
                    color="danger"
                    wire:click="rejectBooking"
                >
                    Confirm Rejection
                </x-filament::button>
                
                <x-filament::button
                    color="gray"
                    wire:click="$set('showApprovalModal', false)"
                >
                    Cancel
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</div>