<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Centre</label>
                    <select wire:model="cost_centre_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Select Cost Centre...</option>
                        @foreach ($costCentres as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
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
                Generate Report
            </button>
        </form>

        @if (!empty($incomeRows) || !empty($expenseRows))
            <div class="grid grid-cols-1 gap-4">
                {{-- Income --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-3 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-200">Income</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Account</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Credit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($incomeRows as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $row['account'] }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-600">{{ number_format($row['debit'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-600">{{ number_format($row['credit'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-medium text-green-600">{{ number_format($row['net'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-sm font-bold text-right">Total Income</td>
                                <td class="px-4 py-2 text-sm text-right font-bold text-green-600">{{ number_format($totalIncome, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Expenses --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Expenses</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Account</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Credit</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($expenseRows as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $row['account'] }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-600">{{ number_format($row['debit'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-600">{{ number_format($row['credit'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-medium text-red-600">{{ number_format($row['net'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-sm font-bold text-right">Total Expenses</td>
                                <td class="px-4 py-2 text-sm text-right font-bold text-red-600">{{ number_format($totalExpenses, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Net --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900 dark:text-gray-100">Net Profit / (Loss)</span>
                        <span class="text-xl font-bold {{ ($totalIncome - $totalExpenses) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            GHS {{ number_format($totalIncome - $totalExpenses, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>