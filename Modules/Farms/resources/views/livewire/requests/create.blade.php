<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">New Farm Request</h1>
                    <p class="text-sm text-gray-500">{{ $farm->name }}</p>
                </div>
                <a href="{{ route('farms.requests.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline">Back</a>
            </div>

            <form wire:submit="submitRequest" class="space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Request Type *</label>
                        <select wire:model="requestType" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="materials">Materials</option>
                            <option value="funds">Funds</option>
                            <option value="equipment">Equipment</option>
                            <option value="services">Services</option>
                            <option value="labour">Labour</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urgency *</label>
                        <select wire:model="urgency" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" wire:model="title" class="w-full rounded-md border-gray-300 text-sm" />
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
                </div>

                <!-- Line Items -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Items</label>
                        <button type="button" wire:click="addItem"
                                class="text-xs text-indigo-600 hover:underline">+ Add Item</button>
                    </div>

                    <div class="space-y-2">
                        @foreach($items as $index => $item)
                            <div class="grid grid-cols-12 gap-2 items-start">
                                <div class="col-span-5">
                                    <input type="text" wire:model="items.{{ $index }}.description"
                                           placeholder="Description *"
                                           class="w-full rounded-md border-gray-300 text-xs" />
                                    @error("items.{$index}.description") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-2">
                                    <input type="number" wire:model="items.{{ $index }}.quantity"
                                           placeholder="Qty" step="0.01" min="0"
                                           class="w-full rounded-md border-gray-300 text-xs" />
                                </div>
                                <div class="col-span-2">
                                    <input type="text" wire:model="items.{{ $index }}.unit"
                                           placeholder="Unit"
                                           class="w-full rounded-md border-gray-300 text-xs" />
                                </div>
                                <div class="col-span-2">
                                    <input type="number" wire:model="items.{{ $index }}.unit_cost"
                                           placeholder="Unit cost" step="0.01" min="0"
                                           class="w-full rounded-md border-gray-300 text-xs" />
                                </div>
                                <div class="col-span-1 flex justify-center pt-1.5">
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                                class="text-red-400 hover:text-red-600 text-xs">
                                            &times;
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Submit Request
                    </button>
                    <a href="{{ route('farms.requests.index', $farm->slug) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
