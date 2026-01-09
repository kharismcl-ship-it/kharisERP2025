<flux:fieldset>
    @if($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <label class="group block border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-indigo-500">
        @if ($value)
            <img src="{{ $value->temporaryUrl() }}" alt="{{ $previewAlt }}" class="mx-auto h-28 w-auto rounded-md shadow-sm" />
            <div class="mt-3 flex items-center justify-center gap-3">
                @if ($remove)
                    <button type="button" wire:click="{{ $remove }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md bg-red-600 text-white hover:bg-red-700">Remove</button>
                @endif
                <span class="text-xs text-gray-500">Click to replace</span>
            </div>
        @else
            <div class="flex flex-col items-center gap-2 py-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-10 w-10 text-gray-400">
                    <path d="M2 6a2 2 0 0 1 2-2h4l1.586 1.586A2 2 0 0 0 11 6h7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6zm6 6a3 3 0 1 0 6 0 3 3 0 0 0-6 0z" />
                </svg>
                <span class="text-sm text-gray-700">{{ $promptHeading }}</span>
                <span class="text-xs text-gray-500">{{ $promptText }}</span>
            </div>
        @endif
        <input type="file" wire:model="{{ $model }}" accept="{{ $accept }}" class="sr-only" />
    </label>
    <div wire:loading wire:target="{{ $model }}" class="mt-2 text-sm text-gray-500">Uploading...</div>

    @error($model)
        <flux:error>{{ $message }}</flux:error>
    @enderror
</flux:fieldset>