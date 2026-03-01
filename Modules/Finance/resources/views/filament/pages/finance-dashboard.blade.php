<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
        {{-- Cash / Bank Balance --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank Balance</div>
            <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                GHS {{ number_format($stats['bankBalance'], 2) }}
            </div>
        </x-filament::card>

        {{-- Revenue MTD --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (MTD)</div>
            <div class="mt-1 text-2xl font-bold text-green-600">
                GHS {{ number_format($stats['revenueMtd'], 2) }}
            </div>
        </x-filament::card>

        {{-- AR Total --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Accounts Receivable</div>
            <div class="mt-1 text-2xl font-bold text-blue-600">
                GHS {{ number_format($stats['arTotal'], 2) }}
            </div>
            <div class="mt-1 text-xs text-gray-400">{{ $stats['unpaidCount'] }} unpaid invoice(s)</div>
        </x-filament::card>

        {{-- AP Total --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Accounts Payable</div>
            <div class="mt-1 text-2xl font-bold text-orange-600">
                GHS {{ number_format($stats['apTotal'], 2) }}
            </div>
        </x-filament::card>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-3">
        {{-- Cash collected MTD --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cash Collected (MTD)</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
                GHS {{ number_format($stats['cashMtd'], 2) }}
            </div>
        </x-filament::card>

        {{-- Overdue invoices --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue Invoices</div>
            <div class="mt-1 text-xl font-semibold {{ $stats['overdueCount'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                {{ $stats['overdueCount'] }}
            </div>
        </x-filament::card>

        {{-- Quick links --}}
        <x-filament::card>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Reports</div>
            <div class="space-y-1 text-sm">
                <a href="{{ route('filament.company-admin.pages.trial-balance') }}" class="block text-primary-600 hover:underline">Trial Balance</a>
                <a href="{{ route('filament.company-admin.pages.income-statement') }}" class="block text-primary-600 hover:underline">Income Statement</a>
                <a href="{{ route('filament.company-admin.pages.ar-aging-report') }}" class="block text-primary-600 hover:underline">AR Aging</a>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
