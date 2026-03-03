<x-filament-panels::page>
    @php
        $columnMeta = [
            'pending'     => ['border' => 'border-gray-400',   'bg' => 'bg-gray-100 dark:bg-gray-800'],
            'in_progress' => ['border' => 'border-yellow-400', 'bg' => 'bg-yellow-50 dark:bg-yellow-950/30'],
            'blocked'     => ['border' => 'border-red-400',    'bg' => 'bg-red-50 dark:bg-red-950/30'],
            'completed'   => ['border' => 'border-green-500',  'bg' => 'bg-green-50 dark:bg-green-950/30'],
        ];
        $priorityBadge = [
            '4' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
            '3' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
            '2' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            '1' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        ];
        $priorityLabels = ['4' => 'Urgent', '3' => 'High', '2' => 'Medium', '1' => 'Low'];
    @endphp

    <div
        class="space-y-4"
        x-data="{
            dragging: null,
            dragOver: null,
            startDrag(taskId) { this.dragging = taskId; },
            endDrag() { this.dragging = null; this.dragOver = null; },
            onDrop(status) {
                if (this.dragging) {
                    $wire.dispatch('task-status-changed', { taskId: this.dragging, newStatus: status });
                    this.endDrag();
                }
            }
        }"
    >
        {{-- Project filter --}}
        <div class="flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Filter by Project:</label>
            <select
                wire:model.live="selectedProjectId"
                class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
                <option value="">All Projects</option>
                @foreach($this->getProjects() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Kanban columns --}}
        <div class="overflow-x-auto pb-2">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4" style="min-width: 640px;">
            @foreach($this->getColumns() as $column)
                @php
                    $tasks = $this->getTasksByStatus($column['key']);
                    $meta  = $columnMeta[$column['key']] ?? ['border' => 'border-gray-400', 'bg' => 'bg-gray-100 dark:bg-gray-800'];
                @endphp
                <div
                    class="flex flex-col gap-3 min-h-[240px] rounded-xl {{ $meta['bg'] }} border-t-4 {{ $meta['border'] }} p-3"
                    x-on:dragover.prevent="dragOver = '{{ $column['key'] }}'"
                    x-on:drop.prevent="onDrop('{{ $column['key'] }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $column['key'] }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-200">{{ $column['label'] }}</h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300">
                            {{ $tasks->count() }}
                        </span>
                    </div>

                    @forelse($tasks as $task)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $task->id }})"
                            x-on:dragend="endDrag()"
                            :class="{ 'opacity-50 scale-95': dragging === {{ $task->id }} }"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all select-none"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-1 leading-tight">{{ $task->name }}</p>
                            @if($task->project)
                                <span class="inline-block rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 text-xs px-2 py-0.5 mb-2">
                                    {{ $task->project->name }}
                                </span>
                            @endif
                            <div class="flex items-center justify-between mt-1 text-xs">
                                @php
                                    $p = (string)($task->priority ?? '1');
                                    $badgeClass = $priorityBadge[$p] ?? 'bg-gray-100 text-gray-600';
                                    $isOverdue = $task->due_date && $task->due_date->isPast() && $column['key'] !== 'completed';
                                @endphp
                                <span class="rounded-full px-2 py-0.5 font-semibold {{ $badgeClass }}">
                                    {{ $priorityLabels[$p] ?? "P{$p}" }}
                                </span>
                                <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ $task->due_date?->format('d M') ?? '—' }}
                                    @if($isOverdue) <span class="text-red-400">&bull;</span> @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 truncate">
                                {{ $task->contractor?->name ?? 'Unassigned' }}
                            </p>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-xs text-gray-400 dark:text-gray-600 py-8">
                            No tasks
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
        </div>
    </div>
</x-filament-panels::page>
