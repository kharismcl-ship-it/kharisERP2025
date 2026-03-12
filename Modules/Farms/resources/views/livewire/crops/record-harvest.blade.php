<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Record Harvest</h1>
                    <p class="text-sm text-gray-500">
                        {{ $cropCycle->crop_name ?? 'Crop Cycle' }} &bull; {{ $farm->name }}
                    </p>
                </div>
                <a href="{{ route('farms.crops.show', [$farm->slug, $cropCycle]) }}"
                   class="text-sm text-blue-600 hover:underline">Back</a>
            </div>

            <!-- Progress bar if expected yield set -->
            @if($cropCycle->expected_yield)
                <div class="bg-gray-50 rounded-lg p-4 mb-5 text-sm">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-600">Total Harvested So Far</span>
                        <span class="font-medium">{{ number_format($this->totalHarvested, 2) }} / {{ number_format($cropCycle->expected_yield, 2) }} {{ $cropCycle->yield_unit ?? 'kg' }}</span>
                    </div>
                    @if($this->yieldVsTarget !== null)
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, $this->yieldVsTarget) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $this->yieldVsTarget }}% of target yield</p>
                    @endif
                </div>
            @endif

            <form wire:submit="recordHarvest" class="space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harvest Date *</label>
                        <input type="date" wire:model="harvestDate" class="w-full rounded-md border-gray-300 text-sm" />
                        @error('harvestDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div></div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                        <input type="number" wire:model="quantity" step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 text-sm" />
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                        <input type="text" wire:model="unit" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (optional)</label>
                    <input type="number" wire:model="unitPrice" step="0.01" min="0"
                           placeholder="Price per unit"
                           class="w-full rounded-md border-gray-300 text-sm" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buyer Name (optional)</label>
                        <input type="text" wire:model="buyerName" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Storage Location (optional)</label>
                        <input type="text" wire:model="storageLocation" class="w-full rounded-md border-gray-300 text-sm" />
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                        Record Harvest
                    </button>
                    <a href="{{ route('farms.crops.show', [$farm->slug, $cropCycle]) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
