<x-filament-panels::page>
    @if(count($alerts))
    <div class="space-y-2 mb-4">
        @foreach($alerts as $alert)
        <x-filament::section>
            <div class="flex items-center gap-3
                @if($alert['type'] === 'danger') text-danger-700 dark:text-danger-400
                @elseif($alert['type'] === 'warning') text-warning-700 dark:text-warning-400
                @else text-info-700 dark:text-info-400 @endif">
                @if($alert['type'] === 'danger')
                    <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0"/>
                @elseif($alert['type'] === 'warning')
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0"/>
                @else
                    <x-heroicon-o-information-circle class="w-5 h-5 shrink-0"/>
                @endif
                <span class="text-sm font-medium">{{ $alert['message'] }}</span>
            </div>
        </x-filament::section>
        @endforeach
    </div>
    @endif

    <p class="text-xs text-gray-400 text-right mt-2">Data for {{ now()->year }}. Dashboard refreshes on page load.</p>
</x-filament-panels::page>
