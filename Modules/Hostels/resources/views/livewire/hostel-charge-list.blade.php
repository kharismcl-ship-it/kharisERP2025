<div>
    <h2 class="text-2xl font-bold">Fee Types</h2>

    @forelse ($feeTypes as $feeType)
        <div class="mt-4">
            <p>{{ $feeType->name }} - {{ $feeType->amount }}</p>
        </div>
    @empty
        <p class="mt-4">No fee types found.</p>
    @endforelse

    <div class="mt-4">
        {{ $feeTypes->links() }}
    </div>
</div>
