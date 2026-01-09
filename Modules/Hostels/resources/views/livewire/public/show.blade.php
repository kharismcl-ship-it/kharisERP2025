<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                    <div class="mt-2 flex items-center text-gray-600">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        {{ $hostel->region }}, {{ $hostel->city }}
                    </div>
                    <div class="mt-2 flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $hostel->gender_policy === 'male' ? 'blue' : ($hostel->gender_policy === 'female' ? 'pink' : 'green') }}-100 text-{{ $hostel->gender_policy === 'male' ? 'blue' : ($hostel->gender_policy === 'female' ? 'pink' : 'green') }}-800">
                            {{ ucfirst($hostel->gender_policy) }} Hostel
                        </span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('hostels.public.booking', $hostel) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Book Now
                    </a>
                </div>
            </div>

            @if($hostel->notes)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900">About This Hostel</h3>
                    <p class="mt-2 text-gray-600">{{ $hostel->notes }}</p>
                </div>
            @endif

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-900">Available Rooms</h2>

                <!-- Room Filters -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="selectedRoomType" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <select
                            id="selectedRoomType"
                            wire:model.live="selectedRoomType"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">All Room Types</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="selectedGenderPolicy" class="block text-sm font-medium text-gray-700">Gender Policy</label>
                        <select
                            id="selectedGenderPolicy"
                            wire:model.live="selectedGenderPolicy"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">All Policies</option>
                            @foreach($genderPolicies as $policy)
                                <option value="{{ $policy }}">{{ ucfirst($policy) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Room Listings -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($this->rooms as $room)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors duration-200">
                            <div class="flex justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Room {{ $room->room_number }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $room->type)) }}
                                </span>
                            </div>

                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                </svg>
                                {{ $room->max_occupancy ?? 'N/A' }} Max Occupancy
                            </div>

                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                {{ $room->billing_cycle === 'per_night' ? 'Per Night' : ($room->billing_cycle === 'per_semester' ? 'Per Semester' : 'Per Year') }}
                            </div>

                            <div class="mt-4">
                                <p class="text-xl font-bold text-gray-900">
                                    {{ number_format($room->base_rate, 2) }}
                                    <span class="text-sm font-normal text-gray-500">
                                        {{ $room->billing_cycle === 'per_night' ? '/night' : ($room->billing_cycle === 'per_semester' ? '/semester' : '/year') }}
                                    </span>
                                </p>
                            </div>

                            <div class="mt-4">
                                @if($room->gender_policy)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $room->gender_policy === 'male' ? 'blue' : ($room->gender_policy === 'female' ? 'pink' : 'green') }}-100 text-{{ $room->gender_policy === 'male' ? 'blue' : ($room->gender_policy === 'female' ? 'pink' : 'green') }}-800">
                                        {{ ucfirst($room->gender_policy) }}
                                    </span>
                                @endif
                            </div>

                            @if($room->beds->count() > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-900">Available Beds</h4>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($room->beds as $bed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $bed->bed_number }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Book Now Button for this specific room -->
                            <div class="mt-4">
                                <a href="{{ route('hostels.public.booking', $hostel) }}?room={{ $room->id }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Book This Room
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">No rooms available</h3>
                            <p class="mt-1 text-gray-500">There are currently no rooms available matching your criteria.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
