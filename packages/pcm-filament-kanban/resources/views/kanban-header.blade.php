<div class="mb-2 px-1 flex items-center justify-between">
    <h3 class="font-semibold text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400">
        {{ $status['title'] }}
    </h3>
    <span class="text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full px-2 py-0.5">
        {{ count($status['records']) }}
    </span>
</div>