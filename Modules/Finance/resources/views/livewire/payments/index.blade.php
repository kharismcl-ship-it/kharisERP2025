<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Payments</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->invoice?->invoice_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->invoice?->customer_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @else
            <p>No payments found.</p>
        @endif
    </div>
</div>