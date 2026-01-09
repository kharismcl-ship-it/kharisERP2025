<div>
    <h2 class="text-2xl font-bold">Tenants</h2>

    @forelse ($tenants as $tenant)
        <div class="mt-4">
            <p>{{ $tenant->name }}</p>
        </div>
    @empty
        <p class="mt-4">No tenants found.</p>
    @endforelse

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</div>
