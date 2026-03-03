<x-filament-panels::page>
    @php
        $stageColors = [
            'lead'          => 'border-gray-400',
            'qualified'     => 'border-blue-400',
            'proposal'      => 'border-indigo-400',
            'negotiation'   => 'border-yellow-400',
            'won'           => 'border-green-500',
            'lost'          => 'border-red-400',
        ];
        $stageHeaderColors = [
            'lead'        => 'bg-gray-100 dark:bg-gray-800',
            'qualified'   => 'bg-blue-50 dark:bg-blue-950/40',
            'proposal'    => 'bg-indigo-50 dark:bg-indigo-950/40',
            'negotiation' => 'bg-yellow-50 dark:bg-yellow-950/40',
            'won'         => 'bg-green-50 dark:bg-green-950/40',
            'lost'        => 'bg-red-50 dark:bg-red-950/40',
        ];
    @endphp

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
        <div class="overflow-x-auto pb-2">
        <div class="flex gap-3" style="min-width: max-content;">
            @foreach($this->getColumns() as $stage)
                @php
                    $opps        = $this->getOpportunitiesByStage($stage);
                    $totalValue  = $opps->sum('estimated_value');
                    $borderColor = $stageColors[$stage] ?? 'border-gray-400';
                    $bgColor     = $stageHeaderColors[$stage] ?? 'bg-gray-100 dark:bg-gray-800';
                @endphp
                <div
                    class="flex flex-col gap-2 min-h-[240px] w-56 rounded-xl {{ $bgColor }} border-t-4 {{ $borderColor }} p-3 shrink-0"
                    x-on:dragover.prevent="dragOver = '{{ $stage }}'"
                    x-on:drop.prevent="onDrop('{{ $stage }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $stage }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            {{ ucwords(str_replace('_', ' ', $stage)) }}
                        </h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300">
                            {{ $opps->count() }}
                        </span>
                    </div>
                    @if($totalValue > 0)
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-2">
                        GHS {{ number_format((float)$totalValue, 0) }}
                    </div>
                    @endif

                    @forelse($opps as $opp)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $opp->id }})"
                            x-on:dragend="endDrag()"
                            :class="{ 'opacity-50 scale-95': dragging === {{ $opp->id }} }"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all select-none"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 truncate leading-tight">{{ $opp->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                {{ $opp->contact?->full_name ?? '—' }}
                            </p>
                            <div class="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">
                                        GHS {{ number_format((float)$opp->estimated_value, 0) }}
                                    </span>
                                    @php $prob = (int)($opp->probability_pct ?? 0); @endphp
                                    <span class="rounded-full px-2 py-0.5 font-semibold
                                        {{ $prob >= 70 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' :
                                          ($prob >= 40 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' :
                                                         'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300') }}">
                                        {{ $prob }}%
                                    </span>
                                </div>
                                {{-- Probability bar --}}
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                    <div class="h-1 rounded-full {{ $prob >= 70 ? 'bg-green-500' : ($prob >= 40 ? 'bg-yellow-400' : 'bg-red-400') }}"
                                         style="width: {{ $prob }}%"></div>
                                </div>
                                @if($opp->expected_close_date)
                                    <p class="{{ $opp->expected_close_date->isPast() && $stage !== 'won' && $stage !== 'lost' ? 'text-red-500 font-semibold' : '' }}">
                                        Close: {{ $opp->expected_close_date->format('d M Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-xs text-gray-400 dark:text-gray-600 py-8">
                            No opportunities
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
        </div>
    </div>
</x-filament-panels::page>
