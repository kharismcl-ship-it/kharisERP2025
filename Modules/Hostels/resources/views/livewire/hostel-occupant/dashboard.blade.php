<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Hostel Occupant Dashboard</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <div class="text-blue-800 font-bold text-2xl">{{ $this->stats['active_bookings'] }}</div>
                        <div class="text-blue-600">Active Bookings</div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <div class="text-yellow-800 font-bold text-2xl">{{ $this->stats['pending_bookings'] }}</div>
                        <div class="text-yellow-600">Pending Bookings</div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="text-green-800 font-bold text-2xl">{{ $this->stats['open_maintenance'] }}</div>
                        <div class="text-green-600">Open Maintenance Requests</div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Recent Bookings</h3>
                        <a href="{{ route('hostel_occupant.bookings.create') }}" class="text-blue-600 hover:underline">Create New Booking</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('hostel_occupant.bookings.show', $booking) }}" class="text-blue-600 hover:underline">
                                                {{ $booking->booking_reference }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->hostel->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->room->room_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $booking->check_in_date->format('M j, Y') }} - {{ $booking->check_out_date->format('M j, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No bookings found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>