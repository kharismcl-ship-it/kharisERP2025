@php
    $children = $allEmployees->filter(fn ($e) => $e->reporting_to_employee_id == $employee->id)->values();
    $initials = strtoupper(substr($employee->first_name ?? '', 0, 1) . substr($employee->last_name ?? '', 0, 1));
@endphp

<div class="flex flex-col items-center">
    {{-- Employee Card --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-3 w-40 text-center hover:shadow-md transition-shadow">
        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 font-bold text-sm flex items-center justify-center mx-auto mb-2">
            {{ $initials ?: '?' }}
        </div>
        <p class="text-xs font-semibold text-gray-800 dark:text-white truncate" title="{{ $employee->full_name ?? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) }}">
            {{ $employee->full_name ?? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" title="{{ $employee->job_title ?? $employee->jobPosition?->title ?? '' }}">
            {{ $employee->job_title ?? $employee->jobPosition?->title ?? '—' }}
        </p>
        @if($employee->department?->name)
            <p class="text-xs text-primary-500 dark:text-primary-400 truncate mt-0.5">
                {{ $employee->department->name }}
            </p>
        @endif
    </div>

    @if($children->isNotEmpty())
        {{-- Vertical connector to horizontal bar --}}
        <div class="w-0.5 h-4 bg-gray-300 dark:bg-gray-600"></div>

        {{-- Children container --}}
        <div class="flex gap-6 relative">
            {{-- Horizontal bar across children (only if more than one) --}}
            @if($children->count() > 1)
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gray-300 dark:bg-gray-600" style="left: 50%; transform: translateX(-50%); width: calc(100% - 80px);"></div>
            @endif

            @foreach($children as $child)
                <div class="flex flex-col items-center">
                    {{-- Vertical connector from bar to child card --}}
                    <div class="w-0.5 h-4 bg-gray-300 dark:bg-gray-600"></div>
                    @include('hr::livewire.org-chart._node', [
                        'employee'     => $child,
                        'allEmployees' => $allEmployees,
                    ])
                </div>
            @endforeach
        </div>
    @endif
</div>