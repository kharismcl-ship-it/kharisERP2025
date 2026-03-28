<div class="w-72 flex-shrink-0 mb-5 flex flex-col">
    @include($this->headerView)

    <div
        data-status-id="{{ $status['id'] }}"
        class="flex flex-col flex-1 gap-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-xl min-h-[8rem]"
    >
        @foreach($status['records'] as $record)
            @include($this->recordView)
        @endforeach
    </div>
</div>