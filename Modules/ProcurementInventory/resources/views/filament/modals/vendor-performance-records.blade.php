<div class="overflow-x-auto">
    @if($records->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">No records found for this period.</p>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <th class="pb-2 pr-4">PO</th>
                <th class="pb-2 pr-4">GRN</th>
                <th class="pb-2 pr-4">Expected</th>
                <th class="pb-2 pr-4">Actual</th>
                <th class="pb-2 pr-4">Days Late</th>
                <th class="pb-2 pr-4">Quality %</th>
                <th class="pb-2">Price Var %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <td class="py-2 pr-4 font-mono text-xs">{{ $r->purchaseOrder?->po_number ?? '—' }}</td>
                <td class="py-2 pr-4 font-mono text-xs">{{ $r->goodsReceipt?->grn_number ?? '—' }}</td>
                <td class="py-2 pr-4">{{ $r->expected_delivery_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="py-2 pr-4">{{ $r->actual_delivery_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="py-2 pr-4 {{ $r->days_late > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $r->days_late > 0 ? "+{$r->days_late}" : $r->days_late }}
                </td>
                <td class="py-2 pr-4 {{ (float)$r->quality_rate < 95 ? 'text-yellow-600' : 'text-green-600' }}">
                    {{ number_format((float)$r->quality_rate, 1) }}%
                </td>
                <td class="py-2 {{ abs((float)$r->price_variance_pct) > 5 ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                    {{ number_format((float)$r->price_variance_pct, 1) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>