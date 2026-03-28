<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Organisation Chart</h1>
        <p class="text-sm text-gray-500 mt-1">Reporting structure for your company</p>
    </div>

    @if($rootEmployees->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-700 text-sm dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300">
            No employees with a reporting structure found. Set the "Reporting To" field on employee records to build the chart.
        </div>
    @else
        {{-- Horizontal scroll wrapper for wide trees --}}
        <div class="overflow-x-auto pb-8">
            <div class="flex gap-12 justify-center min-w-max py-4">
                @foreach($rootEmployees as $root)
                    @include('hr::livewire.org-chart._node', [
                        'employee'     => $root,
                        'allEmployees' => $allEmployees,
                    ])
                @endforeach
            </div>
        </div>

        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 text-center">
            Showing {{ $allEmployees->count() }} active employees • Scroll horizontally for wide organisations
        </p>
    @endif
</div>