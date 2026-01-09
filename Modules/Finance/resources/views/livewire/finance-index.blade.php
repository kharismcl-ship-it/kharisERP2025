<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Finance</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <ul class="list-disc pl-6">
            @forelse ($accounts as $account)
                <li><span class="font-mono">{{ $account->code }}</span> — {{ $account->name }}</li>
            @empty
                <li>No accounts found.</li>
            @endforelse
        </ul>
    </div>
</div>
