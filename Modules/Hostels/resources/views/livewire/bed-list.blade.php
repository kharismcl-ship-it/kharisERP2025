<div>
    <h2 class="text-2xl font-bold">Beds in {{ $room->name }}</h2>

    @forelse ($beds as $bed)
        <div class="mt-4">
            <p>{{ $bed->name }}</p>
        </div>
    @empty
        <p class="mt-4">No beds found in this room.</p>
    @endforelse

    <div class="mt-4">
        {{ $beds->links() }}
    </div>
</div>
