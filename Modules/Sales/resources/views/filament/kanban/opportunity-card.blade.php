@php
    $prob = (int) ($record->probability_pct ?? 0);
    $isWonOrLost = in_array($status['key'], ['closed_won', 'closed_lost']);
    $closeDate = $record->expected_close_date
        ? (\Carbon\Carbon::instance($record->expected_close_date))
        : null;
    $isOverdue = $closeDate && $closeDate->isPast() && ! $isWonOrLost;
@endphp
<div class="p-3">
    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate leading-snug">
        {{ $record->title }}
    </p>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
        {{ $record->contact?->full_name ?? '—' }}
    </p>

    <div class="mt-2.5 space-y-1.5">
        {{-- Value + probability --}}
        <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-gray-700 dark:text-gray-200">
                GHS {{ number_format((float) ($record->estimated_value ?? 0), 0) }}
            </span>
            <span class="rounded-full px-2 py-0.5 text-xs font-semibold
                {{ $prob >= 70 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' :
                   ($prob >= 40 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' :
                                  'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300') }}">
                {{ $prob }}%
            </span>
        </div>

        {{-- Probability bar --}}
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
            <div class="h-1 rounded-full transition-all duration-300
                {{ $prob >= 70 ? 'bg-green-500' : ($prob >= 40 ? 'bg-yellow-400' : 'bg-red-400') }}"
                 style="width: {{ min($prob, 100) }}%">
            </div>
        </div>

        @if($closeDate)
            <p class="text-xs {{ $isOverdue ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                Close: {{ $closeDate->format('d M Y') }}
                @if($isOverdue)
                    &bull; overdue
                @endif
            </p>
        @endif
    </div>
</div>
