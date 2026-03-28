<x-filament-panels::page>
    <div x-data wire:ignore.self class="flex overflow-x-auto overflow-y-hidden gap-4 pb-4 min-h-[70vh]">
        @foreach($statuses as $status)
            @include($this->statusView)
        @endforeach

        <div wire:ignore>
            @include($this->scriptsView)
        </div>
    </div>

    @unless($disableEditModal)
        <x-pcm-filament-kanban::edit-record-modal/>
    @endunless
</x-filament-panels::page>