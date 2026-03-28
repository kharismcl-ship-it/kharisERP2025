<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Search by Lot Number</h3>
            <div class="flex gap-3">
                <input
                    type="text"
                    wire:model="lotSearch"
                    placeholder="e.g. LOT-202603-00001"
                    class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
                <button
                    wire:click="searchLot"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700"
                >
                    Search
                </button>
            </div>

            @if($searchError)
                <p class="mt-2 text-sm text-red-600">{{ $searchError }}</p>
            @endif
        </div>

        {{-- Results --}}
        @if($lot && $chain)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Lot Details --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span>📦</span> Lot Details
                    @if($lot->is_recalled)
                        <span class="ml-2 px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs font-bold">RECALLED</span>
                    @endif
                </h4>
                <dl class="space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Lot Number</dt><dd class="font-mono font-bold">{{ $lot->lot_number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Quality Grade</dt><dd>{{ $lot->quality_grade }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Quantity</dt><dd>{{ number_format($lot->quantity_kg, 2) }} {{ $lot->unit }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Harvest Date</dt><dd>{{ $lot->harvest_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Expiry Date</dt><dd>{{ $lot->expiry_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Storage</dt><dd>{{ $lot->storage_location ?? '—' }}</dd></div>
                    @if($lot->moisture_content_pct)
                    <div class="flex justify-between"><dt class="text-gray-500">Moisture</dt><dd>{{ $lot->moisture_content_pct }}%</dd></div>
                    @endif
                    @if($lot->aflatoxin_ppb)
                    <div class="flex justify-between"><dt class="text-gray-500">Aflatoxin</dt><dd>{{ $lot->aflatoxin_ppb }} ppb</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Farm & Harvest --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2"><span>🌾</span> Farm & Harvest</h4>
                <dl class="space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Farm</dt><dd>{{ $chain['farm']?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Crop Cycle</dt><dd>{{ $chain['crop_cycle']?->name ?? '—' }}</dd></div>
                    @if($chain['harvest'])
                    <div class="flex justify-between"><dt class="text-gray-500">Harvest Qty</dt><dd>{{ number_format($chain['harvest']->quantity, 3) }} {{ $chain['harvest']->unit ?? 'kg' }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">Inputs Applied</dt><dd>{{ $chain['inputs']->count() }} records</dd></div>
                </dl>
            </div>

            {{-- Orders --}}
            @if($chain['orders']->count())
            <div class="md:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2"><span>🛒</span> Orders Containing This Lot</h4>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-2">Order #</th><th class="pb-2">Customer</th><th class="pb-2">Status</th><th class="pb-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chain['orders'] as $order)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-1 font-mono">{{ $order->order_number ?? '#' . $order->id }}</td>
                            <td class="py-1">{{ $order->customer_name ?? '—' }}</td>
                            <td class="py-1">{{ $order->status }}</td>
                            <td class="py-1">{{ $order->created_at?->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Recall info --}}
            @if($lot->is_recalled)
            <div class="md:col-span-2 rounded-xl border border-red-300 bg-red-50 dark:bg-red-900/20 p-6">
                <h4 class="font-semibold text-red-700 dark:text-red-400 mb-2">⚠️ Recall Information</h4>
                <p class="text-sm text-red-700">{{ $lot->recall_reason }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
</x-filament-panels::page>