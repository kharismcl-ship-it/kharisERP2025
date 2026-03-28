{{--
    v4 FIX SUMMARY:
    1. Removed <x-filament-panels::form> wrapper — no longer exists in v4; use plain <form> with wire:submit
    2. Changed :slideOver= to :slide-over= (kebab-case in v4)
    3. Replaced <x-slot name="header"> with <x-slot name="heading"> (v4 modal API)
    4. Replaced <x-slot name="footer"> with <x-slot name="footerActions"> (v4 modal API)
    5. x-on:click="isOpen = false" → $dispatch('close-modal', {id: 'kanban--edit-record-modal'})
--}}
<x-filament::modal
    id="kanban--edit-record-modal"
    :slide-over="$this->getEditModalSlideOver()"
    :width="$this->getEditModalWidth()"
>
    <x-slot name="heading">
        {{ $this->getEditModalTitle() }}
    </x-slot>

    <form wire:submit="editModalFormSubmitted">
        {{ $this->form }}

        <div class="flex justify-end gap-3 mt-6">
            <x-filament::button type="submit" color="primary">
                {{ $this->getEditModalSaveButtonLabel() }}
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                x-on:click="$dispatch('close-modal', {id: 'kanban--edit-record-modal'})"
            >
                {{ $this->getEditModalCancelButtonLabel() }}
            </x-filament::button>
        </div>
    </form>
</x-filament::modal>