<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">My Farms</h1>

        @if($farms->isEmpty())
            <div class="bg-white shadow sm:rounded-lg p-12 text-center text-gray-400">
                No farms found. Contact your administrator to set up farms.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($farms as $farm)
                    <a href="{{ route('farms.dashboard', $farm->slug) }}"
                       class="bg-white shadow sm:rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $farm->name }}</h2>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($farm->status === 'active') bg-green-100 text-green-800
                                @elseif($farm->status === 'fallow') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($farm->status) }}
                            </span>
                        </div>
                        @if($farm->location)
                            <p class="text-sm text-gray-500 mb-2">{{ $farm->location }}</p>
                        @endif
                        @if($farm->total_area)
                            <p class="text-xs text-gray-400">
                                {{ number_format($farm->total_area, 1) }} {{ $farm->area_unit ?? 'acres' }}
                                &bull; {{ ucfirst($farm->type ?? 'mixed') }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
