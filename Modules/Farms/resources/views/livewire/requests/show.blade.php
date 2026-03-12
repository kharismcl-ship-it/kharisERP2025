<div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6 space-y-5">

            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $request->title }}</h1>
                    <p class="text-sm text-gray-500">{{ $request->reference }} &bull; {{ $farm->name }}</p>
                </div>
                <a href="{{ route('farms.requests.index', $farm->slug) }}" class="text-sm text-blue-600 hover:underline">Back</a>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm border-b pb-4">
                <div class="flex justify-between"><span class="text-gray-500">Type:</span><span class="capitalize">{{ str_replace('_', ' ', $request->request_type) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Urgency:</span><span class="capitalize">{{ $request->urgency }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status:</span>
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                        @if($request->status === 'approved') bg-green-100 text-green-800
                        @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Submitted:</span><span>{{ $request->created_at->format('M j, Y H:i') }}</span></div>
                @if($request->approved_at)
                    <div class="flex justify-between col-span-2"><span class="text-gray-500">Approved:</span><span>{{ $request->approved_at->format('M j, Y H:i') }}</span></div>
                @endif
                @if($request->rejection_reason)
                    <div class="col-span-2 bg-red-50 rounded p-3">
                        <p class="text-xs font-medium text-red-700 mb-1">Rejection Reason</p>
                        <p class="text-sm text-red-800">{{ $request->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            @if($request->description)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Description</h3>
                    <p class="text-sm text-gray-800">{{ $request->description }}</p>
                </div>
            @endif

            <!-- Items -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Items</h3>
                <table class="w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Qty</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Unit</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Unit Cost</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($request->items as $item)
                            <tr>
                                <td class="px-3 py-2">{{ $item->description }}</td>
                                <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                                <td class="px-3 py-2">{{ $item->unit ?? '—' }}</td>
                                <td class="px-3 py-2 text-right">{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '—' }}</td>
                                <td class="px-3 py-2 text-right font-medium">
                                    {{ ($item->unit_cost && $item->quantity) ? number_format($item->unit_cost * $item->quantity, 2) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
