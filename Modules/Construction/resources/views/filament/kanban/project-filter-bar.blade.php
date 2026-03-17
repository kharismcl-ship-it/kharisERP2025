<div class="flex items-center gap-3 flex-wrap">
    <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
        Filter by Project:
    </label>
    <select
        wire:model.live="selectedProjectId"
        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-white text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
    >
        <option value="">All Projects</option>
        @foreach($projects as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
</div>
