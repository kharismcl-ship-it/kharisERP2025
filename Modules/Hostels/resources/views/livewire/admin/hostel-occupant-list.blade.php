<div>
    <h2 class="text-2xl font-bold">Hostel Occupants</h2>

    @forelse ($hostelOccupants as $hostelOccupant)
        <div class="mt-4">
            <p>{{ $hostelOccupant->name }}</p>
        </div>
    @empty
        <p class="mt-4">No hostel occupants found.</p>
    @endforelse

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</div>
