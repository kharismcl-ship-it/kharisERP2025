<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Submit Daily Report</h1>
                    <p class="text-sm text-gray-500">{{ $farm->name }}</p>
                </div>
                <a href="{{ route('farms.daily-reports.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline">
                    Back
                </a>
            </div>

            <form wire:submit="submit" class="space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Date *</label>
                        <input type="date" wire:model="reportDate" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('reportDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reported By</label>
                        <select wire:model="workerId" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Select worker...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary *</label>
                    <textarea wire:model="summary" rows="3"
                              placeholder="Overall summary of the day's activities..."
                              class="w-full rounded-md border-gray-300 text-sm"></textarea>
                    @error('summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activities Done *</label>
                    <textarea wire:model="activitiesDone" rows="4"
                              placeholder="List all activities completed today..."
                              class="w-full rounded-md border-gray-300 text-sm"></textarea>
                    @error('activitiesDone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issues Noted</label>
                    <textarea wire:model="issuesNoted" rows="3"
                              placeholder="Any problems, pests, diseases, or concerns..."
                              class="w-full rounded-md border-gray-300 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <textarea wire:model="recommendations" rows="2"
                              placeholder="Suggested actions or follow-ups..."
                              class="w-full rounded-md border-gray-300 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weather Observation</label>
                    <input type="text" wire:model="weatherObservation"
                           placeholder="e.g. Sunny, 28°C, light wind"
                           class="w-full rounded-md border-gray-300 text-sm" />
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                        Submit Report
                    </button>
                    <a href="{{ route('farms.daily-reports.index', $farm->slug) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
