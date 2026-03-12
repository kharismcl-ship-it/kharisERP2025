<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 capitalize">
                    {{ str_replace('_', ' ', $batch->animal_type) }}
                    @if($batch->breed) <span class="text-gray-400 font-normal text-lg">({{ $batch->breed }})</span> @endif
                </h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <a href="{{ route('farms.livestock.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline self-center">Back</a>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500 mb-1">Current Count</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($batch->current_count) }}</p>
                <p class="text-xs text-gray-400">Initial: {{ number_format($batch->initial_count) }}</p>
            </div>
            @if($this->growthRate !== null)
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500 mb-1">Growth Rate</p>
                    <p class="text-2xl font-bold text-green-600">{{ $this->growthRate }} kg/day</p>
                </div>
            @endif
            @if($this->fcr !== null)
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-xs text-gray-500 mb-1">Feed Conversion Ratio</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $this->fcr }}</p>
                    <p class="text-xs text-gray-400">kg feed / kg gain</p>
                </div>
            @endif
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-6">
                @foreach(['summary' => 'Summary', 'feed' => 'Feed', 'weight' => 'Weight', 'health' => 'Health', 'mortality' => 'Mortality'] as $tab => $label)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                            class="pb-3 text-sm font-medium border-b-2 transition-colors
                                {{ $activeTab === $tab ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Quick Actions -->
        <div class="flex gap-3">
            <button wire:click="$set('showFeedModal', true)" class="px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700">Log Feed</button>
            <button wire:click="$set('showWeightModal', true)" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">Log Weight</button>
            <button wire:click="$set('showHealthModal', true)" class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">Health Event</button>
            <button wire:click="$set('showMortalityModal', true)" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700">Mortality</button>
        </div>

        <!-- Summary Tab -->
        @if($activeTab === 'summary')
            <div class="bg-white rounded-lg shadow p-5 text-sm space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex justify-between"><span class="text-gray-500">Reference:</span><span>{{ $batch->batch_reference ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status:</span><span class="capitalize">{{ $batch->status }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Acquired:</span><span>{{ $batch->acquisition_date?->format('M j, Y') ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Acquisition Cost:</span><span>{{ $batch->acquisition_cost ? number_format($batch->acquisition_cost, 2) : '—' }}</span></div>
                </div>
                @if($batch->notes)
                    <div class="mt-3 pt-3 border-t">
                        <p class="text-gray-500 text-xs mb-1">Notes</p>
                        <p>{{ $batch->notes }}</p>
                    </div>
                @endif

                @php $healthSummary = $this->healthSummary; @endphp
                @if($healthSummary['next_due'])
                    <div class="mt-3 pt-3 border-t bg-yellow-50 rounded p-3">
                        <p class="text-xs font-medium text-yellow-800">Next Health Event Due</p>
                        <p class="text-sm">{{ $healthSummary['next_due']->event_type ?? 'Health Check' }} — {{ $healthSummary['next_due']->next_due_date->format('M j, Y') }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Feed, Weight, Health, Mortality tabs just show placeholder tables -->
        @if($activeTab !== 'summary')
            <div class="bg-white rounded-lg shadow p-5 text-sm text-gray-400 text-center py-12">
                Records will appear here after logging. Use the quick action buttons above to log new entries.
            </div>
        @endif

    </div>

    <!-- Feed Modal -->
    @if($showFeedModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Feed</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" wire:model="feedDate" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Feed Type</label>
                        <input type="text" wire:model="feedType" placeholder="e.g. maize, hay" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity (kg) *</label>
                        <input type="number" wire:model="feedQuantityKg" step="0.1" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('feedQuantityKg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                        <input type="number" wire:model="feedCost" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" /></div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="logFeed" class="px-5 py-2 bg-green-600 text-white rounded-md text-sm">Save</button>
                    <button wire:click="$set('showFeedModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Weight Modal -->
    @if($showWeightModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Weight</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" wire:model="weightDate" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Avg Weight (kg) *</label>
                        <input type="number" wire:model="avgWeightKg" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('avgWeightKg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="logWeight" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm">Save</button>
                    <button wire:click="$set('showWeightModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mortality Modal -->
    @if($showMortalityModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Mortality</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" wire:model="mortalityDate" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Count *</label>
                        <input type="number" wire:model="mortalityCount" min="1" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Cause</label>
                        <input type="text" wire:model="mortalityCause" placeholder="e.g. disease, injury" class="w-full rounded-md border-gray-300 text-sm" /></div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="logMortality" class="px-5 py-2 bg-red-600 text-white rounded-md text-sm">Log Mortality</button>
                    <button wire:click="$set('showMortalityModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Health Modal -->
    @if($showHealthModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Health Event</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" wire:model="healthDate" class="w-full rounded-md border-gray-300 text-sm" /></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Event Type *</label>
                        <select wire:model="healthEventType" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="vaccination">Vaccination</option>
                            <option value="deworming">Deworming</option>
                            <option value="treatment">Treatment</option>
                            <option value="checkup">Checkup</option>
                            <option value="other">Other</option>
                        </select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea wire:model="healthDescription" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea>
                        @error('healthDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                        <input type="date" wire:model="nextDueDate" class="w-full rounded-md border-gray-300 text-sm" /></div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="logHealthEvent" class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm">Save</button>
                    <button wire:click="$set('showHealthModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
