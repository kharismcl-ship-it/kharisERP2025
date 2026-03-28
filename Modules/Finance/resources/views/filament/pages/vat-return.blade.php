<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" wire:model="from_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" wire:model="to_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Type</label>
                    <select wire:model="tax_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="vat">VAT</option>
                        <option value="nhil">NHIL</option>
                        <option value="getf">GETF</option>
                        <option value="withholding">Withholding Tax</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="fi-btn fi-btn-color-primary fi-btn-size-md inline-flex items-center justify-center rounded-lg border border-transparent bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                Calculate
            </button>
        </form>

        @if ($generated)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Output {{ strtoupper($tax_type) }} (Sales)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">GHS {{ number_format($output_tax, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Input {{ strtoupper($tax_type) }} (Purchases)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">GHS {{ number_format($input_tax, 2) }}</p>
                </div>
                <div class="rounded-xl shadow p-6 {{ $net_payable >= 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-green-50 dark:bg-green-900/20' }}">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Net {{ strtoupper($tax_type) }} Payable</p>
                    <p class="text-2xl font-bold {{ $net_payable >= 0 ? 'text-red-600' : 'text-green-600' }} mt-1">
                        GHS {{ number_format(abs($net_payable), 2) }}
                        @if ($net_payable < 0) <span class="text-sm font-normal">(Refundable)</span> @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>