<div class="flex flex-wrap gap-2">
    @if(!empty($getPlaceholders))
        @foreach($getPlaceholders as $placeholder)
            <x-filament::button 
                color="gray" 
                size="sm"
                x-data=""
                x-on:click="document.getElementById('template-content').value += '\{\{!! $placeholder !!\}\}'"
                type="button"
                class="cursor-pointer"
            >
                \{\{!! $placeholder !!\}\}
            </x-filament::button>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">Select a template code to see available placeholders</p>
    @endif
</div>