<x-filament-panels::page>
    @php
        $columnMeta = [
            'pending'     => ['border' => 'border-gray-400',    'bg' => 'bg-gray-100 dark:bg-gray-800'],
            'in_progress' => ['border' => 'border-yellow-400',  'bg' => 'bg-yellow-50 dark:bg-yellow-950/30'],
            'on_hold'     => ['border' => 'border-orange-400',  'bg' => 'bg-orange-50 dark:bg-orange-950/30'],
            'completed'   => ['border' => 'border-green-500',   'bg' => 'bg-green-50 dark:bg-green-950/30'],
        ];
    @endphp

    <div
        class="space-y-4"
        x-data="{
            dragging: null,
            dragOver: null,
            startDrag(phaseId) { this.dragging = phaseId; },
            endDrag() { this.dragging = null; this.dragOver = null; },
            onDrop(status) {
                if (this.dragging) {
                    $wire.dispatch('phase-status-changed', { phaseId: this.dragging, newStatus: status });
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
                    $phases = $this->getPhasesByStatus($column['key']);
                    $meta   = $columnMeta[$column['key']] ?? ['border' => 'border-gray-400', 'bg' => 'bg-gray-100 dark:bg-gray-800'];
                @endphp
                <div
                    class="flex flex-col gap-3 min-h-[240px] rounded-xl {{ $meta['bg'] }} border-t-4 {{ $meta['border'] }} p-3"
                    x-on:dragover.prevent="dragOver = '{{ $column['key'] }}'"
                    x-on:drop.prevent="onDrop('{{ $column['key'] }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $column['key'] }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-200">
                            {{ $column['label'] }}
                        </h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300">
                            {{ $phases->count() }}
                        </span>
                    </div>

                    @forelse($phases as $phase)
                        @php
                            $progress  = (int)($phase->progress_percent ?? 0);
                            $isOverdue = $phase->planned_end && $phase->planned_end->isPast() && $column['key'] !== 'completed';
                        @endphp
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $phase->id }})"
                            x-on:dragend="endDrag()"
                            :class="{ 'opacity-50 scale-95': dragging === {{ $phase->id }} }"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all select-none"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-1 leading-tight">{{ $phase->name }}</p>
                            @if($phase->project)
                                <span class="inline-block rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 text-xs px-2 py-0.5 mb-2">
                                    {{ $phase->project->name }}
                                </span>
                            @endif

                            {{-- Progress bar --}}
                            <div class="mt-1 mb-2">
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    <span>Progress</span>
                                    <span class="font-semibold">{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $progress >= 100 ? 'bg-green-500' : ($progress >= 50 ? 'bg-yellow-400' : 'bg-blue-500') }}"
                                         style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400 dark:text-gray-500">
                                    {{ $phase->planned_start?->format('d M') ?? '—' }}
                                </span>
                                <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                                    → {{ $phase->planned_end?->format('d M Y') ?? '—' }}
                                    @if($isOverdue) <span class="text-red-400 ml-0.5">overdue</span> @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-xs text-gray-400 dark:text-gray-600 py-8">
                            No phases
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
        </div>
    </div>
</x-filament-panels::page>
