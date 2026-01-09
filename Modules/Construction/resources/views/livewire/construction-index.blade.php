<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Construction</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <ul class="list-disc pl-6">
            @forelse ($projects as $project)
                <li>{{ $project->name }}</li>
            @empty
                <li>No construction projects found.</li>
            @endforelse
        </ul>
    </div>
</div>
