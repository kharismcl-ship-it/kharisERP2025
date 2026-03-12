<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Livestock</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                <option value="active">Active</option>
                <option value="sold">Sold</option>
                <option value="deceased">Deceased</option>
                <option value="">All</option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($batches as $batch)
                <a href="{{ route('farms.livestock.show', [$farm->slug, $batch]) }}"
                   class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <h2 class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $batch->animal_type) }}</h2>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            @if($batch->status === 'active') bg-green-100 text-green-800
                            @elseif($batch->status === 'sold') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($batch->status) }}
                        </span>
                    </div>
                    @if($batch->breed)
                        <p class="text-sm text-gray-500 mb-2">{{ $batch->breed }}</p>
                    @endif
                    <div class="text-xs text-gray-500 space-y-1">
                        <p>Count: <span class="font-semibold text-gray-800">{{ number_format($batch->current_count) }}</span></p>
                        @if($batch->batch_reference)
                            <p>Ref: {{ $batch->batch_reference }}</p>
                        @endif
                        @if($batch->acquisition_date)
                            <p>Acquired: {{ $batch->acquisition_date->format('M j, Y') }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center text-gray-400">
                    No livestock batches found.
                </div>
            @endforelse
        </div>

        <div>{{ $batches->links() }}</div>

    </div>
</div>
