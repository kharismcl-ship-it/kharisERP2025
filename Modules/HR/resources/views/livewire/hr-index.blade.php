<div class="space-y-6">
    <h1 class="text-2xl font-semibold">HR</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <ul class="list-disc pl-6">
            @forelse ($departments as $department)
                <li>{{ $department->name }}</li>
            @empty
                <li>No departments found.</li>
            @endforelse
        </ul>
    </div>
</div>
