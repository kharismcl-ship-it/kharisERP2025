@php
    $priorityBadge = [
        '4' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        '3' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
        '2' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        '1' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    ];
    $priorityLabels = ['4' => 'Urgent', '3' => 'High', '2' => 'Medium', '1' => 'Low'];
    $p         = (string) ($record->priority ?? '1');
    $badgeClass = $priorityBadge[$p] ?? 'bg-gray-100 text-gray-600';
    $isOverdue  = $record->due_date && $record->due_date->isPast() && $status['key'] !== 'completed';
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

    <div class="flex items-center justify-between text-xs mt-1">
        <span class="rounded-full px-2 py-0.5 font-semibold {{ $badgeClass }}">
            {{ $priorityLabels[$p] ?? "P{$p}" }}
        </span>
        <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
            {{ $record->due_date?->format('d M') ?? '—' }}
            @if($isOverdue) &bull; @endif
        </span>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 truncate">
        {{ $record->contractor?->name ?? 'Unassigned' }}
    </p>
</div>
