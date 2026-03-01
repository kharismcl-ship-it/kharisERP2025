<x-filament-panels::page>
    {{-- Date filter --}}
    <div class="flex items-end gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">As of Date</label>
            <input type="date" wire:model="asOf" wire:change="loadReport"
                class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm dark:bg-gray-800 dark:text-white"
                value="{{ $asOf }}">
        </div>
    </div>

    {{-- Balance indicator --}}
    @if (!$balanced)
        <div class="mb-4 rounded bg-red-50 dark:bg-red-900/30 border border-red-300 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            Warning: Trial balance is out of balance. Debits: GHS {{ number_format($totalDebits, 2) }} | Credits: GHS {{ number_format($totalCredits, 2) }}
        </div>
    @endif

    {{-- Table --}}
    <x-filament::card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 w-24">Code</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300">Account Name</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 w-32">Type</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right w-36">Debit (GHS)</th>
                    <th class="pb-2 font-semibold text-gray-600 dark:text-gray-300 text-right w-36">Credit (GHS)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2 font-mono text-gray-500">{{ $row['code'] }}</td>
                        <td class="py-2">{{ $row['name'] }}</td>
                        <td class="py-2 text-gray-500">{{ $row['type'] }}</td>
                        <td class="py-2 text-right">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}</td>
                        <td class="py-2 text-right">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">No transactions found up to {{ $asOf }}.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                <tr>
                    <td colspan="3" class="pt-2">Total</td>
                    <td class="pt-2 text-right {{ $balanced ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($totalDebits, 2) }}
                    </td>
                    <td class="pt-2 text-right {{ $balanced ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($totalCredits, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </x-filament::card>
</x-filament-panels::page>
