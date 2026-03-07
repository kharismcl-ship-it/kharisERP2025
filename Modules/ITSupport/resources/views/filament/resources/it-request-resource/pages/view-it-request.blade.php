<x-filament-panels::page>
    {{ $this->infolist }}

    @if ($this->record)
        <div class="mt-6">
            <livewire:commentions::comments :model="$this->record" />
        </div>
    @endif
</x-filament-panels::page>
