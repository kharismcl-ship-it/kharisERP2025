<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">New Maintenance Request</h1>
            <p class="mt-0.5 text-sm text-gray-500">Describe the issue and our team will attend to it.</p>
        </div>
        <a href="{{ route('hostel_occupant.maintenance.index') }}"
           class="inline-flex items-center self-start rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:self-auto">
            &larr; Back
        </a>
    </div>

    {{-- ── Form ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <form wire:submit.prevent="createRequest" class="space-y-5">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- Hostel (read-only — locked to the occupant's hostel) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hostel</label>
                    <p class="text-sm font-medium text-gray-900">{{ $hostel?->name ?? '—' }}</p>
                </div>

                {{-- Room --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Room (Optional)</label>
                    <select wire:model="selectedRoom"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a room (optional)</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Title *</label>
                    <input type="text"
                           wire:model="title"
                           placeholder="Brief description of the issue"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority *</label>
                    <select wire:model="priority"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="low">Low — No immediate impact</option>
                        <option value="medium">Medium — Needs attention soon</option>
                        <option value="high">High — Significantly affecting stay</option>
                        <option value="urgent">Urgent — Safety or emergency</option>
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
                <textarea wire:model="description"
                          rows="5"
                          placeholder="Describe the problem in detail — what happened, where exactly, and when you first noticed it..."
                          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('hostel_occupant.maintenance.index') }}"
                   class="w-full inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:w-auto">
                    Cancel
                </a>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors sm:w-auto">
                    Submit Request
                </button>
            </div>

        </form>
    </div>

</div>
