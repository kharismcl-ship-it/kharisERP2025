<x-flux::container class="py-12">
    <div class="text-center mb-12">
        <x-flux::heading size="3xl" class="font-bold">
            Find Your Perfect Hostel
        </x-flux::heading>
        <x-flux::text size="lg" class="mt-4 max-w-xl mx-auto">
            Discover comfortable and affordable accommodation for students and travelers
        </x-flux::text>
    </div>

    <!-- Search and Filters -->
    <x-flux::card class="p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-flux::label for="search">Search</x-flux::label>
                <x-flux::input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Hostel name, code, location..."
                    class="mt-1"
                />
            </div>

            <div>
                <x-flux::label for="location">Location</x-flux::label>
                <x-flux::input
                    type="text"
                    id="location"
                    wire:model.live.debounce.300ms="location"
                    placeholder="City, area..."
                    class="mt-1"
                />
            </div>

            <div>
                <x-flux::label for="genderPolicy">Gender Policy</x-flux::label>
                <x-flux::select
                    id="genderPolicy"
                    wire:model.live="genderPolicy"
                    class="mt-1"
                >
                    <option value="">All Policies</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="mixed">Mixed</option>
                </x-flux::select>
            </div>

            <div class="flex items-end">
                <x-flux::button
                    wire:click="resetFilters"
                    variant="outline"
                    class="w-full"
                >
                    Reset Filters
                </x-flux::button>
            </div>
        </div>
    </x-flux::card>

    <!-- Hostel Listings -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($hostels as $hostel)
            <x-flux::card class="overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <x-flux::heading size="xl" class="font-bold">
                                {{ $hostel->name }}
                            </x-flux::heading>
                            <x-flux::text size="sm" >
                                {{ $hostel->region }}, {{ $hostel->city }}{{ $hostel->country ? ", {$hostel->country}" : '' }}
                            </x-flux::text>
                        </div>
                        <x-flux::badge 
                            :color="$hostel->gender_policy === 'male' ? 'blue' : ($hostel->gender_policy === 'female' ? 'pink' : 'green')"
                            size="sm"
                        >
                            {{ ucfirst($hostel->gender_policy) }}
                        </x-flux::badge>
                    </div>

                    <div class="mt-4 flex items-center">
                        <x-flux::icon name="map-pin" class="flex-shrink-0 mr-1.5 h-5 w-5"  />
                        <x-flux::text size="sm" >
                            {{ $hostel->code }}
                        </x-flux::text>
                    </div>

                    @if($hostel->description)
                    <x-flux::text size="sm" class="mt-2">
                        {{ Str::limit($hostel->description, 100) }}
                    </x-flux::text>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-flux::text size="sm" class="mt-2">Available Rooms</x-flux::text>
                            <x-flux::heading size="lg" class="font-semibold">
                                {{ $hostel->available_rooms }} / {{ $hostel->total_rooms }}
                            </x-flux::heading>
                        </div>
                        <div>
                            <x-flux::text size="sm">Capacity</x-flux::text>
                            <x-flux::heading size="lg" class="font-semibold">
                                {{ $hostel->capacity ?? 'N/A' }}
                            </x-flux::heading>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-flux::button 
                            :href="route('hostels.public.show', $hostel)" 
                            variant="primary"
                            class="w-full justify-center"
                        >
                            View Details
                        </x-flux::button>
                    </div>
                </div>
            </x-flux::card>
        @empty
            <div class="col-span-3 text-center py-12">
                <x-flux::icon name="building" class="mx-auto h-12 w-12 text-gray-400" />
                <x-flux::heading size="lg" class="mt-2">No hostels found</x-flux::heading>
                <x-flux::text  class="mt-1">Try adjusting your search or filter criteria.</x-flux::text>
                <div class="mt-6">
                    <x-flux::button
                        wire:click="resetFilters"
                        variant="primary"
                    >
                        Reset Filters
                    </x-flux::button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($hostels->hasPages())
        <div class="mt-8">
            {{ $hostels->links() }}
        </div>
    @endif
</x-flux::container>
