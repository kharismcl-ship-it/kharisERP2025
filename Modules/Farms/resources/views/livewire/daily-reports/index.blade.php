<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Daily Reports</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <a href="{{ route('farms.daily-reports.create', $farm->slug) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                Submit Report
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 flex gap-3">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 text-sm">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="reviewed">Reviewed</option>
            </select>
            <input type="date" wire:model.live="dateFilter" class="rounded-md border-gray-300 text-sm"
                   placeholder="Filter by date" />
        </div>

        <!-- Reports Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Summary</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-4 py-3 font-medium">
                                {{ $report->report_date?->format('M j, Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $report->worker?->name ?? 'Staff' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate">
                                {{ Str::limit($report->summary, 80) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($report->status === 'reviewed') bg-green-100 text-green-800
                                    @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('farms.daily-reports.show', [$farm->slug, $report]) }}"
                                       class="text-xs text-indigo-600 hover:underline">View</a>
                                    @if($report->status === 'submitted')
                                        <button wire:click="openReviewModal({{ $report->id }})"
                                                class="text-xs text-green-600 hover:underline">
                                            Review
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">No reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $reports->links() }}</div>
        </div>

    </div>

    <!-- Review Modal -->
    @if($showReviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Mark Report as Reviewed</h3>
                <p class="text-sm text-gray-600 mb-4">This will mark the report as reviewed and record your name as reviewer.</p>
                <div class="flex gap-3 justify-end">
                    <button wire:click="approveReview"
                            class="px-5 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                        Confirm Review
                    </button>
                    <button wire:click="$set('showReviewModal', false)"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
