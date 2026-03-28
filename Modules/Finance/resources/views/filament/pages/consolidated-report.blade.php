<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="generate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
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
                Generate Group P&L
            </button>
        </form>

        @if (!empty($companies))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Income</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Expenses</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Net P&L</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($companies as $company)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $company['name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-green-600">{{ number_format($company['income'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-red-600">{{ number_format($company['expenses'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-bold {{ $company['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($company['net'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-300">
                            <tr>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">GROUP TOTAL</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-green-600">{{ number_format(collect($companies)->sum('income'), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-red-600">{{ number_format(collect($companies)->sum('expenses'), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold {{ collect($companies)->sum('net') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format(collect($companies)->sum('net'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>