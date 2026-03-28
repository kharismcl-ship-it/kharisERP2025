<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name (search)</label>
                    <input type="text" wire:model="customer_name" placeholder="Search customer name..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" wire:model="date_from" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" wire:model="date_to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>
            <button type="submit" class="fi-btn fi-btn-color-primary fi-btn-size-md inline-flex items-center justify-center rounded-lg border border-transparent bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                Generate Statement
            </button>
        </form>

        @if (!empty($rows))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Statement for: {{ $customer_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $date_from }} to {{ $date_to }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Debit (GHS)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Credit (GHS)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance (GHS)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['invoice_number'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $row['invoice_date'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $row['due_date'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $row['type'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium {{ $row['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($row['balance'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-right text-gray-900 dark:text-gray-100">Totals</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format(collect($rows)->sum('debit'), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold">{{ number_format(collect($rows)->sum('credit'), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold {{ $closing_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($closing_balance, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @elseif ($generated)
            <div class="text-center py-8 text-gray-500">No invoices found for the given criteria.</div>
        @endif
    </div>
</x-filament-panels::page>