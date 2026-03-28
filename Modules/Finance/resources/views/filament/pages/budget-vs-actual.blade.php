<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget</label>
                    <select wire:model="budget_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Select Budget...</option>
                        @foreach ($budgets as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">As of Date</label>
                    <input type="date" wire:model="as_of_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>
            <div>
                <button type="submit" class="fi-btn fi-btn-color-primary fi-btn-size-md inline-flex items-center justify-center rounded-lg border border-transparent bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    Generate Report
                </button>
            </div>
        </form>

        @if (!empty($rows))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Account</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Annual Budget</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actual YTD</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Variance</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Variance %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row['account'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['budget'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($row['actual'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium {{ $row['variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($row['variance'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right {{ $row['variance_pct'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($row['variance_pct'], 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>