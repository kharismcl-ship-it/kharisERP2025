<x-filament-panels::page>
    @php
        $columnColors = [
            'new'        => 'border-blue-400',
            'screening'  => 'border-yellow-400',
            'interview'  => 'border-purple-400',
            'assessment' => 'border-indigo-400',
            'offer'      => 'border-teal-400',
            'hired'      => 'border-green-500',
            'rejected'   => 'border-red-400',
        ];
        $badgeColors = [
            'new'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'screening'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
            'interview'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
            'assessment' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'offer'      => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300',
            'hired'      => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            'rejected'   => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        ];
    @endphp

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
        <div class="overflow-x-auto pb-2">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" style="min-width: 640px;">
            @foreach($this->getColumns() as $status)
                @php
                    $applicants  = $this->getApplicantsByStatus($status);
                    $statusLabels = \Modules\HR\Models\Applicant::STATUSES;
                    $borderColor = $columnColors[$status] ?? 'border-gray-400';
                    $badgeColor  = $badgeColors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';
                @endphp
                <div
                    class="flex flex-col gap-2 min-h-[240px] rounded-xl bg-gray-100 dark:bg-gray-800 border-t-4 {{ $borderColor }} p-3"
                    x-on:dragover.prevent="dragOver = '{{ $status }}'"
                    x-on:drop.prevent="onDrop('{{ $status }}')"
                    :class="{ 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20': dragOver === '{{ $status }}' }"
                >
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </h3>
                        <span class="rounded-full {{ $badgeColor }} px-2 py-0.5 text-xs font-semibold">
                            {{ $applicants->count() }}
                        </span>
                    </div>

                    @forelse($applicants as $applicant)
                        <div
                            draggable="true"
                            x-on:dragstart="startDrag({{ $applicant->id }})"
                            x-on:dragend="endDrag()"
                            :class="{ 'opacity-50 scale-95': dragging === {{ $applicant->id }} }"
                            class="cursor-grab rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all select-none"
                        >
                            <div class="flex items-start gap-2">
                                <div class="shrink-0 w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-300 uppercase">
                                    {{ mb_substr($applicant->first_name ?? $applicant->full_name, 0, 1) }}{{ mb_substr($applicant->last_name ?? '', 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm text-gray-800 dark:text-gray-100 truncate leading-tight">
                                        {{ $applicant->full_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                        {{ $applicant->jobVacancy?->title ?? '—' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-2 text-xs text-gray-400 dark:text-gray-500">
                                <span>{{ $applicant->applied_date?->format('d M Y') ?? '—' }}</span>
                                @if($applicant->source)
                                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-gray-600 dark:text-gray-300">
                                        {{ \Modules\HR\Models\Applicant::SOURCES[$applicant->source] ?? $applicant->source }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-xs text-gray-400 dark:text-gray-600 py-8">
                            No applicants
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
        </div>
    </div>
</x-filament-panels::page>
