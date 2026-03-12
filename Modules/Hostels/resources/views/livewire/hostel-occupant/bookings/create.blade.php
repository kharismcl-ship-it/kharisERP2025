<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">New Booking</h1>
            <p class="mt-0.5 text-sm text-gray-500">Select a hostel, room, and dates to create a booking.</p>
        </div>
        <a href="{{ route('hostel_occupant.bookings.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Back to Bookings
        </a>
    </div>

    {{-- ── Form ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <form wire:submit.prevent="createBooking" class="space-y-6">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- Hostel (read-only — locked to the occupant's hostel) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hostel</label>
                    <p class="text-sm font-medium text-gray-900">{{ $hostel?->name ?? '—' }}</p>
                </div>

                {{-- Booking Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Booking Type *</label>
                    <select wire:model.live="bookingType"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="academic">Academic Year</option>
                        <option value="short_stay">Short Stay</option>
                    </select>
                    @error('bookingType') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                @if($bookingType === 'academic')
                    {{-- Academic Year --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Academic Year *</label>
                        <input type="text"
                               wire:model="academicYear"
                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g. 2025/2026">
                        @error('academicYear') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Semester --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Semester *</label>
                        <select wire:model="semester"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                        @error('semester') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Check-in Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Check-in Date *</label>
                    <input type="date"
                           wire:model="checkInDate"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('checkInDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Check-out Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Check-out Date *</label>
                    <input type="date"
                           wire:model="checkOutDate"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('checkOutDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="border-t border-gray-100 pt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                {{-- Room --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Room *</label>
                    <select wire:model.live="selectedRoom"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }} ({{ ucfirst($room->room_type ?? $room->type ?? 'Standard') }})</option>
                        @endforeach
                    </select>
                    @error('selectedRoom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                @if($selectedRoom && count($beds) > 0)
                    {{-- Bed --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Bed (Optional)</label>
                        <select wire:model="selectedBed"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Any available bed</option>
                            @foreach($beds as $bed)
                                <option value="{{ $bed->id }}">{{ $bed->bed_number }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('hostel_occupant.bookings.index') }}"
                   class="w-full inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:w-auto">
                    Cancel
                </a>
                <button type="submit"
                        @if(!$selectedRoom) disabled @endif
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors sm:w-auto">
                    Create Booking
                </button>
            </div>

        </form>
    </div>

</div>
