<x-filament-panels::page>
    @php
        $statuses      = $this->getKanbanStatuses();
        $filterBarView = $this->getKanbanFilterBarView();
    @endphp

    {{-- Optional per-board filter bar (e.g. project selector) --}}
    @if($filterBarView)
        <div class="mb-4">
            @include($filterBarView['view'], $filterBarView['props'] ?? [])
        </div>
    @endif

    {{-- Search bar --}}
    <div class="mb-5 max-w-xs">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Search…"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
        >
    </div>

    {{-- Board --}}
    <div
        x-data="{
            dragging: null,
            dragOver: null,
            startDrag(id) { this.dragging = id; },
            endDrag() { this.dragging = null; this.dragOver = null; },
            onDrop(status) {
                if (this.dragging !== null) {
                    $wire.dispatch('kanban-card-moved', { recordId: this.dragging, newStatus: status });
                    this.dragging = null;
                    this.dragOver = null;
                }
            }
        }"
    >
        <div class="overflow-x-auto pb-4">
            <div class="flex gap-4" style="min-width: max-content;">

                @foreach($statuses as $status)
                    @php
                        $records     = $this->getKanbanRecords($status['key']);
                        $borderClass = $status['border_class'] ?? 'border-gray-400';
                        $dotColor    = $status['dot_color'] ?? 'bg-gray-400';
                    @endphp

                    <div
                        class="flex flex-col rounded-xl bg-gray-100 dark:bg-gray-800 border-t-4 {{ $borderClass }} p-3"
                        style="width: 256px; min-height: 360px;"
                        x-on:dragover.prevent="dragOver = '{{ $status['key'] }}'"
                        x-on:dragleave.self="if (dragOver === '{{ $status['key'] }}') dragOver = null"
                        x-on:drop.prevent="onDrop('{{ $status['key'] }}')"
                        :class="{ 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-950': dragOver === '{{ $status['key'] }}' }"
                    >
                        {{-- Column header --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-block w-2 h-2 rounded-full shrink-0 {{ $dotColor }}"></span>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-600 dark:text-gray-300 truncate">
                                    {{ $status['label'] }}
                                </h3>
                            </div>
                            <span class="ml-2 shrink-0 rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs font-bold text-gray-500 dark:text-gray-300 shadow-sm">
                                {{ $records->count() }}
                            </span>
                        </div>

                        {{-- Cards --}}
                        <div class="flex flex-col gap-2 flex-1">
                            @forelse($records as $record)
                                <div
                                    draggable="true"
                                    x-on:dragstart="startDrag({{ $record->getKey() }})"
                                    x-on:dragend="endDrag()"
                                    :class="{ 'opacity-50 scale-95': dragging === {{ $record->getKey() }} }"
                                    class="cursor-grab rounded-lg bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-shadow select-none overflow-hidden"
                                >
                                    @include($this->getCardView(), ['record' => $record, 'status' => $status])
                                </div>
                            @empty
                                <div class="flex flex-1 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 py-10" style="min-height: 80px;">
                                    <p class="text-xs text-gray-400 dark:text-gray-600">Drop cards here</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-filament-panels::page>
