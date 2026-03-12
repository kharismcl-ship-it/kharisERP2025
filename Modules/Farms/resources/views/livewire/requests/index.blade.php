<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Farm Requests</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <a href="{{ route('farms.requests.create', $farm->slug) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                New Request
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="fulfilled">Fulfilled</option>
            </select>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urgency</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $request)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $request->reference }}</td>
                            <td class="px-4 py-3 font-medium">{{ $request->title }}</td>
                            <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $request->request_type) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                    @if($request->urgency === 'urgent') bg-red-100 text-red-800
                                    @elseif($request->urgency === 'high') bg-orange-100 text-orange-800
                                    @elseif($request->urgency === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($request->urgency) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                    @if($request->status === 'approved') bg-green-100 text-green-800
                                    @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('farms.requests.show', [$farm->slug, $request]) }}"
                                       class="text-xs text-indigo-600 hover:underline">View</a>
                                    @if($request->status === 'pending')
                                        <button wire:click="openAction({{ $request->id }}, 'approve')"
                                                class="text-xs text-green-600 hover:underline">Approve</button>
                                        <button wire:click="openAction({{ $request->id }}, 'reject')"
                                                class="text-xs text-red-600 hover:underline">Reject</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $requests->links() }}</div>
        </div>

    </div>

    <!-- Action Modal -->
    @if($showActionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $actionType === 'approve' ? 'Approve Request' : 'Reject Request' }}
                </h3>
                @if($actionType === 'reject')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                        <textarea wire:model="rejectionReason" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
                    </div>
                @else
                    <p class="text-sm text-gray-600 mb-4">Confirm approval of this request?</p>
                @endif
                <div class="flex gap-3 justify-end">
                    <button wire:click="confirmAction"
                            class="px-5 py-2 {{ $actionType === 'approve' ? 'bg-green-600' : 'bg-red-600' }} text-white rounded-md text-sm hover:opacity-90">
                        Confirm
                    </button>
                    <button wire:click="$set('showActionModal', false)"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
