<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ json_encode($record->toArray()) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-3 cursor-grab shadow-sm hover:shadow-md transition-shadow font-medium text-gray-700 dark:text-gray-200 text-sm select-none"
>
    {{ $record->{$this->recordTitleAttribute} }}
</div>