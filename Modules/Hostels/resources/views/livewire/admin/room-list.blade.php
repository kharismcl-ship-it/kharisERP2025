<div>
    <h2 class="text-2xl font-bold">Rooms in {{ $hostel->name }}</h2>

    @forelse ($rooms as $room)
        <div class="mt-4">
            <a href="#" class="text-blue-500 hover:underline">{{ $room->name }}</a>
        </div>
    @empty
        <p class="mt-4">No rooms found in this hostel.</p>
    @endforelse

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
