<x-filament-panels::page>
    {{ $this->infolist }}

    <div class="mt-6">
        <x-commentions::comments :model="$this->record" />
    </div>
</x-filament-panels::page>
