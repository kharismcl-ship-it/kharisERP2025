<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if (!empty($this->importResults))
            <x-filament::section>
                <x-slot name="heading">Import Results</x-slot>

                <div class="space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Requisitions Created:</strong> {{ $this->importResults['processed'] }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Rows Failed:</strong> {{ $this->importResults['failed'] }}
                    </p>

                    @if (!empty($this->importResults['errors']))
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-danger-600">Errors:</p>
                            <ul class="mt-2 space-y-1 list-disc list-inside text-sm text-danger-500">
                                @foreach ($this->importResults['errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>