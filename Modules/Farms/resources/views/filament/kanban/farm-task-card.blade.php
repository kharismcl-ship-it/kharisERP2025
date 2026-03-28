<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ json_encode($record->toArray()) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-3 cursor-grab shadow-sm hover:shadow-md transition-shadow select-none space-y-2"
>
    {{-- Title --}}
    <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm leading-snug">
        {{ $record->title }}
    </div>

    {{-- Priority badge + task type --}}
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $priorityClasses = match ($record->priority) {
                'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                'high'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                'medium' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                default  => 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300',
            };
        @endphp
        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $priorityClasses }}">
            {{ ucfirst($record->priority ?? 'low') }}
        </span>

        @if ($record->task_type)
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ str($record->task_type)->replace('_', ' ')->title() }}
            </span>
        @endif
    </div>

    {{-- Due date --}}
    @if ($record->due_date)
        @php
            $isOverdue = ! $record->completed_at && now()->gt($record->due_date);
        @endphp
        <div class="flex items-center gap-1 text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
            <x-heroicon-o-calendar class="w-3.5 h-3.5 flex-shrink-0" />
            <span>{{ \Carbon\Carbon::parse($record->due_date)->format('d M Y') }}</span>
            @if ($isOverdue)
                <span class="font-semibold">&middot; Overdue</span>
            @endif
        </div>
    @endif

    {{-- Assigned worker --}}
    <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
        <x-heroicon-o-user class="w-3.5 h-3.5 flex-shrink-0" />
        <span>
            @if ($record->relationLoaded('assignedWorker') && $record->assignedWorker)
                {{ $record->assignedWorker->display_name }}
            @elseif ($record->assigned_to_worker_id)
                {{ \Modules\Farms\Models\FarmWorker::find($record->assigned_to_worker_id)?->display_name ?? 'Worker #' . $record->assigned_to_worker_id }}
            @else
                <em>Unassigned</em>
            @endif
        </span>
    </div>

    {{-- Farm name --}}
    @if ($record->relationLoaded('farm') && $record->farm)
        <div class="text-xs text-gray-400 dark:text-gray-500 truncate">
            {{ $record->farm->name }}
        </div>
    @endif
</div>