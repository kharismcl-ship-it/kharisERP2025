<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $cropCycle->crop_name ?? 'Crop Cycle' }}
                    @if($cropCycle->variety) <span class="text-gray-500 font-normal text-lg">({{ $cropCycle->variety }})</span> @endif
                </h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('farms.crops.harvest', [$farm->slug, $cropCycle]) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                    Record Harvest
                </a>
                <a href="{{ route('farms.crops.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline self-center">
                    Back
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-6">
                @foreach(['overview' => 'Overview', 'scouting' => 'Scouting', 'inputs' => 'Inputs', 'activities' => 'Activities', 'financials' => 'Financials'] as $tab => $label)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                            class="pb-3 text-sm font-medium border-b-2 transition-colors
                                {{ $activeTab === $tab ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Overview -->
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
                    <h3 class="font-semibold">Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium capitalize">{{ $cropCycle->status }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Planted</span><span>{{ $cropCycle->planting_date?->format('M j, Y') ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Expected Harvest</span><span>{{ $cropCycle->expected_harvest_date?->format('M j, Y') ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Area</span><span>{{ $cropCycle->planted_area ? $cropCycle->planted_area.' '.($cropCycle->planted_area_unit ?? 'acres') : '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Expected Yield</span><span>{{ $cropCycle->expected_yield ? $cropCycle->expected_yield.' '.($cropCycle->yield_unit ?? 'kg') : '—' }}</span></div>
                    </div>
                </div>
                <div class="md:col-span-2 bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold mb-3 text-sm">P&L Summary</h3>
                    @php $pnl = $this->pnl; @endphp
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Revenue</span><span class="text-green-600 font-medium">{{ number_format($pnl['revenue'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Input Costs</span><span class="text-red-500">{{ number_format($pnl['inputCost'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Activity Costs</span><span class="text-red-500">{{ number_format($pnl['activityCost'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Other Expenses</span><span class="text-red-500">{{ number_format($pnl['otherExpense'], 2) }}</span></div>
                        <div class="flex justify-between border-t pt-2 font-semibold"><span>Net Profit</span><span class="{{ $pnl['netProfit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($pnl['netProfit'], 2) }}</span></div>
                        @if($this->yieldVsTarget !== null)
                            <div class="flex justify-between text-xs text-gray-500 pt-1">
                                <span>Yield vs Target</span><span>{{ $this->yieldVsTarget }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Scouting -->
        @if($activeTab === 'scouting')
            <div class="flex justify-end mb-3">
                <button wire:click="$set('showScoutingModal', true)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    Log Scouting
                </button>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->scoutingRecords as $record)
                            <tr>
                                <td class="px-4 py-3">{{ $record->scouting_date?->format('M j, Y') }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $record->scouting_type ?? 'general') }}</td>
                                <td class="px-4 py-3">{{ Str::limit($record->notes, 100) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No scouting records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Inputs -->
        @if($activeTab === 'inputs')
            <div class="flex justify-end mb-3">
                <button wire:click="$set('showInputModal', true)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    Log Input
                </button>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Input</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->inputApplications as $input)
                            <tr>
                                <td class="px-4 py-3">{{ $input->application_date?->format('M j, Y') }}</td>
                                <td class="px-4 py-3">{{ $input->input_name }}</td>
                                <td class="px-4 py-3 text-right">{{ $input->quantity }} {{ $input->unit }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($input->total_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No input applications.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Activities -->
        @if($activeTab === 'activities')
            <div class="flex justify-end mb-3">
                <button wire:click="$set('showActivityModal', true)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    Log Activity
                </button>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->activities as $activity)
                            <tr>
                                <td class="px-4 py-3">{{ $activity->activity_date?->format('M j, Y') }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $activity->activity_type ?? '—') }}</td>
                                <td class="px-4 py-3">{{ Str::limit($activity->description, 80) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($activity->cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No activities logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Financials -->
        @if($activeTab === 'financials')
            @php $pnl = $this->pnl; @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Revenue</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($pnl['revenue'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Total Cost</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($pnl['totalCost'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Net Profit</p>
                    <p class="text-2xl font-bold {{ $pnl['netProfit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($pnl['netProfit'], 2) }}</p>
                </div>
                @if($this->yieldVsTarget !== null)
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Yield vs Target</p>
                        <p class="text-2xl font-bold">{{ $this->yieldVsTarget }}%</p>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- Scouting Modal -->
    @if($showScoutingModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Scouting Record</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" wire:model="scoutingDate" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <input type="text" wire:model="scoutingType" placeholder="e.g. pest, disease, growth" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes *</label>
                        <textarea wire:model="scoutingNotes" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
                        @error('scoutingNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="saveScouting" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save</button>
                    <button wire:click="$set('showScoutingModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Input Modal -->
    @if($showInputModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Input Application</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input type="date" wire:model="inputDate" class="w-full rounded-md border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select wire:model="inputType" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Select...</option>
                                <option value="fertilizer">Fertilizer</option>
                                <option value="pesticide">Pesticide</option>
                                <option value="herbicide">Herbicide</option>
                                <option value="seed">Seed</option>
                                <option value="water">Water/Irrigation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Input Name *</label>
                        <input type="text" wire:model="inputName" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('inputName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" wire:model="inputQuantity" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <input type="text" wire:model="inputUnit" class="w-full rounded-md border-gray-300 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Cost</label>
                        <input type="number" wire:model="inputCost" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="saveInput" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save</button>
                    <button wire:click="$set('showInputModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Activity Modal -->
    @if($showActivityModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Log Activity</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                            <input type="date" wire:model="activityDate" class="w-full rounded-md border-gray-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <input type="text" wire:model="activityType" placeholder="e.g. weeding, pruning" class="w-full rounded-md border-gray-300 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea wire:model="activityDescription" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
                        @error('activityDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                        <input type="number" wire:model="activityCost" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="saveActivity" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save</button>
                    <button wire:click="$set('showActivityModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
