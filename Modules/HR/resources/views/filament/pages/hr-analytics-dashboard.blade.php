<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Employees --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900">
                    <x-filament::icon icon="heroicon-o-users" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_employees'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Active --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-success-100 text-success-600 dark:bg-success-900">
                    <x-filament::icon icon="heroicon-o-user-check" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Employees</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_employees'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- On Leave --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-warning-100 text-warning-600 dark:bg-warning-900">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">On Leave Today</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['on_leave_today'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Pending Leaves --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-info-100 text-info-600 dark:bg-info-900">
                    <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Leave Requests</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_leave_count'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Open Vacancies --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900">
                    <x-filament::icon icon="heroicon-o-briefcase" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Open Vacancies</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['open_vacancies'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Active Loans --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-danger-100 text-danger-600 dark:bg-danger-900">
                    <x-filament::icon icon="heroicon-o-credit-card" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Loans</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_loans'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Active Training --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-success-100 text-success-600 dark:bg-success-900">
                    <x-filament::icon icon="heroicon-o-academic-cap" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Training Programs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_training'] }}</p>
                </div>
            </div>
        </x-filament::card>

        {{-- Last Payroll --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800">
                    <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Paid Payroll</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $stats['last_payroll'] ? $stats['last_payroll']->period_label : 'None' }}
                    </p>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>