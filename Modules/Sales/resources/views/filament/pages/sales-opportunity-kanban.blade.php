<x-filament-panels::page>
    <div
        class="space-y-4"
        x-data="{
            dragging: null,
            dragOver: null,
            startDrag(id) { this.dragging = id; },
            endDrag() { this.dragging = null; this.dragOver = null; },
            onDrop(stage) {
                if (this.dragging) {
                    $wire.dispatch('opportunity-stage-changed', { opportunityId: this.dragging, newStage: stage });
                    this.endDrag();
                }
            }
        }"
    >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach($this->getColumns() as $stage)
                @php $opps = $this->getOpportunitiesByStage($stage); @endphp
                <div
                    class="flex flex-col gap-2 min-h-[200px] rounded-xl bg-gray-100 dark:bg-gray-800 p-3"
                    x-on:dragover.prevent="dragOver = '{{ $stage }}'"
                    x-on:drop.prevent="onDrop('{{ $stage }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $stage }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            {{ ucwords(str_replace('_', ' ', $stage)) }}
                        </h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-300">
                            {{ $opps->count() }}
                        </span>
                    </div>

                    @foreach($opps as $opp)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $opp->id }})"
                            x-on:dragend="endDrag()"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 truncate">{{ $opp->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $opp->contact?->full_name ?? '—' }}
                            </p>
                            <div class="mt-2 space-y-0.5 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>GHS {{ number_format((float)$opp->estimated_value, 0) }}</span>
                                    <span>{{ $opp->probability_pct ?? 0 }}%</span>
                                </div>
                                <p>{{ $opp->expected_close_date?->format('d M Y') ?? '—' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
