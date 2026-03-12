<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Report an Incident</h1>
            <p class="mt-0.5 text-sm text-gray-500">Submit a safety or maintenance concern for prompt follow-up.</p>
        </div>
        <a href="{{ route('hostel_occupant.incidents.index') }}"
           class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:self-auto">
            &larr; Back to Incidents
        </a>
    </div>

    {{-- ── Card ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="p-5 sm:p-6">

            @if(!$hostelId)
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    You need an active booking to report an incident. Please contact the hostel directly.
                </div>
            @else
                <form wire:submit="submit" class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            wire:model="title"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Brief title of the incident"
                        />
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Severity <span class="text-red-500">*</span></label>
                        <select
                            wire:model="severity"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm"
                        >
                            <option value="low">Low — Minor nuisance</option>
                            <option value="medium">Medium — Needs attention soon</option>
                            <option value="high">High — Urgent issue</option>
                            <option value="critical">Critical — Emergency</option>
                        </select>
                        @error('severity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea
                            wire:model="description"
                            rows="5"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Describe the incident in detail — what happened, where, and when..."
                        ></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-1 sm:flex-row">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700 transition-colors sm:w-auto"
                        >
                            Submit Report
                        </button>
                        <a
                            href="{{ route('hostel_occupant.incidents.index') }}"
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
