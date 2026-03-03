<x-filament-panels::page>
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
        <div class="flex items-center gap-4 mb-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Filter by Project:</label>
            <select
                wire:model.live="selectedProjectId"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
            >
                <option value="">All Projects</option>
                @foreach($this->getProjects() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($this->getColumns() as $column)
                @php $phases = $this->getPhasesByStatus($column['key']); @endphp
                <div
                    class="flex flex-col gap-3 min-h-[200px] rounded-xl bg-gray-100 dark:bg-gray-800 p-3"
                    x-on:dragover.prevent="dragOver = '{{ $column['key'] }}'"
                    x-on:drop.prevent="onDrop('{{ $column['key'] }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $column['key'] }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-200">
                            {{ ucwords(str_replace('_', ' ', $column['key'])) }}
                        </h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            {{ $phases->count() }}
                        </span>
                    </div>

                    @foreach($phases as $phase)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $phase->id }})"
                            x-on:dragend="endDrag()"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-1">{{ $phase->name }}</p>
                            @if($phase->project)
                                <span class="inline-block rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 text-xs px-2 py-0.5 mb-1">
                                    {{ $phase->project->name }}
                                </span>
                            @endif
                            <div class="flex items-center justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $phase->progress_percent ?? 0 }}%</span>
                                <span>{{ $phase->planned_end?->format('d M') ?? '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
