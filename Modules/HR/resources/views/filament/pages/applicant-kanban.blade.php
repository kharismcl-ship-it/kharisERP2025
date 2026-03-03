<x-filament-panels::page>
    <div
        class="space-y-4"
        x-data="{
            dragging: null,
            dragOver: null,
            startDrag(id) { this.dragging = id; },
            endDrag() { this.dragging = null; this.dragOver = null; },
            onDrop(status) {
                if (this.dragging) {
                    $wire.dispatch('applicant-status-changed', { applicantId: this.dragging, newStatus: status });
                    this.endDrag();
                }
            }
        }"
    >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($this->getColumns() as $status)
                @php
                    $applicants = $this->getApplicantsByStatus($status);
                    $statusLabels = \Modules\HR\Models\Applicant::STATUSES;
                @endphp
                <div
                    class="flex flex-col gap-2 min-h-[200px] rounded-xl bg-gray-100 dark:bg-gray-800 p-3"
                    x-on:dragover.prevent="dragOver = '{{ $status }}'"
                    x-on:drop.prevent="onDrop('{{ $status }}')"
                    :class="{ 'ring-2 ring-primary-500': dragOver === '{{ $status }}' }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </h3>
                        <span class="rounded-full bg-white dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-300">
                            {{ $applicants->count() }}
                        </span>
                    </div>

                    @foreach($applicants as $applicant)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $applicant->id }})"
                            x-on:dragend="endDrag()"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                        >
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 truncate">
                                {{ $applicant->full_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                {{ $applicant->jobVacancy?->title ?? '—' }}
                            </p>
                            <div class="flex items-center justify-between mt-2 text-xs text-gray-400 dark:text-gray-500">
                                <span>{{ $applicant->applied_date?->format('d M Y') ?? '—' }}</span>
                                @if($applicant->source)
                                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5">
                                        {{ \Modules\HR\Models\Applicant::SOURCES[$applicant->source] ?? $applicant->source }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
