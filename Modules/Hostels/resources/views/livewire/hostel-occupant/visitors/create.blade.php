<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Pre-Register a Visitor</h1>
            <p class="mt-0.5 text-sm text-gray-500">Let reception know who to expect for a faster check-in.</p>
        </div>
        <a href="{{ route('hostel_occupant.visitors.index') }}"
           class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:self-auto">
            &larr; Back to Visitors
        </a>
    </div>

    {{-- ── Card ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="p-5 sm:p-6">

            @if(!$hostelId)
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    You need an active booking to pre-register visitors.
                </div>
            @else
                <form wire:submit="submit" class="space-y-5">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Visitor Name <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                wire:model="visitorName"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Full name"
                            />
                            @error('visitorName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                wire:model="visitorPhone"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="e.g. 0701234567"
                            />
                            @error('visitorPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose of Visit <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            wire:model="purpose"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="e.g. Social visit, delivery, family..."
                        />
                        @error('purpose') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Arrival <span class="text-red-500">*</span></label>
                        <input
                            type="datetime-local"
                            wire:model="expectedArrival"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        />
                        @error('expectedArrival') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-1 sm:flex-row">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors sm:w-auto"
                        >
                            Pre-Register Visitor
                        </button>
                        <a
                            href="{{ route('hostel_occupant.visitors.index') }}"
                            class="w-full inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:w-auto"
                        >
                            Cancel
                        </a>
                    </div>

                </form>
            @endif

        </div>
    </div>

</div>
