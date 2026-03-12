<div>
    <h2 class="text-2xl font-bold">Bookings</h2>

    @forelse ($bookings as $booking)
        <div class="mt-4">
            <p>Booking for {{ $booking->tenant->name }} in bed {{ $booking->bed->name }}</p>
        </div>
    @empty
        <p class="mt-4">No bookings found.</p>
    @endforelse

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
