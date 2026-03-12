<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Crop Cycles</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                <option value="">All Statuses</option>
                <option value="planned">Planned</option>
                <option value="growing">Growing</option>
                <option value="harvested">Harvested</option>
                <option value="failed">Failed</option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($cycles as $cycle)
                <a href="{{ route('farms.crops.show', [$farm->slug, $cycle]) }}"
                   class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <h2 class="font-semibold text-gray-900">
                            {{ $cycle->crop_name ?? 'Crop Cycle' }}
                            @if($cycle->variety) <span class="text-gray-400 font-normal text-sm">({{ $cycle->variety }})</span> @endif
                        </h2>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            @if($cycle->status === 'growing') bg-green-100 text-green-800
                            @elseif($cycle->status === 'harvested') bg-blue-100 text-blue-800
                            @elseif($cycle->status === 'planned') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($cycle->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 space-y-1">
                        @if($cycle->planting_date)
                            <p>Planted: {{ $cycle->planting_date->format('M j, Y') }}</p>
                        @endif
                        @if($cycle->expected_harvest_date)
                            <p>Expected harvest: {{ $cycle->expected_harvest_date->format('M j, Y') }}</p>
                        @endif
                        @if($cycle->planted_area)
                            <p>Area: {{ $cycle->planted_area }} {{ $cycle->planted_area_unit ?? 'acres' }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center text-gray-400">
                    No crop cycles found.
                </div>
            @endforelse
        </div>

        <div>{{ $cycles->links() }}</div>

    </div>
</div>
