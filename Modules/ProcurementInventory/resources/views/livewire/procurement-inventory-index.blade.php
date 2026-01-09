<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Procurement & Inventory</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <ul class="list-disc pl-6">
            @forelse ($items as $item)
                <li>{{ $item->name }} <span class="text-xs text-gray-500">({{ $item->sku }})</span></li>
            @empty
                <li>No items found.</li>
            @endforelse
        </ul>
    </div>
</div>
