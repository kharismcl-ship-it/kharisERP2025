<x-filament-panels::page>
    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account</label>
            <select wire:model="accountId" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white min-w-[280px]">
                <option value="">— Select account —</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account['id'] }}">{{ $account['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
            <input type="date" wire:model="fromDate" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
            <input type="date" wire:model="toDate" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white">
        </div>
    </div>

    @if (!$accountId)
        <x-filament::card>
            <p class="text-sm text-gray-400 text-center py-6">Select an account to view the ledger.</p>
        </x-filament::card>
    @else
        {{-- Summary cards --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-6">
            <x-filament::card>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Opening Balance</div>
                <div class="mt-1 text-lg font-bold {{ $openingBalance < 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                    GHS {{ number_format($openingBalance, 2) }}
                </div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Debits</div>
                <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                    GHS {{ number_format($totalDebits, 2) }}
                </div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Credits</div>
                <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                    GHS {{ number_format($totalCredits, 2) }}
                </div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Closing Balance</div>
                <div class="mt-1 text-lg font-bold {{ $closingBalance < 0 ? 'text-red-600' : 'text-blue-700 dark:text-blue-400' }}">
                    GHS {{ number_format($closingBalance, 2) }}
                </div>
            </x-filament::card>
        </div>

        {{-- Ledger table --}}
        <x-filament::card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 w-28">Date</th>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 w-28">Reference</th>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Description</th>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right w-32">Debit</th>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right w-32">Credit</th>
                        <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right w-36">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    {{-- Opening balance row --}}
                    <tr class="bg-gray-50 dark:bg-gray-800/40 font-medium">
                        <td class="py-2 text-gray-500">—</td>
                        <td class="py-2 text-gray-500">—</td>
                        <td class="py-2 text-gray-500 italic">Opening Balance</td>
                        <td class="py-2 text-right">—</td>
                        <td class="py-2 text-right">—</td>
                        <td class="py-2 text-right {{ $openingBalance < 0 ? 'text-red-600' : '' }}">
                            {{ number_format($openingBalance, 2) }}
                        </td>
                    </tr>

                    @forelse ($lines as $line)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-1.5 text-gray-500">{{ $line['date'] }}</td>
                            <td class="py-1.5 font-mono text-xs">{{ $line['reference'] }}</td>
                            <td class="py-1.5">{{ $line['description'] }}</td>
                            <td class="py-1.5 text-right {{ $line['debit'] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300' }}">
                                {{ $line['debit'] > 0 ? number_format($line['debit'], 2) : '—' }}
                            </td>
                            <td class="py-1.5 text-right {{ $line['credit'] > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-300' }}">
                                {{ $line['credit'] > 0 ? number_format($line['credit'], 2) : '—' }}
                            </td>
                            <td class="py-1.5 text-right font-medium {{ $line['balance'] < 0 ? 'text-red-600' : '' }}">
                                {{ number_format($line['balance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">No transactions in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                    <tr>
                        <td colspan="3" class="pt-2">Closing Balance</td>
                        <td class="pt-2 text-right">{{ number_format($totalDebits, 2) }}</td>
                        <td class="pt-2 text-right">{{ number_format($totalCredits, 2) }}</td>
                        <td class="pt-2 text-right {{ $closingBalance < 0 ? 'text-red-600' : 'text-blue-700 dark:text-blue-400' }}">
                            {{ number_format($closingBalance, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </x-filament::card>
    @endif
</x-filament-panels::page>
