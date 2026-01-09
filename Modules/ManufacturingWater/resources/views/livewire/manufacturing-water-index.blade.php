<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Manufacturing — Water</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <ul class="list-disc pl-6">
            @forelse ($plants as $plant)
                <li>{{ $plant->name }}</li>
            @empty
                <li>No plants found.</li>
            @endforelse
        </ul>
    </div>
</div>
