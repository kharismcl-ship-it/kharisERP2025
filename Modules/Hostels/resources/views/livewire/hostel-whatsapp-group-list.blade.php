<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">WhatsApp Groups</h1>
        <a href="{{ route('hostels.whatsapp-groups.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Group</a>
    </div>

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="py-2">Name</th>
                <th class="py-2">Hostel</th>
                <th class="py-2">Group ID</th>
                <th class="py-2">Active</th>
                <th class="py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $group)
                <tr>
                    <td class="border px-4 py-2">{{ $group->name }}</td>
                    <td class="border px-4 py-2">{{ $group->hostel->name }}</td>
                    <td class="border px-4 py-2">{{ $group->group_id }}</td>
                    <td class="border px-4 py-2">{{ $group->is_active ? 'Yes' : 'No' }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('hostels.whatsapp-groups.show', $group) }}" class="text-blue-500">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $groups->links() }}
</div>
