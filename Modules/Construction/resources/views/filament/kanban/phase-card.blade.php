@php
    $progress  = (int) ($record->progress_percent ?? 0);
    $isOverdue = $record->planned_end
        && $record->planned_end->isPast()
        && $status['key'] !== 'completed';
@endphp
<div class="p-3">
    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 leading-snug mb-1.5">
        {{ $record->name }}
    </p>

    @if($record->project)
        <span class="inline-block rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 text-xs px-2 py-0.5 mb-2 font-medium">
            {{ $record->project->name }}
        </span>
    @endif

    {{-- Progress bar --}}
    <div class="mb-2.5">
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
            <span>Progress</span>
            <span class="font-bold">{{ $progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
            <div
                class="h-1.5 rounded-full transition-all duration-300
                    {{ $progress >= 100 ? 'bg-green-500' : ($progress >= 50 ? 'bg-yellow-400' : 'bg-blue-500') }}"
                style="width: {{ min($progress, 100) }}%">
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between text-xs">
        <span class="text-gray-400 dark:text-gray-500">
            {{ $record->planned_start?->format('d M') ?? '—' }}
        </span>
        <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
            → {{ $record->planned_end?->format('d M Y') ?? '—' }}
            @if($isOverdue)
                &bull; overdue
            @endif
        </span>
    </div>
</div>
