<div class="p-3">
    <div class="flex items-start gap-2.5">
        <div class="shrink-0 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-300 uppercase ring-1 ring-primary-200 dark:ring-primary-800">
            {{ mb_strtoupper(mb_substr($record->first_name ?? '', 0, 1) . mb_substr($record->last_name ?? '', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate leading-snug">
                {{ $record->full_name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                {{ $record->jobVacancy?->title ?? '—' }}
            </p>
        </div>
    </div>
    <div class="mt-2.5 flex items-center justify-between">
        <span class="text-xs text-gray-400 dark:text-gray-500">
            {{ $record->applied_date?->format('d M Y') ?? '—' }}
        </span>
        @if($record->source)
            <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-300 font-medium">
                {{ \Modules\HR\Models\Applicant::SOURCES[$record->source] ?? $record->source }}
            </span>
        @endif
    </div>
</div>
