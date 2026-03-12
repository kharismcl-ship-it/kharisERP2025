<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Request a Movie</h1>
            <p class="mt-0.5 text-sm text-gray-500">Submit a request for a movie you'd like to see added.</p>
        </div>
        <a href="{{ route('hostel_occupant.movies.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Movies
        </a>
    </div>

    <form wire:submit="submit" class="space-y-5">
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-700">Movie Details</h2>
            </div>
            <div class="p-5 space-y-5">

                {{-- Title --}}
                <div>
                    <label for="movie-title" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Movie Title <span class="text-red-500">*</span>
                    </label>
                    <input id="movie-title"
                           type="text"
                           wire:model="title"
                           placeholder="e.g. The Dark Knight"
                           class="block w-full rounded-lg border @error('title') border-red-300 bg-red-50 @else border-gray-200 bg-white @enderror px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @error('title')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="movie-description" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Additional Information <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea id="movie-description"
                              wire:model="description"
                              rows="3"
                              placeholder="Year, director, genre, or any other details that will help us find the right movie..."
                              class="block w-full rounded-lg border @error('description') border-red-300 bg-red-50 @else border-gray-200 bg-white @enderror px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"></textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Urgency --}}
                <div>
                    <label for="movie-urgency" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Urgency <span class="text-red-500">*</span>
                    </label>
                    <select id="movie-urgency"
                            wire:model="urgency"
                            class="block w-full rounded-lg border @error('urgency') border-red-300 bg-red-50 @else border-gray-200 bg-white @enderror px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="low">Low — No rush</option>
                        <option value="normal">Normal — When possible</option>
                        <option value="urgent">Urgent — ASAP please</option>
                    </select>
                    @error('urgency')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('hostel_occupant.movies.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto">
                Cancel
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors w-full sm:w-auto">
                <svg class="h-4 w-4" wire:loading.remove wire:target="submit" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span wire:loading.remove wire:target="submit">Submit Request</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        </div>
    </form>

</div>
