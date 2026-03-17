<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Organisation Chart</h1>
        <p class="text-sm text-gray-500 mt-1">Reporting structure for your company</p>
    </div>

    @if($roots->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-700 text-sm">
            No employees with a reporting structure found. Set the "Reporting To" field on employee records to build the chart.
        </div>
    @else
        {{-- Horizontal scroll wrapper for wide trees --}}
        <div class="overflow-x-auto pb-4">
            <ul class="flex flex-col gap-0">
                @foreach($roots as $employee)
                    @include('hr::livewire.org-chart._node', ['employee' => $employee, 'depth' => 0])
                @endforeach
            </ul>
        </div>
    @endif
</div>
