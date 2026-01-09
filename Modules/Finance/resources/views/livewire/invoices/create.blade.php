<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Create Invoice</h1>

    <form wire:submit.prevent="createInvoice" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Name</label>
                    <input 
                        type="text" 
                        id="customer_name" 
                        wire:model="customer_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                    @error('customer_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Date</label>
                    <input 
                        type="date" 
                        id="invoice_date" 
                        wire:model="invoice_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                    @error('invoice_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                    <input 
                        type="date" 
                        id="due_date" 
                        wire:model="due_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                    @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
            <h2 class="text-lg font-medium mb-4">Invoice Lines</h2>
            
            @foreach($lines as $index => $line)
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 items-end">
                    <div class="md:col-span-4">
                        <label for="lines.{{ $index }}.description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <input 
                            type="text" 
                            id="lines.{{ $index }}.description" 
                            wire:model="lines.{{ $index }}.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('lines.' . $index . '.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="lines.{{ $index }}.quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                        <input 
                            type="number" 
                            id="lines.{{ $index }}.quantity" 
                            wire:model="lines.{{ $index }}.quantity"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            step="0.01"
                        >
                        @error('lines.' . $index . '.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="lines.{{ $index }}.unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                        <input 
                            type="number" 
                            id="lines.{{ $index }}.unit_price" 
                            wire:model="lines.{{ $index }}.unit_price"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            step="0.01"
                        >
                        @error('lines.' . $index . '.unit_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="lines.{{ $index }}.line_total" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                        <input 
                            type="number" 
                            id="lines.{{ $index }}.line_total" 
                            wire:model="lines.{{ $index }}.line_total"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            step="0.01"
                            readonly
                        >
                    </div>
                    
                    <div>
                        @if(count($lines) > 1)
                            <button 
                                type="button" 
                                wire:click="removeLine({{ $index }})"
                                class="text-red-500 hover:text-red-700"
                            >
                                Remove
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <button 
                type="button" 
                wire:click="addLine"
                class="mt-2 text-blue-500 hover:text-blue-700"
            >
                + Add Line
            </button>
        </div>

        <div class="flex justify-end">
            <button 
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Create Invoice
            </button>
        </div>
    </form>
</div>